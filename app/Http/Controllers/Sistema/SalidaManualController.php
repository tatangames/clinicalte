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

        // IDs de entrada_medicamento_detalle que ya tuvieron salida,
        // junto con su cantidad despachada total
        $salidas = SalidaRecetaDetalle::select('id_entrada_detalle',
            DB::raw('SUM(cantidad) as total_salida'))
            ->groupBy('id_entrada_detalle')
            ->pluck('total_salida', 'id_entrada_detalle');

        // Calcular stock real por medicamento:
        // suma de entradas - suma de salidas de cada entrada_detalle
        $stockPorMedicamento = EntradaMedicamentoDetalle::select(
            'id_medicamento',
            DB::raw('SUM(cantidad) as total_entrada'),
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
        $regla = array(
            'motivo' => 'required',
            'fecha'  => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            $usuario  = auth()->user();
            $fechaHora = Carbon::parse($request->fecha)->setTimeFrom(Carbon::now());

            $orden = new SalidaReceta();
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

                // Bloquear fila para evitar condición de carrera
                $infoEntrada = EntradaMedicamentoDetalle::where('id', $filaArray['infoIdEntrada'])
                    ->lockForUpdate()
                    ->first();

                if (!$infoEntrada) {
                    DB::rollback();
                    return ['success' => 99];
                }

                // Stock real = cantidad original de entrada - todo lo ya despachado de este lote
                $totalSalidas = SalidaRecetaDetalle::where('id_entrada_detalle', $infoEntrada->id)
                    ->sum('cantidad');

                $stockReal = $infoEntrada->cantidad - $totalSalidas;
                $resta     = $stockReal - $filaArray['infoCantidad'];

                if ($resta < 0) {
                    DB::rollback();
                    return ['success' => 1, 'fila' => $fila, 'cantidad' => $stockReal];
                }

                // Registrar la salida — NO se modifica entrada_medicamento_detalle
                $detalle = new SalidaRecetaDetalle();
                $detalle->id_salidareceta    = $orden->id;
                $detalle->id_entrada_detalle = $filaArray['infoIdEntrada'];
                $detalle->cantidad           = $filaArray['infoCantidad'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 2];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function elegirProductoParaSalida($idproducto){

        $arraySalidas = DB::table('entrada_medicamento AS en')
            ->join('entrada_medicamento_detalle AS deta', 'en.id', '=', 'deta.id_entrada_medicamento')
            ->select('en.fecha', 'deta.id_entrada_medicamento', 'deta.id_medicamento', 'deta.cantidad',
                'deta.precio', 'deta.lote', 'deta.fecha_vencimiento', 'en.numero_factura', 'deta.id AS identradadetalle')
            ->where('deta.id_medicamento', $idproducto)
            ->where('deta.cantidad', '>', 0)
            ->orderBy('deta.fecha_vencimiento', 'ASC')
            ->get();

        $conteo = 1;
        if (count($arraySalidas) == 0) {
            $conteo = 0;
        }


        foreach ($arraySalidas as $dato){

            $infoDe = FarmaciaArticulo::where('id', $dato->id_medicamento)->first();
            $dato->nombre = $infoDe->nombre;
            $dato->fechaVencimiento = date("d-m-Y", strtotime($dato->fecha_vencimiento));

            $dato->fechaEntrada = date("d-m-Y", strtotime($dato->fecha));

            $dato->precio = '$' . number_format((float)$dato->precio, 2, '.', ',');
        }

        return view('backend.admin.farmacia.salidamanual.modalproductosalida', compact('conteo', 'arraySalidas'));
    }

}
