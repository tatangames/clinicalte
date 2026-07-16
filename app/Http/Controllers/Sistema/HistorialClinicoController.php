<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Antecedentes;
use App\Models\AntecedentesMedicos;
use App\Models\Antropometria;
use App\Models\ConsultaPaciente;
use App\Models\ContenidoFarmaceutica;
use App\Models\CuadroClinico;
use App\Models\Diagnostico;
use App\Models\EstadoCivil;
use App\Models\Linea;
use App\Models\Medico;
use App\Models\Motivo;
use App\Models\MotivoFarmacia;
use App\Models\Paciente;
use App\Models\PacienteAntecedentes;
use App\Models\Profesion;
use App\Models\Proveedores;
use App\Models\Receta;
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

class HistorialClinicoController extends Controller
{

    public function indexHistorialClinico($idconsulta)
    {
        $infoConsulta = ConsultaPaciente::findOrFail($idconsulta);
        $infoPaciente = Paciente::findOrFail($infoConsulta->id_paciente);

        // ── Edad legible ──────────────────────────────────────────
        $edadTexto = '—';

        if ($infoPaciente->fecha_nacimiento) {
            $nacimiento = Carbon::parse($infoPaciente->fecha_nacimiento);
            $hoy        = Carbon::now();
            $anos       = (int) $nacimiento->diffInYears($hoy);

            if ($anos >= 1) {
                $edadTexto = $anos . ' año' . ($anos > 1 ? 's' : '');
            } else {
                $meses = (int) $nacimiento->diffInMonths($hoy);
                if ($meses >= 1) {
                    $edadTexto = $meses . ' mes' . ($meses > 1 ? 'es' : '');
                } else {
                    $dias = (int) $nacimiento->diffInDays($hoy);
                    $edadTexto = $dias . ' día' . ($dias !== 1 ? 's' : '');
                }
            }
        }
        // ─────────────────────────────────────────────────────────

        $miFecha       = Carbon::parse($infoPaciente->fecha_nacimiento)->format('d-m-Y');
        $nombreCompleto = $infoPaciente->nombres . ' ' . $infoPaciente->apellidos . ' (' . $edadTexto . ')';
        $totalConsulta  = ConsultaPaciente::where('id_paciente', $infoConsulta->id_paciente)->count();
        $arrayTipoDiagnostico = Diagnostico::orderBy('nombre')->get();

        return view('backend.admin.historialclinico.general.vistahistorialclinico',
            compact('idconsulta', 'infoPaciente', 'nombreCompleto', 'miFecha', 'totalConsulta', 'arrayTipoDiagnostico')
        );
    }


