@extends('adminlte::page')

@section('title', 'Historial Entradas')

@section('content_header')
    <h1>Historial Entradas</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

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
    <div id="divcontenedor">

        {{-- FILTROS --}}
        <section class="content">
            <div class="container-fluid">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtros</h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label style="color: #686868">Fecha Desde:</label>
                                    <input type="date" id="filtro-fecha-desde" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label style="color: #686868">Fecha Hasta:</label>
                                    <input type="date" id="filtro-fecha-hasta" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label style="color: #686868">Número de Factura:</label>
                                    <input type="text" id="filtro-factura" class="form-control" placeholder="Ej: 001-001-0001" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-3 d-flex align-items-end" style="gap: 8px">
                                <button type="button" class="btn btn-primary" onclick="filtrar()">
                                    <i class="fas fa-search mr-1"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-default" onclick="limpiarFiltros()">
                                    <i class="fas fa-times mr-1"></i> Limpiar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- TABLA --}}
        <section class="content" style="margin-top: -10px">
            <div class="container-fluid">
                <div class="card card-blue">
                    <div class="card-header">
                        <h3 class="card-title">Listado</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDatatable">
                                    {{-- La tabla carga solo al presionar Filtrar --}}
                                    <p class="text-muted text-center mt-3">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Usa los filtros y presiona <strong>Filtrar</strong> para ver los resultados.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#tabla')) {
                $('#tabla').DataTable().destroy();
            }

            $('#tabla').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                pagingType: "full_numbers",
                lengthMenu: [[100, 150, -1], [100, 150, "Todo"]],
                language: {
                    sProcessing:     "Procesando...",
                    sLengthMenu:     "Mostrar _MENU_ registros",
                    sZeroRecords:    "No se encontraron resultados",
                    sEmptyTable:     "Ningún dato disponible en esta tabla",
                    sInfo:           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    sInfoEmpty:      "Mostrando 0 a 0 de 0 registros",
                    sInfoFiltered:   "(filtrado de _MAX_ registros)",
                    sSearch:         "Buscar:",
                    oPaginate: {
                        sFirst:    "Primero",
                        sLast:     "Último",
                        sNext:     "Siguiente",
                        sPrevious: "Anterior"
                    },
                    oAria: {
                        sSortAscending:  ": Orden ascendente",
                        sSortDescending: ": Orden descendente"
                    }
                },
                dom:
                    "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                    "tr" +
                    "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });

            $('#tabla_length select').addClass('form-control form-control-sm');
            $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
        }

        function filtrar() {
            var fechaDesde = document.getElementById('filtro-fecha-desde').value;
            var fechaHasta = document.getElementById('filtro-fecha-hasta').value;
            var factura    = document.getElementById('filtro-factura').value.trim();

            // Validar rango: si pone solo una fecha, avisar
            if ((fechaDesde && !fechaHasta) || (!fechaDesde && fechaHasta)) {
                toastr.warning('Si filtra por fecha debe ingresar Fecha Desde y Fecha Hasta.');
                return;
            }

            if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
                toastr.warning('La Fecha Desde no puede ser mayor a la Fecha Hasta.');
                return;
            }

            var params = new URLSearchParams();
            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);
            if (factura)    params.append('factura', factura);

            var ruta = "{{ url('/admin/historialentradas/tabla/index') }}" + '?' + params.toString();

            $('#tablaDatatable').load(ruta, function () {
                initDataTable();
            });
        }

        function limpiarFiltros() {
            document.getElementById('filtro-fecha-desde').value = '';
            document.getElementById('filtro-fecha-hasta').value = '';
            document.getElementById('filtro-factura').value     = '';
        }

        // Enter en el campo factura dispara el filtro
        document.getElementById('filtro-factura').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') filtrar();
        });

        function infoEditar(identrada){
            window.location.href="{{ url('/admin/vista/editar/info/entrada') }}/" + identrada;
        }

    </script>
@endsection
