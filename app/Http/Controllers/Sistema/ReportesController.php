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
use App\Models\EntradaMedicamento;
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



    public function indexReportesGeneral()
    {
        $materiales = FarmaciaArticulo::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.reportes.general.vistareportegeneral', compact('materiales'));
    }


    public function generarPdfExistencias(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'      => sys_get_temp_dir(),
            'format'       => 'LETTER',
            'orientation'  => 'P',
            'default_font' => 'arial',
        ]);

        $mpdf->SetTitle('Existencias Farmacia');
        $mpdf->showImageErrors = false;

        $logoalcaldia  = public_path('images/logo.png');
        $fechaFormat   = \Carbon\Carbon::now('America/El_Salvador')->format('d-m-Y');
        $soloConStock  = $request->query('soloConStock', '1');

        // ── Existencias ──────────────────────────────────────────────────
        $query = DB::table('farmacia_articulo as fa')
            ->leftJoinSub(
                DB::table('entrada_medicamento_detalle')
                    ->select('id_medicamento', DB::raw('SUM(cantidad_fija) as total_ingresado'))
                    ->groupBy('id_medicamento'),
                'ed', 'ed.id_medicamento', '=', 'fa.id'
            )
            ->leftJoinSub(
                DB::table('salida_receta_detalle as srd')
                    ->join('entrada_medicamento_detalle as emd', 'emd.id', '=', 'srd.id_entrada_detalle')
                    ->select('emd.id_medicamento', DB::raw('SUM(srd.cantidad) as total_salido'))
                    ->groupBy('emd.id_medicamento'),
                'sd', 'sd.id_medicamento', '=', 'fa.id'
            )
            ->select(
                'fa.nombre as material',
                DB::raw('(COALESCE(ed.total_ingresado, 0) - COALESCE(sd.total_salido, 0)) as existencia')
            )
            ->orderBy('fa.nombre');

        if ($soloConStock === '1') {
            $query->havingRaw('existencia > 0');
        }

        $existencias = $query->get();

        // ── Subtítulo según filtro ────────────────────────────────────────
        $subtitulo = $soloConStock === '1'
            ? 'Solo medicamentos con stock disponible'
            : 'Todos los medicamentos';

        // ══ ENCABEZADO ═══════════════════════════════════════════════════
        $tabla = "
<table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif; margin-bottom:6px;'>
    <tr>
        <td style='width:22%; border:0.8px solid #000; padding:6px 8px;'>
            <table width='100%'>
                <tr>
                    <td style='width:35%; text-align:left;'>
                        <img src='{$logoalcaldia}' style='height:36px'>
                    </td>
                    <td style='width:65%; text-align:left; color:#000000;
                                font-size:10px; font-weight:bold; line-height:1.3;'>
                        SANTA ANA NORTE<br>EL SALVADOR
                    </td>
                </tr>
            </table>
        </td>
        <td style='width:78%; border:0.8px solid #000;
                    padding:8px; text-align:center; vertical-align:middle;'>
            <div style='font-size:14px; font-weight:bold; color:#000000; letter-spacing:1px;'>
                REPORTE DE EXISTENCIAS DE FARMACIA
            </div>
            <div style='font-size:10px; color:#000000; margin-top:3px;'>
                {$subtitulo} — Fecha: <strong>{$fechaFormat}</strong>
            </div>
        </td>
    </tr>
