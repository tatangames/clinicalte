@extends('adminlte::page')

@section('title', 'Documentos y Recetas')

@section('content_header')
    <div class="d-flex align-items-center">
        <button type="button"
                onclick="vistaAtras()"
                class="btn btn-sm btn-warning font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Atrás
        </button>

        <h1 class="mb-0 ml-3">Documentos y Recetas</h1>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown">
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

    <style>
        /* ── Sistema base compartido ── */
        .seccion-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
        }
        .seccion-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .seccion-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #e8f4fd;
            color: #1a7abf;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .seccion-titulo {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        /* ── Ficha del paciente ── */
        .ficha-paciente {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .ficha-foto {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #e9ecef;
            flex-shrink: 0;
        }
        .ficha-foto-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 28px;
            border: 2px dashed #ced4da;
            flex-shrink: 0;
        }
        .ficha-nombre {
            font-size: 15px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 6px;
        }
        .ficha-badge {
            display: inline-block;
            background: #e8f4fd;
            color: #1a7abf;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 500;
            margin: 2px 4px 2px 0;
        }

        /* ── Tabs ── */
        .tabs-nav {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s;
            white-space: nowrap;
        }
        .tab-btn img {
            width: 18px;
            height: 18px;
        }
        .tab-btn:hover { color: #1a7abf; }
        .tab-btn.active {
            color: #1a7abf;
            border-bottom-color: #1a7abf;
        }
        .tab-pane-custom { display: none; }
        .tab-pane-custom.active { display: block; }
    </style>

    <div class="py-2">

        {{-- Ficha del paciente --}}
        <div class="seccion-card">
            <div class="seccion-header">
                <div class="seccion-icon"><i class="fas fa-id-card"></i></div>
                <p class="seccion-titulo">Ficha clínica</p>
            </div>
            <div class="ficha-paciente">
                @if($infoPaciente->foto)
                    <img src="{{ url('storage/archivos/' . $infoPaciente->foto) }}"
                         alt="Foto Paciente" class="ficha-foto">
                @else
                    <div class="ficha-foto-placeholder"><i class="fas fa-user"></i></div>
                @endif
                <div>
                    <p class="ficha-nombre">{{ $nombreCompleto }}</p>
                    <span class="ficha-badge"><i class="fas fa-birthday-cake mr-1"></i>{{ $miFecha }}</span>
                    <span class="ficha-badge"><i class="fas fa-folder mr-1"></i>Exp. #{{ $infoPaciente->numero_expediente }}</span>
                    <span class="ficha-badge"><i class="fas fa-stethoscope mr-1"></i>{{ $totalConsulta }} consulta{{ $totalConsulta != 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="seccion-card">

            <div class="tabs-nav" id="tabsNav">
                <button class="tab-btn active" data-tab="tab-antecedentes">
                    <img src="{{ asset('images/personacard.png') }}" alt=""> Antecedentes
                </button>
                <button class="tab-btn" data-tab="tab-antrop">
                    <img src="{{ asset('images/corazonrojo.png') }}" alt=""> SV + Antrop.
                </button>
                <button class="tab-btn" data-tab="tab-recetas">
                    <img src="{{ asset('images/medicamento.png') }}" alt=""> Recetas
                </button>
                <button class="tab-btn" data-tab="tab-cuadro">
                    <img src="{{ asset('images/prescripcion.png') }}" alt=""> Cuadro clínico
                </button>
            </div>

            @can('ver.tabla.antecedentes')
                <div class="tab-pane-custom active" id="tab-antecedentes">
                    <div id="tablaAntecedentes"></div>
                </div>
            @endcan

            @can('ver.tabla.antropometria')
                <div class="tab-pane-custom" id="tab-antrop">
                    <div id="tablaAntropSv"></div>
                </div>
            @endcan

            @can('ver.tabla.recetas')
                <div class="tab-pane-custom" id="tab-recetas">
                    <div id="tablaRecetas"></div>
                </div>
            @endcan

            @can('ver.tabla.historialclinico')
                <div class="tab-pane-custom" id="tab-cuadro">
                    <div id="tablaCuadroClinico"></div>
                </div>
            @endcan

        </div>

    </div>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/datatables-config.js') }}"></script>

    <script>
        $(document).ready(function () {

            let idPaciente = {{ $idpaciente }};

            // ── Cargar bloques AJAX ──
            const bloques = [
                { div: 'tablaAntecedentes',   ruta: '/admin/documentoreceta/bloque/antecedentes' },
                { div: 'tablaAntropSv',        ruta: '/admin/documentoreceta/bloque/antropometriasv' },
                { div: 'tablaRecetas',         ruta: '/admin/documentoreceta/bloque/recetas' },
                { div: 'tablaCuadroClinico',   ruta: '/admin/documentoreceta/bloque/cuadroclinico' },
            ];

            bloques.forEach(({ div, ruta }) => {
                const el = document.getElementById(div);
                if (el) $('#' + div).load("{{ URL::to('/') }}" + ruta + '/' + idPaciente);
            });

            // ── Lógica de tabs ──
            document.querySelectorAll('#tabsNav .tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#tabsNav .tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-pane-custom').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.dataset.tab)?.classList.add('active');
                });
            });
        });

        function vistaAtras() {
            window.location.href = "{{ url('/admin/buscarexpediente/index') }}";
        }
    </script>
@endsection
