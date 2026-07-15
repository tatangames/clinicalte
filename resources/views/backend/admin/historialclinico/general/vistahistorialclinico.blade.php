@extends('adminlte::page')

@section('title', 'Historial Clínico')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

{{-- ─── Assets nav derecha ─── --}}
@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline ml-1">{{ Auth::guard('admin')->user()->nombre }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('admin.perfil') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> Editar Perfil
            </a>
        </div>
    </li>

    <li class="nav-item">
        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-link btn btn-link border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-md-inline">Cerrar Sesión</span>
            </button>
        </form>
    </li>
@endsection

{{-- ─── Contenido ─── --}}
@section('content')
    <section class="content pt-2">
        <div class="container-fluid">

            {{-- Botón atrás --}}
            <div class="mb-3">
                <button type="button" class="btn btn-warning btn-sm" style="color: white" onclick="vistaAsignaciones()">
                    <i class="fas fa-arrow-left mr-1"></i> Atrás
                </button>
            </div>

            {{-- Ficha del paciente + acciones --}}
            <div class="row mb-3">

                {{-- Datos del paciente --}}
                <div class="col-md-8">
                    <div class="card card-outline card-primary mb-0">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                @if($infoPaciente->foto)
                                    <img src="{{ url('storage/archivos/'.$infoPaciente->foto) }}"
                                         width="90" height="90" class="img-thumbnail mr-3" alt="Foto paciente" style="object-fit:cover">
                                @else
                                    <img src="{{ asset('images/foto-default.png') }}"
                                         width="90" height="90" class="img-thumbnail mr-3" alt="Sin foto">
                                @endif

                                <div>
                                    <h5 class="font-weight-bold mb-1">{{ $nombreCompleto }}</h5>
                                    <div class="d-flex flex-wrap gap-1" style="gap:6px">
                                    <span class="badge badge-primary px-2 py-1">
                                        <i class="fas fa-calendar-alt mr-1"></i> {{ $miFecha }}
                                    </span>
                                        <span class="badge badge-info px-2 py-1">
                                        Exp. #{{ $infoPaciente->numero_expediente }}
                                    </span>
                                        <span class="badge badge-secondary px-2 py-1">
                                        Consulta #{{ $totalConsulta }}
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="col-md-4">
                    <div class="card card-outline card-secondary mb-0 h-100">
                        <div class="card-body d-flex flex-column justify-content-center py-3" style="gap:8px">
                            <button type="button" class="btn btn-warning btn-block" style="color: white" onclick="vistaDatosGenerales()">
                                <i class="fas fa-user-edit mr-1"></i> Datos generales
                            </button>
                            <button type="button" class="btn btn-danger btn-block" onclick="finalizarConsulta()">
                                <i class="fas fa-check-circle mr-1"></i> Finalizar consulta
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tabs clínicos --}}
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" href="#tab_1" data-toggle="tab">
                                <img src="{{ asset('images/personacard.png') }}" height="18" width="18" class="mr-1">
                                Antecedentes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="#tab_2" data-toggle="tab">
                                <img src="{{ asset('images/corazonrojo.png') }}" height="18" width="18" class="mr-1">
                                SV + Antrop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="#tab_3" data-toggle="tab">
                                <img src="{{ asset('images/medicamento.png') }}" height="18" width="18" class="mr-1">
                                Recetas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="#tab_4" data-toggle="tab">
                                <img src="{{ asset('images/prescripcion.png') }}" height="18" width="18" class="mr-1">
                                Cuadro clínico
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="#tab_5" data-toggle="tab">
                                <img src="{{ asset('images/notas.png') }}" height="18" width="18" class="mr-1">
                                Notas
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">

                        {{-- Tab 1: Antecedentes --}}
                        <div class="tab-pane active" id="tab_1">
                            @can('ver.tabla.antecedentes')
                                <div id="tablaAntecedentes"></div>
                            @endcan
                        </div>

                        {{-- Tab 2: SV + Antropometría --}}
                        <div class="tab-pane" id="tab_2">
                            @can('ver.tabla.antropometria')
                                <div id="tablaAntropSv"></div>
                            @endcan
                        </div>

                        {{-- Tab 3: Recetas --}}
                        <div class="tab-pane" id="tab_3">
                            @can('ver.tabla.recetas')
                                <div id="tablaRecetas"></div>
                            @endcan
                        </div>

                        {{-- Tab 4: Cuadro clínico --}}
                        <div class="tab-pane" id="tab_4">
                            @can('ver.tabla.historialclinico')
                                <div id="tablaCuadroClinico"></div>
                            @endcan
                        </div>

                        {{-- Tab 5: Notas --}}
                        <div class="tab-pane" id="tab_5">
                            @can('ver.tabla.notas')
                                <div id="tablaNotas"></div>
                            @endcan
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>


    {{-- ══════════════════════════════════════════
         MODALES
    ══════════════════════════════════════════ --}}

    {{-- Modal: Nuevo cuadro clínico --}}
    <div class="modal fade" id="modalNuevoHistoClinico">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Nuevo cuadro clínico</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-historial-clinico">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-group col-md-8 mb-0">
                                <label class="font-weight-bold">Tipo de diagnóstico</label>
                                <select class="form-control" id="select-tipo-diagnostico">
                                    <option value="">Seleccionar opción</option>
                                    @foreach($arrayTipoDiagnostico as $item)
                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pt-3">
                                <button type="button" class="btn btn-warning btn-sm" style="color: white" onclick="nuevoDiagnosticoExtra()">
                                    <i class="fas fa-plus mr-1"></i> Nuevo tipo
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción</label>
                            <textarea id="editorCuadroClinico"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="guardarNuevoCuadroClinico()">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Editar cuadro clínico --}}
    <div class="modal fade" id="modalEditarHistoClinico">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Editar cuadro clínico</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-historial-clinico-editar">
                        <input type="hidden" id="idCuadroClinico-editar">
                        <div class="form-group col-md-7">
                            <label class="font-weight-bold">Tipo de diagnóstico</label>
                            <select class="form-control" id="select-tipo-diagnostico-editar"></select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción</label>
                            <textarea id="editorCuadroClinico-editar"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    @can('boton.actualizar.historial.clinico')
                        <button type="button" class="btn btn-success" onclick="actualizarCuadroClinico()">
                            <i class="fas fa-save mr-1"></i> Actualizar
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo tipo de diagnóstico --}}
    <div class="modal fade" id="modalExtraDiagnostico">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Nuevo tipo de diagnóstico</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-extradiagnostico">
                        <div class="form-group">
                            <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                            <input maxlength="150" id="extranombre-diagnostico-nuevo" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción <span class="text-muted">(opcional)</span></label>
                            <input maxlength="800" id="extradescripcion-diagnostico-nuevo" class="form-control" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="guardarExtraDiagnostico()">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nueva nota --}}
    <div class="modal fade" id="modalNuevaNota">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Nueva nota</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-notas-nuevo">
                        <div class="form-group" style="max-width:220px">
                            <label class="font-weight-bold">Fecha</label>
                            <input id="notas-fecha-nuevo" type="date" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Nota</label>
                            <textarea id="editorNotas"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="registrarNuevaNota()">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Editar nota --}}
    <div class="modal fade" id="modalEditarNota">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Editar nota</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-notas-editar">
                        <input type="hidden" id="idnota-editar">
                        <div class="form-group" style="max-width:220px">
                            <label class="font-weight-bold">Fecha</label>
                            <input id="notas-fecha-editar" type="date" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Nota</label>
                            <textarea id="editorNotasEditar"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="registrarNotaEditar()">
                        <i class="fas fa-save mr-1"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

