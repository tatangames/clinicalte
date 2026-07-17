@extends('adminlte::page')

@section('title', 'Asignaciones')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

{{-- ───────────────────────────────────────────────
     Assets adicionales (nav derecha)
──────────────────────────────────────────────── --}}
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

{{-- ───────────────────────────────────────────────
     Contenido principal
──────────────────────────────────────────────── --}}
@section('content')
    <section class="content pt-3">
        <div class="container-fluid">

            {{-- Encabezado de página --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0 font-weight-bold">Asignación de Salas</h4>
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/cronometro_gris.png') }}" width="28" height="28" class="mr-2" alt="Contador">
                    <span id="contador" class="font-weight-bold mr-3 text-secondary"></span>
                    <button type="button" class="btn btn-primary btn-sm" onclick="modalAgregar()">
                        <i class="fas fa-plus mr-1"></i> Nueva Asignación
                    </button>
                </div>
            </div>

            {{-- Tarjetas de sala --}}
            <div class="row">

                {{-- SALA: ENFERMERÍA --}}
                <div class="col-md-6 mb-3" id="bloque01enfermeria">
                    <div class="card card-outline card-primary h-100">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 font-weight-bold flex-grow-1">
                                <i class="fas fa-heartbeat mr-1 text-primary"></i>
                                Enfermería
                            </h5>
                            <span class="badge badge-light border mr-2" id="txtConteoEnfermeria">
                            {{ $conteoEnfermeria }} en espera
                        </span>
                            <button class="btn btn-warning btn-sm" style="color: white" onclick="modalTablaEnfermeria()">
                                <i class="fas fa-list mr-1"></i> Ver espera
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-1">Paciente en sala</p>
                            <div class="form-control font-weight-bold" id="paciente-enfermeria">
                                {{ $arrayPaciente['salaEnfermeriaPaciente'] }}
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-right">
                            <button id="opciones-enfermeria" class="btn btn-info btn-sm" disabled onclick="abrirSelectPaciente(2)">
                                <i class="fas fa-arrow-circle-right mr-1"></i> Opciones
                            </button>
                        </div>
                    </div>
                </div>

                {{-- SALA: CONSULTORIO --}}
                <div class="col-md-6 mb-3" id="bloque02consultoria">
                    <div class="card card-outline card-purple h-100">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 font-weight-bold flex-grow-1">
                                <i class="fas fa-stethoscope mr-1 text-purple"></i>
                                Consultorio
                            </h5>
                            <span class="badge badge-light border mr-2" id="txtConteoConsultoria">
                            {{ $conteoConsultorio }} en espera
                        </span>
                            <button class="btn btn-warning btn-sm" style="color: white" onclick="modalTablaConsultoria()">
                                <i class="fas fa-list mr-1"></i> Ver espera
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-1">Paciente en sala</p>
                            <div class="form-control font-weight-bold" id="paciente-consultorio">
                                {{ $arrayPaciente['salaConsultorioPaciente'] }}
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-right">
                            <button id="opciones-consultorio" class="btn btn-info btn-sm" disabled onclick="abrirSelectPaciente(1)">
                                <i class="fas fa-arrow-circle-right mr-1"></i> Opciones
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════
         MODALES
    ═══════════════════════════════════════════════ --}}

    {{-- 1. Nueva asignación --}}
    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Asignación a sala de espera</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="form-group">
                            <label class="font-weight-bold">Buscar paciente <span style="color: red">*</span></label>
                            <p class="text-muted small mb-1">Buscar por: nombre, apellido o número de documento</p>
                            <p class="text-muted small mb-2">Resultado: # Expediente — Nombre y apellido — Documento</p>
                            <input id="repuesto" data-info="0" autocomplete="off"
                                   class="form-control" onkeyup="buscarPaciente(this)" maxlength="400" type="text"
                                   placeholder="Escriba para buscar…">
                            <div class="droplista" style="position:absolute;z-index:9;width:75%"></div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Razón de uso <span style="color: red">*</span></label>
                            <select id="select-razon" class="form-control">
                                <option value="">Seleccione una razón de uso</option>
                                @foreach($arrayRazonUso as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Sala de espera <span style="color: red">*</span></label>
                            <select id="select-salaespera" class="form-control">
                                <option value="">Seleccione una sala</option>
                                @foreach($arraySalaEspera as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarRegistro()">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- 2. Lista de espera — Enfermería --}}
    <div class="modal fade" id="modalTablaEnfermeria">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Pacientes en espera — Enfermería</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="tablaDatatableEnfermeria"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- 3. Lista de espera — Consultorio --}}
    <div class="modal fade" id="modalTablaConsultoriaModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Pacientes en espera — Consultorio</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="tablaDatatableConsultoria"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- 4. Editar — traslado de cola --}}
    <div class="modal fade" id="modalTablaEditarSalas">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Traslado de cola</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar-traslado">
                        <input type="hidden" id="id-traslado-cola">

                        <div class="form-group">
                            <label class="font-weight-bold">Sala actual</label>
                            <select class="form-control" id="select-editar-salaactual"></select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Razón de uso</label>
                            <select class="form-control" id="select-editar-razonuso"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarAjustesEditados()">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- 5. Ficha administrativa --}}
    <div class="modal fade" id="modalFichaAdministrativa">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Ficha administrativa de ingreso</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-ficha-administrativa">
                        <input type="hidden" id="idpacientemodal-dentrosala">

                        <div class="row">

                            {{-- Columna izquierda: foto + acciones --}}
                            <div class="col-md-4 text-center">
                                <p class="font-weight-bold mb-2" id="textofoto">FOTOGRAFÍA</p>
                                <img id="foto-paciente-ficha" width="120" height="120"
                                     class="img-thumbnail mb-3" alt="Foto del paciente" style="object-fit:cover">

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-block mb-1" onclick="vistaHistorialClinico()">
                                        <i class="fas fa-file-medical mr-1"></i> Historial clínico
                                    </button>
                                    <button type="button" class="btn btn-warning btn-block mb-1" onclick="trasladoPacienteModal()" style="color:white">
                                        <i class="fas fa-exchange-alt mr-1"></i> Trasladar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-block" onclick="preguntaLiberarSala()">
                                        <i class="fas fa-door-open mr-1"></i> Liberar sala
                                    </button>
                                </div>
                            </div>

                            {{-- Columna derecha: datos --}}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-weight-bold">Paciente</label>
                                    <input type="text" id="txtNombre" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Hora de ingreso a sala de espera</label>
                                    <input type="text" id="txtHoraEntroEspera" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Hora de ingreso a sala</label>
                                    <input type="text" id="txtHoraEntro" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Número de expediente</label>
                                    <input type="text" id="txtNumeroConsulta" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Razón de uso</label>
                                    <select class="form-control" id="select-dentrosala-razonuso" disabled></select>
                                </div>

                                <div id="razonuso-btn-editar">
                                    <button type="button" class="btn btn-warning btn-sm" onclick="modificarSelectRazonUso()">
                                        <i class="fas fa-edit mr-1"></i> Editar razón de uso
                                    </button>
                                </div>
                                <div id="razonuso-btn-guardar" style="display:none">
                                    <button type="button" class="btn btn-success btn-sm" onclick="actualizarSelectRazonUso()">
                                        <i class="fas fa-save mr-1"></i> Actualizar
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- 6. Traslado de paciente dentro de sala --}}
    <div class="modal fade" id="modalTrasladoPaciente">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Traslado de paciente</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formulario-infotraslado">
                        <input type="hidden" id="id-trasladopaciente">

                        <div class="form-group">
                            <label class="font-weight-bold">Sala actual</label>
                            <input type="text" id="txtSalaActual-info" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Sala a asignar</label>
                            <select class="form-control" id="select-sala-asignar"></select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Razón de uso</label>
                            <select class="form-control" id="select-razouso-v2"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarNuevoTraslado()">
                        <i class="fas fa-save mr-1"></i> Guardar traslado
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- 7. Seleccionar paciente cuando hay varios en sala --}}
    <div class="modal fade" id="modalSelectConteoSala">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-danger">Pacientes en sala</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Seleccionar paciente</label>
                        <select class="form-control" id="select-conteo-paciente"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="buscarFichaAdministrativaModal()">
                        <i class="fas fa-eye mr-1"></i> Ver ficha
                    </button>
                </div>
            </div>
        </div>
    </div>

