@extends('adminlte::page')

@section('title', 'Editar Receta')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline">{{ Auth::guard('admin')->user()->nombre }}</span>
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

    @php
        // Mapa de estados: número → etiqueta legible
        $estadoLabels = [
            1 => 'Pendiente',
            2 => 'Procesada',
            3 => 'Denegada',
        ];
        $estadoLabel = $estadoLabels[$infoReceta->estado] ?? 'Desconocido';
    @endphp

    <style>
        :root {
            --rx-blue:     #1a6fc4;
            --rx-blue-lt:  #e8f1fb;
            --rx-green:    #1a8a5a;
            --rx-green-lt: #e6f5ef;
            --rx-amber:    #d97706;
            --rx-amber-lt: #fef3c7;
            --rx-red:      #c0392b;
            --rx-gray:     #6c757d;
            --rx-border:   #dee2e6;
            --rx-surface:  #f8f9fa;
            --rx-white:    #ffffff;
            --rx-shadow:   0 2px 8px rgba(0,0,0,.08);
        }

        .rx-wrap { width: 100%; padding: 0 0.5rem 3rem; }

        /* ── Cabecera paciente ── */
        .rx-patient-bar {
            display: flex; align-items: center; gap: 14px;
            background: var(--rx-white);
            border: 1px solid var(--rx-border);
            border-left: 4px solid var(--rx-blue);
            border-radius: 10px;
            padding: .85rem 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--rx-shadow);
        }
        .rx-patient-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--rx-blue-lt); color: var(--rx-blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .rx-patient-label { font-size: 11px; color: var(--rx-gray); text-transform: uppercase; letter-spacing: .05em; margin: 0; }
        .rx-patient-name  { font-size: 15px; font-weight: 700; color: #212529; margin: 0; }

        /* ── Estado badge ── */
        .rx-estado-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .rx-estado-activo    { background: var(--rx-green-lt); color: var(--rx-green); }
        .rx-estado-inactivo  { background: #fde8e6; color: var(--rx-red); }
        .rx-estado-procesada { background: var(--rx-blue-lt); color: var(--rx-blue); }
        .rx-estado-denegada  { background: #fde8e6; color: var(--rx-red); }

        /* ── Secciones ── */
        .rx-section {
            background: var(--rx-white);
            border: 1px solid var(--rx-border);
            border-radius: 10px;
            margin-bottom: 1.25rem;
            box-shadow: var(--rx-shadow);
            overflow: hidden;
        }
        .rx-section-head {
            display: flex; align-items: center; gap: 10px;
            padding: .75rem 1.2rem;
            background: var(--rx-surface);
            border-bottom: 1px solid var(--rx-border);
        }
        .rx-section-icon {
            width: 30px; height: 30px; border-radius: 7px;
            background: var(--rx-blue-lt); color: var(--rx-blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .rx-section-title { font-size: 13px; font-weight: 700; color: #343a40; margin: 0; }
        .rx-section-body  { padding: 1.1rem 1.2rem; }

        /* ── Labels ── */
        .rx-label {
            font-size: 11.5px; font-weight: 600;
            color: var(--rx-gray);
            text-transform: uppercase; letter-spacing: .04em;
            margin-bottom: 4px; display: block;
        }

        /* ── Inputs ── */
        .rx-section .form-control {
            border-radius: 7px; border-color: var(--rx-border); font-size: 13px;
        }
        .rx-section .form-control:focus {
            border-color: var(--rx-blue);
            box-shadow: 0 0 0 3px rgba(26,111,196,.12);
        }

        /* ── Tabla ── */
        .rx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .rx-table thead tr { background: var(--rx-surface); }
        .rx-table thead th {
            padding: .65rem 1rem;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em;
            color: var(--rx-gray);
            border-bottom: 2px solid var(--rx-border);
            white-space: nowrap;
        }
        .rx-table tbody tr { transition: background .15s; }
        .rx-table tbody tr:hover { background: #f1f6fd; }
        .rx-table tbody td {
            padding: .6rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .rx-table tbody tr:last-child td { border-bottom: none; }
        .rx-table .form-control { font-size: 13px; border-radius: 6px; }

        /* Fila cargada del servidor — borde izquierdo suave para distinguirlas */
        .rx-table tbody tr.fila-existente { border-left: 3px solid var(--rx-blue-lt); }
        .rx-table tbody tr.fila-nueva     { border-left: 3px solid var(--rx-green-lt); }

        .rx-row-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--rx-blue-lt); color: var(--rx-blue);
            font-size: 12px; font-weight: 700;
        }

        /* ── Botones ── */
        .rx-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .45rem 1rem; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: filter .15s, transform .1s; text-decoration: none;
        }
        .rx-btn:active { transform: scale(.97); }
        .rx-btn-primary { background: var(--rx-blue);  color: #fff; }
        .rx-btn-primary:hover  { filter: brightness(1.1); color: #fff; }
        .rx-btn-success { background: var(--rx-green); color: #fff; }
        .rx-btn-success:hover  { filter: brightness(1.1); color: #fff; }
        .rx-btn-danger  { background: var(--rx-red);   color: #fff; }
        .rx-btn-danger:hover   { filter: brightness(1.1); color: #fff; }
        .rx-btn-back    { background: #fff; color: #495057; border: 1px solid var(--rx-border); }
        .rx-btn-back:hover { background: var(--rx-surface); }
        .rx-btn-outline { background: transparent; border: 1px solid var(--rx-blue); color: var(--rx-blue); }
        .rx-btn-outline:hover { background: var(--rx-blue-lt); }
        .rx-btn-amber { background: var(--rx-amber); color: #fff; }
        .rx-btn-amber:hover { filter: brightness(1.1); color: #fff; }

        /* ── Topbar ── */
        .rx-topbar {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 8px; margin-bottom: 1.25rem;
        }

        /* ── Divider ── */
        .rx-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 1.5rem 0 1rem;
            color: var(--rx-gray); font-size: 11px;
            font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        }
        .rx-divider::before, .rx-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--rx-border);
        }

        /* ── Badge ── */
        .rx-badge {
            display: inline-block; padding: 2px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 700;
        }
        .rx-badge-blue  { background: var(--rx-blue-lt); color: var(--rx-blue); }
        .rx-badge-green { background: var(--rx-green-lt); color: var(--rx-green); }
        .rx-badge-amber { background: var(--rx-amber-lt); color: var(--rx-amber); }

        /* ── Empty placeholder ── */
        .rx-empty {
            text-align: center; padding: 2.5rem 1rem; color: #adb5bd;
        }
        .rx-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; }

        /* ── Aviso solo lectura ── */
        .rx-readonly-notice {
            display: flex; align-items: center; gap: 10px;
            background: var(--rx-amber-lt);
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: .65rem 1rem;
            font-size: 13px; color: #92400e;
            margin-bottom: 1.25rem;
        }

        /* ── Footer sticky ── */
        .rx-footer-save {
            position: sticky; bottom: 0;
            background: var(--rx-white);
            border-top: 1px solid var(--rx-border);
            padding: .9rem 1.2rem;
            text-align: right;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 -4px 12px rgba(0,0,0,.06);
            z-index: 10;
        }

        /* ── Select2 ── */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 7px !important;
            border-color: var(--rx-border) !important;
            font-size: 13px !important;
        }

        /* ── Cantidad bodega — chip visual ── */
        .rx-stock-chip {
            display: inline-flex; align-items: center; gap: 4px;
            background: var(--rx-green-lt); color: var(--rx-green);
            border-radius: 6px; padding: 3px 8px;
            font-size: 12px; font-weight: 600;
        }
    </style>

    <div class="rx-wrap">

        {{-- ── Topbar ── --}}
        <div class="rx-topbar">
            <button type="button" class="rx-btn rx-btn-back" onclick="salirHistorial()">
                <i class="fas fa-arrow-left"></i> Atrás
            </button>
            <span style="font-size:14px; font-weight:600; color:#343a40;">
            <i class="fas fa-file-medical-alt mr-1 text-primary"></i>{{ $titulo }}
        </span>
        </div>

        {{-- ── Aviso si receta no es editable ── --}}
        @if($infoReceta->estado != 1)
            <div class="rx-readonly-notice">
                <i class="fas fa-lock"></i>
                {{-- FIX: mostrar etiqueta legible en lugar del número --}}
                <span>Esta receta está en estado <strong>{{ $estadoLabel }}</strong> y no puede modificarse.</span>
            </div>
        @endif

        {{-- ── Paciente ── --}}
        <div class="rx-patient-bar">
            <div class="rx-patient-avatar"><i class="fas fa-user-injured"></i></div>
            <div>
                <p class="rx-patient-label">Receta para paciente</p>
                <p class="rx-patient-name">{{ $nombreCompleto }}</p>
            </div>
            <div class="ml-auto d-flex align-items-center gap-2" style="gap:8px">

                {{-- FIX: badge de estado con etiqueta e ícono según valor --}}
                @if($infoReceta->estado == 1)
                    <span class="rx-estado-badge rx-estado-activo">
                        <i class="fas fa-circle" style="font-size:7px"></i> Pendiente
                    </span>
                @elseif($infoReceta->estado == 2)
                    <span class="rx-estado-badge rx-estado-procesada">
                        <i class="fas fa-check-circle" style="font-size:10px"></i> Procesada
                    </span>
                @elseif($infoReceta->estado == 3)
                    <span class="rx-estado-badge rx-estado-denegada">
                        <i class="fas fa-times-circle" style="font-size:10px"></i> Denegada
                    </span>
                @else
                    <span class="rx-estado-badge rx-estado-inactivo">
                        <i class="fas fa-circle" style="font-size:7px"></i> {{ $estadoLabel }}
                    </span>
                @endif

                <span class="rx-badge rx-badge-amber"><i class="fas fa-edit mr-1"></i>Editar receta</span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             SECCIÓN 1 — Datos generales
        ══════════════════════════════════════════ --}}
        <div class="rx-section">
            <div class="rx-section-head">
                <div class="rx-section-icon"><i class="fas fa-calendar-alt"></i></div>
                <span class="rx-section-title">Datos generales</span>
            </div>
            <div class="rx-section-body">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label class="rx-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" value="{{ $fechaActual }}">
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="rx-label">Diagnóstico</label>
                        <select id="select-dianostico" class="form-control">
                            <option value="">Seleccionar…</option>
                            @foreach($arrayDiagnostico as $item)
                                <option value="{{ $item->id }}"
                                    {{ $infoReceta->id_diagnostico == $item->id ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="rx-label">Próxima cita</label>
                        <input type="date" class="form-control" id="proxima-cita"
                               value="{{ $infoReceta->proxima_cita }}">
                    </div>

                    <div class="col-md-8">
                        <label class="rx-label">Indicaciones generales</label>
                        <textarea class="form-control" id="text-indicacion-general" rows="3"
                                  placeholder="Indicaciones generales para el paciente…">{{ $infoReceta->descripcion_general }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             SECCIÓN 2 — Agregar medicamento (solo si editable)
        ══════════════════════════════════════════ --}}
        @if($infoReceta->estado == 1)

            <div class="rx-divider"><i class="fas fa-pills"></i> Agregar medicamento</div>

            <div class="rx-section">
                <div class="rx-section-head">
                    <div class="rx-section-icon"><i class="fas fa-plus-square"></i></div>
                    <span class="rx-section-title">Nuevo medicamento</span>
                </div>
                <div class="rx-section-body">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="rx-label">Fuente de financiamiento</label>
                            <select id="select-fuente" class="form-control" onchange="cargarTablaProducto()">
                                @foreach($arrayFuente as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="rx-label">Medicamento</label>
                            <select id="select-medicamento" class="form-control" onchange="getNombreGenerico()">
                                <option value="" disabled selected>Seleccione primero la fuente</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="rx-label">Nombre genérico</label>
                            <input type="text" class="form-control" id="nombre-generico" disabled
                                   placeholder="Se carga automáticamente">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="rx-label">Cantidad</label>
                            <input type="number" min="0" max="100" class="form-control"
                                   id="cantidad" placeholder="0">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="rx-label">Vía</label>
                            <select id="select-via" class="form-control">
                                <option value="">Seleccionar…</option>
                                @foreach($arrayVia as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $infoReceta->id_via == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="rx-label">Indicaciones del medicamento</label>
                            <textarea class="form-control" id="indicacion-medicamento" rows="2"
                                      placeholder="Dosis, frecuencia…"></textarea>
                        </div>

                    </div>

                    <div class="text-right mt-1">
                        <button type="button" class="rx-btn rx-btn-primary" onclick="agregarFila()">
                            <i class="fas fa-plus"></i> Agregar a la receta
                        </button>
                    </div>

                </div>
            </div>

        @endif

        {{-- ══════════════════════════════════════════
             SECCIÓN 3 — Detalle de medicamentos
        ══════════════════════════════════════════ --}}
        <div class="rx-divider"><i class="fas fa-list-ul"></i> Detalle de receta</div>

        <div class="rx-section">
            <div class="rx-section-head">
                <div class="rx-section-icon"><i class="fas fa-list-ul"></i></div>
                <span class="rx-section-title">Medicamentos en la receta</span>
                <span class="rx-badge rx-badge-blue ml-auto" id="contador-filas">
                {{ count($arrayDetalle) }} {{ count($arrayDetalle) == 1 ? 'medicamento' : 'medicamentos' }}
            </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="rx-table" id="matriz">
                    <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th style="width:100px">Cant. a retirar</th>
                        <th style="width:110px">Stock bodega</th>
                        <th>Vía</th>
                        <th>Indicaciones</th>
                        @if($infoReceta->estado == 1)
                            <th style="width:80px">Acción</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($arrayDetalle as $item)
                        <tr class="fila-existente">
                            <td>
                                <span class="rx-row-num" id="fila{{ $item->contador }}">{{ $item->contador }}</span>
                            </td>
                            <td>
                                <input name="arrayNombre[]" disabled
                                       data-idmedicamento="{{ $item->idEntradaDeta }}"
                                       value="{{ $item->nombre }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <input disabled value="{{ $item->lote }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <input name="arrayCantidad[]" disabled value="{{ $item->cantidad }}"
                                       class="form-control form-control-sm" type="number">
                            </td>
                            <td>
                                <span class="rx-stock-chip">
                                    <i class="fas fa-boxes" style="font-size:10px"></i>
                                    {{ $item->cantidadActual }}
                                </span>
                            </td>
                            <td>
                                <input name="arrayVia[]" disabled
                                       data-idvia="{{ $item->id_via }}"
                                       value="{{ $item->nombreVia }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <textarea name="arrayIndicacion[]"
                                          class="form-control form-control-sm"
                                          rows="2">{{ $item->descripcion }}</textarea>
                            </td>
                            @if($infoReceta->estado == 1)
                                <td>
                                    <button type="button"
                                            class="rx-btn rx-btn-danger"
                                            style="padding:.3rem .7rem;font-size:12px;"
                                            onclick="borrarFila(this)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr id="fila-vacia">
                            <td colspan="{{ $infoReceta->estado == 1 ? 8 : 7 }}">
                                <div class="rx-empty">
                                    <i class="fas fa-prescription-bottle-alt"></i>
                                    No hay medicamentos en esta receta.
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>

            @if($infoReceta->estado == 1)
                @can('boton.actualizar.receta')
                    <div class="rx-footer-save">
                        <button type="button" class="rx-btn rx-btn-success" onclick="preguntarGuardar()">
                            <i class="fas fa-save"></i> Actualizar receta médica
                        </button>
                    </div>
                @endcan
            @endif

        </div>

    </div>{{-- /rx-wrap --}}

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            ['#select-dianostico', '#select-via', '#select-medicamento'].forEach(function(sel){
                $(sel).select2({
                    theme: "bootstrap-5",
                    language: { noResults: () => "Sin resultados" }
                });
            });

            cargarTablaProducto();
            actualizarContador();
        });

        /* ── Navegación ── */
        function salirHistorial() {
            let idconsulta = {{ $infoConsulta->id }};
            window.location.href = "{{ url('/admin/historial/clinico/vista') }}/" + idconsulta;
        }

        function salirVistaHistorialClinico(idconsulta) {
            window.location.href = "{{ url('/admin/historial/clinico/vista') }}/" + idconsulta;
        }

        /* ── Cargar medicamentos por fuente ── */
        function cargarTablaProducto() {
            var idFuente = document.getElementById('select-fuente').value;
            document.getElementById("nombre-generico").value = "";

            if (!idFuente) {
                document.getElementById("select-medicamento").options.length = 0;
                $('#select-medicamento').append('<option value="" disabled selected>Seleccione primero la fuente</option>');
                return;
            }

            openLoading();
            var fd = new FormData();
            fd.append('idfuente', idFuente);

            axios.post(urlAdmin + '/admin/recetas/medicamentos/porfuente', fd)
                .then(res => {
                    closeLoading();
                    if (res.data.success === 1) {
                        document.getElementById("select-medicamento").options.length = 0;
                        if (res.data.hayfilas) {
                            $('#select-medicamento').append('<option value="" data-lote="" data-cantitotal="" data-nombre="" selected>Seleccionar medicamento</option>');
                            $.each(res.data.dataArray, (k, v) => {
                                $('#select-medicamento').append(
                                    `<option value="${v.id}" data-lote="${v.lote}" data-cantitotal="${v.cantidadTotal}" data-nombre="${v.nombre}">${v.nombretotal}</option>`
                                );
                            });
                        } else {
                            $('#select-medicamento').append('<option value="" data-lote="" data-cantitotal="" data-nombre="">Sin medicamentos disponibles</option>');
                        }
                    } else {
                        toastr.error('No se pudo cargar la información');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('No se pudo cargar la información'); });
        }

        /* ── Nombre genérico ── */
        function getNombreGenerico() {
            var sel = document.getElementById("select-medicamento");
            var opt = sel.options[sel.selectedIndex];
            document.getElementById("nombre-generico").value = opt.getAttribute("data-generico") || '';
        }

        /* ── Agregar fila ── */
        function agregarFila() {
            var idmedicamento = document.getElementById("select-medicamento").value;
            var indicaciones  = document.getElementById("indicacion-medicamento").value.trim();
            var cantidad      = document.getElementById("cantidad").value;
            var idvia         = document.getElementById("select-via").value;
            var selVia        = document.getElementById("select-via");
            var nombreVia     = selVia.options[selVia.selectedIndex].text;
            var reglaEntero   = /^[0-9]\d*$/;

            if (!idmedicamento) { toastr.error('Seleccione un medicamento'); return; }
            if (!indicaciones)  { toastr.error('Las indicaciones son requeridas'); return; }
            if (!cantidad || !cantidad.match(reglaEntero) || parseInt(cantidad) <= 0) {
                toastr.error('Ingrese una cantidad válida (entero positivo)'); return;
            }
            if (parseInt(cantidad) > 9000000) { toastr.error('La cantidad máxima es 9 millones'); return; }
            if (!idvia) { toastr.error('Seleccione una vía'); return; }

            var sel      = document.getElementById("select-medicamento");
            var opt      = sel.options[sel.selectedIndex];
            var nombre   = opt.getAttribute("data-nombre");
            var lote     = opt.getAttribute("data-lote");
            var hayTotal = parseInt(opt.getAttribute("data-cantitotal"));

            if (parseInt(cantidad) > hayTotal) {
                Swal.fire({
                    title: 'Cantidad excedida',
                    text: `Solo hay ${hayTotal} unidades disponibles en bodega.`,
                    type: 'warning',
                    confirmButtonColor: '#1a6fc4',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Quitar placeholder vacío si existe
            var filaVacia = document.getElementById('fila-vacia');
            if (filaVacia) filaVacia.remove();

            var nFilas = $('#matriz > tbody > tr').length + 1;

            var fila = `
        <tr class="fila-nueva">
            <td><span class="rx-row-num" id="fila${nFilas}">${nFilas}</span></td>
            <td>
                <input name="arrayNombre[]" disabled data-idmedicamento="${idmedicamento}"
                       value="${nombre}" class="form-control form-control-sm" type="text">
            </td>
            <td>
                <input disabled value="${lote}" class="form-control form-control-sm" type="text">
            </td>
            <td>
                <input name="arrayCantidad[]" disabled value="${cantidad}"
                       class="form-control form-control-sm" type="number">
            </td>
            <td>
                <span class="rx-stock-chip">
                    <i class="fas fa-boxes" style="font-size:10px"></i>
                    ${hayTotal}
                </span>
            </td>
            <td>
                <input name="arrayVia[]" disabled data-idvia="${idvia}"
                       value="${nombreVia}" class="form-control form-control-sm" type="text">
            </td>
            <td>
                <textarea name="arrayIndicacion[]" class="form-control form-control-sm" rows="2">${indicaciones}</textarea>
            </td>
            <td>
                <button type="button" class="rx-btn rx-btn-danger"
                        style="padding:.3rem .7rem;font-size:12px;"
                        onclick="borrarFila(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

            $("#matriz tbody").append(fila);
            actualizarContador();

            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: 'Agregado',
                showConfirmButton: false,
                timer: 1200
            });

            document.getElementById("indicacion-medicamento").value = "";
            document.getElementById("cantidad").value = "";
            document.getElementById('select-via').selectedIndex = 0;
            $("#select-via").trigger("change");
        }

        /* ── Borrar fila ── */
        function borrarFila(el) {
            el.closest('tr').remove();
            setearFila();
            actualizarContador();
        }

        function setearFila() {
            var rows = document.querySelectorAll('#matriz tbody tr:not(#fila-vacia)');
            var conteo = 0;
            rows.forEach(function(row) {
                conteo++;
                var span = row.cells[0].querySelector('.rx-row-num');
                if (span) { span.id = 'fila' + conteo; span.textContent = conteo; }
            });

            if (conteo === 0) {
                var cols = {{ $infoReceta->estado == 1 ? 8 : 7 }};
                $('#matriz tbody').html(`
            <tr id="fila-vacia">
                <td colspan="${cols}">
                    <div class="rx-empty">
                        <i class="fas fa-prescription-bottle-alt"></i>
                        No hay medicamentos en esta receta.
                    </div>
                </td>
            </tr>`);
            }
        }

        function actualizarContador() {
            var n = $('#matriz > tbody > tr:not(#fila-vacia)').length;
            document.getElementById('contador-filas').textContent = n + (n === 1 ? ' medicamento' : ' medicamentos');
        }

        /* ── Confirmar actualización ── */
        function preguntarGuardar() {
            Swal.fire({
                title: '¿Actualizar receta?',
                text: 'Se guardarán los cambios realizados.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a8a5a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.value) {
                    actualizarReceta();
                }
            });
        }

        function actualizarReceta() {
            var fecha             = document.getElementById('fecha').value;
            var diagnostico       = document.getElementById('select-dianostico').value;
            var indicacionGeneral = document.getElementById('text-indicacion-general').value;
            var proximaCita       = document.getElementById('proxima-cita').value;
            var reglaEntero       = /^[0-9]\d*$/;

            if (!fecha)       { toastr.error('La fecha es requerida'); return; }
            if (!diagnostico) { toastr.error('El diagnóstico es requerido'); return; }

            var nRegistro = $('#matriz > tbody > tr:not(#fila-vacia)').length;
            if (nRegistro <= 0) { toastr.error('Agregue al menos un medicamento'); return; }

            var arrayIdMedicamentos = $("input[name='arrayNombre[]']").map(function(){ return $(this).data('idmedicamento'); }).get();
            var arrayIdVia          = $("input[name='arrayVia[]']").map(function(){ return $(this).data('idvia'); }).get();
            var arrayCantidad       = $("input[name='arrayCantidad[]']").map(function(){ return $(this).val(); }).get();
            var arrayIndicaciones   = $("#matriz textarea[name='arrayIndicacion[]']").map(function(){ return $(this).val(); }).get();

            colorBlancoTabla();

            for (var a = 0; a < arrayIdMedicamentos.length; a++) {
                var idMedic    = arrayIdMedicamentos[a];
                var cantidad   = arrayCantidad[a];
                var indicacion = arrayIndicaciones[a];

                if (!idMedic) {
                    colorRojoTabla(a);
                    alertaMensaje('info', 'Error en fila', `Fila #${a+1}: medicamento no disponible. Borre la fila y vuelva a seleccionarlo.`);
                    return;
                }
                if (!cantidad || !cantidad.match(reglaEntero)) {
                    colorRojoTabla(a); toastr.error(`Fila #${a+1}: cantidad inválida.`); return;
                }
                if (parseInt(cantidad) <= 0 || parseInt(cantidad) > 9000000) {
                    colorRojoTabla(a); toastr.error(`Fila #${a+1}: cantidad fuera de rango.`); return;
                }
                if (!indicacion.trim()) {
                    colorRojoTabla(a); toastr.error(`Fila #${a+1}: las indicaciones son requeridas.`); return;
                }
            }

            openLoading();

            var idreceta  = {{ $idreceta }};
            var contenedor = [];
            for (var i = 0; i < arrayIdMedicamentos.length; i++) {
                contenedor.push({
                    infoIdMedicamento: arrayIdMedicamentos[i],
                    infoCantidad:      arrayCantidad[i],
                    infoIndicacion:    arrayIndicaciones[i],
                    infoIdVia:         arrayIdVia[i]
                });
            }

            var fd = new FormData();
            fd.append('contenedorArray',  JSON.stringify(contenedor));
            fd.append('idreceta',         idreceta);
            fd.append('fecha',            fecha);
            fd.append('diagnostico',      diagnostico);
            fd.append('indicacionGeneral',indicacionGeneral);
            fd.append('proximaCita',      proximaCita);

            axios.post(urlAdmin + '/admin/recetas/actualizar/parapaciente', fd)
                .then(res => {

                    console.log(res);

                    closeLoading();
                    var idconsulta = res.data.idconsulta;

                    if (res.data.success === 1) {
                        Swal.fire({
                            title: 'Receta no modificable',
                            text: 'La receta cambió de estado a: ' + res.data.estado,
                            type: 'error',
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Recargar',
                            allowOutsideClick: false
                        }).then(function (result) {
                            if (result.value) {
                                salirVistaHistorialClinico(idconsulta);
                            }
                        });

                    } else if (res.data.success === 2) {
                        Swal.fire({
                            title: 'Receta actualizada',
                            type: 'success',
                            confirmButtonColor: '#1a8a5a',
                            confirmButtonText: 'Aceptar',
                            allowOutsideClick: false
                        }).then(function (result) {
                            if (result.value) {
                                salirVistaHistorialClinico(idconsulta);
                            }
                        });

                    } else {
                        toastr.error('Error al actualizar la receta');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('Error al actualizar la receta'); });
        }

        function colorBlancoTabla() {
            $("#matriz tbody tr").css('background', '');
        }

        function colorRojoTabla(index) {
            $("#matriz tr:eq(" + (index + 1) + ")").css('background', '#fde8e6');
        }
    </script>
@endsection
