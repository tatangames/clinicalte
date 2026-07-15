<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\ConsultaPaciente;
use App\Models\ContenidoFarmaceutica;
use App\Models\Diagnostico;
use App\Models\EstadoCivil;
use App\Models\Linea;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\MotivoFarmacia;
use App\Models\Paciente;
use App\Models\Profesion;
use App\Models\Proveedores;
use App\Models\SalasEspera;
use App\Models\SubLinea;
use App\Models\TipoAntecedente;
use App\Models\TipoDocumento;
use App\Models\TipoFarmaceutica;
use App\Models\TipoPaciente;
use App\Models\TipoProveedor;
use App\Models\ViaReceta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AsignacionesController extends Controller
{


    public function indexAsignaciones()
    {
        // 1 query por catálogos en paralelo conceptual, 1 query agrupada para conteos
        [$arrayRazonUso, $arraySalaEspera] = [
            Motivo::orderBy('nombre')->get(),
            SalasEspera::orderBy('nombre')->get(),
        ];

        $salas = ConsultaPaciente::whereIn('id_salaespera', [1, 2])
            ->whereIn('estado_paciente', [0, 1])
            ->selectRaw('id_salaespera, estado_paciente, COUNT(*) as total')
            ->groupBy('id_salaespera', 'estado_paciente')
            ->get()
            ->groupBy('id_salaespera');

        $consultorio = $salas->get(1, collect());
        $enfermeria  = $salas->get(2, collect());

        $conteoConsultorio = (int) ($consultorio->firstWhere('estado_paciente', 0)?->total ?? 0);
        $conteoEnfermeria  = (int) ($enfermeria->firstWhere('estado_paciente', 0)?->total ?? 0);

        $dentroCons = (int) ($consultorio->firstWhere('estado_paciente', 1)?->total ?? 0);
        $dentroEnf  = (int) ($enfermeria->firstWhere('estado_paciente', 1)?->total ?? 0);

        $arrayPaciente = [
            'salaConsultorioPaciente' => $dentroCons > 0 ? "Pacientes asignados: $dentroCons" : "Paciente en sala: 0",
            'salaEnfermeriaPaciente'  => $dentroEnf  > 0 ? "Pacientes asignados: $dentroEnf"  : "Paciente en sala: 0",
            'botonOpcionConsultoria'  => (int) ($dentroCons > 0),
            'botonOpcionEnfermeria'   => (int) ($dentroEnf  > 0),
        ];

        return view('backend.admin.asignaciones.nuevo.vistanuevaasignacion',
            compact('arrayRazonUso', 'arraySalaEspera', 'arrayPaciente', 'conteoConsultorio', 'conteoEnfermeria')
        );
    }


    public function buscadorPaciente(Request $request)
    {
        $queryData = $request->get('query', '');



        // Si viene vacío, retornar vacío de inmediato
        if (trim($queryData) === '') {
            return response('');
        }

        $data = Paciente::where(function ($q) use ($queryData) {
            $q->where('nombres',       'like', "%{$queryData}%")
                ->orWhere('apellidos',    'like', "%{$queryData}%")
                ->orWhere('num_documento','like', "%{$queryData}%");
        })
            ->orderBy('nombres')   // más útil que orderBy id
            ->limit(20)            // sin límite puede traer miles de registros
            ->get(['id', 'nombres', 'apellidos', 'num_documento']);

        if ($data->isEmpty()) {
            return response('');
        }

        $output = '<ul class="dropdown-menu" style="display:block;position:relative;overflow:auto;max-height:300px;width:550px">';

        foreach ($data as $row) {
            $label = 'Exp#' . $row->id . ' &nbsp; ' . e($row->nombres) . ' ' . e($row->apellidos) . ' — ' . e($row->num_documento);
            $output .= '<li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '">'
                . '<a href="#" style="margin-left:3px;font-size:15px;font-weight:bold;color:#000">' . $label . '</a></li>'
                . ($data->count() > 1 ? '<hr>' : '');
        }

        $output .= '</ul>';

        Log::info($output);

        return response($output);  // return, no echo
    }



    function nuevoRegistro(Request $request){

        $regla = array(
            'idpaciente' => 'required',
            'idrazon' => 'required',
            'idsalaespera' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();


        // ESTA EN SALA DE ESPERA
        if($infoPaciente = ConsultaPaciente::where('id_paciente', $request->idpaciente)
            ->where('estado_paciente', 0)->first()){

            $infoSala = SalasEspera::where('id', $infoPaciente->id_salaespera)->first();

            $msj = "El Paciente ya se encuentra en sala de espera: " . $infoSala->nombre;

            return ['success' => 1, 'mensaje' => $msj];
        }

        // ESTA DENTRO DE LA SALA
        if($infoPaciente = ConsultaPaciente::where('id_paciente', $request->idpaciente)
            ->where('estado_paciente', 1)->first()){

            $infoSala = SalasEspera::where('id', $infoPaciente->id_salaespera)->first();

            $msj = "El Paciente ya se encuentra asignado a la sala: " . $infoSala->nombre;

            return ['success' => 1, 'mensaje' => $msj];
        }

        try {
            $fechaCarbon = Carbon::parse(Carbon::now());

            $dato = new ConsultaPaciente();
            $dato->id_paciente = $request->idpaciente;
            $dato->id_motivo = $request->idrazon;
            $dato->fecha_hora = $fechaCarbon;
            $dato->estado_paciente = 0;
            $dato->estado_receta = 0;
            $dato->id_salaespera = $request->idsalaespera;
            $dato->save();

            DB::commit();
            return ['success' => 2];


        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function tablaPacientesEnEspera(){

        $arrayPacientes = ConsultaPaciente::where('estado_paciente', 1)
            ->orderBy('fecha_hora', 'ASC')
            ->get();

        foreach ($arrayPacientes as $dato){

            $fechaFormat = date("d-m-Y h:i A", strtotime($dato->fecha_hora));
            $dato->fechaFormat = $fechaFormat;

            $infoPaciente = Paciente::where('id', $dato->id_paciente)->first();
            $dato->nombrePaciente = $infoPaciente->nombres;
            $dato->apellidoPaciente = $infoPaciente->apellidos;
            $dato->idExpediente = $infoPaciente->id;
            $dato->numdocumento = $infoPaciente->num_documento;
        }

        return view('backend.admin.asignaciones.pacientes.tablapacientesenespera', compact('arrayPacientes'));
    }



























    // muestra la tabla de pacientes en espera para Enfermeria
    public function tablaModalEnfermeria(){

        // lista de pacientes en modo espera para tabla enfermeria
        $arrayTablaEnfermeria = ConsultaPaciente::where('estado_paciente', 0)
            ->where('id_salaespera', 2) // ENFERMERIA
            ->orderBy('fecha_hora', 'ASC')
            ->get();


        foreach ($arrayTablaEnfermeria as $dato){

            $infoPaciente = Paciente::where('id', $dato->id_paciente)->first();
            $infoRazonUso = Motivo::where('id', $dato->id_motivo)->first();

            $dato->nombrepaciente = $infoPaciente->nombres . " " . $infoPaciente->apellidos;
            $dato->razonUso = $infoRazonUso->nombre;

            $dato->horaFormat = date("d-m-Y h:i A", strtotime($dato->fecha_hora));
        }

        return view('backend.admin.asignaciones.tablamodalenfermeria.vistamodaltablaenfermeria', compact('arrayTablaEnfermeria'));
    }



    // muestra la tabla de pacientes en espera para Consultoria
    public function tablaModalConsultoria(){

        // lista de pacientes en modo espera para tabla enfermeria
        $arrayTablaConsultoria = ConsultaPaciente::where('estado_paciente', 0)
            ->where('id_salaespera', 1) // CONSULTORIA
            ->orderBy('fecha_hora', 'ASC')
            ->get();

        foreach ($arrayTablaConsultoria as $dato){

            $infoPaciente = Paciente::where('id', $dato->id_paciente)->first();
            $infoRazonUso = Motivo::where('id', $dato->id_motivo)->first();

            $dato->nombrepaciente = $infoPaciente->nombres . " " . $infoPaciente->apellidos;
            $dato->razonUso = $infoRazonUso->nombre;

            $dato->horaFormat = date("d-m-Y h:i A", strtotime($dato->fecha_hora));
        }

        return view('backend.admin.asignaciones.tablamodalconsultoria.vistamodaltablaconsultoria', compact('arrayTablaConsultoria'));
    }

    // informacion de un paciente
    public function informacionPaciente(Request $request){

        $regla = array(
            'id' => 'required', // id consulta_paciente
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if($info = ConsultaPaciente::where('id', $request->id)->first()){

            $arraySala = SalasEspera::orderBy('nombre', 'ASC')->get();
            $arrayRazonUso = Motivo::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $info,
                'arraysala' => $arraySala,
                'arrayrazonuso' => $arrayRazonUso];
        }else{
            return ['success' => 2];
        }
    }


    public function guardarInformacionEditadaPaciente(Request $request){

        $regla = array(
            'idconsulta' => 'required', // id consulta_paciente
            'idsala' => 'required',
            'idrazonuso' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        ConsultaPaciente::where('id', $request->idconsulta)->update([
            'id_salaespera' => $request->idsala,
            'id_motivo' => $request->idrazonuso,
        ]);

        return ['success' => 1];
    }


    // finalizar la consulta medica
    public function finalizarConsultaPaciente(Request $request){

        $regla = array(
            'idconsulta' => 'required', // id consulta_paciente
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        ConsultaPaciente::where('id', $request->idconsulta)->update([
            'estado_paciente' => 2, // consulta finalizada
        ]);

        return ['success' => 1];
    }

    // ingresar paciente a la sala
    public function ingresarPacienteALaSala(Request $request){

        $regla = array(
            'idconsulta' => 'required', // id consulta_paciente
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $fechaCarbon = Carbon::parse(Carbon::now());

        ConsultaPaciente::where('id', $request->idconsulta)->update([
            'estado_paciente' => 1, // paciente dentro a la Sala
            'hora_dentrosala' => $fechaCarbon
        ]);

        $infoPaciente = ConsultaPaciente::where('id', $request->idconsulta)->first();
        $infoSala = SalasEspera::where('id', $infoPaciente->id_salaespera)->first();

        return ['success' => 1, 'nombresala' => $infoSala->nombre];
    }

    public function personasDentroSala(Request $request){

        // 1- CUNSULTORIA
        // 2- ENFERMERIA

        $regla = array(
            'tipo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $listado = ConsultaPaciente::where('estado_paciente', 1)
            ->where('id_salaespera', $request->tipo)
            ->get();

        foreach ($listado as $data){
            $infoPaciente = Paciente::where('id', $data->id_paciente)->first();

            $data->nombre = $infoPaciente->nombres;
        }

        return ['success' => 1, "listado" => $listado];
    }




    public function vistaEditarPaciente($idpaciente){

        $infoPa = Paciente::where('id', $idpaciente)->first();

        $arrayEstadoCivil = EstadoCivil::orderBy('nombre')->get();
        $arrayTipoPaciente = TipoPaciente::orderBy('nombre')->get();
        $arrayTipoDocumento = TipoDocumento::orderBy('nombre')->get();
        $arrayProfesion = Profesion::orderBy('nombre')->get();

        if($infoPa->sexo == 'M'){
            $tiposexo = 1;
        }else{
            $tiposexo = 2;
        }

        return view('backend.admin.expedientes.buscar.editarpaciente.vistaeditarpaciente', compact('infoPa', 'arrayEstadoCivil',
            'arrayTipoPaciente', 'tiposexo', 'arrayTipoDocumento', 'arrayProfesion', 'idpaciente'));
    }



    public function informacionPacienteDentroDeSala(Request $request){


        $regla = array(
            'idconsulta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        // buscar directamente con id consulta
        if($infoConsulta = ConsultaPaciente::where('id', $request->idconsulta)->first()){

            $infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();
            $nombrePaciente = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

            if($infoPaciente->foto == null){
                $hayfoto = 0;
            }else{
                $hayfoto = 1;
            }

            $horaEntroEsperar = date("h:i A", strtotime($infoConsulta->fecha_hora));
            $horaEntroSala = date("h:i A", strtotime($infoConsulta->hora_dentrosala));

            $arrayrazonuso = Motivo::orderBy('nombre')->get();

            // CONTEO DIRECTO
            $numeroConsulta = $infoPaciente->numero_expediente;

            return ['success' => 1, 'infoconsulta' => $infoConsulta,
                'infopaciente' => $infoPaciente, 'hayfoto' => $hayfoto,
                'horaentro' => $horaEntroSala, 'entroespera' => $horaEntroEsperar,
                'arrayrazonuso' => $arrayrazonuso, 'numeroConsulta' => $numeroConsulta,
                'nombrepaciente' => $nombrePaciente];
        }
        else{
            return ['success' => 2];
        }
    }

    public function actualizarRazonUsoPaciente(Request $request){

        $regla = array(
            'idconsulta' => 'required',
            'idrazonuso' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        ConsultaPaciente::where('id', $request->idconsulta)->update([
            'id_motivo' => $request->idrazonuso,
        ]);

        return ['success' => 1];
    }

    // liberar sala del paciente
    public function liberarSalaPaciente(Request $request){

        $regla = array(
            'idconsulta' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        ConsultaPaciente::where('id', $request->idconsulta)->update([
            'estado_paciente' => 2, // liberado
        ]);

        return ['success' => 1];
    }


    public function informacionPacienteDentroSala(Request $request){

        $regla = array(
            'idconsulta' => 'required', // id consulta_paciente
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if($info = ConsultaPaciente::where('id', $request->idconsulta)->first()){

            $arraySala = SalasEspera::orderBy('nombre', 'ASC')->get();

            $infoSala = SalasEspera::where('id', $info->id_salaespera)->first();
            $arrayRazonUso = Motivo::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $info,
                'arraysala' => $arraySala,
                'arrayrazonuso' => $arrayRazonUso, 'salactual' => $infoSala->nombre];
        }else{
            return ['success' => 2];
        }
    }



    public function reseteoTrasladoPacienteNuevaSala(Request $request){


        $regla = array(
            'idconsulta' => 'required', // id consulta_paciente
            'nuevasala' => 'required',
            'nuevomotivo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if(ConsultaPaciente::where('id', $request->idconsulta)->first()){

            $fechaCarbon = Carbon::parse(Carbon::now());

            ConsultaPaciente::where('id', $request->idconsulta)->update([
                'id_salaespera' => $request->nuevasala,
                'id_motivo' => $request->nuevomotivo,
                'fecha_hora' => $fechaCarbon, // cambia fecha a cuando esta en sala de espera
                'estado_paciente' => 0, // vuelve a sala de espera
                'hora_dentrosala' => null,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    // recargando vista por cronometro
    public function recargandoVistaCronometro(Request $request)
    {
        $salas = ConsultaPaciente::whereIn('id_salaespera', [1, 2])
            ->whereIn('estado_paciente', [0, 1])
            ->selectRaw('id_salaespera, estado_paciente, COUNT(*) as total')
            ->groupBy('id_salaespera', 'estado_paciente')
            ->get()
            ->groupBy('id_salaespera');

        $consultorio = $salas->get(1, collect());
        $enfermeria  = $salas->get(2, collect());

        $conteoConsultorio = (int) ($consultorio->firstWhere('estado_paciente', 0)?->total ?? 0);
        $conteoEnfermeria  = (int) ($enfermeria->firstWhere('estado_paciente', 0)?->total ?? 0);

        $dentroCons  = (int) ($consultorio->firstWhere('estado_paciente', 1)?->total ?? 0);
        $dentroEnf   = (int) ($enfermeria->firstWhere('estado_paciente', 1)?->total ?? 0);

        $arrayPaciente = [
            'salaConsultorioPaciente' => $dentroCons > 0 ? "Pacientes asignados: $dentroCons" : "Paciente en sala: 0",
            'salaEnfermeriaPaciente'  => $dentroEnf  > 0 ? "Pacientes asignados: $dentroEnf"  : "Paciente en sala: 0",
            'botonOpcionConsultoria'  => $dentroCons > 0 ? 1 : 0,
            'botonOpcionEnfermeria'   => $dentroEnf  > 0 ? 1 : 0,
        ];

        return response()->json([
            'success'          => 1,
            'arraypaciente'    => $arrayPaciente,
            'conteoConsultorio' => $conteoConsultorio,
            'conteoEnfermeria'  => $conteoEnfermeria,
        ]);
    }



}
