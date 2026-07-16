@extends('adminlte::page')

@section('title', 'Registrar Artículo')

@section('content_header')
    <h1>Registrar Artículo</h1>
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

        <section class="content">
            <div class="container-fluid">
                <div class="card card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">AGREGAR ARTÍCULO</h3>
                    </div>
                    <div class="card-body">

                        <form id="formulario-articulo">

                            {{-- FILA 1: Línea / SubLínea --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Línea <span style="color: red">*</span></label>
                                        <select class="form-control" id="select-linea" onchange="verificarLinea()">
                                            <option value="">Seleccionar Opción</option>
                                            @foreach($arrayLinea as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Sub Línea</label>
                                        <select class="form-control" id="select-sublinea">
                                            <option value="">Seleccionar Opción</option>
                                            @foreach($arraySubLinea as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- FILA 1-1: Código Presupuestario --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Código Presupuestario <span style="color: red">*</span></label>
                                        <select class="form-control" id="select-obj">
                                            @foreach($arrayObjEspecifico as $item)
                                                <option value="{{ $item->id }}">{{ $item->codigo}} - {{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- FILA 2: Código / Nombre --}}
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Código de Artículo</label>
                                        <input type="text" maxlength="300" autocomplete="off"
                                               class="form-control" id="codigo-articulo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Nombre / Descripción <span style="color: red">*</span></label>
                                        <input type="text" maxlength="300" autocomplete="off"
                                               class="form-control" id="nombre-descripcion">
                                    </div>
                                </div>
                            </div>

                            {{-- BLOQUE MEDICAMENTOS (visible solo si Línea = Medicamento) --}}
                            <div id="bloque-medicamentos" style="display:none">
                                <hr>

                                {{-- Nombre Genérico / Envase --}}
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Nombre Genérico</label>
                                            <input type="text" maxlength="300" autocomplete="off"
                                                   class="form-control" id="nombre-generico">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Envase</label>
                                            @include('backend.admin.farmacia.registrararticulo.select_extra', [
                                                'selectId'   => 'select-envase',
                                                'items'      => $arrayEnvase,
                                                'tipoModal'  => 1,
                                            ])
                                        </div>
                                    </div>
                                </div>

                                {{-- Forma Farmacéutica / Concentración --}}
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Forma Farmacéutica</label>
                                            @include('backend.admin.farmacia.registrararticulo.select_extra', [
                                                 'selectId'   => 'select-formafarmaceutica',
                                                 'items'      => $arrayFormaFarmaceutica,
                                                 'tipoModal'  => 2,
                                             ])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Concentración</label>
                                            @include('backend.admin.farmacia.registrararticulo.select_extra', [
                                                'selectId'   => 'select-concentracion',
                                                'items'      => $arrayConcentracion,
                                                'tipoModal'  => 3,
                                            ])
                                        </div>
                                    </div>
                                </div>

                                {{-- Contenido / Vía de Administración --}}
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Contenido</label>
                                            @include('backend.admin.farmacia.registrararticulo.select_extra', [
                                                 'selectId'   => 'select-contenido',
                                                 'items'      => $arrayContenido,
                                                 'tipoModal'  => 4,
                                             ])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label text-muted">Vía de Administración</label>
                                            @include('backend.admin.farmacia.registrararticulo.select_extra', [
                                                'selectId'   => 'select-viaadministracion',
                                                'items'      => $arrayAdministracion,
                                                'tipoModal'  => 5,
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- /BLOQUE MEDICAMENTOS --}}

                            {{-- Existencia Mínima --}}
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-muted">Existencia Mínima (para ser notificado)</label>
                                        <input type="number" min="0" max="9000000"
                                               autocomplete="off" class="form-control" id="existencia-minima">
                                    </div>
                                </div>
                            </div>

                        </form>

                        <hr>
                        <div class="text-right">
                            <button type="button" class="btn btn-success" onclick="registrar()">
                                <i class="fas fa-save mr-1"></i> Registrar Artículo
                            </button>
                        </div>

                    </div>{{-- /card-body --}}
                </div>
            </div>
        </section>

        {{-- ========== MODAL EXTRA INFORMACIÓN ========== --}}
        <div class="modal fade" id="modalExtraInformacion" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="txtTituloExtra"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="idtipo-extra-info" type="hidden">
                        <div class="form-group mt-2">
                            <label>Nombre</label>
                            <input maxlength="300" id="extranombre-via-nuevo" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="guardarExtraInformacion()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        // ─── Configuración de selects con Select2 ───────────────────────────────────
        const SELECT2_IDS = [
            'select-linea', 'select-sublinea', 'select-envase',
            'select-formafarmaceutica', 'select-concentracion',
            'select-contenido', 'select-viaadministracion',
            'select-obj'
        ];

        const SELECT2_CONFIG = {
            theme: 'bootstrap-5',
            language: { noResults: () => 'Búsqueda no encontrada' }
        };

        // Map: tipo → { selectId, titulo }
        const TIPO_CONFIG = {
            1: { selectId: 'select-envase',            titulo: 'Registrar Tipo: Envase' },
            2: { selectId: 'select-formafarmaceutica', titulo: 'Registrar Tipo: Forma Farmacéutica' },
            3: { selectId: 'select-concentracion',     titulo: 'Registrar Tipo: Concentración' },
            4: { selectId: 'select-contenido',          titulo: 'Registrar Tipo: Contenido' },
            5: { selectId: 'select-viaadministracion', titulo: 'Registrar Tipo: Vía de Administración' },
        };

        $(document).ready(function () {
            SELECT2_IDS.forEach(id => $(`#${id}`).select2(SELECT2_CONFIG));
        });

        // ─── Helpers ────────────────────────────────────────────────────────────────
        function resetSelect(id) {
            $(`#${id}`).val('').trigger('change');
        }

        function repoblarSelect(selectId, lista) {
            const $sel = $(`#${selectId}`);
            $sel.empty().append('<option value="">Seleccionar Opción</option>');
            lista.forEach(item => $sel.append(`<option value="${item.id}">${item.nombre}</option>`));
            $sel.trigger('change');
        }

        // ─── Mostrar/ocultar bloque medicamentos ────────────────────────────────────
        function verificarLinea() {
            const id = $('#select-linea').val();
            $('#bloque-medicamentos').toggle(id == 1);
        }

        // ─── Modal extra información ─────────────────────────────────────────────────
        function verModalExtraInformacion(idtipo) {
            const cfg = TIPO_CONFIG[idtipo];
            if (!cfg) return;

            $('#extranombre-via-nuevo').val('');
            $('#idtipo-extra-info').val(idtipo);
            $('#txtTituloExtra').text(cfg.titulo);
            $('#modalExtraInformacion').modal('show');
        }

        function guardarExtraInformacion() {
            const idtipo = $('#idtipo-extra-info').val();
            const nombre = $('#extranombre-via-nuevo').val().trim();

            if (!nombre) {
                toastr.error('Nombre es requerido');
                return;
            }

            const formData = new FormData();
            formData.append('idtipo', idtipo);
            formData.append('nombre', nombre);

            openLoading();
            axios.post(urlAdmin + '/admin/guardar/contenidofarma/get/listado', formData)
                .then(({ data }) => {
                    closeLoading();
                    const cfg = TIPO_CONFIG[data.success];
                    if (cfg && data.lista) {
                        repoblarSelect(cfg.selectId, data.lista);
                        toastr.success('Guardado correctamente');
                        $('#modalExtraInformacion').modal('hide');
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(() => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        // ─── Registrar artículo ──────────────────────────────────────────────────────
        function registrar() {
            const idLinea         = $('#select-linea').val();
            const idSubLinea      = $('#select-sublinea').val();
            const codigoArticulo  = $('#codigo-articulo').val().trim();
            const nombre          = $('#nombre-descripcion').val().trim();
            let   existenciaMin   = $('#existencia-minima').val().trim();
            const nombreGenerico  = $('#nombre-generico').val().trim();
            const idEnvase        = $('#select-envase').val();
            const idFormaFarmace  = $('#select-formafarmaceutica').val();
            const idConcentracion = $('#select-concentracion').val();
            const idContenido     = $('#select-contenido').val();
            const idAdministracion= $('#select-viaadministracion').val();
            const idCodigo= $('#select-obj').val();

            // Validaciones
            if (!idLinea) {
                toastr.error('Línea es requerida');
                return;
            }
            if (!nombre) {
                toastr.error('Nombre es requerido');
                return;
            }
            if (!idCodigo) {
                toastr.error('Código Presupuestario es requerida');
                return;
            }

            if (existenciaMin === '') {
                existenciaMin = 0;
            } else {
                const val = parseInt(existenciaMin, 10);
                if (isNaN(val) || val < 0) {
                    toastr.error('Existencia Mínima debe ser un número positivo');
                    return;
                }
                if (val > 9000000) {
                    toastr.error('Existencia Mínima no puede superar 9 millones');
                    return;
                }
                existenciaMin = val;
            }

            const formData = new FormData();
            formData.append('idLinea',         idLinea);
            formData.append('idSubLinea',      idSubLinea);
            formData.append('codigoArticulo',  codigoArticulo);
            formData.append('nombre',          nombre);
            formData.append('existencia',      existenciaMin);
            formData.append('idEnvase',        idEnvase);
            formData.append('idFormaFarma',    idFormaFarmace);
            formData.append('idConcentracion', idConcentracion);
            formData.append('idContenido',     idContenido);
            formData.append('idAdministracion',idAdministracion);
            formData.append('nombreGenerico',  nombreGenerico);
            formData.append('idCodigo',  idCodigo);

            openLoading();
            axios.post(urlAdmin + '/admin/farmacia/registrar/nuevo', formData)
                .then(({ data }) => {
                    closeLoading();

                    if (data.success === 1) {
                        Swal.fire({
                            title: 'Código Repetido',
                            text: 'El Código de Artículo ya se encuentra registrado.',
                            icon: 'error',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar'
                        });
                    } else if (data.success === 2) {
                        toastr.success('Registrado correctamente');
                        borrarCampos();
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(() => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        // ─── Limpiar formulario ──────────────────────────────────────────────────────
        function borrarCampos() {
            document.getElementById('formulario-articulo').reset();

            SELECT2_IDS.forEach(id => resetSelect(id));
            $('#bloque-medicamentos').hide();
        }
    </script>
@endsection