</table>
";

        // ── Sin datos ─────────────────────────────────────────────────────
        if ($existencias->isEmpty()) {
            $tabla .= "
<div style='text-align:center; margin-top:60px; font-family:Arial, sans-serif;'>
    <p style='font-size:13px; color:#888;'>No se encontraron existencias disponibles.</p>
</div>
";
        } else {

            $tabla .= "
<table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif; margin-top:8px;'>
    <thead>
        <tr>
            <th style='background:#f2f4f8; color:#000; font-size:9px; font-weight:bold;
                        border:1px solid #000; padding:5px 3px; text-align:center; width:5%;'>#</th>
            <th style='background:#f2f4f8; color:#000; font-size:9px; font-weight:bold;
                        border:1px solid #000; padding:5px 3px; text-align:center; width:75%;'>Medicamento</th>
            <th style='background:#f2f4f8; color:#000; font-size:9px; font-weight:bold;
                        border:1px solid #000; padding:5px 3px; text-align:center; width:20%;'>Existencia</th>
        </tr>
    </thead>
    <tbody>
";

            $cont = 1;

            foreach ($existencias as $item) {
                $existencia = (int) $item->existencia;

                $tabla .= "
    <tr>
        <td style='border:1px solid #000; font-size:9px; padding:4px; text-align:center;'>{$cont}</td>
        <td style='border:1px solid #000; font-size:9px; padding:4px;'>{$item->material}</td>
        <td style='border:1px solid #000; font-size:9px; padding:4px; text-align:center;'>{$existencia}</td>
    </tr>
    ";
                $cont++;
            }

            $tabla .= "
    </tbody>
</table>
";
        }

        // ── Generar PDF ────────────────────────────────────────────────
        $stylesheet = file_get_contents(public_path('css/cssbodega.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }



    public function generarReporteFinalv2($desde, $hasta, $soloExistencia = '0')
    {
        ini_set('memory_limit', '6024M');
        ini_set('pcre.backtrack_limit', '10000000');
        ini_set('pcre.recursion_limit', '10000000');

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        $dataArray = [];

        // *** IDs salida_receta RANGO desde-hasta (para ENTREGADO TOTAL) ***
        $pilaIdSalidaRecetaRango = DB::table('salida_receta AS sr')
            ->where(function ($query) use ($start, $end) {
                $query->whereIn('sr.id_recetas', function ($sub) {
                    $sub->select('id')->from('recetas')->where('estado', 2);
                })
                    ->orWhere('sr.tipo_salida', 'manual');
            })
            ->whereBetween('sr.fecha', [$start, $end])
            ->pluck('sr.id')
            ->toArray();

        // *** IDs salida_receta ACUMULADO hasta "hasta" (para ENTREGADO) ***
        $pilaIdSalidaRecetaHasta = DB::table('salida_receta AS sr')
            ->where(function ($query) use ($end) {
                // Salidas con receta (estado=2)
                $query->whereIn('sr.id_recetas', function ($sub) {
                    $sub->select('id')->from('recetas')->where('estado', 2);
                })
                    // O salidas manuales (sin receta)
                    ->orWhere('sr.tipo_salida', 'manual');
            })
            ->where('sr.fecha', '<=', $end)
            ->pluck('sr.id')
            ->toArray();

        // *** Pre-cargar SalidaRecetaDetalle ***
        $allDetallesHasta = DB::table('salida_receta_detalle')
            ->whereIn('id_salidareceta', $pilaIdSalidaRecetaHasta)
            ->select('id_entrada_detalle', 'cantidad')
            ->get()
            ->groupBy('id_entrada_detalle');

        $allDetallesRango = DB::table('salida_receta_detalle')
            ->whereIn('id_salidareceta', $pilaIdSalidaRecetaRango)
            ->select('id_entrada_detalle', 'cantidad')
            ->get()
            ->groupBy('id_entrada_detalle');

        // *** Pre-cargar relaciones ***
        $allEntradas = EntradaMedicamento::all()->keyBy('id');
        $allProveedores = Proveedores::all()->keyBy('id');
        $allFuentes = FuenteFinanciamiento::all()->keyBy('id');
        $allLineas = Linea::all()->keyBy('id');

        $arrayMedicamentos = FarmaciaArticulo::orderBy('nombre', 'ASC')->get();
        $contador = 0;

        $sumatoriaTotalDescargado = 0;
        $sumatoriaTotalDescargadoDonac = 0;
        $sumatoriaTotalDescaFecha = 0;
        $sumatoriaTotalDescaDonacionFecha = 0;
        $sumatoriaTotalExistencia = 0;
        $sumatoriaTotalDona = 0;

        foreach ($arrayMedicamentos as $dato) {

            $arrayDetalle = EntradaMedicamentoDetalle::where('id_medicamento', $dato->id)->get();
            $infoLinea = $allLineas->get($dato->id_linea);

            foreach ($arrayDetalle as $fila) {

                // ============================================================
                // ENTREGADO ACUMULADO HASTA LA FECHA FINAL
                // ============================================================
                $entregado_hasta_COL = 0;

                if (isset($allDetallesHasta[$fila->id])) {
                    foreach ($allDetallesHasta[$fila->id] as $d) {
                        $entregado_hasta_COL += $d->cantidad;
                    }
                }

                // ============================================================
                // EXISTENCIA
                // ============================================================
                $existencia_rango_COL = $fila->cantidad_fija - $entregado_hasta_COL;

                // ============================================================
                // ENTREGADO TOTAL EN EL RANGO
                // ============================================================
                $entregadoTotalF_COL = 0;

                if (isset($allDetallesRango[$fila->id])) {
                    foreach ($allDetallesRango[$fila->id] as $d) {
                        $entregadoTotalF_COL += $d->cantidad;
                    }
                }

                $infoEntradaFi = $allEntradas->get($fila->id_entrada_medicamento);
                $infoProve = $infoEntradaFi ? $allProveedores->get($infoEntradaFi->id_proveedor) : null;
                $infoFuenteFi = $infoEntradaFi ? $allFuentes->get($infoEntradaFi->id_fuentefina) : null;

                $fechaVen = date("d-m-Y", strtotime($fila->fecha_vencimiento));

                // ============================================================
                // COSTOS
                // ============================================================
                $precioFormat_COL = '$' . number_format((float)$fila->precio, 4, '.', ',');
                $precioFormatDonacion_COL = '$' . number_format((float)$fila->precio_donacion, 4, '.', ',');
                $cantidadInicial_COL = $fila->cantidad_fija;

                // ============================================================
                // TOTALES
                // ============================================================
                $totalDescargado_COL = '$' . number_format((float)($fila->precio * $entregado_hasta_COL), 4, '.', ',');
                $sumatoriaTotalDescargado += ($fila->precio * $entregado_hasta_COL);

                $totalDescargadoDonac_COL = '$' . number_format((float)($fila->precio_donacion * $entregado_hasta_COL), 2, '.', ',');
                $sumatoriaTotalDescargadoDonac += ($fila->precio_donacion * $entregado_hasta_COL);

                $totalDescaFecha_COL = '$' . number_format((float)($fila->precio * $entregadoTotalF_COL), 4, '.', ',');
                $sumatoriaTotalDescaFecha += ($fila->precio * $entregadoTotalF_COL);

                $totalDescaDonacionFecha_COL = '$' . number_format((float)($fila->precio_donacion * $entregadoTotalF_COL), 4, '.', ',');
                $sumatoriaTotalDescaDonacionFecha += ($fila->precio_donacion * $entregadoTotalF_COL);

                $totalExistencia_COL = '$' . number_format((float)($fila->precio * $existencia_rango_COL), 4, '.', ',');
                $sumatoriaTotalExistencia += ($fila->precio * $existencia_rango_COL);

                $totalExistenciaDona_COL = '$' . number_format((float)($fila->precio_donacion * $existencia_rango_COL), 4, '.', ',');
                $sumatoriaTotalDona += ($fila->precio_donacion * $existencia_rango_COL);

                // ============================================================
                // FILTRO (SOLO AFECTA LO QUE SE MUESTRA)
                // ============================================================
                if ($soloExistencia === '1') {
                    if ($existencia_rango_COL <= 0 && $entregadoTotalF_COL <= 0) {
                        continue;
                    }
                }

                $contador++;

                $dataArray[] = [
                    'contador' => $contador,
                    'codigo' => $dato->codigo_articulo,
                    'nombre' => $dato->nombre . " | " . $dato->id,
                    'financiamiento' => $infoFuenteFi ? $infoFuenteFi->nombre : '',
                    'linea' => $infoLinea ? $infoLinea->nombre : '',
                    'proveedor' => $infoProve ? $infoProve->nombre : '',
                    'lote' => $fila->lote,
                    'fecha_vencimiento' => $fechaVen,
                    'costo' => $precioFormat_COL,
                    'costo_donacion' => $precioFormatDonacion_COL,
                    'cantidad_inicial' => $cantidadInicial_COL,
                    'entregado' => $entregado_hasta_COL,
                    'entregadototal' => $entregadoTotalF_COL,
                    'existencia' => $existencia_rango_COL,
                    'total_descargado' => $totalDescargado_COL,
                    'total_descargado_donacion' => $totalDescargadoDonac_COL,
                    'totaldescafecha' => $totalDescaFecha_COL,
                    'totaldescadonacionfecha' => $totalDescaDonacionFecha_COL,
                    'total_existencia' => $totalExistencia_COL,
                    'totalexistencia_dona' => $totalExistenciaDona_COL,
                ];


            }
        }

        // --- Sumatorias formato ---
        $sumatoriaTotalDescaDonacionFecha = '$' . number_format((float)$sumatoriaTotalDescaDonacionFecha, 2, '.', ',');
        $sumatoriaTotalDescargadoDonac = '$' . number_format(round($sumatoriaTotalDescargadoDonac, 2), 2, '.', ',');
        $sumatoriaTotalDescaFecha = '$' . number_format((float)$sumatoriaTotalDescaFecha, 2, '.', ',');
        $sumatoriaTotalDescargado = '$' . number_format((float)$sumatoriaTotalDescargado, 2, '.', ',');
        $sumatoriaTotalExistencia = '$' . number_format((float)$sumatoriaTotalExistencia, 2, '.', ',');
        $sumatoriaTotalDona = '$' . number_format((float)$sumatoriaTotalDona, 4, '.', ',');



        // --- Agrupar por linea ---
        $dataGrouped = collect($dataArray)->groupBy('linea');

        $contadorCorrelativo = 0;

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER', 'orientation' => 'L']);
        $mpdf->SetTitle('Reporte Final');
        $mpdf->showImageErrors = false;

        $logoGobiernoData = base64_encode(file_get_contents(public_path('images/gobiernologo.jpg')));
        $logoGobierno = 'data:image/jpg;base64,' . $logoGobiernoData;

        $logoAlcaldiaData = base64_encode(file_get_contents(public_path('images/logojpg.jpg')));
        $logoAlcaldia = 'data:image/jpg;base64,' . $logoAlcaldiaData;

        $tabla = "
<table style='width: 100%; border-collapse: collapse; margin-bottom: 0px'>
    <tr>
        <td style='width: 15%; text-align: left;'>
            <img src='$logoAlcaldia' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
        </td>
        <td style='width: 60%; text-align: center;'>
            <h1 style='font-size: 16px; margin: 0; color: #003366;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
            <h3 style='font-size: 16px; margin: 0; color: #003366;'>Clinica Municipal Cristobal Peraza</h3>
            <h3 style='font-size: 16px; margin: 0; color: #003366;'>REPORTE DE EXISTENCIAS POR FECHAS</h3>
            <h3 style='font-size: 16px; margin: 0; color: #003366;'><strong>INTERVALO DESDE:</strong> $desdeFormat <strong>HASTA</strong> $hastaFormat</h3>
        </td>
        <td style='width: 10%; text-align: right;'>
            <img src='$logoGobierno' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
        </td>
    </tr>
</table>
<hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
";

        $stylesheet = file_get_contents('css/cssreportefinal.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $tablaHeader = "
<table id='tablaFor' style='margin-top: 40px'><tbody>
<tr>
    <td style='font-weight: bold; font-size: 12px'>#</td>
    <td style='font-weight: bold; font-size: 12px'>CODIGO</td>
    <td style='font-weight: bold; font-size: 12px'>DESCRIPCION</td>
    <td style='font-weight: bold; font-size: 12px'>FINANCIAMIENTO</td>
    <td style='font-weight: bold; font-size: 12px'>LINEA</td>
    <td style='font-weight: bold; font-size: 12px'>PROVEEDOR</td>
    <td style='font-weight: bold; font-size: 12px'>LOTE</td>
    <td style='font-weight: bold; font-size: 12px'>FECHA VENCIMIENTO</td>
    <td style='font-weight: bold; font-size: 12px'>COSTO</td>
    <td style='font-weight: bold; font-size: 12px'>COSTO DONA.</td>
    <td style='font-weight: bold; font-size: 12px'>CANTIDAD INICIAL</td>
    <td style='font-weight: bold; font-size: 12px'>ENTREGADO</td>
    <td style='font-weight: bold; font-size: 12px'>ENTREGADO TOTAL</td>
    <td style='font-weight: bold; font-size: 12px'>EXISTENCIA</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCARGADO</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCARGADO DONAC.</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCA. FECHAS</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCA. DONA FECHAS</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL EXISTENCIA</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL EXISTENCIA DONA.</td>
</tr>";

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->WriteHTML($tablaHeader, 2);

        // *** FILAS POR GRUPO en bloques de 40 para evitar pcre.backtrack_limit ***
        $chunkSize = 40;

        foreach ($dataGrouped as $linea => $items) {

            $mpdf->WriteHTML("<tr style='background-color: #ddd; font-weight: bold;'>
        <td colspan='20'>$linea</td>
    </tr>", 2);

            $chunk = '';
            $filasEnChunk = 0;

            foreach ($items as $fila) {
                $contadorCorrelativo++;

                $detaCodigo = $fila['codigo'];
                $detaNombre = $fila['nombre'];
                $detaFinanci = $fila['financiamiento'];
                $detaProveedor = $fila['proveedor'];
                $detaLote = $fila['lote'];
                $detaFechaVen = $fila['fecha_vencimiento'];
                $detaCosto = $fila['costo'];
                $detaCostoDonacion = $fila['costo_donacion'];
                $detaCantiIni = $fila['cantidad_inicial'];
                $detaEntregado = $fila['entregado'];
                $detaEntregadoTotal = $fila['entregadototal'];
                $detaExistencia = $fila['existencia'];
                $detaTotalDesc = $fila['total_descargado'];
                $detaTotalDescDonacion = $fila['total_descargado_donacion'];
                $totalDescaFecha = $fila['totaldescafecha'];
                $totalDescaDonacionFecha = $fila['totaldescadonacionfecha'];
                $detaTotalExis = $fila['total_existencia'];
                $detaTotalExistenciaDona = $fila['totalexistencia_dona'];

                $chunk .= "<tr>
            <td>$contadorCorrelativo</td>
            <td>$detaCodigo</td>
            <td>$detaNombre</td>
            <td>$detaFinanci</td>
            <td>$linea</td>
            <td>$detaProveedor</td>
            <td>$detaLote</td>
            <td>$detaFechaVen</td>
            <td>$detaCosto</td>
            <td>$detaCostoDonacion</td>
            <td>$detaCantiIni</td>
            <td>$detaEntregado</td>
            <td>$detaEntregadoTotal</td>
            <td>$detaExistencia</td>
            <td>$detaTotalDesc</td>
            <td>$detaTotalDescDonacion</td>
            <td>$totalDescaFecha</td>
            <td>$totalDescaDonacionFecha</td>
            <td>$detaTotalExis</td>
            <td>$detaTotalExistenciaDona</td>
        </tr>";

                $filasEnChunk++;

                if ($filasEnChunk >= $chunkSize) {
                    $mpdf->WriteHTML($chunk, 2);
                    $chunk = '';
                    $filasEnChunk = 0;
                }
            }

            if ($chunk !== '') {
                $mpdf->WriteHTML($chunk, 2);
            }
        }

        // *** SUMATORIAS + CIERRE ***
        $tablaFooter = "
<tr>
    <td style='font-weight: bold; font-size: 12px'>#</td>
    <td style='font-weight: bold; font-size: 12px'>CODIGO</td>
    <td style='font-weight: bold; font-size: 12px'>DESCRIPCION</td>
    <td style='font-weight: bold; font-size: 12px'>FINANCIAMIENTO</td>
    <td style='font-weight: bold; font-size: 12px'>LINEA</td>
    <td style='font-weight: bold; font-size: 12px'>PROVEEDOR</td>
    <td style='font-weight: bold; font-size: 12px'>LOTE</td>
    <td style='font-weight: bold; font-size: 12px'>FECHA VENCIMIENTO</td>
    <td style='font-weight: bold; font-size: 12px'>COSTO</td>
    <td style='font-weight: bold; font-size: 12px'>COSTO DONA.</td>
    <td style='font-weight: bold; font-size: 12px'>CANTIDAD INICIAL</td>
    <td style='font-weight: bold; font-size: 12px'>ENTREGADO</td>
    <td style='font-weight: bold; font-size: 12px'>ENTREGADO TOTAL</td>
    <td style='font-weight: bold; font-size: 12px'>EXISTENCIA</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCARGADO</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCARGADO DONAC.</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCA. FECHAS</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL DESCA. DONA FECHAS</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL EXISTENCIA</td>
    <td style='font-weight: bold; font-size: 12px'>TOTAL EXISTENCIA DONA.</td>
</tr>
<tr>
    <td colspan='14' style='text-align: right; font-weight: bold'></td>
    <td style='font-weight: bold'>$sumatoriaTotalDescargado</td>
    <td style='font-weight: bold'>$sumatoriaTotalDescargadoDonac</td>
    <td style='font-weight: bold'>$sumatoriaTotalDescaFecha</td>
    <td style='font-weight: bold'>$sumatoriaTotalDescaDonacionFecha</td>
    <td style='font-weight: bold'>$sumatoriaTotalExistencia</td>
    <td style='font-weight: bold'>$sumatoriaTotalDona</td>
</tr>
</tbody></table>

<table style='border-collapse: collapse;' border='1' width='500'><tbody>
<tr>
    <td style='font-weight: bold; font-size: 11px'>Total Descargado</td>
    <td style='font-weight: bold; font-size: 11px'>Total Existencias</td>
</tr>
<tr>
    <td style='font-weight: bold; font-size: 11px'>$sumatoriaTotalDescargado</td>
    <td style='font-weight: bold; font-size: 11px'>$sumatoriaTotalExistencia</td>
</tr>
</tbody></table>
<br><br>";

        $mpdf->WriteHTML($tablaFooter, 2);
        $mpdf->Output();
    }


    public function movimientosMedicamento($id, $desde = null, $hasta = null)
    {
        ini_set('memory_limit', '1024M');
        ini_set('pcre.backtrack_limit', '10000000');
        ini_set('pcre.recursion_limit', '10000000');

        $medicamento = FarmaciaArticulo::findOrFail($id);

        $start = $desde ? Carbon::parse($desde)->startOfDay() : null;
        $end = $hasta ? Carbon::parse($hasta)->endOfDay() : null;

        $desdeFormat = $desde ? date("d-m-Y", strtotime($desde)) : 'Inicio';
        $hastaFormat = $hasta ? date("d-m-Y", strtotime($hasta)) : 'Hoy';

        // *** ENTRADAS ***
        $entradas = DB::table('entrada_medicamento_detalle AS emd')
            ->join('entrada_medicamento AS em', 'em.id', '=', 'emd.id_entrada_medicamento')
            ->join('proveedores AS p', 'p.id', '=', 'em.id_proveedor')
            ->join('fuente_financiamiento AS ff', 'ff.id', '=', 'em.id_fuentefina')
            ->where('emd.id_medicamento', $id)
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('em.fecha', [$start, $end]);
            })
            ->select(
                'em.fecha',
                'em.numero_factura',
                'p.nombre AS proveedor',
                'ff.nombre AS fuente',
                'emd.lote',
                'emd.fecha_vencimiento',
                'emd.cantidad_fija AS cantidad_entrada',
                'emd.precio',
                'emd.precio_donacion'
            )
            ->orderBy('em.fecha', 'ASC')
            ->get();

        // *** SALIDAS: receta (estado=2) + manuales ***
        $salidas = DB::table('salida_receta_detalle AS srd')
            ->join('salida_receta AS sr', 'sr.id', '=', 'srd.id_salidareceta')
            ->join('entrada_medicamento_detalle AS emd', 'emd.id', '=', 'srd.id_entrada_detalle')
            ->join('usuario AS u', 'u.id', '=', 'sr.id_usuario')
            ->leftJoin('recetas AS r', 'r.id', '=', 'sr.id_recetas')
            ->where('emd.id_medicamento', $id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('sr.tipo_salida', 'receta')
                        ->where('r.estado', 2);
                })->orWhere('sr.tipo_salida', 'manual');
            })
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('sr.fecha', [$start, $end]);
            })
            ->select(
                'sr.fecha',
                'sr.tipo_salida',
                'r.id AS id_receta',
                'u.nombre AS usuario',
                'emd.lote',
                'srd.cantidad AS cantidad_salida',
                'emd.precio',
                'emd.precio_donacion',
                'sr.notas'
            )
            ->orderBy('sr.fecha', 'ASC')
            ->get();

        // *** TOTALES ***
        $totalEntradas = $entradas->sum('cantidad_entrada');
        $totalSalidas = $salidas->sum('cantidad_salida');
        $totalSalidasReceta = $salidas->where('tipo_salida', 'receta')->sum('cantidad_salida');
        $totalSalidasManual = $salidas->where('tipo_salida', 'manual')->sum('cantidad_salida');
        $existenciaFinal = $totalEntradas - $totalSalidas;

        // *** mPDF ***
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'orientation' => 'P',
            'margin_top' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_bottom' => 10,
        ]);

        $mpdf->SetTitle('Movimientos ' . $medicamento->nombre);
        $mpdf->showImageErrors = false;

        $logoGobiernoData = base64_encode(file_get_contents(public_path('images/gobiernologo.jpg')));
        $logoGobierno = 'data:image/jpeg;base64,' . $logoGobiernoData;

        $logoAlcaldiaData = base64_encode(file_get_contents(public_path('images/logojpg.jpg')));
        $logoAlcaldia = 'data:image/jpg;base64,' . $logoAlcaldiaData;

        $stylesheet = "
        body { font-family: Arial, sans-serif; font-size: 9px; }
        table { border-collapse: collapse; width: 100%; }
        td, th { text-decoration: none; border: none; }
        a { text-decoration: none; color: inherit; }
    ";
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->SetHTMLFooter("
        <table style='width:100%; font-size:9px;'>
            <tr><td style='text-align:right;'>Página {PAGENO} de {nb}</td></tr>
        </table>
    ");

        // *** ENCABEZADO ***
        $header = "
<table style='width:100%; border-collapse:collapse; margin-bottom:0px'>
    <tr>
        <td style='width:15%; text-align:left;'>
            <img src='$logoAlcaldia' alt='Santa Ana Norte' style='max-width:100px; height:auto;'>
        </td>
        <td style='width:70%; text-align:center;'>
            <h1 style='font-size:15px; margin:0; color:#003366;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
            <h3 style='font-size:13px; margin:0; color:#003366;'>Clínica Municipal Cristóbal Peraza</h3>
            <h3 style='font-size:12px; margin:0; color:#003366;'>REPORTE DE MOVIMIENTOS DE MEDICAMENTO</h3>
            <h3 style='font-size:11px; margin:2px 0 0 0; color:#003366;'>
                <strong>" . strtoupper($medicamento->nombre) . "</strong>
            </h3>
            <h4 style='font-size:10px; margin:1px 0 0 0; color:#003366;'>
                PERÍODO: $desdeFormat &nbsp;–&nbsp; $hastaFormat
            </h4>
        </td>
        <td style='width:15%; text-align:right;'>

        </td>
    </tr>
</table>
<hr style='border:none; border-top:2px solid #003366; margin:0;'>
";
        $mpdf->WriteHTML($header, 2);

        // ══════════════════════════════════════════
        // SECCIÓN ENTRADAS — header de tabla
        // ══════════════════════════════════════════
        $entradasHeader = "
<table style='width:100%; border-collapse:collapse; margin-top:6px;'>
    <tr style='background-color:#888888; color:#ffffff;'>
        <td colspan='9' style='font-size:10px; font-weight:bold; padding:3px 5px;'>
            ENTRADAS DE MEDICAMENTO &nbsp;|&nbsp; Total unidades: $totalEntradas
        </td>
    </tr>
    <tr style='background-color:#ddd;'>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>FECHA</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>N° FACTURA</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>PROVEEDOR</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>FUENTE</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>LOTE</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>FECHA VEN.</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>CANTIDAD</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>COSTO</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>COSTO DONA.</td>
    </tr>";
        $mpdf->WriteHTML($entradasHeader, 2);

        // *** Filas de entradas en chunks ***
        $chunkSize = 40;
        $chunk = '';
        $filasEnChunk = 0;

        if ($entradas->isEmpty()) {
            $mpdf->WriteHTML("<tr><td colspan='9' style='font-size:9px; padding:3px; text-align:center;'>Sin entradas en el período</td></tr>", 2);
        } else {
            foreach ($entradas as $e) {
                $fechaE = date('d-m-Y', strtotime($e->fecha));
                $fechaVenE = date('d-m-Y', strtotime($e->fecha_vencimiento));
                $costoE = '$' . number_format((float)$e->precio, 2, '.', ',');
                $costoDonaE = '$' . number_format((float)$e->precio_donacion, 2, '.', ',');

                $chunk .= "
<tr>
    <td style='font-size:8px; padding:1px 3px;'>$fechaE</td>
    <td style='font-size:8px; padding:1px 3px;'>$e->numero_factura</td>
    <td style='font-size:8px; padding:1px 3px;'>$e->proveedor</td>
    <td style='font-size:8px; padding:1px 3px;'>$e->fuente</td>
    <td style='font-size:8px; padding:1px 3px;'>$e->lote</td>
    <td style='font-size:8px; padding:1px 3px;'>$fechaVenE</td>
    <td style='font-size:8px; padding:1px 3px; text-align:center;'>$e->cantidad_entrada</td>
    <td style='font-size:8px; padding:1px 3px;'>$costoE</td>
    <td style='font-size:8px; padding:1px 3px;'>$costoDonaE</td>
</tr>";

                $filasEnChunk++;
                if ($filasEnChunk >= $chunkSize) {
                    $mpdf->WriteHTML($chunk, 2);
                    $chunk = '';
                    $filasEnChunk = 0;
                }
            }
            if ($chunk !== '') {
                $mpdf->WriteHTML($chunk, 2);
            }
        }

        $mpdf->WriteHTML("</table>", 2);

        // ══════════════════════════════════════════
        // SECCIÓN SALIDAS — header de tabla
        // ══════════════════════════════════════════
        $salidasHeader = "
<table style='width:100%; border-collapse:collapse; margin-top:6px;'>
    <tr style='background-color:#888888; color:#ffffff;'>
        <td colspan='6' style='font-size:10px; font-weight:bold; padding:3px 5px;'>
            SALIDAS &nbsp;|&nbsp; Total: $totalSalidas &nbsp;(Receta: $totalSalidasReceta &nbsp;/&nbsp; Manual: $totalSalidasManual)
        </td>
    </tr>
    <tr style='background-color:#ddd;'>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>FECHA</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>TIPO</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>N° RECETA</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>USUARIO</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>LOTE</td>
        <td style='font-weight:bold; font-size:9px; padding:2px 3px; text-decoration:none; border:none;'>CANTIDAD</td>
    </tr>";
        $mpdf->WriteHTML($salidasHeader, 2);

        // *** Filas de salidas en chunks ***
        $chunk = '';
        $filasEnChunk = 0;

        if ($salidas->isEmpty()) {
            $mpdf->WriteHTML("<tr><td colspan='6' style='font-size:9px; padding:3px; text-align:center;'>Sin salidas en el período</td></tr>", 2);
        } else {
            foreach ($salidas as $s) {
                $fechaS = date('d-m-Y H:i', strtotime($s->fecha));
                $costoS = '$' . number_format((float)$s->precio, 2, '.', ',');

                if ($s->tipo_salida === 'manual') {
                    $tipoBadge = "<span style='color:#e67e22; font-size:8px; font-weight:bold;'>MANUAL</span>";
                    $numReceta = '—';
                } else {
                    $tipoBadge = "<span style='color:#333; font-size:8px; font-weight:bold;'>RECETA</span>";
                    $numReceta = $s->id_receta ? '# ' . $s->id_receta : '—';
                }

                $chunk .= "
<tr>
    <td style='font-size:8px; padding:1px 3px;'>$fechaS</td>
    <td style='font-size:8px; padding:1px 3px; text-align:center;'>$tipoBadge</td>
    <td style='font-size:8px; padding:1px 3px; text-align:center;'>$numReceta</td>
    <td style='font-size:8px; padding:1px 3px;'>$s->usuario</td>
    <td style='font-size:8px; padding:1px 3px;'>$s->lote</td>
    <td style='font-size:8px; padding:1px 3px; text-align:center;'>$s->cantidad_salida</td>
</tr>";

                $filasEnChunk++;
                if ($filasEnChunk >= $chunkSize) {
                    $mpdf->WriteHTML($chunk, 2);
                    $chunk = '';
                    $filasEnChunk = 0;
                }
            }
            if ($chunk !== '') {
                $mpdf->WriteHTML($chunk, 2);
            }
        }

        $mpdf->WriteHTML("</table>", 2);

        // ══════════════════════════════════════════
        // RESUMEN FINAL
        // ══════════════════════════════════════════
        $resumen = "
<table style='border-collapse:collapse; margin-top:6px;' border='1' width='420'>
    <tr style='background-color:#888888; color:#ffffff;'>
        <td style='font-weight:bold; font-size:10px; padding:3px 5px;'>Total Entradas</td>
        <td style='font-weight:bold; font-size:10px; padding:3px 5px;'>Salidas Receta</td>
        <td style='font-weight:bold; font-size:10px; padding:3px 5px;'>Salidas Manual</td>
        <td style='font-weight:bold; font-size:10px; padding:3px 5px;'>Total Salidas</td>
        <td style='font-weight:bold; font-size:10px; padding:3px 5px;'>Existencia</td>
    </tr>
    <tr>
        <td style='font-size:10px; padding:3px 5px; font-weight:bold; text-align:center;'>$totalEntradas</td>
        <td style='font-size:10px; padding:3px 5px; font-weight:bold; text-align:center;'>$totalSalidasReceta</td>
        <td style='font-size:10px; padding:3px 5px; font-weight:bold; text-align:center;'>$totalSalidasManual</td>
        <td style='font-size:10px; padding:3px 5px; font-weight:bold; text-align:center;'>$totalSalidas</td>
        <td style='font-size:10px; padding:3px 5px; font-weight:bold; text-align:center;'>$existenciaFinal</td>
    </tr>
</table>
";

        $mpdf->WriteHTML($resumen, 2);
        $mpdf->Output();
    }

}
