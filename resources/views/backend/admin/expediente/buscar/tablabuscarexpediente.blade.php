<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%"># Exp.</th>
                                <th style="width: 18%">Nombres</th>
                                <th style="width: 18%">Apellidos</th>
                                <th style="width: 10%">Tipo Doc.</th>
                                <th style="width: 12%">Documento</th>
                                <th style="width: 10%">Tipo Paciente</th>
                                <th style="width: 10%">Profesión</th>
                                <th style="width: 12%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arrayExpedientes as $dato)
                                <tr>
                                    <td>{{ $dato->numero_expediente }}</td>
                                    <td>{{ $dato->nombres }}</td>
                                    <td>{{ $dato->apellidos }}</td>
                                    <td>{{ $dato->tipoDocumento?->nombre }}</td>
                                    <td>{{ $dato->num_documento }}</td>
                                    <td>{{ $dato->tipoPaciente?->nombre }}</td>
                                    <td>{{ $dato->profesion?->nombre }}</td>
                                    <td>
                                        <div style="text-align: center;">
                                            <button type="button" title="Documentos y Recetas"
                                                    class="btn btn-success"
                                                    onclick="infoDocumentoReceta({{ $dato->id }})">
                                                <i class="fas fa-file"></i>
                                            </button>
                                            <button type="button" title="Datos Generales"
                                                    class="btn btn-primary"
                                                    onclick="infoEditarPaciente({{ $dato->id }})">
                                                <i class="fas fa-user"></i>
                                            </button>
                                            <button type="button" title="Ficha General"
                                                    class="btn btn-warning" style="color: white"
                                                    onclick="infoImpresion({{ $dato->id }})">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <script>
                            closeLoading();
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{--
=====================================================================
CONTROLADOR — método para la tabla (buscar)
=====================================================================

public function tablaBuscar(Request $request)
{
    $query = Paciente::with(['tipoDocumento', 'tipoPaciente', 'profesion']);

    if ($request->filled('nombre')) {
        $query->where(function ($q) use ($request) {
            $q->where('nombres',   'like', '%' . $request->nombre . '%')
              ->orWhere('apellidos', 'like', '%' . $request->nombre . '%');
        });
    }

    if ($request->filled('expediente')) {
        $query->where('numero_expediente', 'like', '%' . $request->expediente . '%');
    }

    if ($request->filled('documento')) {
        // Quitar guion para comparar por si acaso viene con o sin él
        $doc = str_replace('-', '', $request->documento);
        $query->where(function ($q) use ($request, $doc) {
            $q->where('num_documento', 'like', '%' . $request->documento . '%')
              ->orWhere('num_documento', 'like', '%' . $doc . '%');
        });
    }

    $arrayExpedientes = $query->orderBy('numero_expediente')->get();

    return view('backend.expediente.tabla', compact('arrayExpedientes'));
}

=====================================================================
RUTA
=====================================================================

Route::get('/admin/expediente/tabla/buscar', [ExpedienteController::class, 'tablaBuscar'])
     ->name('admin.expediente.tabla.buscar');

Route::get('/admin/expediente/nuevo', [ExpedienteController::class, 'vistaExpedienteNuevo'])
     ->name('admin.expediente.nuevo');

=====================================================================
--}}
