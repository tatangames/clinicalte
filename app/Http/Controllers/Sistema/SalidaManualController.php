<?php

namespace App\Http\Controllers\Sistema;


use App\Http\Controllers\Controller;
use App\Models\ArticuloMedicamento;
use App\Models\ContenidoFarmaceutica;
use App\Models\EntradaMedicamento;
use App\Models\EntradaMedicamentoDetalle;
use App\Models\FarmaciaArticulo;
use App\Models\FuenteFinanciamiento;
use App\Models\Linea;
use App\Models\MotivoFarmacia;
use App\Models\ObjetoEspecifico;
use App\Models\Proveedores;
use App\Models\SalidaReceta;
use App\Models\SalidaRecetaDetalle;
use App\Models\SubLinea;
use App\Models\TipoFactura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class SalidaManualController extends Controller
{

    public function indexSalidaFarmacia()
    {
        $arrayMotivo = MotivoFarmacia::orderBy('nombre')->get();

        // Stock real por medicamento: cantidad_fija (entrada) - salidas despachadas
        $stockPorMedicamento = EntradaMedicamentoDetalle::select(
            'id_medicamento',
            DB::raw('SUM(cantidad_fija) as total_entrada'),
            DB::raw('SUM(COALESCE((
                SELECT SUM(srd.cantidad)
                FROM salida_receta_detalle srd
                WHERE srd.id_entrada_detalle = entrada_medicamento_detalle.id
            ), 0)) as total_salida')
        )
            ->groupBy('id_medicamento')
            ->get()
            ->mapWithKeys(function ($row) {
                return [
                    $row->id_medicamento => $row->total_entrada - $row->total_salida
                ];
            });

        // Solo medicamentos con stock > 0
        $idsConStock = $stockPorMedicamento->filter(fn($stock) => $stock > 0)->keys();

        $arrayProducto = FarmaciaArticulo::whereIn('id', $idsConStock)
            ->orderBy('nombre')
            ->get();

        foreach ($arrayProducto as $detalle) {
            $stock = $stockPorMedicamento[$detalle->id] ?? 0;

            $detalle->nombretotal = ($detalle->codigo_articulo
                    ? $detalle->codigo_articulo . ' - ' . $detalle->nombre
                    : $detalle->nombre)
                . ' (Existencia: ' . $stock . ')';
        }

        return view('backend.admin.farmacia.salidamanual.vistaordensalidamanual',
            compact('arrayMotivo', 'arrayProducto'));
    }


    public function registrarOrdenSalidaFarmacia(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'motivo' => 'required',
            'fecha'  => 'required'
        ]);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            $usuario   = auth()->user();
            $fechaHora = Carbon::parse($request->fecha)->setTimeFrom(Carbon::now());

            $orden              = new SalidaReceta();
            $orden->id_recetas  = null;
            $orden->id_usuario  = $usuario->id;
            $orden->id_motivo   = $request->motivo;
            $orden->fecha       = $fechaHora;
            $orden->notas       = $request->observaciones;
            $orden->tipo_salida = 'manual';
            $orden->save();

            $fila = 0;

            foreach ($datosContenedor as $filaArray) {
                $fila++;

                $infoEntrada = EntradaMedicamentoDetalle::where('id', $filaArray['infoIdEntrada'])
                    ->lockForUpdate()
                    ->first();

                if (!$infoEntrada) {
                    DB::rollback();
                    return ['success' => 99];
                }

                $totalSalidas = SalidaRecetaDetalle::where('id_entrada_detalle', $infoEntrada->id)
                    ->sum('cantidad');

                // cantidad_fija = lo que entró originalmente en este lote
                $stockReal = $infoEntrada->cantidad_fija - $totalSalidas;

                if (($stockReal - $filaArray['infoCantidad']) < 0) {
                    DB::rollback();
                    return ['success' => 1, 'fila' => $fila, 'cantidad' => $stockReal];
                }

                $detalle                     = new SalidaRecetaDetalle();
                $detalle->id_salidareceta    = $orden->id;
                $detalle->id_entrada_detalle = $filaArray['infoIdEntrada'];
                $detalle->cantidad           = $filaArray['infoCantidad'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 2];

        } catch (\Throwable $e) {
            Log::error('registrarOrdenSalidaFarmacia: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function elegirProductoParaSalida($idproducto)
    {
        $idsLotes = EntradaMedicamentoDetalle::where('id_medicamento', $idproducto)
            ->pluck('id');

        $salidas = SalidaRecetaDetalle::whereIn('id_entrada_detalle', $idsLotes)
            ->select('id_entrada_detalle', DB::raw('SUM(cantidad) as total_salida'))
            ->groupBy('id_entrada_detalle')
            ->pluck('total_salida', 'id_entrada_detalle');

        $arraySalidas = DB::table('entrada_medicamento AS en')
            ->join('entrada_medicamento_detalle AS deta', 'en.id', '=', 'deta.id_entrada_medicamento')
            ->join('farmacia_articulo AS fa', 'fa.id', '=', 'deta.id_medicamento')
            ->select(
                'en.fecha',
                'deta.id_entrada_medicamento',
                'deta.id_medicamento',
                'deta.cantidad_fija',
                'deta.precio',
                'deta.lote',
                'deta.fecha_vencimiento',
                'en.numero_factura',
                'deta.id AS identradadetalle',
                'fa.nombre'
            )
            ->where('deta.id_medicamento', $idproducto)
            ->orderBy('deta.fecha_vencimiento', 'ASC')
            ->get();

        // stdClass no permite asignar propiedades en filter,
        // se hace en un foreach normal antes de filtrar
        $resultado = [];
        foreach ($arraySalidas as $dato) {
            $totalSalida     = $salidas[$dato->identradadetalle] ?? 0;
            $dato->stockReal = $dato->cantidad_fija - $totalSalida;

            if ($dato->stockReal <= 0) continue; // saltar lotes agotados

            $dato->fechaVencimiento = \Carbon\Carbon::parse($dato->fecha_vencimiento)->format('d-m-Y');
            $dato->fechaEntrada     = \Carbon\Carbon::parse($dato->fecha)->format('d-m-Y');
            $dato->precio           = '$' . number_format((float) $dato->precio, 2, '.', ',');

            $resultado[] = $dato;
        }

        $conteo = count($resultado) > 0 ? 1 : 0;

        return view('backend.admin.farmacia.salidamanual.modalproductosalida',
            compact('conteo', 'resultado'));
    }

}
