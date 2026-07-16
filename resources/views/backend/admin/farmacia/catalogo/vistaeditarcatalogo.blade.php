@extends('adminlte::page')

@section('title', 'Editar Catálogo')

@section('content_header')
    <h1>Editar Catálogo</h1>
@stop

{{-- Activa plugins que necesitas --}}
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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
            <div class="container-fluid">
                <button type="button" onclick="volverAtras()" style="color: white" class="btn btn-warning btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    Atras
                </button>
            </div>
        </section>

        <section class="content" style="margin-top: 20px">
            <div class="container-fluid">
                <div class="card card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">ARTICULO: {{ $infoArticulo->nombre }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <form id="formulario-articulo">

                                    <section class="content">
                                        <div class="container-fluid">
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-10" style="color: #686868">Línea: </label>
                                                        <div class="col-md-10">
                                                            <select class="form-control select2-farma" id="select-linea" onchange="verificarLinea()">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayLinea as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticulo->id_linea == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-10" style="color: #686868">sub Línea: </label>
                                                        <div class="col-md-10">
                                                            <select class="form-control select2-farma" id="select-sublinea">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arraySubLinea as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticulo->id_sublinea == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </section>

                                    <section style="margin-top: 25px">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-10" style="color: #686868">Código de Artículo: </label>
                                                    <div class="col-md-10">
                                                        <input type="text" maxlength="300" autocomplete="off"
                                                               class="form-control" id="codigo-articulo" value="{{ $infoArticulo->codigo_articulo }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-10" style="color: #686868">Nombre/Descripción: </label>
                                                    <div class="col-md-10">
                                                        <input type="text" maxlength="300" value="{{ $infoArticulo->nombre }}" autocomplete="off" class="form-control" id="nombre-descripcion">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- BLOQUE PARA LINEA MEDICAMENTO -->
                                    @if($tieneExtras == 1)

                                        <section style="margin-top: 25px">
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-10" style="color: #686868">Nombre Generico: </label>
                                                        <div class="col-md-10">
                                                            <input type="text" maxlength="300" autocomplete="off" value="{{ $nombreGenerico }}" class="form-control" id="nombre-generico">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="color: #686868">Envase: </label>
                                                        <div class="col-md-10 input-group">
                                                            <select class="form-control select2-farma" id="select-envase">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayEnvase as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticuloMedi->id_con_far_envase == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(1)">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section style="margin-top: 15px">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="color: #686868">Forma Farmaceutica: </label>
                                                        <div class="col-md-10 input-group">
                                                            <select class="form-control select2-farma" id="select-formafarmaceutica">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayFormaFarmaceutica as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticuloMedi->id_con_far_forma == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(2)">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="color: #686868">Concentración: </label>
                                                        <div class="col-md-10 input-group">
                                                            <select class="form-control select2-farma" id="select-concentracion">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayConcentracion as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticuloMedi->id_con_far_concentracion == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(3)">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <section style="margin-top: 15px">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label style="color: #686868">Contenido: </label>
                                                        <div class="col-md-10 input-group">
                                                            <select class="form-control select2-farma" id="select-contenido">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayContenido as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticuloMedi->id_con_far_contenido == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(4)">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-10" style="color: #686868">Via Administración: </label>
                                                        <div class="col-md-10 input-group">
                                                            <select class="form-control select2-farma" id="select-viaadministracion">
                                                                <option value="">Seleccionar Opción</option>
                                                                @foreach($arrayAdministracion as $item)
                                                                    <option value="{{ $item->id }}" {{ $infoArticuloMedi->id_con_far_administra == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(5)">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                    @else
                                        <!-- BLOQUE OCULTO PARA LINEA MEDICAMENTO -->
                                        <div id="bloque-medicamentos" style="display: none">

                                            <section style="margin-top: 25px">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-10" style="color: #686868">Nombre Generico: </label>
                                                            <div class="col-md-10">
                                                                <input type="text" maxlength="300" autocomplete="off" class="form-control" id="nombre-generico">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label style="color: #686868">Envase: </label>
                                                            <div class="col-md-10 input-group">
                                                                <select class="form-control select2-farma-lazy" id="select-envase">
                                                                    <option value="">Seleccionar Opción</option>
                                                                    @foreach($arrayEnvase as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(1)">
                                                                    <i class="fas fa-plus text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <section style="margin-top: 15px">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label style="color: #686868">Forma Farmaceutica: </label>
                                                            <div class="col-md-10 input-group">
                                                                <select class="form-control select2-farma-lazy" id="select-formafarmaceutica">
                                                                    <option value="">Seleccionar Opción</option>
                                                                    @foreach($arrayFormaFarmaceutica as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(2)">
                                                                    <i class="fas fa-plus text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label style="color: #686868">Concentración: </label>
                                                            <div class="col-md-10 input-group">
                                                                <select class="form-control select2-farma-lazy" id="select-concentracion">
                                                                    <option value="">Seleccionar Opción</option>
                                                                    @foreach($arrayConcentracion as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(3)">
                                                                    <i class="fas fa-plus text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <section style="margin-top: 15px">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label style="color: #686868">Contenido: </label>
                                                            <div class="col-md-10 input-group">
                                                                <select class="form-control select2-farma-lazy" id="select-contenido">
                                                                    <option value="">Seleccionar Opción</option>
                                                                    @foreach($arrayContenido as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(4)">
                                                                    <i class="fas fa-plus text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="col-md-10" style="color: #686868">Via Administración: </label>
                                                            <div class="col-md-10 input-group">
                                                                <select class="form-control select2-farma-lazy" id="select-viaadministracion">
                                                                    <option value="">Seleccionar Opción</option>
                                                                    @foreach($arrayAdministracion as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-warning" onclick="verModalExtraInformacion(5)">
                                                                    <i class="fas fa-plus text-white"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                        </div>
                                        <!-- END BLOQUE PARA LINEA MEDICAMENTO -->
                                    @endif
                                    <!-- END BLOQUE PARA LINEA MEDICAMENTO -->

                                    <section style="margin-top: 15px">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-10" style="color: #686868">Existencia Mínima (para ser notificado): </label>
                                                    <div class="col-md-10">
                                                        <input type="number" onkeypress="return valida_numero(event);" value="{{ $infoArticulo->existencia_minima }}" autocomplete="off" class="form-control" id="existencia-minima">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                </form>

                                <br>
                                <hr>

                                <section class="content">
                                    <div class="container-fluid">
                                        <div style="margin-right: 30px">
                                            <button type="button" style="float: right" class="btn btn-success" onclick="guardarInformacion();">
                                                <i class="fas fa-save mr-1"></i> Actualizar Artículo
                                            </button>
                                        </div>
                                    </div>
                                </section>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="modal fade" id="modalExtraInformacion">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="txtTituloExtra"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formulario-extra-datos">
                            <div class="card-body">
                                <div>
                                    <input id="idtipo-extra-info" type="hidden">
                                </div>
                                <div class="form-group" style="margin-top: 20px">
                                    <div class="box-header with-border">
                                        <label>Nombre</label>
                                    </div>
                                    <input maxlength="300" id="extranombre-via-nuevo" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="guardarExtraInformacion()">
                            <i class="fas fa-save mr-1"></i> Guardar
                        </button>
                    </div>
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

        // Configuración base reutilizable para Select2
        var select2Config = {
            theme: "bootstrap-5",
            language: {
                noResults: function () {
                    return "Búsqueda no encontrada";
                }
            }
        };

        // Inicializa Select2 en un selector dado
        function initSelect2(selector) {
            $(selector).select2(select2Config);
        }

        // Reinicia Select2 en selects del bloque lazy (medicamentos ocultos)
        function initSelect2Lazy() {
            $('.select2-farma-lazy').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2(select2Config);
                }
            });
        }

        $(document).ready(function () {

            // Inicializar Select2 en selects visibles siempre
            $('.select2-farma').each(function () {
                initSelect2(this);
            });

            let tieneExtra = {{ $tieneExtras }};

            // Si ya hay extras, los selects de farmacia ya están en el DOM visible
            // y tienen clase select2-farma, así que ya se inicializaron arriba.
            // Si no hay extras, el bloque está oculto; se inicia cuando se muestra.

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function verificarLinea() {
            var id = document.getElementById('select-linea').value;
            var bloque = document.getElementById('bloque-medicamentos');

            if (bloque) {
                if (id == 1) {
                    bloque.style.display = "block";
                    // Inicializar Select2 en los selects del bloque lazy al mostrarse
                    initSelect2Lazy();
                } else {
                    bloque.style.display = "none";
                }
            }
        }

        function valida_numero(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla == 8) return true;
            patron = /[0-9.]/;
            tecla_final = String.fromCharCode(tecla);
            return patron.test(tecla_final);
        }

        function guardarInformacion() {

            var idLinea           = document.getElementById('select-linea').value;
            var idSubLinea        = document.getElementById('select-sublinea').value;
            var codigoArticulo    = document.getElementById('codigo-articulo').value;
            var nombre            = document.getElementById('nombre-descripcion').value;
            var existenciaMinima  = document.getElementById('existencia-minima').value;

            if (idLinea === '') {
                toastr.error('Línea es requerido');
                return;
            }

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if (existenciaMinima === '') {
                existenciaMinima = 0;
            } else {
                if (!existenciaMinima.match(reglaNumeroEntero)) {
                    toastr.error('Existencia Mínima debe ser un número entero');
                    return;
                }
                if (existenciaMinima < 0) {
                    toastr.error('Existencia Mínima no debe tener negativos');
                    return;
                }
                if (existenciaMinima > 9000000) {
                    toastr.error('Existencia Mínima máximo debe ser 9 millones');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();

            let hayExtras  = {{ $tieneExtras }};
            let idArticulo = {{ $idarticulo }};

            var boolDatos = 0;

            if (hayExtras === 1) {
                boolDatos = 1;
            } else if (idLinea === '1') {
                boolDatos = 1;
            }

            if (boolDatos === 1) {
                let idEnvase        = document.getElementById('select-envase').value;
                let idFormaFarmace  = document.getElementById('select-formafarmaceutica').value;
                let idConcentracion = document.getElementById('select-concentracion').value;
                let idContenido     = document.getElementById('select-contenido').value;
                let idAdministracion = document.getElementById('select-viaadministracion').value;
                let nombreGenerico  = document.getElementById('nombre-generico').value;

                formData.append('idEnvase',        idEnvase);
                formData.append('idFormaFarma',    idFormaFarmace);
                formData.append('idConcentracion', idConcentracion);
                formData.append('idContenido',     idContenido);
                formData.append('idAdministracion',idAdministracion);
                formData.append('nombreGenerico',  nombreGenerico);
            }

            formData.append('idArticulo',    idArticulo);
            formData.append('idLinea',       idLinea);
            formData.append('idSubLinea',    idSubLinea);
            formData.append('codigoArticulo',codigoArticulo);
            formData.append('nombre',        nombre);
            formData.append('existencia',    existenciaMinima);
            formData.append('infoextra',     boolDatos);

            axios.post(urlAdmin + '/admin/catalogo/individual/actualizar', formData, {})
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.success('Actualizado');
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function volverAtras() {
            window.location.href = "{{ url('/admin/catalogo/index') }}";
        }

        function verModalExtraInformacion(idtipo) {

            document.getElementById("formulario-extra-datos").reset();

            var titulos = {
                1: "Registrar Tipo: Envase",
                2: "Registrar Tipo: Forma Farmaceutica",
                3: "Registrar Tipo: Concentración",
                4: "Registrar Tipo: Contenido",
                5: "Registrar Tipo: Via Administración"
            };

            document.getElementById("txtTituloExtra").innerHTML = titulos[idtipo] || "";
            $('#idtipo-extra-info').val(idtipo);
            $('#modalExtraInformacion').modal('show');
        }

        function guardarExtraInformacion() {

            var idtipo = document.getElementById("idtipo-extra-info").value;
            var nombre = document.getElementById("extranombre-via-nuevo").value;

            if (nombre === '') {
                toastr.error('Nombre es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('idtipo', idtipo);
            formData.append('nombre', nombre);

            // Mapa de idtipo → id del select a refrescar
            var selectMap = {
                1: 'select-envase',
                2: 'select-formafarmaceutica',
                3: 'select-concentracion',
                4: 'select-contenido',
                5: 'select-viaadministracion'
            };

            axios.post(urlAdmin + '/admin/guardar/contenidofarma/get/listado', formData, {})
                .then((response) => {
                    closeLoading();

                    var successCode = response.data.success;

                    if (successCode >= 1 && successCode <= 5) {

                        toastr.success('Guardado correctamente');

                        var selectId = selectMap[successCode];
                        var $select  = $('#' + selectId);

                        // Destruir Select2, vaciar y reconstruir opciones
                        $select.select2('destroy');
                        $select.empty().append('<option value="">Seleccionar Opción</option>');

                        $.each(response.data.lista, function (key, val) {
                            $select.append('<option value="' + val.id + '">' + val.nombre + '</option>');
                        });

                        // Reinicializar Select2
                        $select.select2(select2Config);
                        $select.trigger('change');

                        $('#modalExtraInformacion').modal('hide');

                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }
    </script>
@endsection
