@extends('adminlte::page')

@section('title', 'Catálogo')

@section('content_header')
    <h1>Catálogo</h1>
@stop


{{-- Activa plugins que necesitas --}}
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />


    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline">
            {{ Auth::guard('admin')->user()->nombre }}
        </span>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('admin.perfil') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i>
                Editar Perfil
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

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button" onclick="modalAgregar()" class="btn btn-dark btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Nuevo Registro
                </button>
            </div>

        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
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
        <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

        <script>
            $(function () {
                const ruta = "{{ url('/admin/catalogo/tabla/index') }}";

                function initDataTable() {
                    // Si ya hay instancia, destrúyela antes de re-crear
                    if ($.fn.DataTable.isDataTable('#tabla')) {
                        $('#tabla').DataTable().destroy();
                    }

                    // Inicializa
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
                            sProcessing: "Procesando...",
                            sLengthMenu: "Mostrar _MENU_ registros",
                            sZeroRecords: "No se encontraron resultados",
                            sEmptyTable: "Ningún dato disponible en esta tabla",
                            sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                            sInfoEmpty: "Mostrando 0 a 0 de 0 registros",
                            sInfoFiltered: "(filtrado de _MAX_ registros)",
                            sSearch: "Buscar:",
                            oPaginate: {sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior"},
                            oAria: {sSortAscending: ": Orden ascendente", sSortDescending: ": Orden descendente"}
                        },
                        dom:
                            "<'row align-items-center'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-right'f>>" +
                            "tr" +
                            "<'row align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                    });

                    // Estilitos
                    $('#tabla_length select').addClass('form-control form-control-sm');
                    $('#tabla_filter input').addClass('form-control form-control-sm').css('display', 'inline-block');
                }

                function cargarTabla() {
                    $('#tablaDatatable').load(ruta, function () {
                        // AQUI debe existir exactamente un <table id="tabla"> en la parcial
                        initDataTable();
                    });
                }

                // Primera carga
                cargarTabla();

                // Exponer recarga para tus flujos (crear/editar)
                window.recargar = function () {
                    cargarTabla();
                };
            });
        </script>

    <script>

        function infoEditar(idarticulo){
            window.location.href="{{ url('/admin/catalogo/individual/vista/editar') }}/" + idarticulo;
        }


    </script>

@endsection
