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
use App\Models\EntradaMedicamentoDetalle;
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
use App\Models\SalidaReceta;
use App\Models\SalidaRecetaDetalle;
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

class SalidaRecetaController extends Controller
{


    public function indexSalidaFarmaciaPorReceta(){

        return view('backend.admin.farmacia.salidareceta.vistasalidarecetafarmacia');
    }



    public function tablaSalidaFarmaciaPorReceta($estado, $desde, $hasta){

        if($estado == '1'){

            $arrayRecetas = Receta::where('estado', 1)
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($arrayRecetas as $info){

                $info->fechaFormat = date("d-m-Y", strtotime($info->fecha));

                $infoPaciente = Paciente::where('id', $info->id_paciente)->first();

                $info->nombrepaciente = $info->nombres . " " . $infoPaciente->apellidos;

                $infoUsuario = Usuario::where('id', $info->id_usuario)->first();
                $info->doctor = $infoUsuario->nombre;

                $botonRetornar = 0;
                // VERIFICAR SI YA FINALIZO LA FICHA O NO
                if($infoConsu = ConsultaPaciente::where('id', $info->id_consulta)->first()){

                    if($infoConsu->estado_paciente == 2){
                        // LA FINA ES FINALIZADA, SE DEBE DE VOLVER A 1
                        $botonRetornar = 1;
                    }
                }

                $info->btnRetornar = $botonRetornar;
            }

            return view('backend.admin.farmacia.salidareceta.tablas.tablarecetapendiente', compact('arrayRecetas'));


        }else if($estado == '2'){

            $start = Carbon::parse($desde)->startOfDay();
            $end = Carbon::parse($hasta)->endOfDay();

            // PROCESADOS

            $arrayRecetas = Receta::where('estado', 2)
                ->whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($arrayRecetas as $info){

                $info->fechaFormat = date("d-m-Y", strtotime($info->fecha));

                if($info->fecha_estado != null){
                    $info->fechaEstadoFormat = date("d-m-Y", strtotime($info->fecha_estado));
                }else{
                    $info->fechaEstadoFormat = "";
                }


                $infoPaciente = Paciente::where('id', $info->id_paciente)->first();

                $info->nombrepaciente = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

                $infoUsuario = Usuario::where('id', $info->id_usuario)->first();
                $info->doctor = $infoUsuario->nombre;
            }

            return view('backend.admin.farmacia.salidareceta.tablas.tablarecetasalidaprocesada', compact('arrayRecetas'));

        }else{

            $start = Carbon::parse($desde)->startOfDay();
            $end = Carbon::parse($hasta)->endOfDay();

            $arrayRecetas = Receta::where('estado', 3)
                ->whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($arrayRecetas as $info){

                $info->fechaFormat = date("d-m-Y", strtotime($info->fecha));

                $info->fechaEstadoFormat = date("d-m-Y", strtotime($info->fecha_estado));

                $infoPaciente = Paciente::where('id', $info->id_paciente)->first();

                $info->nombrepaciente = $info->nombres . " " . $infoPaciente->apellidos;

                $infoUsuario = Usuario::where('id', $info->id_usuario)->first();
                $info->nombreuser = $infoUsuario->nombre;
            }

            // TABLA PARA DENEGADOS
            return view('backend.admin.farmacia.salidareceta.tablas.tablarecetadenegada', compact('arrayRecetas'));
        }
    }


    public function infoRecetaParaDenegar(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if($infoReceta = Receta::where('id', $request->id)->first()){

            $infoDoctor = Usuario::where('id', $infoReceta->id_usuario)->first();

            $infoPaciente = Paciente::where('id', $infoReceta->id_paciente)->first();

            $paciente = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

            return ['success' => 1, 'doctor' => $infoDoctor->nombre, 'paciente' => $paciente];
        }else{
            return ['success' => 2];
        }
    }


