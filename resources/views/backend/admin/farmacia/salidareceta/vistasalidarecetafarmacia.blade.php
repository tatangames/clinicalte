@extends('adminlte::page')

@section('title', 'Orden de Salida')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button"
           aria-haspopup="true" aria-expanded="false">
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

@section('content')

    <style>
        :root {
            --os-blue:     #1a6fc4;
            --os-blue-lt:  #e8f1fb;
            --os-green:    #1a8a5a;
            --os-green-lt: #e6f5ef;
            --os-red:      #c0392b;
            --os-red-lt:   #fde8e6;
            --os-amber:    #d97706;
            --os-amber-lt: #fef3c7;
            --os-gray:     #6c757d;
            --os-border:   #dee2e6;
            --os-surface:  #f8f9fa;
            --os-white:    #ffffff;
            --os-shadow:   0 2px 8px rgba(0,0,0,.08);
        }

        .os-wrap { width: 100%; padding: 1rem 0.5rem 3rem; }

        .os-card {
            background: var(--os-white);
            border: 1px solid var(--os-border);
            border-radius: 10px;
            box-shadow: var(--os-shadow);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .os-card-head {
            display: flex; align-items: center; gap: 10px;
            padding: .85rem 1.2rem;
            background: var(--os-surface);
            border-bottom: 1px solid var(--os-border);
        }
        .os-card-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--os-green-lt); color: var(--os-green);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }
        .os-card-title { font-size: 14px; font-weight: 700; color: #343a40; margin: 0; }
        .os-card-body  { padding: 1.1rem 1.2rem; }

        .os-label {
            font-size: 11px; font-weight: 700;
            color: var(--os-gray);
            text-transform: uppercase; letter-spacing: .04em;
            margin-bottom: 4px; display: block;
        }

        .os-card-body .form-control {
            border-radius: 7px; border-color: var(--os-border); font-size: 13px;
        }
        .os-card-body .form-control:focus {
            border-color: var(--os-blue);
            box-shadow: 0 0 0 3px rgba(26,111,196,.12);
        }

        .os-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .45rem 1rem; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: filter .15s, transform .1s;
        }
        .os-btn:active { transform: scale(.97); }
        .os-btn-danger { background: var(--os-red); color: #fff; }
        .os-btn-danger:hover { filter: brightness(1.08); color: #fff; }
        .os-btn-dark   { background: #6c757d; color: #fff; }
        .os-btn-dark:hover   { filter: brightness(1.08); color: #fff; }

        .os-filters {
            display: flex; align-items: flex-end;
            flex-wrap: wrap; gap: 12px;
        }
        .os-filter-group { display: flex; flex-direction: column; }

        #tablaDatatable table { font-size: 13px; }
        #tablaDatatable thead th {
            background: var(--os-surface);
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .04em;
            color: var(--os-gray);
        }

        #select-estado { font-weight: 600; }
    </style>

    <div class="os-wrap">

        {{-- ── Card de filtros ── --}}
        <div class="os-card">
            <div class="os-card-head">
                <div class="os-card-icon"><i class="fas fa-file-prescription"></i></div>
                <span class="os-card-title">Orden de Salida — Receta Médica</span>
            </div>
            <div class="os-card-body">
                <div class="os-filters">

                    <div class="os-filter-group">
                        <label class="os-label">Estado</label>
                        <select class="form-control" id="select-estado"
                                style="min-width:140px;" onchange="verificarEstado()">
                            <option value="1">⏳ Pendiente</option>
                            <option value="2">✅ Procesado</option>
                            <option value="3">🚫 Denegado</option>
                        </select>
                    </div>

                    <div class="os-filter-group">
                        <label class="os-label">Fecha desde</label>
                        <input type="date" class="form-control" id="fecha-inicio"
                               autocomplete="off" onchange="verificarEstado()">
                    </div>

                    <div class="os-filter-group">
                        <label class="os-label">Fecha hasta</label>
                        <input type="date" class="form-control" id="fecha-fin"
                               autocomplete="off" onchange="verificarEstado()">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Tabla dinámica ── --}}
        <div class="os-card">
            <div class="os-card-body p-0">
                <div id="tablaDatatable" style="padding: 1rem;"></div>
            </div>
        </div>

    </div>{{-- /os-wrap --}}


    {{-- ══ MODAL: Denegar receta ══ --}}
    <div class="modal fade" id="modalDenegar">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header" style="background:var(--os-surface); border-bottom:1px solid var(--os-border);">
                    <h5 class="modal-title" style="font-weight:700; font-size:15px;">
                        <i class="fas fa-ban mr-2 text-danger"></i>Denegar receta
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-denegar">
                        <input type="hidden" id="id-denegar">
                        <div class="form-group">
                            <label class="os-label">Paciente</label>
                            <input type="text" class="form-control" disabled
                                   id="paciente-denegar" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="os-label">Recetado por</label>
                            <input type="text" class="form-control" disabled
                                   id="doctor-denegar" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="os-label">
                                Motivo de denegación <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" maxlength="500"
                                   id="descripcion-denegar" autocomplete="off"
                                   placeholder="Escriba el motivo…">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="os-btn os-btn-dark" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="os-btn os-btn-danger" onclick="guardarDenegado()">
                        <i class="fas fa-ban"></i> Denegar
                    </button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-config.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            openLoading();
            $('#tablaDatatable').load(
                "{{ URL::to('/admin/salida/medicamento/porreceta/tabla') }}/1/-/-"
            );
        });

        /* ── Cargar tabla según filtros ── */
        function verificarEstado() {
            var estado      = document.getElementById('select-estado').value;
            var fechainicio = document.getElementById('fecha-inicio').value;
            var fechafin    = document.getElementById('fecha-fin').value;

            if (!fechainicio || !fechafin) {
                document.getElementById('tablaDatatable').innerHTML = '';
                return;
            }

            if (new Date(fechainicio) > new Date(fechafin)) {
                toastr.error('La fecha de inicio no puede ser mayor que la fecha fin');
                return;
            }

            openLoading();
            $('#tablaDatatable').load(
                "{{ URL::to('/admin/salida/medicamento/porreceta/tabla') }}/"
                + estado + "/" + fechainicio + "/" + fechafin
            );
        }

        /* ── Procesar receta ── */
        function procesarRecetaMedica(idreceta) {
            window.location.href = "{{ url('/admin/vista/procesar/recetamedica') }}/" + idreceta;
        }

        /* ── Denegar: abrir modal ── */
        function infoDenegarReceta(idreceta) {
            openLoading();
            document.getElementById('formulario-denegar').reset();

            axios.post(urlAdmin + '/admin/orden/salida/informacion/paradenegar', { id: idreceta })
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#id-denegar').val(idreceta);
                        $('#paciente-denegar').val(response.data.paciente);
                        $('#doctor-denegar').val(response.data.doctor);
                        $('#modalDenegar').modal('show');
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        /* ── Denegar: guardar ── */
        function guardarDenegado() {
            var idreceta    = document.getElementById('id-denegar').value;
            var descripcion = document.getElementById('descripcion-denegar').value.trim();

            if (!descripcion) { toastr.error('El motivo es requerido'); return; }

            openLoading();
            var fd = new FormData();
            fd.append('id', idreceta);
            fd.append('descripcion', descripcion);

            axios.post(urlAdmin + '/admin/orden/salida/guardar/denegacion', fd)
                .then(function (response) {
                    closeLoading();
                    $('#modalDenegar').modal('hide');

                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Ya procesada',
                            text: 'Esta receta ya había sido procesada.',
                            icon: 'warning',
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Recargar',
                            allowOutsideClick: false
                        }).then(function (r) { if (r.isConfirmed) verificarEstado(); });

                    } else if (response.data.success === 2) {
                        Swal.fire({
                            title: 'Ya denegada',
                            text: 'Esta receta ya había sido denegada.',
                            icon: 'warning',
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Recargar',
                            allowOutsideClick: false
                        }).then(function (r) { if (r.isConfirmed) verificarEstado(); });

                    } else if (response.data.success === 3) {
                        toastr.success('Receta denegada correctamente');
                        verificarEstado();
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }

        /* ── Retornar paciente a sala ── */
        function retornarPaciente(id) {
            Swal.fire({
                title: '¿Retornar paciente?',
                text: 'El paciente regresará a dentro de sala.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a8a5a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, retornar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then(function (result) {
                if (result.isConfirmed) retornarPacienteSala(id);
            });
        }

        function retornarPacienteSala(id) {
            openLoading();
            axios.post(urlAdmin + '/admin/paciente/retonarsala', { id: id })
                .then(function (response) {
                    closeLoading();
                    if (response.data.success === 1) {
                        location.reload();
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
        }
    </script>
@endsection
