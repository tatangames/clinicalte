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

class ReportesController extends Controller
{


    public function reporteRecetaPaciente($idreceta)
    {
        // ── Datos ─────────────────────────────────────────────────────────
        $infoReceta = Receta::findOrFail($idreceta);
        $infoPaciente = Paciente::findOrFail($infoReceta->id_paciente);

        $nombrePaciente = trim(($infoPaciente->nombres ?? '') . ' ' . ($infoPaciente->apellidos ?? ''));

        // Edad legible
        $edadTexto = '—';
        if ($infoPaciente->fecha_nacimiento) {
            $nacimiento = Carbon::parse($infoPaciente->fecha_nacimiento);
            $hoy = Carbon::now();
            $anos = (int)$nacimiento->diffInYears($hoy);

            if ($anos >= 1) {
                $edadTexto = $anos . ' año' . ($anos > 1 ? 's' : '');
            } else {
                $meses = (int)$nacimiento->diffInMonths($hoy);
                $edadTexto = $meses >= 1
                    ? $meses . ' mes' . ($meses > 1 ? 'es' : '')
                    : '0';
            }
        }

        $fechaReceta = $infoReceta->fecha
            ? date('d-m-Y', strtotime($infoReceta->fecha))
            : '—';
        $fechaProxCita = $infoReceta->proxima_cita
            ? date('d-m-Y', strtotime($infoReceta->proxima_cita))
            : null;

        // ── Detalle de medicamentos ────────────────────────────────────────
        $arrayRecetaDeta = DB::table('recetas_detalle AS deta')
            ->join('entrada_medicamento_detalle AS enta', 'deta.id_entrada_detalle', '=', 'enta.id')
            ->join('farmacia_articulo AS fa', 'fa.id', '=', 'enta.id_medicamento')
            ->select('fa.nombre', 'deta.id_recetas', 'deta.cantidad', 'deta.descripcion', 'deta.id_via')
            ->where('deta.id_recetas', $idreceta)
            ->orderBy('fa.nombre', 'ASC')
            ->get();

        foreach ($arrayRecetaDeta as $info) {
            $infoVia = ViaReceta::find($info->id_via);
            $info->nombreVia = $infoVia->nombre ?? '—';
        }

        // ── mPDF ──────────────────────────────────────────────────────────
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'margin_top' => 8,
            'margin_bottom' => 16,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);
        $mpdf->SetTitle('Receta Médica');
        $mpdf->showImageErrors = false;

        $logoAlcaldia = 'images/logo.png';

        // ══════════════════════════════════════════════════════════════════
        // ENCABEZADO
        // ══════════════════════════════════════════════════════════════════
        $tabla = "
<table width='100%' style='border-collapse:collapse; font-family:Arial,sans-serif; margin-bottom:10px;'>
    <tr>
        <td style='width:22%; border:0.8px solid #000; padding:6px 8px; vertical-align:middle;'>
            <table width='100%'>
                <tr>
                    <td style='width:35%; text-align:left;'>
                        <img src='{$logoAlcaldia}' style='height:38px;'>
                    </td>
                    <td style='width:65%; text-align:left; color:#104e8c;
                               font-size:12px; font-weight:bold; line-height:1.35;'>
                        SANTA ANA NORTE<br>EL SALVADOR
                    </td>
                </tr>
            </table>
        </td>
        <td style='width:78%; border:0.8px solid #000; padding:6px 8px;
                   text-align:center; vertical-align:middle;
                   font-family:Arial,sans-serif; font-size:14px; font-weight:bold; line-height:1.5;'>
            Clínica Municipal Cristóbal Peraza<br>
            Tahuilapa, Distrito de Metapán, Santa Ana Norte<br>
            <span style='font-size:14px;'>Receta Médica</span>
        </td>
    </tr>
</table>
";

