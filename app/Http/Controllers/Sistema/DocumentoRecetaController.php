<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\Antropometria;
use App\Models\ConsultaPaciente;
use App\Models\Diagnostico;
use App\Models\EntradaMedicamento;
use App\Models\EstadoCivil;
use App\Models\Paciente;
use App\Models\PacienteAntecedentes;
use App\Models\Profesion;
use App\Models\Receta;
use App\Models\TipeoSanguineo;
use App\Models\TipoDocumento;
use App\Models\TipoPaciente;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentoRecetaController extends Controller
{
    public function indexDocumentosRecetas($idpaciente){

        $infoPaciente = Paciente::where('id', $idpaciente)->first();

        $edad = Carbon::parse($infoPaciente->fecha_nacimiento)->age;

        $miFecha = date("d-m-Y", strtotime($infoPaciente->fecha_nacimiento));

        $nombreCompleto = $infoPaciente->nombres . " " . $infoPaciente->apellidos . " (" . $edad . " Años)";

        $totalConsulta = ConsultaPaciente::where('id_paciente', $infoPaciente->id)->count();

        return view('backend.admin.expediente.documentosrecetas.vistadocumentorecetas', compact('idpaciente',
            'edad', 'miFecha', 'nombreCompleto', 'totalConsulta', 'infoPaciente'));
    }

    public function tablaAntecedentesPorPaciente($idpaciente){


        $b1_antecedentes = null;
        // buscar si paciente tiene antecedentes
        if($infoAntecedente = Antecedentes::where('id_paciente', $idpaciente)->first()){
            $b1_antecedentes = $infoAntecedente;
        }

        // ARRAY TIPEO SANGUINEO
        $b1_arrayTipeoSanguineo = TipeoSanguineo::orderBy('nombre', 'ASC')->get();

        // ARRAY DE ANTECEDENTES MEDICOS
        $b1_arrayAntecedentesMedico = AntecedentesMedicos::where('id_tipo', 1)
            ->orderBy('nombre', 'ASC')
            ->get();

        // ARRAY COMPLICACIONES AGUDAS
        $b1_arrayComplicacionAguda = AntecedentesMedicos::where('id_tipo', 2)
            ->orderBy('nombre', 'ASC')
            ->get();

        // ARRAY ENFERMEDADES CRONICAS
        $b1_arrayEnfermedadCronicas = AntecedentesMedicos::where('id_tipo', 3)
            ->orderBy('nombre', 'ASC')
            ->get();

        // ARRAY ANTECEDENTES CRONICOS
        $b1_arrayAntecedenteCronicos = AntecedentesMedicos::where('id_tipo', 4)
            ->orderBy('nombre', 'ASC')
            ->get();

        // ARRAY DE ID DEL PACIENTE SEGUN TIPO DE ANTECEDENTES
        $b1_arrayIdPacienteAntecedente = PacienteAntecedentes::where('id_paciente', $idpaciente)->get();

        return view('backend.admin.expediente.documentosrecetas.tablas.tablaantecedentespaciente', compact('b1_antecedentes',
            'b1_arrayTipeoSanguineo',
            'b1_arrayAntecedentesMedico', 'b1_arrayIdPacienteAntecedente', 'b1_arrayComplicacionAguda',
            'b1_arrayEnfermedadCronicas', 'b1_arrayAntecedenteCronicos'));
    }


    public function tablaAntropometriaPorPaciente($idpaciente){

        // de todas las consultas obtener donde este el id paciente

        $listaID = DB::table('antropometria AS an')
            ->join('consulta_paciente AS con', 'an.id_consulta', '=', 'con.id')
            ->select('an.id', 'con.id_paciente')
            ->where('con.id_paciente', $idpaciente)
            ->get();

        $pilaIdAntro = array();

        foreach ($listaID as $info){
            array_push($pilaIdAntro, $info->id);
        }

        $bloqueAntropSv = Antropometria::whereIn('id', $pilaIdAntro)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($bloqueAntropSv as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->hora));

            $infoUsuario = Usuario::where('id', $dato->id_usuario)->first();

            $dato->nomusuario = $infoUsuario->nombre;
        }

        return view('backend.admin.expediente.documentosrecetas.tablas.tablaantropometriapaciente', compact('bloqueAntropSv'));
    }


    public function tablaRecetasPorPaciente($idpaciente){

        $arrayRecetas = Receta::where('id_paciente', $idpaciente)
            ->orderBy('fecha')
            ->get();

        foreach ($arrayRecetas as $dato){

            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->fechaProFormat = date("d-m-Y", strtotime($dato->proxima_cita));

            $infoUsuario = Usuario::where('id', $dato->id_usuario)->first();
            $dato->nombreusuario = $infoUsuario->nombre;
        }

        return view('backend.admin.expediente.documentosrecetas.tablas.tablarecetapaciente', compact('arrayRecetas'));
    }

    public function tablaCuadroClinicoPorPaciente($idpaciente){

        $bloqueCuadroClinico= DB::table('cuadro_clinico AS cl')
            ->join('consulta_paciente AS con', 'con.id', '=', 'cl.id_consulta')
            ->select('con.fecha_hora', 'cl.id_diagnostico', 'cl.descripcion',
                'cl.id_diagnostico', 'cl.id', 'con.id_paciente', 'con.id AS idconsulta', 'cl.id_usuario')
            ->where('con.id_paciente', $idpaciente)
            ->orderBy('con.fecha_hora', 'ASC')
            ->get();

        foreach ($bloqueCuadroClinico as $dato){

            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha_hora));

            $infoDiagnostico = Diagnostico::where('id', $dato->id_diagnostico)->first();

            $dato->nombreDiagnostico = $infoDiagnostico->nombre;

            $infoDoctor = Usuario::where('id', $dato->id_usuario)->first();
            $dato->doctor = $infoDoctor->nombre;
        }

        return view('backend.admin.expediente.documentosrecetas.tablas.tablacuadroclinico', compact('bloqueCuadroClinico'));
    }



}
