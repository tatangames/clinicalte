<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\ConsultaPaciente;
use App\Models\EntradaMedicamento;
use App\Models\EstadoCivil;
use App\Models\Paciente;
use App\Models\Profesion;
use App\Models\TipoDocumento;
use App\Models\TipoPaciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ExpedienteController extends Controller
{

    public function indexNuevoExpediente()
    {
        $arrayTipoPaciente = TipoPaciente::orderBy('nombre')->get();
        $arrayEstadoCivil = EstadoCivil::orderBy('nombre')->get();
        $arrayTipoDocumento = TipoDocumento::orderBy('nombre')->get();
        $arrayProfesion = Profesion::orderBy('nombre')->get();

        return view('backend.admin.expediente.nuevo.vistanuevoexpediente', compact('arrayTipoPaciente',
        'arrayEstadoCivil', 'arrayTipoDocumento', 'arrayProfesion'));
    }

    public function nuevoExpediente(Request $request)
    {
        // 1. Validación con mensajes en español
        $request->validate([
            'nombre'         => 'required',
            'numexpediente'  => 'required',
            'sexopaciente'   => 'required',
            ]);

        // 2. Expediente duplicado
        if (Paciente::where('numero_expediente', $request->numexpediente)->exists()) {
            return response()->json(['success' => 1]);
        }

        DB::beginTransaction();

        try {
            // 3. Subir foto si viene
            $nomDocumento = null;

            if ($request->hasFile('documento')) {
                $nomDocumento = $this->subirFoto($request->file('documento'));
            }

            // 4. Crear el paciente
            Paciente::create([
                'id_tipo'          => $request->tipopaciente,
                'id_estado_civil'  => $request->estadocivil,
                'id_tipo_documento'=> $request->tipodocumento,
                'id_profesion'     => $request->profesion,
                'nombres'          => $request->nombre,
                'apellidos'        => $request->apellido,
                'fecha_nacimiento' => $request->fechanacimiento,
                'sexo'             => $request->sexopaciente == 1 ? 'M' : 'F',
                'referido_por'     => $request->referido,
                'num_documento'    => $request->numdocumento,
                'correo'           => $request->correo,
                'celular'          => $request->celular,
                'telefono'         => $request->telefono,
                'direccion'        => $request->direccion,
                'foto'             => $nomDocumento,
                'numero_expediente'=> $request->numexpediente,
            ]);

            DB::commit();
            return response()->json(['success' => 2]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar expediente: ' . $e->getMessage(), [
                'expediente' => $request->numexpediente,
                'trace'      => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => 99]);
        }
    }


    /**
     * Sube la foto del paciente al disco configurado y retorna el nombre del archivo.
     */
    private function subirFoto(UploadedFile $archivo): string
    {
        $nombre      = Str::random(15) . '_' . str_replace(' ', '_', microtime());
        $extension   = strtolower('.' . $archivo->getClientOriginalExtension());
        $nombreFinal = $nombre . $extension;

        Storage::disk('archivos')->put($nombreFinal, \File::get($archivo));

        return $nombreFinal;
    }



    // ************ BUSCAR EXPEDIENTE *************************************



    public function indexBuscarExpediente()
    {
        return view('backend.admin.expediente.buscar.vistabuscarexpediente');
    }

    public function tablaBuscarExpediente(Request $request)
    {
        $query = Paciente::with(['tipoDocumento', 'tipoPaciente', 'profesion'])
            ->orderBy('nombres');

        if ($request->filled('expediente')) {
            $query->where('numero_expediente', 'like', '%' . $request->expediente . '%');
        }

        if ($request->filled('documento')) {
            $doc = str_replace('-', '', $request->documento);
            $query->where(function ($q) use ($request, $doc) {
                $q->where('num_documento', 'like', '%' . $request->documento . '%')
                    ->orWhere('num_documento', 'like', '%' . $doc . '%');
            });
        }

        $arrayExpedientes = $query->get();

        return view('backend.admin.expediente.buscar.tablabuscarexpediente', compact('arrayExpedientes'));
    }


    public function generarReporteFichaGeneralPaciente($idpaciente)
    {
        $infoPaciente = Paciente::with(['profesion', 'tipoDocumento', 'estadoCivil'])
            ->findOrFail($idpaciente);

        $nombreCompleto = trim(($infoPaciente->nombres ?? '') . ' ' . ($infoPaciente->apellidos ?? ''));

        // ── Edad legible ───────────────────────────────────────────────
        $edadTexto = '—';

        if ($infoPaciente->fecha_nacimiento) {
            $nacimiento = Carbon::parse($infoPaciente->fecha_nacimiento);
            $hoy = Carbon::now();

            $anos = (int) $nacimiento->diffInYears($hoy);

            if ($anos >= 1) {
                $edadTexto = $anos . ' año' . ($anos > 1 ? 's' : '');
            } else {
                $meses = (int) $nacimiento->diffInMonths($hoy);

                if ($meses >= 1) {
                    $edadTexto = $meses . ' mes' . ($meses > 1 ? 'es' : '');
                } else {
                    $edadTexto = '0';
                }
            }
        }

        $fechaFormat = $infoPaciente->fecha_nacimiento
            ? date('d-m-Y', strtotime($infoPaciente->fecha_nacimiento))
            : '—';
        $profesionNombre = $infoPaciente->profesion?->nombre ?? '—';
        $tipoDocNombre = $infoPaciente->tipoDocumento?->nombre ?? '—';
        $tipoCivilNombre = $infoPaciente->estadoCivil?->nombre ?? '—';

        // ── mPDF ──────────────────────────────────────────────────────
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Ficha Paciente');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        // ══ ENCABEZADO ════════════════════════════════════════════════
        $html = "
<table width='100%' style='border-collapse:collapse; font-family:Arial, sans-serif;'>
    <tr>
        <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
            <table width='100%'>
                <tr>
                    <td style='width:30%; text-align:left;'>
                        <img src='{$logoalcaldia}' style='height:38px'>
                    </td>
                    <td style='width:70%; text-align:left; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                        SANTA ANA NORTE<br>EL SALVADOR
                    </td>
                </tr>
            </table>
        </td>
       <td style='width:75%; border:0.8px solid #000;
           padding:6px 8px; text-align:center; font-size:14px; font-weight:bold; vertical-align:middle;'>

        <div class='contenedorp'>
            <p>
                Clínica Municipal Cristóbal Peraza <br>
                Tahuilapa, Distrito de Metapán, Santa Ana Norte <br>
                Hoja de Datos Generales de Paciente <br><br>
            </p>
        </div>

    </td>
        </tr>
    </table>
    <br>

        <p style='text-align:center;'>
            Expediente: $infoPaciente->numero_expediente
        </p>

    ";

        // ══ FOTO ══════════════════════════════════════════════════════
        if ($infoPaciente->foto) {
            $imagePath = public_path('storage/archivos/' . $infoPaciente->foto);
            if (file_exists($imagePath)) {
                $html .= "
<table width='100%' style='font-family:Arial,sans-serif; margin-bottom:10px;'>
    <tr>
        <td style='text-align:center;'>
            <img src='{$imagePath}' width='150' height='150'
                 style='border:1px solid #ccc; border-radius:4px;'>
        </td>
    </tr>
</table>
";
            }
        }

        // ══ DATOS GENERALES ═══════════════════════════════════════════
        $filas = [
            ['Nombre completo', $nombreCompleto],
            ['Fecha de nacimiento', $fechaFormat],
            ['Edad', $edadTexto],
            ['Sexo', $infoPaciente->sexo ?? '—'],
            ['Estado civil', $tipoCivilNombre],
            ['Tipo de documento', $tipoDocNombre],
            [
                'Número de documento',
                $infoPaciente->num_documento
                    ? substr($infoPaciente->num_documento, 0, 7) . '-' . substr($infoPaciente->num_documento, 7)
                    : '—'
            ],
            ['Correo electrónico', $infoPaciente->correo ?? '—'],
            ['Teléfono celular', $infoPaciente->celular ?? '—'],
            ['Teléfono alternativo', $infoPaciente->telefono ?? '—'],
            ['Domicilio', $infoPaciente->direccion ?? '—'],
            ['Profesión', $profesionNombre],
            ['Referido por', $infoPaciente->referido_por ?? '—'],
        ];

        $html .= "
<table width='100%' style='border-collapse:collapse; font-family:Arial,sans-serif; font-size:13px;'>
";
        foreach ($filas as [$label, $valor]) {
            $html .= "
    <tr>
        <td style='width:35%; border:0.8px solid #ccc; padding:6px 8px;
                   font-weight:bold; background:#f5f5f5;'>{$label}</td>
        <td style='width:65%; border:0.8px solid #ccc; padding:6px 8px;'>{$valor}</td>
    </tr>";
        }

        $html .= "
</table>
";

        // ══ RENDER ════════════════════════════════════════════════════
        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->setFooter('Página: {PAGENO}/{nb}');
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
    }




    public function indexEditarPaciente($idpaciente)
    {
        $infoPa = Paciente::where('id', $idpaciente)->firstOrFail();

        $arrayEstadoCivil   = EstadoCivil::orderBy('nombre')->get();
        $arrayTipoPaciente  = TipoPaciente::orderBy('nombre')->get();
        $arrayTipoDocumento = TipoDocumento::orderBy('nombre')->get();
        $arrayProfesion     = Profesion::orderBy('nombre')->get();

        $tiposexo = $infoPa->sexo === 'M' ? 1 : 2;

        return view('backend.admin.expediente.editarpaciente.vistaeditarpaciente', compact(
            'infoPa',
            'arrayEstadoCivil',
            'arrayTipoPaciente',
            'arrayTipoDocumento',
            'arrayProfesion',
            'tiposexo',
            'idpaciente'
        ));
    }


    public function actualizarExpediente(Request $request)
    {
        $request->validate([
            'idpaciente'     => 'required|integer',
            'nombre'         => 'required|string',
            'numExpediente'  => 'required|string',
            'sexopaciente'   => 'required',
            'fechanacimiento'=> 'required|date',
            'estadocivil'    => 'required|integer',
            'tipodocumento'  => 'required|integer',
        ]);

        $infoPaciente = Paciente::where('id', $request->idpaciente)->first();

        if (!$infoPaciente) {
            return response()->json(['success' => 99]);
        }

        // Expediente duplicado en otro paciente
        if (Paciente::where('numero_expediente', $request->numExpediente)
            ->where('id', '!=', $request->idpaciente)
            ->exists()) {
            return response()->json(['success' => 1]);
        }

        DB::beginTransaction();

        try {

            $nomDocumento = $infoPaciente->foto; // conservar foto actual por defecto

            if ($request->hasFile('documento')) {

                $nuevaFoto = $this->subirFoto($request->file('documento'));

                // Eliminar foto anterior si existe
                if ($infoPaciente->foto && Storage::disk('archivos')->exists($infoPaciente->foto)) {
                    Storage::disk('archivos')->delete($infoPaciente->foto);
                }

                $nomDocumento = $nuevaFoto;
            }

            $genero = $request->sexopaciente == 1 ? 'M' : 'F';

            Paciente::where('id', $request->idpaciente)->update([
                'numero_expediente'  => $request->numExpediente,
                'nombres'            => $request->nombre,
                'apellidos'          => $request->apellido,
                'fecha_nacimiento'   => $request->fechanacimiento,
                'sexo'               => $genero,
                'id_tipo'            => $request->tipopaciente,
                'id_estado_civil'    => $request->estadocivil,
                'id_tipo_documento'  => $request->tipodocumento,
                'id_profesion'       => $request->profesion,
                'num_documento'      => $request->numdocumento,
                'telefono'           => $request->telefono,
                'celular'            => $request->celular,
                'correo'             => $request->correo,
                'direccion'          => $request->direccion,
                'referido_por'       => $request->referido,
                'foto'               => $nomDocumento,
            ]);

            DB::commit();
            return response()->json(['success' => 2]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al actualizar expediente: ' . $e->getMessage(), [
                'idpaciente' => $request->idpaciente,
                'trace'      => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => 99]);
        }
    }








}
