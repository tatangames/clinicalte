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
use App\Models\ObjetoEspecifico;
use App\Models\Proveedores;
use App\Models\SubLinea;
use App\Models\TipoFactura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FarmaciaController extends Controller
{


    public function indexRegistroArticulo()
    {
        $arrayLinea = Linea::orderBy('nombre')->get();
        $arraySubLinea = SubLinea::orderBy('nombre')->get();
        $arrayEnvase = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 1)->orderBy('nombre')->get();
        $arrayFormaFarmaceutica = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 2)->orderBy('nombre')->get();
        $arrayConcentracion = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 3)->orderBy('nombre')->get();
        $arrayContenido = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 4)->orderBy('nombre')->get();
        $arrayAdministracion = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 5)->orderBy('nombre')->get();
        $arrayObjEspecifico = ObjetoEspecifico::orderBy('nombre')->get();

        return view('backend.admin.farmacia.registrararticulo.vistaregistrararticulo', compact('arrayLinea',
            'arraySubLinea', 'arrayEnvase', 'arrayFormaFarmaceutica', 'arrayConcentracion',
            'arrayContenido', 'arrayAdministracion', 'arrayObjEspecifico'));
    }


    public function registrarArticulo(Request $request)
    {
        $regla = array(
            'idLinea' => 'required',
            'nombre' => 'required',
            'idCodigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            if($request->codigoArticulo != null){
                if(FarmaciaArticulo::where('codigo_articulo', $request->codigoArticulo)->first()){
                    return ['success' => 1];
                }
            }

            $articulo = new FarmaciaArticulo();
            $articulo->id_linea = $request->idLinea;
            $articulo->id_sublinea = $request->idSubLinea;
            $articulo->id_objespecifico = $request->idCodigo;
            $articulo->nombre = $request->nombre;
            $articulo->codigo_articulo = $request->codigoArticulo;
            $articulo->existencia_minima = $request->existencia;
            $articulo->save();

            if($request->idLinea == 1){
                // SOLO GUARDAR SI ES TIPO MEDICAMENTOS

                $dato = new ArticuloMedicamento();
                $dato->id_farmacia_articulo = $articulo->id;
                $dato->id_con_far_envase = $request->idEnvase;
                $dato->id_con_far_forma = $request->idFormaFarma;
                $dato->id_con_far_concentracion = $request->idConcentracion;
                $dato->id_con_far_contenido = $request->idContenido;
                $dato->id_con_far_administra = $request->idAdministracion;
                $dato->nombre_generico = $request->nombreGenerico;
                $dato->save();
            }

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function guardarExtraContenidoFarmaceutica(Request $request){

        $regla = array(
            'idtipo' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new ContenidoFarmaceutica();
        $dato->id_tipo_farmaceutica = $request->idtipo;
        $dato->nombre = $request->nombre;
        $dato->save();

        if($request->idtipo == 1){
            $lista = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 1)
                ->orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'lista' => $lista];
        }
        else if($request->idtipo == 2){
            $lista = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 2)
                ->orderBy('nombre', 'ASC')->get();

            return ['success' => 2, 'lista' => $lista];
        }
        else if($request->idtipo == 3){
            $lista = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 3)
                ->orderBy('nombre', 'ASC')->get();

            return ['success' => 3, 'lista' => $lista];
        }
        else if($request->idtipo == 4){
            $lista = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 4)
                ->orderBy('nombre', 'ASC')->get();

            return ['success' => 4, 'lista' => $lista];
        }else{
            $lista = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 5)
                ->orderBy('nombre', 'ASC')->get();

            return ['success' => 5, 'lista' => $lista];
        }
    }




    //************************* INGRESAR ARTICULO ************************************

    public function indexIngresoArticulo(){

        $arrayTipoFactura = TipoFactura::orderBy('nombre')->get();
        $arrayProveedor = Proveedores::orderBy('nombre')->get();

        // SOLO USAR FONDOS PROPIOS
        $arrayFuente = FuenteFinanciamiento::where('id', 3)->get();

        return view('backend.admin.farmacia.ingreso.vistaingresoinventario', compact('arrayTipoFactura',
            'arrayFuente', 'arrayProveedor'));
    }


    public function buscarMedicamento(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = FarmaciaArticulo::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo_articulo', 'LIKE', "%{$query}%")
                ->get();

            foreach ($data as $info){

                if($info->codigo_articulo != null){
                    $info->nombreunido = $info->codigo_articulo . ' - ' . $info->nombre;
                }else{
                    $info->nombreunido = $info->nombre;
                }

                // BUSCAR CANTIDAD Y ULTIMO PRECIO DEL MISMO

                $info->existencia = 88;

                if($filaUltima = EntradaMedicamentoDetalle::where('id_medicamento', $info->id)
                    ->orderBy('id', 'DESC')
                    ->first()){

                    $precioUltimo = '$' . number_format((float)$filaUltima->precio, 2, '.', ',');

                    $info->ultimoprecio = $precioUltimo;
                }else{
                    $info->ultimoprecio = "No Tiene un Registro aun";
                }
            }

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; max-height: 300px; width: 550px">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'" data-ultimoprecio="'.$row->ultimoprecio.'" data-existencia="'.$row->existencia.'">'.$row->nombreunido.'</li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="'.$row->id.'"  data-ultimoprecio="'.$row->ultimoprecio.'" data-existencia="'.$row->existencia.'">'.$row->nombreunido.'</li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }



    public function registrarNuevoMedicamento(Request $request){

        $regla = array(
            'numFactura' => 'required',
            'tipoFactura' => 'required',
            'fuenteFina' => 'required',
            'proveedor' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            $usuario = auth()->user();

            $fechaCarbon = Carbon::parse(Carbon::now());

            $nuevoMedi = new EntradaMedicamento();
            $nuevoMedi->numero_factura = $request->numFactura;
            $nuevoMedi->id_tipofactura = $request->tipoFactura;
            $nuevoMedi->id_fuentefina = $request->fuenteFina;
            $nuevoMedi->id_proveedor = $request->proveedor;
            $nuevoMedi->id_usuario = $usuario->id;
            $nuevoMedi->fecha = $fechaCarbon;
            $nuevoMedi->save();

            foreach ($datosContenedor as $filaArray) {

                // infoPrecioDonacion

                $infoMedicamento = FarmaciaArticulo::where('id', $filaArray['infoIdMedicamento'])->first();

                $detalle = new EntradaMedicamentoDetalle();
                $detalle->id_entrada_medicamento = $nuevoMedi->id;
                $detalle->id_medicamento = $filaArray['infoIdMedicamento'];
                $detalle->nombre_copia = $infoMedicamento->nombre;
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->cantidad_fija = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
                $detalle->precio_donacion = $filaArray['infoPrecioDonacion'];
                $detalle->lote = $filaArray['infoLote'];
                $detalle->fecha_vencimiento = $filaArray['infoFecha'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function actualizarNuevoMedicamento(Request $request){

        $regla = array(
            'numFactura' => 'required',
            'tipoFactura' => 'required',
            'fuenteFina' => 'required',
            'proveedor' => 'required',
            'identrada' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            EntradaMedicamento::where('id', $request->identrada)->update([
                'id_tipofactura' => $request->tipoFactura,
                'id_fuentefina' => $request->fuenteFina,
                'id_proveedor' => $request->proveedor,
                'fecha' => $request->fecha,
                'numero_factura' => $request->numFactura
            ]);

            foreach ($datosContenedor as $filaArray) {

                $infoMedicamento = FarmaciaArticulo::where('id', $filaArray['infoIdMedicamento'])->first();

                $detalle = new EntradaMedicamentoDetalle();
                $detalle->id_entrada_medicamento = $request->identrada;
                $detalle->id_medicamento = $filaArray['infoIdMedicamento'];
                $detalle->nombre_copia = $infoMedicamento->nombre;
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->cantidad_fija = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
                $detalle->precio_donacion = $filaArray['infoPrecioDonacion'];

                $detalle->lote = $filaArray['infoLote'];
                $detalle->fecha_vencimiento = $filaArray['infoFecha'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }














}
