@extends('adminlte::page')

@section('title', 'Ingreso de Medicamento a Inventario')

@section('content_header')
    <h1>Ingreso de Medicamento a Inventario</h1>
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

        {{-- ═══════════════════════════════════════════════
             CARD 1: DATOS DE FACTURA
        ════════════════════════════════════════════════ --}}
        <div class="card card-gray-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Datos de Factura</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label text-muted">N° Factura</label>
                            <input type="text" maxlength="100" autocomplete="off"
                                   class="form-control" id="numero-factura">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Tipo de Factura <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="select-tipofactura">
                                @foreach($arrayTipoFactura as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Fuente de Financiamiento <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="select-fuente-financiamiento">
                                @foreach($arrayFuente as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Proveedor <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="select-proveedor">
                                @foreach($arrayProveedor as $item)
                                    <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ═══════════════════════════════════════════════
             CARD 2: PRODUCTO A INGRESAR
        ════════════════════════════════════════════════ --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pills mr-2"></i>Producto a Ingresar</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Buscar Producto <span class="text-danger">*</span>
                            </label>
                            <input id="inputBuscador" data-idmedicamento="0" autocomplete="off"
                                   class="form-control" onkeyup="buscarMaterial(this)"
                                   maxlength="300" type="text">
                            <div class="droplista" id="midropmenu"
                                 style="position:absolute; z-index:9; width:75%"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label text-muted">Existencia</label>
                            <input type="text" disabled autocomplete="off"
                                   class="form-control" id="existencia">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label text-muted">Último Costo</label>
                            <input type="text" disabled autocomplete="off"
                                   class="form-control" id="ultimo-costo">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Cantidad <span class="text-danger">*</span>
                            </label>
                            <input type="text" autocomplete="off"
                                   class="form-control" id="cantidad">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Lote <span class="text-danger">*</span>
                            </label>
                            <input type="text" maxlength="100" autocomplete="off"
                                   class="form-control" id="lote">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Fecha de Vencimiento <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="fecha-vencimiento">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Precio Producto <span class="text-danger">*</span>
                            </label>
                            <input type="text" autocomplete="off" class="form-control"
                                   id="precio-producto" placeholder="0.0000000000">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="col-form-label text-muted">
                                Precio Costo (Donación) <span class="text-danger">*</span>
                            </label>
                            <input type="text" autocomplete="off" class="form-control"
                                   id="precio-donacion" placeholder="0.0000000000">
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-primary" onclick="agregarFila()">
                    <i class="fas fa-plus mr-1"></i> Agregar a Tabla
                </button>
            </div>
        </div>


        {{-- ═══════════════════════════════════════════════
             CARD 3: DETALLE DE INGRESO
        ════════════════════════════════════════════════ --}}
        <div class="card card-gray-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>Detalle de Ingreso</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0" id="matriz">
                    <thead class="thead-light">
                    <tr>
                        <th style="width:4%">#</th>
                        <th style="width:30%">Producto</th>
                        <th style="width:8%">Cantidad</th>
                        <th style="width:12%">Precio</th>
                        <th style="width:10%">Lote</th>
                        <th style="width:10%">Fecha V.</th>
                        <th style="width:8%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                            <tr>
                                <th class="text-center">Precio Total</th>
                                <th class="text-center">Precio Costo</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="width:130px">
                                    <span class="form-control text-center font-weight-bold" id="precioTotal">$0.00</span>
                                </td>
                                <td style="width:130px">
                                    <span class="form-control text-center font-weight-bold" id="precioTotalDonacion">$0.00</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-left mb-4" style="padding-bottom: 16px">
            <button type="button" class="btn btn-success btn-lg" onclick="preguntarGuardar()">
                <i class="fas fa-save"></i> Guardar Listado de Medicamentos
            </button>
        </div>

    </div>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        // ─── Constantes de validación ────────────────────────────────────────────────
        const REGEX_ENTERO  = /^[0-9]\d*$/;
        const REGEX_DECIMAL = /^([0-9]+\.?[0-9]{0,10})$/;
        const MAX_VALOR     = 9000000;

        let seguroBuscador      = true;
        let txtContenedorGlobal = null;

        $(document).ready(function () {
            $('#select-proveedor').select2({
                theme: 'bootstrap-5',
                language: { noResults: () => 'Búsqueda no encontrada' }
            });

            $(document).on('click', () => $('.droplista').hide());
        });

        // ─── Buscar producto ─────────────────────────────────────────────────────────
        function buscarMaterial(e) {
            if (!seguroBuscador) return;
            seguroBuscador = false;
            txtContenedorGlobal = e;

            const texto = e.value;
            if (texto === '') $(e).attr('data-idmedicamento', 0);

            axios.post(urlAdmin + '/admin/buscar/nombre/medicamento', { query: texto })
                .then(({ data }) => { seguroBuscador = true; $('#midropmenu').fadeIn().html(data); })
                .catch(() => { seguroBuscador = true; });
        }

        function modificarValor(edrop) {
            $(txtContenedorGlobal)
                .val($(edrop).text())
                .attr('data-idmedicamento', edrop.id);

            $('#existencia').val(edrop.dataset.existencia);
            $('#ultimo-costo').val(edrop.dataset.ultimoprecio);
            document.activeElement.blur();
        }

        // ─── Validaciones ────────────────────────────────────────────────────────────
        function validarEntero(val, label) {
            if (val === '')                { toastr.error(`${label} es requerido`);                         return false; }
            if (!REGEX_ENTERO.test(val))   { toastr.error(`${label} debe ser un número entero positivo`);   return false; }
            if (parseInt(val) <= 0)        { toastr.error(`${label} debe ser mayor a cero`);                return false; }
            if (parseInt(val) > MAX_VALOR) { toastr.error(`${label} máximo ${MAX_VALOR.toLocaleString()}`); return false; }
            return true;
        }

        function validarDecimal(val, label) {
            if (val === '')                  { toastr.error(`${label} es requerido`);                             return false; }
            if (!REGEX_DECIMAL.test(val))    { toastr.error(`${label} debe ser número decimal (hasta 10 dec.)`); return false; }
            if (parseFloat(val) < 0)         { toastr.error(`${label} no debe ser negativo`);                    return false; }
            if (parseFloat(val) > MAX_VALOR) { toastr.error(`${label} máximo ${MAX_VALOR.toLocaleString()}`);    return false; }
            return true;
        }

        // ─── Agregar fila ────────────────────────────────────────────────────────────
        function agregarFila() {
            const inputBuscador  = document.getElementById('inputBuscador');
            const cantidad       = document.getElementById('cantidad').value.trim();
            const lote           = document.getElementById('lote').value.trim();
            const fechaVenc      = document.getElementById('fecha-vencimiento').value;
            const precioProducto = document.getElementById('precio-producto').value.trim();
            const precioDonacion = document.getElementById('precio-donacion').value.trim();

            if (inputBuscador.dataset.idmedicamento == 0) { toastr.error('Producto es requerido'); return; }
            if (!validarEntero(cantidad, 'Cantidad'))                return;
            if (!lote)      { toastr.error('Lote es requerido'); return; }
            if (!fechaVenc) { toastr.error('Fecha de Vencimiento es requerida'); return; }
            if (!validarDecimal(precioProducto, 'Precio Producto')) return;
            if (!validarDecimal(precioDonacion, 'Precio Donación')) return;

            const [anio, mes, dia] = fechaVenc.split('-');
            const fechaFormat   = `${parseInt(dia)}/${parseInt(mes)}/${anio}`;
            const nFilas        = $('#matriz > tbody > tr').length + 1;
            const nomProducto   = inputBuscador.value;
            const idMedicamento = inputBuscador.dataset.idmedicamento;

            $('#matriz tbody').append(`
            <tr>
                <td><p id="fila${nFilas}" class="form-control text-center" style="max-width:55px">${nFilas}</p></td>
                <td><input name="arrayNombre[]" disabled data-idmedicamento="${idMedicamento}" value="${nomProducto}" class="form-control" type="text"></td>
                <td><input name="arrayCantidad[]" disabled value="${cantidad}" class="form-control" type="text"></td>
                <td>
                    <input name="arrayPrecio[]" data-precio="${precioProducto}" disabled value="$${precioProducto}" class="form-control" type="text">
                    <input name="arrayPrecioDonacion[]" data-preciodonacion="${precioDonacion}" disabled value="$${precioDonacion}" class="form-control" type="hidden">
                </td>
                <td><input name="arrayLote[]" disabled value="${lote}" class="form-control" type="text"></td>
                <td><input name="arrayFecha[]" data-fecha="${fechaVenc}" disabled value="${fechaFormat}" class="form-control" type="text"></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-block" onclick="borrarFila(this)">Borrar</button></td>
            </tr>`);

            calcularFilas();

            Swal.fire({ position: 'center', type: 'success', title: 'Agregado al Detalle', showConfirmButton: false, timer: 1500 });

            $(txtContenedorGlobal).attr('data-idmedicamento', '0');
            ['cantidad','fecha-vencimiento','precio-producto','precio-donacion',
                'inputBuscador','existencia','ultimo-costo']
                .forEach(id => { document.getElementById(id).value = ''; });
        }

        // ─── Borrar / reordenar filas ─────────────────────────────────────────────────
        function borrarFila(btn) {
            $(btn).closest('tr').remove();
            setearFila();
        }

        function setearFila() {
            $('#matriz tbody tr').each(function (i) { $(this).find('td:first p').text(i + 1); });
            calcularFilas();
        }

        // ─── Calcular totales ────────────────────────────────────────────────────────
        function calcularFilas() {
            const cantidades   = $("input[name='arrayCantidad[]']").map((_, el) => +$(el).val()).get();
            const precios      = $("input[name='arrayPrecio[]']").map((_, el) => +$(el).data('precio')).get();
            const preciosDonac = $("input[name='arrayPrecioDonacion[]']").map((_, el) => +$(el).data('preciodonacion')).get();

            let total = 0, totalDonac = 0;
            cantidades.forEach((cant, i) => {
                total      += cant * (precios[i]      || 0);
                totalDonac += cant * (preciosDonac[i] || 0);
            });

            $('#precioTotal').text('$' + total.toFixed(2));
            $('#precioTotalDonacion').text('$' + totalDonac.toFixed(2));
        }

        // ─── Confirmar y guardar ─────────────────────────────────────────────────────
        function preguntarGuardar() {
            Swal.fire({
                title: '¿Registrar Medicamentos?',
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                confirmButtonText: 'SÍ',
                cancelButtonText: 'NO'
            }).then(function(result) {

                if (result.value) {
                    registrarMedicamento();
                }
            });
        }

        function registrarMedicamento() {
            const numFactura  = $('#numero-factura').val().trim();
            const tipoFactura = $('#select-tipofactura').val();
            const fuenteFina  = $('#select-fuente-financiamiento').val();
            const proveedor   = $('#select-proveedor').val();

            if (!numFactura)  { toastr.error('Número de Factura es requerido');        return; }
            if (!tipoFactura) { toastr.error('Tipo de Factura es requerido');           return; }
            if (!fuenteFina)  { toastr.error('Fuente de Financiamiento es requerida'); return; }
            if (!proveedor)   { toastr.error('Proveedor es requerido');                return; }

            if ($('#matriz > tbody > tr').length === 0) {
                toastr.error('Debe agregar al menos un producto');
                return;
            }

            const arrayIdMedicamento  = $("input[name='arrayNombre[]']").map((_, el) => $(el).data('idmedicamento')).get();
            const arrayCantidad       = $("input[name='arrayCantidad[]']").map((_, el) => $(el).val()).get();
            const arrayPrecio         = $("input[name='arrayPrecio[]']").map((_, el) => $(el).data('precio')).get();
            const arrayPrecioDonacion = $("input[name='arrayPrecioDonacion[]']").map((_, el) => $(el).data('preciodonacion')).get();
            const arrayLote           = $("input[name='arrayLote[]']").map((_, el) => $(el).val()).get();
            const arrayFecha          = $("input[name='arrayFecha[]']").map((_, el) => $(el).data('fecha')).get();

            colorBlancoTabla();
            for (let a = 0; a < arrayIdMedicamento.length; a++) {
                const fila = `Fila #${a + 1}`;
                if (arrayIdMedicamento[a] == 0) {
                    colorRojoTabla(a);
                    alertaMensaje('info', 'No encontrado', `${fila}: El Producto no se encuentra. Borre la fila y búsquelo nuevamente.`);
                    return;
                }
                if (!validarEntero(String(arrayCantidad[a]),        `${fila} — Cantidad`))        { colorRojoTabla(a); return; }
                if (!validarDecimal(String(arrayPrecio[a]),         `${fila} — Precio Producto`)) { colorRojoTabla(a); return; }
                if (!validarDecimal(String(arrayPrecioDonacion[a]), `${fila} — Precio Donación`)) { colorRojoTabla(a); return; }
                if (!arrayLote[a])  { colorRojoTabla(a); toastr.error(`${fila}: Lote no encontrado`);  return; }
                if (!arrayFecha[a]) { colorRojoTabla(a); toastr.error(`${fila}: Fecha no encontrada`); return; }
            }

            const contenedorArray = arrayIdMedicamento.map((idMed, i) => ({
                infoIdMedicamento : idMed,
                infoCantidad      : arrayCantidad[i],
                infoPrecio        : arrayPrecio[i],
                infoLote          : arrayLote[i],
                infoFecha         : arrayFecha[i],
                infoPrecioDonacion: arrayPrecioDonacion[i],
            }));

            const formData = new FormData();
            formData.append('contenedorArray', JSON.stringify(contenedorArray));
            formData.append('numFactura',  numFactura);
            formData.append('tipoFactura', tipoFactura);
            formData.append('fuenteFina',  fuenteFina);
            formData.append('proveedor',   proveedor);

            openLoading();
            axios.post(urlAdmin + '/admin/registrar/nuevo/medicamento', formData)
                .then(({ data }) => {
                    closeLoading();
                    if (data.success === 1) { toastr.success('Registrado correctamente'); limpiar(); }
                    else                    { toastr.error('Error al guardar'); }
                })
                .catch(() => { toastr.error('Error al guardar'); closeLoading(); });
        }

        // ─── Limpiar ─────────────────────────────────────────────────────────────────
        function limpiar() {
            ['inputBuscador','existencia','ultimo-costo','cantidad','lote',
                'fecha-vencimiento','precio-producto','precio-donacion','numero-factura']
                .forEach(id => { document.getElementById(id).value = ''; });

            $('#precioTotal').text('$0.00');
            $('#precioTotalDonacion').text('$0.00');

            ['select-proveedor','select-tipofactura','select-fuente-financiamiento']
                .forEach(id => { document.getElementById(id).selectedIndex = 0; $(`#${id}`).trigger('change'); });

            $('#matriz tbody tr').remove();
        }

        function colorRojoTabla(index) { $(`#matriz tr:eq(${index + 1})`).css('background', '#F1948A'); }
        function colorBlancoTabla()    { $('#matriz tbody tr').css('background', 'white'); }
    </script>
@endsection
