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
use App\Models\Proveedores;
use App\Models\SubLinea;
use App\Models\TipoFactura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CatalogoController extends Controller
{
    public function indexCatalogo()
    {
        return view('backend.admin.farmacia.catalogo.vistacatalogo');
    }

    public function tablaCatalogo()
    {
        // Calcular stock por medicamento
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

        $arrayCatalogo = FarmaciaArticulo::with('linea')
            ->with('subLinea')
            ->orderBy('nombre')
            ->get();

        // Agregar existencia a cada artículo
        foreach ($arrayCatalogo as $articulo) {
            $articulo->existencia = $stockPorMedicamento[$articulo->id] ?? 0;
        }

        return view('backend.admin.farmacia.catalogo.tablacatalogo', compact('arrayCatalogo'));
    }

    public function vistaEditarArticuloCatalogo($idarticulo){

        $infoArticulo = FarmaciaArticulo::where('id', $idarticulo)->first();

        $arrayLinea = Linea::orderBy('nombre','ASC')->get();
        $arraySubLinea = SubLinea::orderBy('nombre','ASC')->get();


        // array envase
        $arrayEnvase = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 1)->orderBy('nombre')->get();

        // array forma farmaceutica
        $arrayFormaFarmaceutica = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 2)->orderBy('nombre')->get();


        // array concentracion
        $arrayConcentracion = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 3)->orderBy('nombre')->get();


        // array contenido
        $arrayContenido = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 4)->orderBy('nombre')->get();


        // array forma administracion
        $arrayAdministracion = ContenidoFarmaceutica::where('id_tipo_farmaceutica', 5)->orderBy('nombre')->get();


        $tieneExtras = 0;
        $infoArticuloMedi = null;
        $nombreGenerico = "";
        if($infoMedi = ArticuloMedicamento::where('id_farmacia_articulo', $idarticulo)->first()){
            $tieneExtras = 1;
            $infoArticuloMedi = $infoMedi;
            $nombreGenerico = $infoMedi->nombre_generico;
        }

        return view('backend.admin.farmacia.catalogo.vistaeditarcatalogo', compact('infoArticulo',
            'arrayLinea', 'arraySubLinea', 'arrayEnvase', 'arrayFormaFarmaceutica', 'arrayConcentracion',
            'arrayContenido', 'arrayAdministracion', 'tieneExtras',
            'infoArticuloMedi', 'idarticulo', 'nombreGenerico'));
    }



    public function actualizarArticuloCatalogo(Request $request){

        $regla = array(
            'idArticulo' => 'required',
            'idLinea' => 'required',
            'nombre' => 'required',
            'infoextra' => 'required' // saber si agregara datos extras
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {

            FarmaciaArticulo::where('id', $request->idArticulo)->update([
                'id_linea' => $request->idLinea,
                'id_sublinea' => $request->idSubLinea,
                'nombre' => $request->nombre,
                'codigo_articulo' => $request->codigoArticulo,
                'existencia_minima' => $request->existencia,
            ]);


            if(ArticuloMedicamento::where('id_farmacia_articulo', $request->idArticulo)->first()){

                ArticuloMedicamento::where('id_farmacia_articulo', $request->idArticulo)->update([
                    'id_con_far_envase' => $request->idEnvase,
                    'id_con_far_forma' => $request->idFormaFarma,
                    'id_con_far_concentracion' => $request->idConcentracion,
                    'id_con_far_contenido' => $request->idContenido,
                    'id_con_far_administra' => $request->idAdministracion,
                    'nombre_generico' => $request->nombreGenerico,
                ]);
            }
            else if($request->infoextra == 1){

                // CREAR AL NUEVO DATOS EXTRA

                $datoNuevo = new ArticuloMedicamento();
                $datoNuevo->id_farmacia_articulo = $request->idArticulo;
                $datoNuevo->id_con_far_envase = $request->idEnvase;
                $datoNuevo->id_con_far_forma = $request->idFormaFarma;
                $datoNuevo->id_con_far_concentracion = $request->idConcentracion;
                $datoNuevo->id_con_far_contenido = $request->idContenido;
                $datoNuevo->id_con_far_administra = $request->idAdministracion;
                $datoNuevo->nombre_generico = $request->nombreGenerico;
                $datoNuevo->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




    //************************** HISTORIAL DE ENTRADAS ************************************

    public function indexHistorialEntradas()
    {
        return view('backend.admin.historial.entradas.vistaentradas');
    }


    public function tablaHistorialEntradas(Request $request)
    {
        $query = EntradaMedicamento::with(['proveedor', 'fuenteFinanciamiento', 'tipoFactura'])
            ->orderBy('fecha', 'DESC');

        // Filtro rango de fechas
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $query->whereBetween('fecha', [
                $request->fecha_desde,
                $request->fecha_hasta,
            ]);
        }

        // Filtro número de factura (búsqueda parcial)
        if ($request->filled('factura')) {
            $query->where('numero_factura', 'LIKE', '%' . $request->factura . '%');
        }

        $arrayEntradas = $query->get();

        return view('backend.admin.historial.entradas.tablaentradas', compact('arrayEntradas'));
    }


    public function vistaEditarEntrada($identrada){

        $infoEntrada = EntradaMedicamento::where('id', $identrada)->first();

        $arrayFuente = FuenteFinanciamiento::orderBy('nombre', 'ASC')->get();
        $arrayTipoFac = TipoFactura::orderBy('nombre', 'ASC')->get();
        $arrayProvee = Proveedores::orderBy('nombre', 'ASC')->get();

        $fechaFormat = Carbon::parse($infoEntrada->fecha)->format('Y-m-d');


        $arrayDetalle = DB::table('entrada_medicamento_detalle AS deta')
            ->join('farmacia_articulo AS fama', 'fama.id', '=', 'deta.id_medicamento')
            ->select('fama.nombre', 'deta.id_entrada_medicamento', 'deta.cantidad_fija',
                'deta.precio', 'deta.lote', 'deta.fecha_vencimiento', 'deta.id')
            ->where('deta.id_entrada_medicamento', $identrada)
            ->orderBy('fama.nombre', 'ASC')
            ->get();

        $contador = 0;
        foreach ($arrayDetalle as $dato){
            $contador++;
            $dato->contador = $contador;
            $dato->fechaVencimiento = date("d-m-Y", strtotime($dato->fecha_vencimiento));
            $dato->precioFormat = '$' . number_format((float)$dato->precio, 2, '.', ',');
        }

        return view('backend.admin.historial.entradas.vistaeditarentrada', compact('identrada',
            'infoEntrada', 'arrayFuente', 'arrayTipoFac', 'arrayProvee', 'fechaFormat',
            'arrayDetalle'));
    }


    function informacionEntradaMediDetalle(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = EntradaMedicamentoDetalle::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }

    function actualizarEntradaMediDetalle(Request $request){

        $regla = array(
            'id' => 'required',
            'precio' => 'required',
            'lote' => 'required',
            'fechavencimiento' => 'required',
            'preciodonacion' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        EntradaMedicamentoDetalle::where('id', $request->id)->update([
            'precio' => $request->precio,
            'lote' => $request->lote,
            'fecha_vencimiento' => $request->fechavencimiento,
            'precio_donacion' => $request->preciodonacion
        ]);

        return ['success' => 1];
    }





}