    public function bloqueHistorialAntecedente($idconsulta){

        $infoConsulta = ConsultaPaciente::where('id', $idconsulta)->first();
        $b1_infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();

        $b1_antecedentes = null;
        // buscar si paciente tiene antecedentes
        if($infoAntecedente = Antecedentes::where('id_paciente', $b1_infoPaciente->id)->first()){
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
        $b1_arrayIdPacienteAntecedente = PacienteAntecedentes::where('id_paciente', $b1_infoPaciente->id)->get();

        $b1_btnAntro = 0;
        // verificar si ya tiene 1 ampometria, para ocultar boton
        if(Antropometria::where('id_consulta', $idconsulta)->first()){
            $b1_btnAntro = 1;
        }

        return view('backend.admin.historialclinico.bloques.bloqueantecedentes', compact('b1_antecedentes', 'b1_arrayTipeoSanguineo',
            'b1_arrayAntecedentesMedico', 'b1_arrayIdPacienteAntecedente', 'b1_arrayComplicacionAguda',
            'b1_arrayEnfermedadCronicas', 'b1_arrayAntecedenteCronicos', 'b1_btnAntro', 'b1_infoPaciente'));
    }


    public function actualizarListadoPacienteAntecedente(Request $request)
    {
        DB::beginTransaction();

        try {
            // Reemplazar checkboxes del paciente
            PacienteAntecedentes::where('id_paciente', $request->idpaciente)->delete();

            if ($request->datocheckbox) {
                $datosCheckboxes = json_decode($request->datocheckbox, true);

                $nuevos = collect($datosCheckboxes)->map(fn($dato) => [
                    'id_paciente'          => $request->idpaciente,
                    'id_antecedente_medico'=> $dato['valorAdicional'],
                ])->toArray();

                PacienteAntecedentes::insert($nuevos);
            }

            // Crear o actualizar antecedentes
            Antecedentes::updateOrCreate(
                ['id_paciente' => $request->idpaciente],
                [
                    'id_tipeo_sanguineo'            => $request->selectSanguineo    ?: null,
                    'antecedentes_familiares'        => $request->textAntecedenteFami,
                    'alergias'                       => $request->textAlergia,
                    'medicamentos_actuales'          => $request->textMedicamento,
                    'nota_antecedente_medico'        => $request->notaAnteceMedico,
                    'nota_complicaciones_diabetes'   => $request->notaCompliDiabete,
                    'nota_enfermedades_cronicas'     => $request->notaEnfermCronica,
                    'nota_antecedentes_quirurgicos'  => $request->notaAnteceQuirur,
                    'antecedentes_oftalmologicos'    => $request->notaAnteceOftamo,
                    'antecedentes_deportivos'        => $request->notaAnteceDeportivo,
                    'menarquia'                      => $request->datoMenarquia,
                    'ciclo_menstrual'                => $request->datoCicloMenstr,
                    'pap'                            => $request->datoPap,
                    'mamografia'                     => $request->datoMamografia,
                    'otros'                          => $request->otrosDetalles,
                ]
            );

            DB::commit();
            return response()->json(['success' => 1]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('actualizarListadoPacienteAntecedente: ' . $e->getMessage(), [
                'idpaciente' => $request->idpaciente,
                'trace'      => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => 99]);
        }
    }


    public function bloqueHistorialAntropSv($idconsulta){

        $bloqueAntropSv = Antropometria::where('id_consulta', $idconsulta)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($bloqueAntropSv as $dato){

            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->horaFormat = date("h:i A", strtotime($dato->hora));

            $infoUsuario = Usuario::where('id', $dato->id_usuario)->first();

            $dato->nomusuario = $infoUsuario->nombre;
        }

        $btnAntrosV = 0;
        // verificar si ya tiene 1 ampometria, para ocultar boton
        if(Antropometria::where('id_consulta', $idconsulta)->first()){
            $btnAntrosV = 1;
        }

        return view('backend.admin.historialclinico.bloques.bloqueantropsv', compact('bloqueAntropSv',
            'btnAntrosV'));
    }

    public function vistaNuevaAntropologia($idconsulta){
        return view('backend.admin.historialclinico.antropsv.vistaantropometriasv', compact('idconsulta'));
    }


    public function vistaVisualizarAntropologia($idantrop){

        $infoAntrop = Antropometria::where('id', $idantrop)->first();
        $infoConsulta = ConsultaPaciente::where('id', $infoAntrop->id_consulta)->first();
        $infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();

        $nombreCompleto = $infoPaciente->nombres . " " . $infoPaciente->apellidos;

        $idconsulta = $infoConsulta->id;

        return view('backend.admin.historialclinico.antropsv.vistaeditarantropometriasv', compact('idantrop',
            'nombreCompleto', 'idconsulta', 'infoAntrop'));
    }


    public function registrarAntropometria(Request $request){

        $regla = array(
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if(Antropometria::where('id_consulta', $request->idconsulta)->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

            $idusuario = Auth::id();

            $horaCarbon = Carbon::parse(Carbon::now());

            $dato = new Antropometria();
            $dato->id_consulta = $request->idconsulta;
            $dato->id_usuario = $idusuario;
            $dato->fecha = $request->fecha;
            $dato->hora = $horaCarbon;
            $dato->frecuencia_cardiaca = $request->freCardiaca;
            $dato->frecuencia_respiratoria = $request->freRespiratoria;
            $dato->presion_arterial = $request->presionArterial;
            $dato->temperatura = $request->temperatura;
            $dato->perim_abdominal = $request->perimetroAbdominal;
            $dato->perim_cefalico = $request->perimetroCefalico;
            $dato->peso_libra = $request->pesoLibra;
            $dato->peso_kilo = $request->pesoKilo;
            $dato->estatura = $request->estatura;
            $dato->imc = $request->imc;
            $dato->resultado_imc = $request->resultadoImc;
            $dato->glucometria_capilar = $request->glucometria;
            $dato->glicohemoglibona_capilar = $request->glicohemoglobina;
            $dato->cetona_capilar = $request->cetona;
            $dato->spo2 = $request->sp02;
            $dato->perim_cintura = $request->perimetroCintura;
            $dato->perim_cadera = $request->perimetroCadera;
            $dato->icc = $request->icc;
            $dato->riesgo_mujer = $request->riesgoMujer;
            $dato->riesgo_hombre = $request->riesgoHombre;
            $dato->gasto_energetico_basal = $request->gastoEnergetico;
            $dato->nota_adicional = $request->otrosDetalles;
            $dato->save();

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            DB::rollback();

            return ['success' => 99];
        }
    }



    public function actualizarAntropometria(Request $request){

        $regla = array(
            'fecha' => 'required',
            'idmodal' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Antropometria::where('id', $request->idmodal)->update([
            'fecha' => $request->fecha,
            'frecuencia_cardiaca' => $request->freCardiaca,
            'frecuencia_respiratoria' => $request->freRespiratoria,
            'presion_arterial' => $request->presionArterial,
            'temperatura' => $request->temperatura,
            'perim_abdominal' => $request->perimetroAbdominal,
            'perim_cefalico' => $request->perimetroCefalico,
            'peso_libra' => $request->pesoLibra,
            'peso_kilo' => $request->pesoKilo,
            'estatura' => $request->estatura,
            'imc' => $request->imc,
            'resultado_imc' => $request->resultadoImc,
            'glucometria_capilar' => $request->glucometria,
            'glicohemoglibona_capilar' => $request->glicohemoglobina,
            'cetona_capilar' => $request->cetona,
            'spo2' => $request->sp02,
            'perim_cintura' => $request->perimetroCintura,
            'perim_cadera' => $request->perimetroCadera,
            'icc' => $request->icc,
            'riesgo_mujer' => $request->riesgoMujer,
            'riesgo_hombre' => $request->riesgoHombre,
            'gasto_energetico_basal' => $request->gastoEnergetico,
            'nota_adicional' => $request->otrosDetalles,
        ]);

        return ['success' => 1];
    }

    public function vistaVisualizarAntropologiaExpedientes($idantrop){

        $infoAntrop = Antropometria::where('id', $idantrop)->first();
        $infoConsulta = ConsultaPaciente::where('id', $infoAntrop->id_consulta)->first();
        $infoPaciente = Paciente::where('id', $infoConsulta->id_paciente)->first();

        $nombreCompleto = $infoPaciente->nombres . " " . $infoPaciente->apellidos;
        $idconsulta = $infoConsulta->id;

        return view('backend.admin.expediente.documentosrecetas.vistaverantropometria', compact('idantrop',
            'nombreCompleto', 'idconsulta', 'infoAntrop'));
    }

    function bloqueHistorialRecetas($idconsulta){

        $infoConsulta = ConsultaPaciente::where('id', $idconsulta)->first();
        $arrayRecetas = Receta::where('id_paciente', $infoConsulta->id_paciente)
            ->orderBy('fecha')
            ->get();

        foreach ($arrayRecetas as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
            $dato->fechaProFormat = date("d-m-Y", strtotime($dato->proxima_cita));

            $infoUsuario = Usuario::where('id', $dato->id_usuario)->first();
            $dato->nombreusuario = $infoUsuario->nombre;
        }

        // mostrar boton
        $existeReceta = 0;
        if(Receta::where('id_consulta', $idconsulta)->first()){
            $existeReceta = 1;
        }

        return view('backend.admin.historialclinico.bloques.bloquerecetas', compact('arrayRecetas', 'existeReceta'));
    }


    public function bloqueHistorialCuadroClinico($idconsulta)
    {
        $bloqueCuadroClinico = DB::table('cuadro_clinico AS cl')
            ->join('consulta_paciente AS con', 'con.id',    '=', 'cl.id_consulta')
            ->join('diagnosticos AS diag',      'diag.id',   '=', 'cl.id_diagnostico')
            ->join('usuario AS u',            'u.id',      '=', 'cl.id_usuario')
            ->select(
                'cl.id',
                'cl.id_consulta',
                'cl.descripcion',
                'cl.id_diagnostico',
                'cl.id_usuario',
                'diag.nombre AS nombreDiagnostico',
                'u.nombre    AS nombreUsuario',
                DB::raw("DATE_FORMAT(con.fecha_hora, '%d-%m-%Y') AS fechaFormat")
            )
            ->where('cl.id_consulta', $idconsulta)
            ->orderBy('con.fecha_hora', 'ASC')
            ->get();

        $haycuadro = CuadroClinico::where('id_consulta', $idconsulta)->exists() ? 1 : 0;

        return view('backend.admin.historialclinico.bloques.bloquecuadroclinico',
            compact('bloqueCuadroClinico', 'idconsulta', 'haycuadro')
        );
    }


    public function borrarAntropometria(Request $request)
    {
        DB::beginTransaction();

        try {
            $antrop = Antropometria::findOrFail($request->id);
            $antrop->delete();

            DB::commit();
            return response()->json(['success' => 1]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('borrarAntropometria: ' . $e->getMessage(), ['id' => $request->id]);
            return response()->json(['success' => 99]);
        }
    }

    public function nuevoHistorialClinico(Request $request){

        $regla = array(
            'idconsulta' => 'required',
            'diagnostico' => 'required'
        );

        // descripcion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $usuario = auth()->user();

            $dato = new CuadroClinico();
            $dato->id_consulta = $request->idconsulta;
            $dato->id_diagnostico = $request->diagnostico;
            $dato->descripcion = $request->descripcion;
            $dato->id_usuario = $usuario->id;
            $dato->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            DB::rollback();

            return ['success' => 99];
        }
    }


    public function informacionHistorialClinico(Request $request){

        $regla = array(
            'id' => 'required' // id de cuadro clinico
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = CuadroClinico::where('id', $request->id)->first()){

            $arrayDiagnostico = Diagnostico::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'info' => $info, 'arraydiagnostico' => $arrayDiagnostico];
        }else{
            return ['success' => 2];
        }
    }


    public function actualizarHistorialClinico(Request $request){

        $regla = array(
            'idCuadro' => 'required',
            'diagnostico' => 'required',
            'descripcion' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuadroClinico::where('id', $request->idCuadro)->first()){

            CuadroClinico::where('id', $request->idCuadro)->update([
                'id_diagnostico' => $request->diagnostico,
                'descripcion' => $request->descripcion
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}
