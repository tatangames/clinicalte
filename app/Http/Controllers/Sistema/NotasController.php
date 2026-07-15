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
use App\Models\NotasPaciente;
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

class NotasController extends Controller
{

    public function bloqueNotasPaciente($idconsulta){

        $infoConsulta = ConsultaPaciente::where('id', $idconsulta)->first();

        $arrayNotas = NotasPaciente::where('id_paciente', $infoConsulta->id_paciente)->orderBy('fecha', 'ASC')->get();

        foreach ($arrayNotas as $dato){
            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));
        }

        return view('backend.admin.historialclinico.bloques.bloquenotas', compact('arrayNotas'));
    }

    public function registrarNotaPaciente(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nota' => 'required',
            'idconsulta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $infoConsulta = ConsultaPaciente::where('id', $request->idconsulta)->first();

        $registro = new NotasPaciente();
        $registro->id_consulta = $request->idconsulta;
        $registro->id_paciente = $infoConsulta->id_paciente;
        $registro->fecha = $request->fecha;
        $registro->nota = $request->nota;

        if($registro->save()){
            return ['success' => 1];
        }
        return ['success' => 99];
    }


    public function borrarNotaPaciente(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(NotasPaciente::where('id', $request->id)->first()){
            NotasPaciente::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }

    public function informacionNotaPaciente(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $info = NotasPaciente::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }

    public function actualizarNotaPaciente(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nota' => 'required',
            'idfila' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        NotasPaciente::where('id', $request->idfila)->update([
            'fecha' => $request->fecha,
            'nota' => $request->nota,
        ]);

        return ['success' => 1];
    }


    public function reporteNotaPaciente($idfila)
    {
        $infoNota    = NotasPaciente::findOrFail($idfila);
        $infoPaciente = Paciente::findOrFail($infoNota->id_paciente);

        $nombrePaciente = trim($infoPaciente->nombres . ' ' . $infoPaciente->apellidos);
        $fechaFormat    = Carbon::parse($infoNota->fecha)->format('d-m-Y');

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

        // ── mPDF ─────────────────────────────────────────────────
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Nota Paciente');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        // ══ ENCABEZADO (igual que ficha general) ═════════════════
        $html = "
        <table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
            <tr>
                <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                    <table width='100%'>
                        <tr>
                            <td style='width:30%; text-align:left;'>
                                <img src='{$logoalcaldia}' style='height:38px'>
                            </td>
                            <td style='width:70%; text-align:left; color:#104e8c; font-size:13px;
                                       font-weight:bold; line-height:1.3;'>
                                SANTA ANA NORTE<br>EL SALVADOR
                            </td>
                        </tr>
                    </table>
                </td>
                <td style='width:75%; border:0.8px solid #000; padding:6px 8px;
                           text-align:center; font-size:14px; font-weight:bold; vertical-align:middle;'>
                    Clínica Municipal Cristóbal Peraza<br>
                    Tahuilapa, Distrito de Metapán, Santa Ana Norte<br>
                    Nota de Paciente
                </td>
            </tr>
        </table>
        <br>
        ";

        // ══ DATOS DEL PACIENTE ════════════════════════════════════
        $html .= "
        <table width='100%' style='border-collapse:collapse; font-family:Arial,sans-serif; font-size:13px; margin-bottom:16px;'>
            <tr>
                <td style='width:35%; border:0.8px solid #ccc; padding:6px 8px;
                           font-weight:bold; background:#f5f5f5;'>Paciente</td>
                <td style='width:65%; border:0.8px solid #ccc; padding:6px 8px;'>{$nombrePaciente}</td>
            </tr>
            <tr>
                <td style='width:35%; border:0.8px solid #ccc; padding:6px 8px;
                           font-weight:bold; background:#f5f5f5;'>Edad</td>
                <td style='width:65%; border:0.8px solid #ccc; padding:6px 8px;'>{$edadTexto}</td>
            </tr>
            <tr>
                <td style='width:35%; border:0.8px solid #ccc; padding:6px 8px;
                           font-weight:bold; background:#f5f5f5;'>Fecha</td>
                <td style='width:65%; border:0.8px solid #ccc; padding:6px 8px;'>{$fechaFormat}</td>
            </tr>
            <tr>
                <td style='width:35%; border:0.8px solid #ccc; padding:6px 8px;
                           font-weight:bold; background:#f5f5f5;'>Expediente</td>
                <td style='width:65%; border:0.8px solid #ccc; padding:6px 8px;'>{$infoPaciente->numero_expediente}</td>
            </tr>
        </table>
        ";

        // ══ CONTENIDO DE LA NOTA ══════════════════════════════════
        $html .= "
    <div style='font-family:Arial,sans-serif; font-size:13px; line-height:1.6;'>
        {$infoNota->nota}
    </div>
    ";

        // ══ RENDER ════════════════════════════════════════════════
        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
    }


}