@stop

{{-- ══════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════ --}}
@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="{{ asset('js/datatables-helper.js') }}"></script>

    <script>
        /* ─── Constantes de página ─── */
        const IDCONSULTA  = {{ $idconsulta }};
        const IDPACIENTE  = {{ $infoPaciente->id }};

        /* ─── Instancias de CKEditor ─── */
        var varGlobalEditorCuadro;
        var varGlobalEditorCuadroEditar;
        var varGlobalNuevaNota;
        var varGlobalEditarNota;

        /* ─── Config toolbar CKEditor (compartida) ─── */
        const ckToolbar = {
            items: ['heading','|','bold','italic','underline','strikethrough',
                '|','numberedList','bulletedList','|','alignment','|','undo','redo']
        };

        /* ─── Inicialización ─── */
        $(document).ready(function () {

            /* Cargar bloques de tabs */
            var bloques = [
                { div: 'tablaAntecedentes',  ruta: "{{ URL::to('/admin/historial/bloque/antecedente') }}/"   + IDCONSULTA },
                { div: 'tablaAntropSv',      ruta: "{{ URL::to('/admin/historial/bloque/antropsv') }}/"      + IDCONSULTA },
                { div: 'tablaRecetas',       ruta: "{{ URL::to('/admin/historial/bloque/recetas') }}/"        + IDCONSULTA },
                { div: 'tablaCuadroClinico', ruta: "{{ URL::to('/admin/historial/bloque/cuadroclinico') }}/"  + IDCONSULTA },
                { div: 'tablaNotas',         ruta: "{{ URL::to('/admin/historial/bloque/notas') }}/"          + IDCONSULTA },
            ];

            bloques.forEach(function (b) {
                if (document.getElementById(b.div)) {
                    $('#' + b.div).load(b.ruta);
                }
            });

            /* Select2 */
            ['#select-tipo-diagnostico', '#select-tipo-diagnostico-editar'].forEach(function (sel) {
                $(sel).select2({ theme: 'bootstrap-5', language: { noResults: function(){ return 'Sin resultados'; } } });
            });

            /* CKEditor ×4 */
            var editores = [
                { selector: '#editorCuadroClinico',        callback: function(e){ varGlobalEditorCuadro        = e; } },
                { selector: '#editorCuadroClinico-editar',  callback: function(e){ varGlobalEditorCuadroEditar  = e; } },
                { selector: '#editorNotas',                 callback: function(e){ varGlobalNuevaNota           = e; } },
                { selector: '#editorNotasEditar',           callback: function(e){ varGlobalEditarNota          = e; } },
            ];

            editores.forEach(function (ed) {
                ClassicEditor.create(document.querySelector(ed.selector), { toolbar: ckToolbar, language: 'es' })
                    .then(ed.callback)
                    .catch(function(){});
            });
        });

        /* ─── Navegación ─── */
        function vistaAsignaciones() {
            window.location.href = "{{ url('/admin/asignaciones/vista/index') }}";
        }

        function vistaDatosGenerales() {
            window.location.href = "{{ url('/admin/asignaciones/info/vista/editarpaciente') }}/" + IDPACIENTE;
        }

        function recargarVista() { location.reload(); }

        /* ─── Finalizar consulta ─── */
        function finalizarConsulta() {
            Swal.fire({
                title: '¿Finalizar consulta?',
                type: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33'
            }).then(function (result) {
                if (result.value) {
                    finalizarConsultaPaciente();
                }
            });
        }

        function finalizarConsultaPaciente() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', IDCONSULTA);

            axios.post(url + '/asignaciones/finalizar/consulta', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Consulta finalizada',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then(function () {
                            vistaAsignaciones();
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Diagnóstico extra ─── */
        function nuevoDiagnosticoExtra() {
            document.getElementById('formulario-extradiagnostico').reset();
            $('#modalExtraDiagnostico').modal('show');
        }

        function guardarExtraDiagnostico() {
            var nombre      = document.getElementById('extranombre-diagnostico-nuevo').value.trim();
            var descripcion = document.getElementById('extradescripcion-diagnostico-nuevo').value;

            if (!nombre) { toastr.error('Nombre es requerido'); return; }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);

            axios.post(url + '/diagnosticos/guardar/getlistado/completo', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Guardado correctamente');

                        var sel = document.getElementById('select-tipo-diagnostico');
                        sel.options.length = 0;
                        $.each(response.data.lista, function (k, v) {
                            sel.add(new Option(v.nombre, v.id));
                        });
                        $('#select-tipo-diagnostico').trigger('change');
                        $('#modalExtraDiagnostico').modal('hide');
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }

        /* ─── Recarga de tablas ─── */
        function recargarTablaCuadroClinico() {
            $('#tablaCuadroClinico').load("{{ URL::to('/admin/historial/bloque/cuadroclinico') }}/" + IDCONSULTA);
        }

        function recargarTablaNotas() {
            $('#tablaNotas').load("{{ URL::to('/admin/historial/bloque/notas') }}/" + IDCONSULTA);
        }

        function recargarTablaAntropometria() {
            $('#tablaAntropSv').load("{{ URL::to('/admin/historial/bloque/antropsv') }}/" + IDCONSULTA);
        }

        /* ─── Notas: nueva ─── */
        function vistaNuevaNota() {
            document.getElementById('formulario-notas-nuevo').reset();
            document.getElementById('notas-fecha-nuevo').value = new Date().toJSON().slice(0, 10);
            varGlobalNuevaNota.setData('');
            $('#modalNuevaNota').modal('show');
        }

        function registrarNuevaNota() {
            var editorNota = varGlobalNuevaNota.getData();
            var fecha      = document.getElementById('notas-fecha-nuevo').value;

            if (!editorNota.trim()) { toastr.error('Nota es requerida'); return; }
            if (!fecha)              { toastr.error('Fecha es requerida'); return; }

            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', IDCONSULTA);
            formData.append('fecha', fecha);
            formData.append('nota', editorNota);

            axios.post(urlAdmin + '/admin/historial/bloque/registrar/nota', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Registrado correctamente');
                        $('#modalNuevaNota').modal('hide');
                        recargarTablaNotas();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }

        /* ─── Notas: editar ─── */
        function informacionEditarNota(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/historial/bloque/notas/informacion', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#idnota-editar').val(id);
                        varGlobalEditarNota.setData(response.data.info.nota);
                        $('#notas-fecha-editar').val(response.data.info.fecha);
                        $('#modalEditarNota').modal('show');
                    } else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch(function () { toastr.error('Error al buscar'); closeLoading(); });
        }

        function registrarNotaEditar() {
            var editorNota = varGlobalEditarNota.getData();
            var fecha      = document.getElementById('notas-fecha-editar').value;
            var idfila     = document.getElementById('idnota-editar').value;

            if (!editorNota.trim()) { toastr.error('Nota es requerida'); return; }
            if (!fecha)              { toastr.error('Fecha es requerida'); return; }

            openLoading();
            var formData = new FormData();
            formData.append('idfila', idfila);
            formData.append('fecha', fecha);
            formData.append('nota', editorNota);

            axios.post(urlAdmin + '/admin/historial/bloque/actualizar/nota', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                        $('#modalEditarNota').modal('hide');
                        recargarTablaNotas();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }

        /* ─── Notas: borrar ─── */
        function modalBorrarNota(id) {
            Swal.fire({
                title: '¿Borrar nota?',
                type: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33'
            }).then(function (result) {
                if (result.value) {
                    borrarNota(id);
                }
            });
        }

        function borrarNota(id) {
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(urlAdmin + '/admin/historial/bloque/notas/borrar', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Borrado');
                        recargarTablaNotas();
                    } else {
                        toastr.error('Error al borrar');
                    }
                })
                .catch(function () { toastr.error('Error al borrar'); closeLoading(); });
        }

        /* ─── Antecedentes ─── */
        function guardarAntecedentes() {
            const checkboxes = document.querySelectorAll('input[name="arrayCheckAntecedentes[]"]');
            const datosCheckboxes = [];

            checkboxes.forEach(function (cb) {
                if (cb.checked) datosCheckboxes.push({ estado: true, valorAdicional: cb.dataset.valor });
            });

            openLoading();
            var formData = new FormData();
            formData.append('idpaciente',          IDPACIENTE);
            formData.append('datocheckbox',        JSON.stringify(datosCheckboxes));
            formData.append('textAntecedenteFami', document.getElementById('text-antecedentes-editar').value);
            formData.append('textAlergia',         document.getElementById('text-alergias-editar').value);
            formData.append('textMedicamento',     document.getElementById('text-medicamento-actual-editar').value);
            formData.append('selectSanguineo',     document.getElementById('select-tipeo-sanguineo').value);
            formData.append('notaAnteceMedico',    document.getElementById('notas_antecedente_medicos').value);
            formData.append('notaCompliDiabete',   document.getElementById('notas_complicacion_diabetes').value);
            formData.append('notaEnfermCronica',   document.getElementById('notas_enfermedad_cronica').value);
            formData.append('notaAnteceQuirur',    document.getElementById('notas_antecedente_quirurgico').value);
            formData.append('notaAnteceOftamo',    document.getElementById('notas_antecedente_quirurgico').value);
            formData.append('notaAnteceDeportivo', document.getElementById('notas_antecedente_deportivos').value);
            formData.append('datoMenarquia',       document.getElementById('dato-menarquia').value);
            formData.append('datoCicloMenstr',     document.getElementById('dato-ciclomenstrual').value);
            formData.append('datoPap',             document.getElementById('dato-pap').value);
            formData.append('datoMamografia',      document.getElementById('dato-mamografia').value);
            formData.append('otrosDetalles',       document.getElementById('otros-detalles').value);

            axios.post(urlAdmin + '/admin/historial/antecedente/actualizacion', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado correctamente');
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }

        /* ─── Helpers ─── */
        function valida_numero(e) {
            var tecla = e.keyCode || e.which;
            if (tecla === 8) return true;
            return /[0-9.]/.test(String.fromCharCode(tecla));
        }

        function generarReporteNota(id) {
            window.open("{{ URL::to('admin/pdf/reporte/notapaciente') }}/" + id);
        }

        function vistaNuevaReceta() {
            window.location.href = "{{ url('/admin/recetas/vista/general') }}/" + IDCONSULTA;
        }

        function infoEditarReceta(idreceta) {
            window.location.href = "{{ url('/admin/recetas/vista/paraeditar') }}/" + idreceta;
        }

        function vistaNuevaAntropologia() {
            window.location.href = "{{ url('/admin/vista/nueva/antropometria') }}/" + IDCONSULTA;
        }
    </script>
@endsection
