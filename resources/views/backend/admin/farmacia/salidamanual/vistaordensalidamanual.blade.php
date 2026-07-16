@extends('adminlte::page')

@section('title', 'Salida Manual')

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
            --sm-blue:     #1a6fc4;
            --sm-blue-lt:  #e8f1fb;
            --sm-green:    #1a8a5a;
            --sm-green-lt: #e6f5ef;
            --sm-red:      #c0392b;
            --sm-red-lt:   #fde8e6;
            --sm-amber:    #d97706;
            --sm-amber-lt: #fef3c7;
            --sm-gray:     #6c757d;
            --sm-border:   #dee2e6;
            --sm-surface:  #f8f9fa;
            --sm-white:    #ffffff;
            --sm-shadow:   0 2px 8px rgba(0,0,0,.08);
        }

        .sm-wrap { width: 100%; padding: 0 0.5rem 3rem; }

        /* ── Cards ── */
        .sm-card {
            background: var(--sm-white);
            border: 1px solid var(--sm-border);
            border-radius: 10px;
            box-shadow: var(--sm-shadow);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .sm-card-head {
            display: flex; align-items: center; gap: 10px;
            padding: .75rem 1.2rem;
            background: var(--sm-surface);
            border-bottom: 1px solid var(--sm-border);
        }
        .sm-card-icon {
            width: 30px; height: 30px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .sm-icon-blue  { background: var(--sm-blue-lt);  color: var(--sm-blue); }
        .sm-icon-green { background: var(--sm-green-lt); color: var(--sm-green); }
        .sm-icon-amber { background: var(--sm-amber-lt); color: var(--sm-amber); }
        .sm-card-title { font-size: 13px; font-weight: 700; color: #343a40; margin: 0; }
        .sm-card-body  { padding: 1.1rem 1.2rem; }

        /* ── Labels ── */
        .sm-label {
            font-size: 11px; font-weight: 700;
            color: var(--sm-gray);
            text-transform: uppercase; letter-spacing: .04em;
            margin-bottom: 4px; display: block;
        }

        /* ── Inputs ── */
        .sm-card-body .form-control,
        .sm-card-body .select2-container--bootstrap-5 .select2-selection {
            border-radius: 7px !important;
            border-color: var(--sm-border) !important;
            font-size: 13px !important;
        }
        .sm-card-body .form-control:focus {
            border-color: var(--sm-blue);
            box-shadow: 0 0 0 3px rgba(26,111,196,.12);
        }

        /* ── Divider ── */
        .sm-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 1.5rem 0 1rem;
            color: var(--sm-gray); font-size: 11px;
            font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        }
        .sm-divider::before, .sm-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--sm-border);
        }

        /* ── Tabla detalle ── */
        .sm-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .sm-table thead tr { background: var(--sm-surface); }
        .sm-table thead th {
            padding: .65rem 1rem;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .05em;
            color: var(--sm-gray);
            border-bottom: 2px solid var(--sm-border);
            white-space: nowrap;
        }
        .sm-table tbody tr { transition: background .15s; }
        .sm-table tbody tr:hover { background: #f1f6fd; }
        .sm-table tbody td {
            padding: .6rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .sm-table tbody tr:last-child td { border-bottom: none; }
        .sm-table .form-control { font-size: 13px; border-radius: 6px; }

        /* Número de fila */
        .sm-row-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--sm-blue-lt); color: var(--sm-blue);
            font-size: 12px; font-weight: 700;
        }

        /* ── Total chip ── */
        .sm-total-chip {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--sm-blue-lt); color: var(--sm-blue);
            border-radius: 8px; padding: 6px 16px;
            font-size: 14px; font-weight: 700;
        }

        /* ── Empty state ── */
        .sm-empty {
            text-align: center; padding: 2.5rem 1rem; color: #adb5bd;
        }
        .sm-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; }

        /* ── Botones ── */
        .sm-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .45rem 1rem; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: filter .15s, transform .1s;
        }
        .sm-btn:active { transform: scale(.97); }
        .sm-btn-success { background: var(--sm-green); color: #fff; }
        .sm-btn-success:hover { filter: brightness(1.08); color: #fff; }
        .sm-btn-danger  { background: var(--sm-red);   color: #fff; }
        .sm-btn-danger:hover  { filter: brightness(1.08); color: #fff; }
        .sm-btn-primary { background: var(--sm-blue);  color: #fff; }
        .sm-btn-primary:hover { filter: brightness(1.08); color: #fff; }

        /* ── Footer sticky ── */
        .sm-footer {
            position: sticky; bottom: 0;
            background: var(--sm-white);
            border-top: 1px solid var(--sm-border);
            padding: .9rem 1.2rem;
            display: flex; align-items: center; justify-content: space-between;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 -4px 12px rgba(0,0,0,.06);
            z-index: 10;
        }

        /* ── Panel de selección de producto (se carga dinámicamente) ── */
        #tablaProductos { margin-top: .5rem; }
    </style>

    <div class="sm-wrap">

        {{-- ══ SECCIÓN 1 — Datos de salida ══ --}}
        <div class="sm-card">
            <div class="sm-card-head">
                <div class="sm-card-icon sm-icon-blue"><i class="fas fa-sign-out-alt"></i></div>
                <span class="sm-card-title">Crear Orden de Salida Manual</span>
            </div>
            <div class="sm-card-body">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="sm-label">Motivo <span class="text-danger">*</span></label>
                        <select class="form-control" id="select-motivo">
                            <option value="">Seleccionar…</option>
                            @foreach($arrayMotivo as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="sm-label">Fecha de salida <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha-salida" autocomplete="off">
                    </div>

                </div>
            </div>
        </div>

        {{-- ══ SECCIÓN 2 — Selección de producto ══ --}}
        <div class="sm-card">
            <div class="sm-card-head">
                <div class="sm-card-icon sm-icon-amber"><i class="fas fa-search"></i></div>
                <span class="sm-card-title">Buscar producto</span>
            </div>
            <div class="sm-card-body">

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="sm-label">Producto</label>
                        <select class="form-control" id="select-producto" onchange="cargarTablaProducto()">
                            <option value="">Seleccionar…</option>
                            @foreach($arrayProducto as $item)
                                <option value="{{ $item->id }}">{{ $item->nombretotal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tabla de lotes cargada dinámicamente --}}
                <div id="tablaProductos"></div>

                {{-- Botón agregar (oculto hasta que haya producto) --}}
                <div id="btnAgregarFila" style="display:none; margin-top:.75rem; text-align:right;">
                    <button type="button" class="sm-btn sm-btn-primary" onclick="agregarFila()">
                        <i class="fas fa-plus"></i> Agregar a tabla
                    </button>
                </div>

            </div>
        </div>

        {{-- ══ SECCIÓN 3 — Observaciones + Detalle ══ --}}
        <div class="sm-divider"><i class="fas fa-list-ul"></i> Detalle de salida</div>

        <div class="sm-card">
            <div class="sm-card-head">
                <div class="sm-card-icon sm-icon-green"><i class="fas fa-list-ul"></i></div>
                <span class="sm-card-title">Productos a despachar</span>
                <span style="
                margin-left:auto;
                background:var(--sm-blue-lt); color:var(--sm-blue);
                padding:2px 10px; border-radius:20px;
                font-size:11px; font-weight:700;"
                      id="contador-filas">
                0 ítems
            </span>
            </div>

            <div style="overflow-x:auto;">
                <table class="sm-table" id="matriz">
                    <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Producto</th>
                        <th style="width:110px;">Cant. salida</th>
                        <th style="width:120px;">Vencimiento</th>
                        <th>Lote</th>
                        <th style="width:120px;">Fecha entrada</th>
                        <th style="width:80px;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="fila-vacia">
                        <td colspan="7">
                            <div class="sm-empty">
                                <i class="fas fa-box-open"></i>
                                Aún no se han agregado productos.<br>
                                <small>Busca un producto y presiona "Agregar a tabla".</small>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Observaciones ── --}}
            <div style="padding:1.1rem 1.2rem; border-top:1px solid var(--sm-border);">
                <label class="sm-label">Observaciones</label>
                <textarea class="form-control" rows="3" id="text-observaciones"
                          style="border-radius:7px; font-size:13px;"
                          placeholder="Notas adicionales sobre la salida…"></textarea>
            </div>

            {{-- Footer sticky ── --}}
            <div class="sm-footer">
                <div class="sm-total-chip">
                    <i class="fas fa-cubes"></i>
                    Total unidades: <span id="cantidadTotal">0</span>
                </div>
                <button type="button" class="sm-btn sm-btn-success" onclick="preguntarGuardar()">
                    <i class="fas fa-save"></i> Guardar salida
                </button>
            </div>
        </div>

    </div>{{-- /sm-wrap --}}

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        /* ── Select2 ── */
        $('#select-producto').select2({
            theme: 'bootstrap-5',
            language: { noResults: function () { return 'Sin resultados'; } }
        });

        /* ── Cargar lotes del producto ── */
        function cargarTablaProducto() {
            var idProducto = document.getElementById('select-producto').value;

            if (!idProducto) {
                document.getElementById('tablaProductos').innerHTML = '';
                document.getElementById('btnAgregarFila').style.display = 'none';
                return;
            }

            openLoading();
            var ruta = "{{ URL::to('/admin/buscar/producto/salida/farmacia') }}/" + idProducto;
            $('#tablaProductos').load(ruta);
        }

        /* ── Resaltar filas ── */
        function colorRojoTabla(index) {
            $("#matriz tr:eq(" + (index + 1) + ")").css('background', '#fde8e6');
        }

        function colorBlancoTabla() {
            $("#matriz tbody tr").css('background', '');
        }

        /* ── Agregar fila al detalle ── */
        function agregarFila() {
            const inputSalidas = document.querySelectorAll('input[name="arraysalida[]"]');
            var hayItems = true;

            inputSalidas.forEach(function (valor) {
                hayItems = false;

                var nombreMedicamento = valor.dataset.nombremedi;
                var idEntrada         = valor.dataset.identrada;
                var fechaVencimiento  = valor.dataset.fechavencimiento;
                var fechaEntrada      = valor.dataset.fechaentrada;
                var loteEntrada       = valor.dataset.lote;
                var inputCantidad     = valor.value;

                if (!inputCantidad || inputCantidad == 0) return;

                // Quitar placeholder vacío
                var filaVacia = document.getElementById('fila-vacia');
                if (filaVacia) filaVacia.remove();

                var nFilas = $('#matriz > tbody > tr').length + 1;

                var fila = `
                <tr>
                    <td><span class="sm-row-num" id="fila${nFilas}">${nFilas}</span></td>
                    <td>
                        <input name="arrayNombre[]" disabled data-identrada="${idEntrada}"
                               value="${nombreMedicamento}" class="form-control form-control-sm" type="text">
                    </td>
                    <td>
                        <input name="arrayCantidad[]" disabled value="${inputCantidad}"
                               class="form-control form-control-sm" type="number">
                    </td>
                    <td>
                        <input disabled value="${fechaVencimiento}"
                               class="form-control form-control-sm" type="text">
                    </td>
                    <td>
                        <input disabled value="${loteEntrada}"
                               class="form-control form-control-sm" type="text">
                    </td>
                    <td>
                        <input disabled value="${fechaEntrada}"
                               class="form-control form-control-sm" type="text">
                    </td>
                    <td>
                        <button type="button" class="sm-btn sm-btn-danger"
                                style="padding:.3rem .7rem; font-size:12px;"
                                onclick="borrarFila(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;

                $("#matriz tbody").append(fila);
                calcularFilas();

                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: 'Agregado',
                    showConfirmButton: false,
                    timer: 1200
                });

                ocultarElecciones();
            });

            if (hayItems) {
                toastr.error('Elija la cantidad de salida primero');
            }
        }

        /* ── Limpiar selección de producto ── */
        function ocultarElecciones() {
            document.getElementById('select-producto').selectedIndex = 0;
            $("#select-producto").trigger("change");
            document.getElementById('tablaProductos').innerHTML = '';
            document.getElementById('btnAgregarFila').style.display = 'none';
        }

        /* ── Borrar fila ── */
        function borrarFila(el) {
            el.closest('tr').remove();
            setearFila();
        }

        function setearFila() {
            var rows = document.querySelectorAll('#matriz tbody tr:not(#fila-vacia)');
            var conteo = 0;
            rows.forEach(function (row) {
                conteo++;
                var span = row.cells[0].querySelector('.sm-row-num');
                if (span) { span.id = 'fila' + conteo; span.textContent = conteo; }
            });

            if (conteo === 0) {
                $('#matriz tbody').html(`
                <tr id="fila-vacia">
                    <td colspan="7">
                        <div class="sm-empty">
                            <i class="fas fa-box-open"></i>
                            Aún no se han agregado productos.<br>
                            <small>Busca un producto y presiona "Agregar a tabla".</small>
                        </div>
                    </td>
                </tr>`);
            }

            calcularFilas();
        }

        /* ── Calcular total ── */
        function calcularFilas() {
            var cantidades = $("#matriz input[name='arrayCantidad[]']").map(function () {
                return parseInt($(this).val()) || 0;
            }).get();

            var total = cantidades.reduce(function (acc, v) { return acc + v; }, 0);
            document.getElementById('cantidadTotal').textContent = total;

            var n = $('#matriz > tbody > tr:not(#fila-vacia)').length;
            document.getElementById('contador-filas').textContent =
                n + (n === 1 ? ' ítem' : ' ítems');
        }

        /* ── Confirmar guardar ── */
        function preguntarGuardar() {
            Swal.fire({
                title: '¿Guardar salida?',
                text: 'Se registrará la orden de salida manual.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a8a5a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then(function (result) {
                if (result.value) {
                    registrarMedicamento();
                }
            });
        }

        /* ── Registrar ── */
        function registrarMedicamento() {
            var motivo       = document.getElementById('select-motivo').value;
            var fecha        = document.getElementById('fecha-salida').value;
            var observaciones= document.getElementById('text-observaciones').value;
            var reglaEntero  = /^[0-9]\d*$/;

            if (!motivo) { toastr.error('El motivo es requerido'); return; }
            if (!fecha)  { toastr.error('La fecha de salida es requerida'); return; }

            var nRegistro = $('#matriz > tbody > tr:not(#fila-vacia)').length;
            if (nRegistro <= 0) { toastr.error('Agregue al menos un producto'); return; }

            var arrayIdEntrada = $("input[name='arrayNombre[]']").map(function () {
                return $(this).data('identrada');
            }).get();
            var arrayCantidad = $("#matriz input[name='arrayCantidad[]']").map(function () {
                return $(this).val();
            }).get();

            colorBlancoTabla();

            for (var a = 0; a < arrayIdEntrada.length; a++) {
                var idE  = arrayIdEntrada[a];
                var cant = arrayCantidad[a];

                if (idE == 0) {
                    colorRojoTabla(a);
                    alertaMensaje('info', 'No encontrado',
                        'Fila #' + (a + 1) + ': el producto no se encontró. Borre la fila y búsquelo de nuevo.');
                    return;
                }
                if (!cant || !cant.match(reglaEntero)) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ': cantidad inválida.');
                    return;
                }
                if (parseInt(cant) <= 0) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ': la cantidad no puede ser 0 o negativa.');
                    return;
                }
                if (parseInt(cant) > 9000000) {
                    colorRojoTabla(a);
                    toastr.error('Fila #' + (a + 1) + ': cantidad máxima 9 millones.');
                    return;
                }
            }

            openLoading();

            var contenedor = [];
            for (var i = 0; i < arrayIdEntrada.length; i++) {
                contenedor.push({ infoIdEntrada: arrayIdEntrada[i], infoCantidad: arrayCantidad[i] });
            }

            var fd = new FormData();
            fd.append('contenedorArray', JSON.stringify(contenedor));
            fd.append('motivo',          motivo);
            fd.append('fecha',           fecha);
            fd.append('observaciones',   observaciones);

            axios.post(urlAdmin + '/admin/registrar/orden/salida/medicamento', fd)
                .then(function (response) {
                    closeLoading();

                    if (response.data.success === 1) {
                        var fila      = response.data.fila;
                        var cantHay   = response.data.cantidad;
                        colorRojoTabla(fila - 1);

                        Swal.fire({
                            title: 'Stock insuficiente',
                            type: 'error',
                            confirmButtonColor: '#1a6fc4',
                            confirmButtonText: 'Aceptar',
                            html: 'En la fila <strong>#' + fila + '</strong> se supera el stock disponible.<br>' +
                                'Unidades disponibles: <strong>' + cantHay + '</strong>'
                        });

                    } else if (response.data.success === 2) {
                        toastr.success('Salida registrada correctamente');
                        limpiar();
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(function () { closeLoading(); toastr.error('Error al guardar'); });
        }

        /* ── Limpiar todo ── */
        function limpiar() {
            document.getElementById('select-producto').selectedIndex = 0;
            $("#select-producto").trigger("change");
            document.getElementById('select-motivo').selectedIndex = 0;
            document.getElementById('tablaProductos').innerHTML = '';
            document.getElementById('btnAgregarFila').style.display = 'none';
            document.getElementById('fecha-salida').value = '';
            document.getElementById('text-observaciones').value = '';

            $('#matriz tbody').html(`
            <tr id="fila-vacia">
                <td colspan="7">
                    <div class="sm-empty">
                        <i class="fas fa-box-open"></i>
                        Aún no se han agregado productos.<br>
                        <small>Busca un producto y presiona "Agregar a tabla".</small>
                    </div>
                </td>
            </tr>`);

            document.getElementById('cantidadTotal').textContent = '0';
            document.getElementById('contador-filas').textContent = '0 ítems';
        }
    </script>
@endsection