@stop


{{-- ═══════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════ --}}
@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/loadingOverlay.js') }}"></script>

    <script>
        $(document).ready(function () {
            window.seguroBuscador = true;
            window.txtContenedorGlobal = null;
            window.idPacienteGlobal = 0;

            $(document).click(function (e) {
                if (!$(e.target).closest('.droplista, #repuesto').length) {
                    $(".droplista").hide();
                }
            });
            validarBotonOpciones();
            countdown();
        });

        /* ─── Botones de opciones ─── */
        function validarBotonOpciones() {
            var btnConsultoria = {{ $arrayPaciente['botonOpcionConsultoria'] }};
            var btnEnfermeria  = {{ $arrayPaciente['botonOpcionEnfermeria'] }};

            document.getElementById("opciones-consultorio").disabled = (btnConsultoria <= 0);
            document.getElementById("opciones-enfermeria").disabled  = (btnEnfermeria  <= 0);
        }

        /* ─── Contador regresivo ─── */
        function countdown() {
            var seconds = 60;
            function tick() {
                var el = document.getElementById("contador");
                seconds--;
                el.innerHTML = "0:" + (seconds < 10 ? "0" : "") + seconds;
                if (seconds > 0) {
                    setTimeout(tick, 1000);
                } else {
                    recargarPaginaCronometro();
                    countdown();
                }
            }
            tick();
        }

        /* ─── Recarga automática por cronómetro ─── */
        function recargarPaginaCronometro() {
            var spinHandle = loadingOverlay().activate();
            ["bloque01enfermeria", "bloque02consultoria"].forEach(function (id) {
                document.getElementById(id).style.display = "none";
            });

            axios.post(urlAdmin + '/admin/asignaciones/recargando/cronometro')
                .then(function (response) {
                    loadingOverlay().cancel(spinHandle);
                    if (response.data.success === 1) {
                        var d = response.data;
                        document.getElementById("opciones-consultorio").disabled = (d.arraypaciente['botonOpcionConsultoria'] <= 0);
                        document.getElementById("opciones-enfermeria").disabled  = (d.arraypaciente['botonOpcionEnfermeria']  <= 0);

                        document.getElementById("txtConteoEnfermeria").innerHTML  = d.conteoEnfermeria  + " en espera";
                        document.getElementById("txtConteoConsultoria").innerHTML = d.conteoConsultorio + " en espera";
                        document.getElementById("paciente-enfermeria").innerHTML  = d.arraypaciente['salaEnfermeriaPaciente'];
                        document.getElementById("paciente-consultorio").innerHTML = d.arraypaciente['salaConsultorioPaciente'];
                    } else {
                        toastr.error('Error al recargar');
                    }
                    volverMostrarBloques();
                })
                .catch(function () {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error al recargar');
                    volverMostrarBloques();
                });
        }

        function volverMostrarBloques() {
            ["bloque01enfermeria", "bloque02consultoria"].forEach(function (id) {
                document.getElementById(id).style.display = "block";
            });
        }

        /* ─── Modal nueva asignación ─── */
        function modalAgregar() {
            document.getElementById("formulario-nuevo").reset();
            window.idPacienteGlobal = 0;
            $('#modalAgregar').modal('show');
        }

        /* ─── Buscador de paciente ─── */
        function buscarPaciente(e) {
            var texto = e.value.trim();

            // limpiar si está vacío, sin tocar el lock
            if (texto === '') {
                $(e).attr('data-info', 0);
                idPacienteGlobal = 0;
                $(".droplista").hide();
                return;
            }

            // mínimo 2 caracteres para no buscar con 1 letra
            if (texto.length < 2) return;

            if (!seguroBuscador) return;
            seguroBuscador = false;

            txtContenedorGlobal = e;

            axios.post(urlAdmin + '/admin/asignaciones/buscar/paciente', { query: texto })
                .then(function (response) {
                    seguroBuscador = true;
                    if (response.data) {
                        $(e).siblings(".droplista").html(response.data).fadeIn();
                    } else {
                        $(".droplista").hide();
                    }
                })
                .catch(function () {
                    seguroBuscador = true;
                    $(".droplista").hide();
                });
        }

        function modificarValor(edrop) {
            $(txtContenedorGlobal).val($(edrop).text()).attr('data-info', edrop.id);
            idPacienteGlobal = edrop.id;
            $(".droplista").hide();
        }



        function guardarRegistro() {
            if (idPacienteGlobal === 0) { toastr.error('Paciente es requerido'); return; }

            var razonUso   = document.getElementById('select-razon').value;
            var salaEspera = document.getElementById('select-salaespera').value;

            if (!razonUso)   { toastr.error('Razón de uso es requerida'); return; }
            if (!salaEspera) { toastr.error('Sala de espera es requerida'); return; }

            openLoading();

            var formData = new FormData();
            formData.append('idpaciente', idPacienteGlobal);
            formData.append('idrazon', razonUso);
            formData.append('idsalaespera', salaEspera);

            axios.post(urlAdmin + '/admin/asignaciones/nuevo/registro', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Nota',
                            text: response.data.mensaje,
                            type: 'info',
                            confirmButtonText: 'Aceptar'
                        });
                    } else if (response.data.success === 2) {
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargarVista();
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        function recargarVista() { location.reload(); }

        /* ─── Tablas de espera ─── */
        function modalTablaEnfermeria() {
            $('#tablaDatatableEnfermeria').load("{{ URL::to('/admin/asignaciones/tablamodal/enfermeria') }}");
            $('#modalTablaEnfermeria').modal('show');
        }

        function modalTablaConsultoria() {
            $('#tablaDatatableConsultoria').load("{{ URL::to('/admin/asignaciones/tablamodal/consultoria') }}");
            $('#modalTablaConsultoriaModal').modal('show');
        }

        /* ─── Editar cola ─── */
        function infoModalEditarSalas(id) {
            openLoading();
            axios.post(urlAdmin + '/admin/asignaciones/informacion/paciente', { id: id })
                .then(function (response) {
                    closeLoading();
                    if (response.data.success !== 1) { toastr.error('Información no encontrada'); return; }

                    var d = response.data;
                    $('#id-traslado-cola').val(d.info.id);

                    var selSala  = document.getElementById("select-editar-salaactual");
                    var selRazon = document.getElementById("select-editar-razonuso");
                    selSala.options.length = 0;
                    selRazon.options.length = 0;

                    $.each(d.arraysala, function (k, v) {
                        selSala.add(new Option(v.nombre, v.id, false, d.info.id_salaespera == v.id));
                    });
                    $.each(d.arrayrazonuso, function (k, v) {
                        selRazon.add(new Option(v.nombre, v.id, false, d.info.id_motivo == v.id));
                    });

                    $('#modalTablaEditarSalas').modal('show');
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function guardarAjustesEditados() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('id-traslado-cola').value);
            formData.append('idsala', document.getElementById('select-editar-salaactual').value);
            formData.append('idrazonuso', document.getElementById('select-editar-razonuso').value);

            axios.post(urlAdmin + '/admin/asignaciones/informacion/guardar', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Paciente actualizado',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar'
                        }).then(function () {
                            recargarVista();
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Finalizar consulta ─── */
        function infoModalEliminarPaciente(id) {
            Swal.fire({
                title: '¿Finalizar consulta?',
                type: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí'
            }).then(function (result) {
                if (result.value) {
                    finalizarConsultaPaciente(id);
                }
            });
        }

        function finalizarConsultaPaciente(idconsulta) {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', idconsulta);

            axios.post(urlAdmin + '/admin/asignaciones/finalizar/consulta', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Consulta finalizada',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar'
                        }).then(function (result) {
                            if (result.value) {
                                recargarVista();
                            }
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Ingresar paciente a sala ─── */
        function infoAsignarAsalaPaciente(idconsulta) {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', idconsulta);

            axios.post(urlAdmin + '/admin/asignaciones/ingresar/paciente/sala', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {

                        Swal.fire({
                            title: 'Asignado',
                            text: 'El paciente está dentro de la sala: ' + response.data.nombresala,
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar'
                        }).then(function (result) {
                            if (result.value) {
                                recargarVista();
                            }
                        });

                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Seleccionar paciente de sala con varios ─── */
        function abrirSelectPaciente(tipo) {
            openLoading();
            var formData = new FormData();
            formData.append('tipo', tipo);

            axios.post(urlAdmin + '/admin/asignaciones/personas/sala', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success !== 1) { toastr.error('Error al buscar'); return; }

                    var sel = document.getElementById("select-conteo-paciente");
                    sel.options.length = 0;
                    $.each(response.data.listado, function (k, v) {
                        sel.add(new Option(v.nombre, v.id));
                    });
                    sel.selectedIndex = 0;
                    $('#modalSelectConteoSala').modal('show');
                })
                .catch(function () { toastr.error('Error al buscar'); closeLoading(); });
        }

        /* ─── Ficha administrativa ─── */
        function buscarFichaAdministrativaModal() {
            $('#modalSelectConteoSala').modal('hide');
            openLoading();

            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('select-conteo-paciente').value);

            axios.post(urlAdmin + '/admin/asignaciones/info/paciente/dentrosala', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success !== 1) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Información no encontrada, recargar la página',
                            type: 'error',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar'
                        }).then(function (result) {
                            if (result.value) {
                                recargarVista();
                            }
                        });
                        return;
                    }

                    var d = response.data;
                    $('#foto-paciente-ficha').prop("src", d.hayfoto === 1
                        ? "{{ url('storage/archivos') }}/" + d.infopaciente.foto
                        : "{{ asset('images/foto-default.png') }}"
                    );
                    document.getElementById('textofoto').innerHTML = d.hayfoto === 1 ? "Fotografía" : "Sin fotografía";

                    $('#idpacientemodal-dentrosala').val(d.infoconsulta.id);
                    $('#txtNombre').val(d.nombrepaciente);
                    $('#txtHoraEntroEspera').val(d.entroespera);
                    $('#txtNumeroConsulta').val(d.numeroConsulta);
                    $('#txtHoraEntro').val(d.horaentro);

                    var sel = document.getElementById("select-dentrosala-razonuso");
                    sel.options.length = 0;
                    $.each(d.arrayrazonuso, function (k, v) {
                        sel.add(new Option(v.nombre, v.id, false, d.infoconsulta.id_motivo == v.id));
                    });

                    sel.disabled = true;
                    document.getElementById("razonuso-btn-editar").style.display = "block";
                    document.getElementById("razonuso-btn-guardar").style.display = "none";

                    $('#modalFichaAdministrativa').modal('show');
                })
                .catch(function () { toastr.error('Error al buscar'); closeLoading(); });
        }

        function modificarSelectRazonUso() {
            document.getElementById("select-dentrosala-razonuso").disabled = false;
            document.getElementById("razonuso-btn-editar").style.display = "none";
            document.getElementById("razonuso-btn-guardar").style.display = "block";
        }

        function actualizarSelectRazonUso() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('idpacientemodal-dentrosala').value);
            formData.append('idrazonuso', document.getElementById('select-dentrosala-razonuso').value);

            axios.post(urlAdmin + '/admin/asignaciones/actualizar/razonuso/paciente', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado');
                        document.getElementById("select-dentrosala-razonuso").disabled = true;
                        document.getElementById("razonuso-btn-guardar").style.display = "none";
                        document.getElementById("razonuso-btn-editar").style.display = "block";
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Liberar sala ─── */
        function preguntaLiberarSala() {
            Swal.fire({
                title: '¿Liberar sala?',
                type: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.value) {
                    liberarSala();
                }
            });
        }

        function liberarSala() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('idpacientemodal-dentrosala').value);

            axios.post(urlAdmin + '/admin/asignaciones/liberarsala/paciente', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Sala liberada',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar vista'
                        }).then(function (result) {
                            if (result.value) {
                                recargarVista();
                            }
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Traslado de paciente dentro de sala ─── */
        function trasladoPacienteModal() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('idpacientemodal-dentrosala').value);

            axios.post(urlAdmin + '/admin/asignaciones/informacion/paciente/dentrosala', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success !== 1) { toastr.error('Error al buscar'); return; }

                    var d = response.data;
                    $('#txtSalaActual-info').val(d.salactual);

                    var selSala  = document.getElementById("select-sala-asignar");
                    var selRazon = document.getElementById("select-razouso-v2");
                    selSala.options.length = 0;
                    selRazon.options.length = 0;

                    $.each(d.arraysala, function (k, v) {
                        selSala.add(new Option(v.nombre, v.id, false, d.info.id_salaespera == v.id));
                    });
                    $.each(d.arrayrazonuso, function (k, v) {
                        selRazon.add(new Option(v.nombre, v.id, false, d.info.id_motivo == v.id));
                    });

                    $('#modalTrasladoPaciente').modal('show');
                })
                .catch(function () { toastr.error('Error al buscar'); closeLoading(); });
        }

        function guardarNuevoTraslado() {
            openLoading();
            var formData = new FormData();
            formData.append('idconsulta', document.getElementById('idpacientemodal-dentrosala').value);
            formData.append('nuevasala', document.getElementById('select-sala-asignar').value);
            formData.append('nuevomotivo', document.getElementById('select-razouso-v2').value);

            axios.post(urlAdmin + '/admin/asignaciones/traslado/paciente/reseteo', formData)
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Traslado guardado',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar vista'
                        }).then(function (result) {
                            if (result.value) {
                                recargarVista();
                            }
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { toastr.error('Error al guardar'); closeLoading(); });
        }

        /* ─── Historial clínico ─── */
        function vistaHistorialClinico() {
            window.location.href = "{{ url('/admin/historial/clinico/vista') }}/" +
                document.getElementById('idpacientemodal-dentrosala').value;
        }
    </script>
@endsection