        // ══════════════════════════════════════════════════════════════════
        // DATOS DEL PACIENTE — estructura 3 columnas igual que el original
        // ══════════════════════════════════════════════════════════════════
        $tabla .= "
<br>
<table width='100%' style='font-family:Arial,sans-serif;'>
    <tr>
        <td style='text-align:left; width:33%;'>
            <p style='font-size:14px; margin:0;'><strong>Paciente: </strong>{$nombrePaciente}</p>
        </td>
        <td style='text-align:center; width:34%;'>
            <p style='font-size:14px; margin:0;'><strong>Edad: </strong>{$edadTexto}</p>
        </td>
        <td style='text-align:right; width:33%;'>
            <p style='font-size:14px; margin:0;'><strong>Fecha: </strong>{$fechaReceta}</p>
        </td>
    </tr>";

        if ($fechaProxCita) {
            $tabla .= "
    <tr>
        <td style='text-align:left; width:33%;'></td>
        <td style='text-align:center; width:34%;'></td>
        <td style='text-align:right; width:40%;'>
            <p style='font-size:14px; margin:0;'><strong>Próxima consulta: </strong>{$fechaProxCita}</p>
        </td>
    </tr>";
        }

        // Indicaciones generales (si las hay) — fila completa debajo
        if (!empty(trim($infoReceta->descripcion_general ?? ''))) {
            $tabla .= "
    <tr>
        <td colspan='3' style='padding-top:4px;'>
            <p style='font-size:14px; margin:0;'>
                <strong>Indicaciones generales: </strong>{$infoReceta->descripcion_general}
            </p>
        </td>
    </tr> <br>";
        }

        $tabla .= "
</table>
<hr style='border:none; border-top:1.5px solid #0c84ff; margin:8px 0;'>
";

        // ══════════════════════════════════════════════════════════════════
        // MEDICAMENTOS — estructura original: bullet + cantidad + vía + indicaciones
        // ══════════════════════════════════════════════════════════════════
        $vueltas = 0;

        foreach ($arrayRecetaDeta as $dato) {
            $vueltas++;
            $marginTop = $vueltas > 1 ? '16px' : '0';

            $tabla .= "
<table width='100%' style='margin-top:{$marginTop}; line-height:1; font-family:Arial,sans-serif;'>
    <tr style='line-height:1;'>
        <td style='text-align:left; width:33%;'>
            <p style='font-size:13px; margin:0;'>
                <strong><ul style='margin:0; padding-left:18px;'>
                    <li>{$dato->nombre}</li>
                </ul></strong>
            </p>
        </td>
        <td style='text-align:center; width:34%;'>
            <p style='font-size:13px; margin:0;'>
                <strong>Cantidad: </strong>{$dato->cantidad}
            </p>
        </td>
        <td style='text-align:right; width:33%;'>
            <p style='font-size:13px; margin:0;'>
                <strong>Vía: </strong>{$dato->nombreVia}
            </p>
        </td>
    </tr>
</table>
<p style='font-size:14px; line-height:1.3; margin:5px 0 0;'>
    <strong>Indicaciones del Medicamento:</strong><br>
    {$dato->descripcion}
</p>
";
        }

        if ($vueltas === 0) {
            $tabla .= "
<p style='font-family:Arial,sans-serif; font-size:12px; color:#888;
          text-align:center; margin-top:10px;'>
    Sin medicamentos registrados en esta receta.
</p>";
        }

        // ── Render ────────────────────────────────────────────────────────
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }



    // Controlador
    public function borrarReceta(Request $request)
    {
        $receta = Receta::where('id', $request->idreceta)
            ->where('estado', 1) // doble validación: solo pendientes
            ->first();

        if (!$receta) {
            return response()->json(['success' => 0]);
        }

        // Borrar el detalle primero (FK)
        RecetaDetalle::where('id_recetas', $receta->id)->delete();
        $receta->delete();

        return response()->json(['success' => 1]);
    }





}