    public function guardarDenegacionReceta(Request $request){

        $regla = array(
            'id' => 'required',
            'descripcion' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($infoReceta = Receta::where('id', $request->id)->first()){

            // ESTADOS
            // 1: pendiente
            // 2: procesada
            // 3: denegada

            if($infoReceta->estado == 2){
                // procesada yap
                return ['success' => 1];
            }

            if($infoReceta->estado == 3){
                // ya fue denegada
                return ['success' => 2];
            }

            if($infoReceta->estado == 1){

                // esta pendiente asi que puede denegar

                $fechaCarbon = Carbon::parse(Carbon::now());
                $usuario = auth()->user();


                Receta::where('id', $request->id)->update([
                    'estado' => 3,
                    'nota_denegada' => $request->descripcion,
                    'fecha_estado' => $fechaCarbon,
                    'id_usuario_estado' => $usuario->id
                ]);

                return ['success' => 3];
            }

            // defecto
            return ['success' => 3];
        }else{
            return ['success' => 2];
        }
    }

    public function retornarPacienteSala(Request $request){

        $regla = array(
            'id' => 'required', // tabla: recetas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $infoReceta = Receta::where('id', $request->id)->first();

        // actualizar
        ConsultaPaciente::where('id', $infoReceta->id_consulta)->update([
            'estado_paciente' => 1, // VUELVE DENTRO DE SALA
        ]);

        return ['success' => 1];
    }

    public function vistaRecetaDetalleProcesar($idreceta)
    {
        $infoReceta   = Receta::findOrFail($idreceta);
        $infoConsulta = ConsultaPaciente::findOrFail($infoReceta->id_consulta);
        $infoPaciente = Paciente::findOrFail($infoConsulta->id_paciente);

        $nombreCompleto = $infoPaciente->nombres . ' ' . $infoPaciente->apellidos;
        $nombreDoctor   = Usuario::where('id', $infoReceta->id_usuario)->value('nombre');
        $fechaReceta    = \Carbon\Carbon::parse($infoReceta->fecha)->format('d-m-Y');
        $edad           = \Carbon\Carbon::parse($infoPaciente->fecha_nacimiento)->age;

        $arrayNombreMedicamento = DB::table('recetas_detalle AS rd')
            ->join('entrada_medicamento_detalle AS enta', 'rd.id_entrada_detalle', '=', 'enta.id')
            ->join('farmacia_articulo AS fama', 'enta.id_medicamento', '=', 'fama.id')
            ->select(
                'fama.nombre',
                'rd.id',
                'rd.id_recetas',
                'rd.id_entrada_detalle',
                'enta.fecha_vencimiento',
                'rd.cantidad AS cantidadRetirar',
                'enta.lote',
                'enta.cantidad AS cantidad_entrada'
            )
            ->where('rd.id_recetas', $idreceta)
            ->orderBy('fama.nombre')
            ->get();

        // Stock real: entradas - salidas previas de cada lote, en un solo query
        $ids = $arrayNombreMedicamento->pluck('id_entrada_detalle');

        $salidas = DB::table('salida_receta_detalle')
            ->whereIn('id_entrada_detalle', $ids)
            ->select('id_entrada_detalle', DB::raw('SUM(cantidad) as total_salida'))
            ->groupBy('id_entrada_detalle')
            ->pluck('total_salida', 'id_entrada_detalle');

        $contador = 0;
        foreach ($arrayNombreMedicamento as $info) {
            $contador++;
            $info->contador         = $contador;
            $info->nombreFormat     = $info->nombre;
            $info->fechaVencimiento = \Carbon\Carbon::parse($info->fecha_vencimiento)->format('d-m-Y');
            $totalSalida            = $salidas[$info->id_entrada_detalle] ?? 0;
            $info->cantidadActual   = $info->cantidad_entrada - $totalSalida;
        }

        return view('backend.admin.farmacia.salidareceta.procesar.vistaprocesarreceta', compact(
            'idreceta', 'infoPaciente', 'nombreCompleto',
            'nombreDoctor', 'fechaReceta', 'edad', 'arrayNombreMedicamento'
        ));
    }


    public function guardarSalidaProcesadaDeReceta(Request $request)
    {
        $validar = Validator::make($request->all(), ['idreceta' => 'required']);
        if ($validar->fails()) { return ['success' => 0]; }

        DB::beginTransaction();

        try {
            $infoReceta = Receta::where('id', $request->idreceta)->lockForUpdate()->first();

            if ($infoReceta->estado != 1) {
                DB::rollback();
                return ['success' => 1];
            }

            $usuario = auth()->user();

            $salida             = new SalidaReceta();
            $salida->id_recetas = $request->idreceta;
            $salida->id_usuario = $usuario->id;
            $salida->fecha      = \Carbon\Carbon::now();
            $salida->notas      = $request->notas;
            $salida->save();

            $arrayDetalle = RecetaDetalle::where('id_recetas', $request->idreceta)->get();

            foreach ($arrayDetalle as $filaArray) {

                $infoEntradaDeta = EntradaMedicamentoDetalle::where('id', $filaArray->id_entrada_detalle)
                    ->lockForUpdate()
                    ->first();

                // Stock real = entrada original - todo lo ya despachado de este lote
                $totalSalidas = SalidaRecetaDetalle::where('id_entrada_detalle', $infoEntradaDeta->id)
                    ->sum('cantidad');

                $stockReal = $infoEntradaDeta->cantidad - $totalSalidas;

                if ($stockReal < $filaArray->cantidad) {
                    DB::rollback();

                    $infoMedicamento  = FarmaciaArticulo::where('id', $infoEntradaDeta->id_medicamento)->first();
                    $fechaVencimiento = \Carbon\Carbon::parse($infoEntradaDeta->fecha_vencimiento)->format('d-m-Y');

                    return [
                        'success'          => 2,
                        'nombre'           => $infoMedicamento->nombre,
                        'cantidadhay'      => $stockReal,
                        'lote'             => $infoEntradaDeta->lote,
                        'fechavencimiento' => $fechaVencimiento,
                        'cantidadsalida'   => $filaArray->cantidad,
                    ];
                }

                // Registrar salida — NO mutar entrada_medicamento_detalle
                $newDetalle                  = new SalidaRecetaDetalle();
                $newDetalle->id_salidareceta = $salida->id;
                $newDetalle->id_entrada_detalle = $filaArray->id_entrada_detalle;
                $newDetalle->cantidad        = $filaArray->cantidad;
                $newDetalle->save();
            }

            Receta::where('id', $request->idreceta)->update(['estado' => 2]);

            DB::commit();
            return ['success' => 3];

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('guardarSalidaProcesadaDeReceta: ' . $e);
            return ['success' => 99];
        }
    }


}
