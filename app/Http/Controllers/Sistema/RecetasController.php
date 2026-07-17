<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\Antropometria;
use App\Models\ArticuloMedicamento;
use App\Models\ConsultaPaciente;
use App\Models\ContenidoFarmaceutica;
use App\Models\CuadroClinico;
use App\Models\Diagnostico;
use App\Models\EstadoCivil;
use App\Models\FarmaciaArticulo;
use App\Models\FuenteFinanciamiento;
use App\Models\Linea;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\MotivoFarmacia;
use App\Models\Paciente;
use App\Models\PacienteAntecedentes;
use App\Models\Profesion;
use App\Models\Proveedores;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\SalasEspera;
use App\Models\SubLinea;
use App\Models\TipeoSanguineo;
use App\Models\TipoAntecedente;
use App\Models\TipoDocumento;
use App\Models\TipoFarmaceutica;
use App\Models\TipoPaciente;
use App\Models\TipoProveedor;
use App\Models\Usuario;
use App\Models\ViaReceta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RecetasController extends Controller
{

    public function indexVistaNuevaReceta($idconsulta){

        $infoConsulta = ConsultaPaciente::where('id', $idconsulta)->first();
        $infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();

        $nombreCompleto = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

        // SOLO SERA FONDOS PROPIOS
        $arrayFuente = FuenteFinanciamiento::where('id', 3)->get();
        $arrayDiagnostico = Diagnostico::orderBy('nombre', 'ASC')->get();
        $arrayVia = ViaReceta::orderBy('nombre', 'ASC')->get();

        $fechaActual = Carbon::now()->toDateString();

        return view('backend.admin.historialclinico.recetas.vistanuevareceta', compact('idconsulta',
            'nombreCompleto', 'arrayFuente', 'arrayDiagnostico', 'arrayVia', 'fechaActual'));
    }

    public function listadoMedicamentosPorFuenteFinan(Request $request)
    {
        $regla = ['idfuente' => 'required'];
        $validar = Validator::make($request->all(), $regla);
        if ($validar->fails()) { return ['success' => 0]; }

        // Traer entradas con su medicamento en un solo query
        $arrayMedicamentos = DB::table('entrada_medicamento AS em')
            ->join('entrada_medicamento_detalle AS deta', 'em.id', '=', 'deta.id_entrada_medicamento')
            ->join('farmacia_articulo AS fa', 'fa.id', '=', 'deta.id_medicamento')
            ->leftJoin('articulo_medicamento AS am', 'am.id_farmacia_articulo', '=', 'deta.id_medicamento')
            ->select(
                'deta.id',
                'deta.id_medicamento',
                'deta.cantidad AS cantidad_entrada',
                'deta.lote',
                'deta.fecha_vencimiento',
                'em.id_fuentefina',
                'fa.nombre',
                'am.nombre_generico'
            )
            ->where('em.id_fuentefina', $request->idfuente)
            ->get();

        // Calcular salidas de todos estos lotes en UN solo query
        $ids = $arrayMedicamentos->pluck('id');

        $salidas = DB::table('salida_receta_detalle')
            ->whereIn('id_entrada_detalle', $ids)
            ->select('id_entrada_detalle', DB::raw('SUM(cantidad) as total_salida'))
            ->groupBy('id_entrada_detalle')
            ->pluck('total_salida', 'id_entrada_detalle');

        $resultado = [];

        foreach ($arrayMedicamentos as $detalle) {
            // Stock real = lo que entró - lo que ya se despachó de este lote
            $totalSalida  = $salidas[$detalle->id] ?? 0;
            $stockReal    = $detalle->cantidad_entrada - $totalSalida;

            // Solo mostrar lotes con stock disponible
            if ($stockReal <= 0) continue;

            $fechaVencimiento = \Carbon\Carbon::parse($detalle->fecha_vencimiento)->format('d-m-Y');

            $detalle->cantidadTotal = $stockReal;
            $detalle->nombretotal   = $detalle->nombre
                . ' (Existencia: ' . $stockReal . ')'
                . ' (Lote: ' . $detalle->lote . ')'
                . ' (Vencimiento: ' . $fechaVencimiento . ')';
            $detalle->nombreGenerico = $detalle->nombre_generico ?? '';

            $resultado[] = $detalle;
        }

        return [
            'success'   => 1,
            'dataArray' => $resultado,
            'hayfilas'  => count($resultado) > 0,
        ];
    }


    public function registroNuevaRecetaParaPaciente(Request $request){

        $regla = array(
            'idconsulta' => 'required',
            'fecha' => 'required',
            'diagnostico' => 'required',
        );

        // indicacionGeneral
        // proximaCita

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Receta::where('id_consulta', $request->idconsulta)->first()){
            return ['success' => 1];
        }
        else{

            DB::beginTransaction();

            try {
                $usuario = auth()->user();


                $infoConsulta = ConsultaPaciente::where('id', $request->idconsulta)->first();
                $datosContenedor = json_decode($request->contenedorArray, true);

                $receta = new Receta();
                $receta->id_consulta = $request->idconsulta;
                $receta->id_paciente = $infoConsulta->id_paciente;
                $receta->id_diagnostico = $request->diagnostico;
                $receta->descripcion_general = $request->indicacionGeneral;
                $receta->fecha = $request->fecha;
                $receta->proxima_cita = $request->proximaCita;
                $receta->estado = 1;
                $receta->id_usuario = $usuario->id;
                $receta->id_usuario_estado = null; // saber que usuario denego receta
                $receta->save();

                // REGISTRAR CADA FILA MEDICAMENTO
                // SE DEBE REGISTRAR EL ID ENTRADA DETALLE Y LA CANTIDAD A RETIRARLE

                foreach ($datosContenedor as $filaArray) {

                    $detalle = new RecetaDetalle();
                    $detalle->id_recetas = $receta->id;
                    $detalle->id_entrada_detalle = $filaArray['infoIdMedicamento']; // VIENE ID ENTRADA MEDICAMENTO DETALLE
                    $detalle->cantidad = $filaArray['infoCantidad'];
                    $detalle->descripcion = $filaArray['infoIndicacion'];
                    $detalle->id_via = $filaArray['infoIdVia'];
                    $detalle->save();
                }

                DB::commit();
                return ['success' => 2];

            }catch(\Throwable $e){
                Log::info('error: ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }
    }



    public function indexVistaEditarVerReceta($idreceta){

        $infoReceta = Receta::where('id', $idreceta)->first();

        $infoConsulta = ConsultaPaciente::where('id', $infoReceta->id_consulta)->first();
        $infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();

        $nombreCompleto = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

        $arrayFuente = FuenteFinanciamiento::where('id',3)->get();

        $arrayDiagnostico = Diagnostico::orderBy('nombre', 'ASC')->get();

        $arrayVia = ViaReceta::orderBy('nombre', 'ASC')->get();

        $fechaActual = Carbon::now()->toDateString();

        $arrayDetalle = DB::table('recetas_detalle AS red')
            ->join('entrada_medicamento_detalle AS entrade', 'entrade.id', '=', 'red.id_entrada_detalle')
            ->join('farmacia_articulo AS fama', 'fama.id', '=', 'entrade.id_medicamento')
            ->select('fama.nombre', 'red.id_recetas', 'entrade.cantidad AS cantidadActual',
                'red.id_via', 'red.cantidad', 'red.descripcion', 'fama.id AS idfarmacia', 'entrade.id AS idEntradaDeta', 'entrade.lote')
            ->where('red.id_recetas', $idreceta)
            ->orderBy('fama.nombre', 'ASC')
            ->get();

        $contador = 0;

        foreach ($arrayDetalle as $info){
            $contador++;

            $info->contador = $contador;

            $nombreGenerico = "";
            if($infoGenerico = ArticuloMedicamento::where('id_farmacia_articulo', $info->idfarmacia)->first()){
                $nombreGenerico = $infoGenerico->nombre_generico;
            }
            $info->nombreGenerico = $nombreGenerico;

            $infoVia = ViaReceta::where('id', $info->id_via)->first();
            $info->nombreVia = $infoVia->nombre;
        }

        if($infoReceta->estado != 1){
            $titulo = "VER FICHA DE RECETA";
        }else{
            $titulo = "MODIFICACIÓN FICHA DE RECETA";
        }

        return view('backend.admin.historialclinico.recetas.vistaeditarreceta', compact('idreceta',
            'nombreCompleto', 'infoReceta', 'arrayDiagnostico', 'arrayFuente', 'arrayVia',
            'fechaActual', 'arrayDetalle', 'titulo', 'infoConsulta'));
    }

    public function actualizarRecetaMedica(Request $request){

        $regla = array(
            'idreceta' => 'required',
            'fecha' => 'required',
            'diagnostico' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($infoReceta = Receta::where('id', $request->idreceta)->first()){

            if($infoReceta->estado == 2){
                return ['success' => 1, 'estado' => 'Procesada', 'idconsulta' => $infoReceta->id_consulta];
            }

            if($infoReceta->estado == 3){
                return ['success' => 1, 'estado' => 'Denegada', 'idconsulta' => $infoReceta->id_consulta];
            }


            DB::beginTransaction();

            try {

                // BORRAR ANTERIORES
                RecetaDetalle::where('id_recetas', $request->idreceta)->delete();
                $datosContenedor = json_decode($request->contenedorArray, true);

                Receta::where('id', $request->idreceta)->update([
                    'id_diagnostico' => $request->diagnostico,
                    'descripcion_general' => $request->indicacionGeneral,
                    'fecha' => $request->fecha,
                    'proxima_cita' => $request->proximaCita,
                ]);

                // REGISTRAR CADA FILA MEDICAMENTO
                foreach ($datosContenedor as $filaArray) {

                    $detalle = new RecetaDetalle();
                    $detalle->id_recetas = $request->idreceta;
                    $detalle->id_entrada_detalle = $filaArray['infoIdMedicamento']; // ID ENTRADA DETALLE
                    $detalle->cantidad = $filaArray['infoCantidad'];
                    $detalle->descripcion = $filaArray['infoIndicacion'];
                    $detalle->id_via = $filaArray['infoIdVia'];
                    $detalle->save();
                }

                DB::commit();
                return ['success' => 2, 'idconsulta' => $infoReceta->id_consulta];

            }catch(\Throwable $e){
                Log::info('error: ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }
        else{
            return ['success' => 2];
        }
    }






}
