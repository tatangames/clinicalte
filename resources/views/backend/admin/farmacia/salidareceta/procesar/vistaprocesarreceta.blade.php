@extends('adminlte::page')

@section('title', 'Procesar Orden')

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
            --pr-blue:     #1a6fc4;
            --pr-blue-lt:  #e8f1fb;
            --pr-green:    #1a8a5a;
            --pr-green-lt: #e6f5ef;
            --pr-red:      #c0392b;
            --pr-red-lt:   #fde8e6;
            --pr-amber:    #d97706;
            --pr-amber-lt: #fef3c7;
            --pr-gray:     #6c757d;
            --pr-border:   #dee2e6;
            --pr-surface:  #f8f9fa;
            --pr-white:    #ffffff;
            --pr-shadow:   0 2px 8px rgba(0,0,0,.08);
        }

        .pr-wrap { width: 100%; padding: 0 0.5rem 3rem; }

        /* ── Topbar ── */
        .pr-topbar {
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 8px;
            margin-bottom: 1.25rem;
        }

        /* ── Cards ── */
        .pr-card {
            background: var(--pr-white);
            border: 1px solid var(--pr-border);
            border-radius: 10px;
            box-shadow: var(--pr-shadow);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .pr-card-head {
            display: flex; align-items: center; gap: 10px;
            padding: .75rem 1.2rem;
            background: var(--pr-surface);
            border-bottom: 1px solid var(--pr-border);
        }
        .pr-card-icon {
            width: 30px; height: 30px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .pr-card-icon-blue  { background: var(--pr-blue-lt);  color: var(--pr-blue); }
        .pr-card-icon-green { background: var(--pr-green-lt); color: var(--pr-green); }
        .pr-card-title { font-size: 13px; font-weight: 700; color: #343a40; margin: 0; }
        .pr-card-body  { padding: 1.1rem 1.2rem; }

        /* ── Ficha paciente ── */
        .pr-patient-bar {
            display: flex; align-items: center; gap: 16px;
        }
        .pr-patient-photo {
            width: 80px; height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid var(--pr-border);
            flex-shrink: 0;
        }
        .pr-patient-name {
            font-size: 15px; font-weight: 700; color: #212529; margin: 0 0 6px;
        }
        .pr-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .pr-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .pr-chip-blue   { background: var(--pr-blue-lt);  color: var(--pr-blue); }
        .pr-chip-green  { background: var(--pr-green-lt); color: var(--pr-green); }
        .pr-chip-amber  { background: var(--pr-amber-lt); color: var(--pr-amber); }

        /* ── Label ── */
        .pr-label {
            font-size: 11px; font-weight: 700;
            color: var(--pr-gray);
            text-transform: uppercase; letter-spacing: .04em;
            margin-bottom: 4px; display: block;
        }

        /* ── Tabla medicamentos ── */
        .pr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .pr-table thead tr { background: var(--pr-surface); }
        .pr-table thead th {
            padding: .65rem 1rem;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em;
            color: var(--pr-gray);
            border-bottom: 2px solid var(--pr-border);
            white-space: nowrap;
        }
        .pr-table tbody tr { transition: background .15s; }
        .pr-table tbody tr:hover:not(.pr-row-alert) { background: #f1f6fd; }
        .pr-table tbody td {
            padding: .6rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .pr-table tbody tr:last-child td { border-bottom: none; }
        .pr-table .form-control { font-size: 13px; border-radius: 6px; }

        /* Fila con cantidad excedida */
        .pr-row-alert { background: #fde8e6 !important; }
        .pr-row-alert td { border-bottom-color: #f5c6cb; }
        .pr-row-alert .form-control {
            border-color: var(--pr-red);
            background: #fff5f5;
        }

        /* Número de fila */
        .pr-row-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--pr-blue-lt); color: var(--pr-blue);
            font-size: 12px; font-weight: 700;
        }
        .pr-row-num-alert {
            background: var(--pr-red-lt); color: var(--pr-red);
        }

        /* Stock chip */
        .pr-stock {
            display: inline-flex; align-items: center; gap: 4px;
            border-radius: 6px; padding: 3px 8px;
            font-size: 12px; font-weight: 600;
        }
        .pr-stock-ok   { background: var(--pr-green-lt); color: var(--pr-green); }
        .pr-stock-low  { background: var(--pr-red-lt);   color: var(--pr-red); }

        /* ── Botones ── */
        .pr-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .45rem 1.1rem; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: filter .15s, transform .1s;
        }
        .pr-btn:active { transform: scale(.97); }
        .pr-btn-back    { background: var(--pr-white); color: #495057; border: 1px solid var(--pr-border); }
        .pr-btn-back:hover { background: var(--pr-surface); }
        .pr-btn-success { background: var(--pr-green); color: #fff; }
        .pr-btn-success:hover { filter: brightness(1.08); color: #fff; }

        /* ── Aviso excedidos ── */
        .pr-alert-notice {
            display: flex; align-items: center; gap: 10px;
            background: var(--pr-red-lt);
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: .65rem 1rem;
            font-size: 13px; color: #721c24;
            margin-bottom: 1rem;
        }

        /* ── Footer sticky ── */
        .pr-footer {
            position: sticky; bottom: 0;
            background: var(--pr-white);
            border-top: 1px solid var(--pr-border);
            padding: .9rem 1.2rem;
            display: flex; align-items: center; justify-content: flex-end; gap: 10px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 -4px 12px rgba(0,0,0,.06);
            z-index: 10;
        }
    </style>

    <div class="pr-wrap">

        {{-- ── Topbar ── --}}
        <div class="pr-topbar">
            <button type="button" class="pr-btn pr-btn-back" onclick="volverAtras()">
                <i class="fas fa-arrow-left"></i> Atrás
            </button>
            <span style="font-size:14px; font-weight:700; color:#343a40;">
                <i class="fas fa-clipboard-check mr-1 text-success"></i> Procesar Receta Médica
            </span>
        </div>

        {{-- ── Ficha del paciente ── --}}
        <div class="pr-card">
            <div class="pr-card-head">
                <div class="pr-card-icon pr-card-icon-blue"><i class="fas fa-user-injured"></i></div>
                <span class="pr-card-title">Ficha clínica</span>
            </div>
            <div class="pr-card-body">
                <div class="pr-patient-bar">

                    @if($infoPaciente->foto)
                        <img class="pr-patient-photo"
                             src="{{ url('storage/archivos/'.$infoPaciente->foto) }}"
                             alt="Foto paciente">
                    @else
                        <img class="pr-patient-photo"
                             src="{{ asset('images/foto-default.png') }}"
                             alt="Sin foto">
                    @endif

                    <div>
                        <p class="pr-patient-name">{{ $nombreCompleto }}</p>
                        <div class="pr-chips">
                            <span class="pr-chip pr-chip-blue">
                                <i class="fas fa-birthday-cake" style="font-size:10px"></i>
                                Edad: {{ $edad }}
                            </span>
                            <span class="pr-chip pr-chip-green">
                                <i class="fas fa-user-md" style="font-size:10px"></i>
                                {{ $nombreDoctor }}
                            </span>
                            <span class="pr-chip pr-chip-amber">
                                <i class="fas fa-calendar-alt" style="font-size:10px"></i>
                                Receta: {{ $fechaReceta }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Aviso si hay filas excedidas (comparación numérica correcta) ── --}}
        @if($arrayNombreMedicamento->contains(fn($i) => $i->cantidadRetirar > $i->cantidadActual))
            <div class="pr-alert-notice">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Uno o más medicamentos <strong>no tienen stock suficiente</strong> (marcados en rojo). Verifique antes de procesar.</span>
            </div>
        @endif

        {{-- ── Tabla de medicamentos ── --}}
        <div class="pr-card">
            <div class="pr-card-head">
                <div class="pr-card-icon pr-card-icon-green"><i class="fas fa-pills"></i></div>
                <span class="pr-card-title">Medicamentos a despachar</span>
                <span style="
                    margin-left:auto;
                    background:var(--pr-green-lt); color:var(--pr-green);
                    padding:2px 10px; border-radius:20px;
                    font-size:11px; font-weight:700;">
                    {{ count($arrayNombreMedicamento) }}
                    {{ count($arrayNombreMedicamento) == 1 ? 'ítem' : 'ítems' }}
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table class="pr-table">
                    <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th style="width:120px;">Cant. a retirar</th>
                        <th style="width:110px;">Stock bodega</th>
                        <th style="width:120px;">Vencimiento</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($arrayNombreMedicamento as $item)
                        @php $excedido = $item->cantidadRetirar > $item->cantidadActual; @endphp
                        <tr class="{{ $excedido ? 'pr-row-alert' : '' }}">
                            <td>
                                <span class="pr-row-num {{ $excedido ? 'pr-row-num-alert' : '' }}">
                                    {{ $item->contador }}
                                </span>
                            </td>
                            <td>
                                <input disabled value="{{ $item->nombreFormat }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <input disabled value="{{ $item->lote }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <input disabled value="{{ $item->cantidadRetirar }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                            <td>
                                <span class="pr-stock {{ $excedido ? 'pr-stock-low' : 'pr-stock-ok' }}">
                                    <i class="fas fa-boxes" style="font-size:10px"></i>
                                    {{ $item->cantidadActual }}
                                    @if($excedido)
                                        <i class="fas fa-exclamation-triangle" style="font-size:10px"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <input disabled value="{{ $item->fechaVencimiento }}"
                                       class="form-control form-control-sm" type="text">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:#adb5bd;">
                                <i class="fas fa-prescription-bottle-alt"
                                   style="font-size:1.5rem; display:block; margin-bottom:.5rem;"></i>
                                Sin medicamentos en esta receta.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Notas --}}
            <div style="padding:1.1rem 1.2rem; border-top:1px solid var(--pr-border);">
                <label class="pr-label">Notas adicionales</label>
                <textarea id="text-notas" rows="3" class="form-control"
                          style="border-radius:7px; font-size:13px;"
                          placeholder="Observaciones del despacho…"></textarea>
            </div>

            {{-- Footer sticky --}}
            <div class="pr-footer">
                <button type="button" class="pr-btn pr-btn-back" onclick="volverAtras()">
                    Cancelar
                </button>
                <button type="button" class="pr-btn pr-btn-success" onclick="preguntarGuardarSalida()">
                    <i class="fas fa-check-circle"></i> Verificar y procesar salida
                </button>
            </div>
        </div>

    </div>{{-- /pr-wrap --}}


    {{-- ══ MODAL: Cantidad superada (fallback) ══ --}}
    <div class="modal fade" id="modalCantiSuperada">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header" style="background:#f8f9fa; border-bottom:1px solid #dee2e6;">
                    <h5 class="modal-title" style="font-weight:700; font-size:15px;">
                        <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Cantidad superada
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="label-cantidad-excedida" style="font-size:13px;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
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

    <script>

        function volverAtras() {
            window.location.href = "{{ url('/admin/salida/medicamento/porreceta/index') }}";
        }

        function recargarVista() {
            location.reload();
        }

        function preguntarGuardarSalida() {
            Swal.fire({
                title: '¿Procesar salida?',
                text: 'Se registrará el despacho de los medicamentos.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a8a5a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then(function (result) {
                if (result.value) {
                    verificarSalida();
                }
            });
        }

        function verificarSalida() {
            openLoading();

            var idreceta = {{ $idreceta }};
            var notas    = document.getElementById('text-notas').value;

            var fd = new FormData();
            fd.append('idreceta', idreceta);
            fd.append('notas',    notas);

            axios.post(urlAdmin + '/admin/receta/procesar/guardarsalida', fd)
                .then(function (response) {
                    closeLoading();

                    // Receta cambió de estado externamente
                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Estado modificado',
                            text: 'El estado de la receta cambió. Revise de nuevo.',
                            type: 'warning',
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Aceptar',
                            allowOutsideClick: false
                        }).then(function (result) {
                            if (result.value) { volverAtras(); }
                        });

                        // Stock insuficiente
                    } else if (response.data.success === 2) {
                        Swal.fire({
                            title: 'Stock insuficiente',
                            type: 'error',
                            allowOutsideClick: false,
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Recargar',
                            html:
                                '<div style="text-align:left; font-size:13px; line-height:1.7;">' +
                                '<strong>Medicamento:</strong> ' + response.data.nombre + '<br>' +
                                '<strong>Lote:</strong> ' + response.data.lote + '<br>' +
                                '<strong>Vencimiento:</strong> ' + response.data.fechavencimiento + '<br>' +
                                '<strong>Stock actual:</strong> ' + response.data.cantidadhay + '<br>' +
                                '<strong>Solicitado:</strong> ' + response.data.cantidadsalida +
                                '</div>'
                        }).then(function (result) {
                            if (result.value) { recargarVista(); }
                        });

                        // Procesado correctamente
                    } else if (response.data.success === 3) {
                        Swal.fire({
                            title: 'Receta procesada',
                            type: 'success',
                            confirmButtonColor: '#1a8a5a',
                            confirmButtonText: 'Aceptar',
                            allowOutsideClick: false
                        }).then(function (result) {
                            if (result.value) { volverAtras(); }
                        });

                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () {
                    closeLoading();
                    toastr.error('Error al registrar');
                });
        }

    </script>
@endsection
