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
use App\Models\SubLinea;
use App\Models\TipoFactura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class SalidaManualController extends Controller
{

    public function indexSalidaFarmacia(){

        $arrayMotivo = MotivoFarmacia::orderBy('nombre')->get();

        $arrayProductoFarmacia = FarmaciaArticulo::all();

        $pilaIdProducto = array();

        foreach ($arrayProductoFarmacia as $info){

            // NECESITO UNICAMENTE CANTIDAD MAYOR A 0

            $cantidad = EntradaMedicamentoDetalle::where('id_medicamento', $info->id)->sum('cantidad');

            if($cantidad > 0){
                array_push($pilaIdProducto, $info->id);
            }
        }


        $arrayProducto = FarmaciaArticulo::whereIn('id', $pilaIdProducto)->orderBy('nombre', 'ASC')->get();

        foreach ($arrayProducto as $detalle){

            $cantidadTotal = EntradaMedicamentoDetalle::where('id_medicamento', $detalle->id)->sum('cantidad');

            if($detalle->codigo_articulo != null){
                $detalle->nombretotal = $detalle->codigo_articulo . ' - ' . $detalle->nombre . ' (Existencia: ' . $cantidadTotal . ')';
            }else{
                $detalle->nombretotal = $detalle->nombre . ' (Existencia: ' . $cantidadTotal . ')';
            }
        }

        return view('backend.admin.farmacia.salidamanual.vistaordensalidamanual', compact('arrayMotivo', 'arrayProducto'));
    }



    public function registrarOrdenSalidaFarmacia(Request $request){

        $regla = array(
            'motivo' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            $usuario = auth()->user();
            $horaCarbon = Carbon::parse(Carbon::now());

            $orden = new OrdenSalida();
            $orden->id_usuario = $usuario->id;
            $orden->id_motivo = $request->motivo;
            $orden->fecha = $request->fecha;
            $orden->hora = $horaCarbon;
            $orden->observaciones = $request->observaciones;
            $orden->save();

            $fila = 0;

            // REGISTRAR CADA SALIDA
            foreach ($datosContenedor as $filaArray) {
                $fila++;

                $infoEntrada = EntradaMedicamentoDetalle::where('id', $filaArray['infoIdEntrada'])->first();

                $resta = $infoEntrada->cantidad - $filaArray['infoCantidad'];

                if($resta < 0){
                    return ['success' => 1, 'fila' => $fila, 'cantidad' => $infoEntrada->cantidad];
                }

                // ACTUALIZAR CANTIDAD

                EntradaMedicamentoDetalle::where('id', $filaArray['infoIdEntrada'])->update([
                    'cantidad' => $resta
                ]);

                $detalle = new OrdenSalidaDetalle();
                $detalle->id_orden_salida = $orden->id;
                $detalle->id_entrada_medi_detalle = $filaArray['infoIdEntrada'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
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
