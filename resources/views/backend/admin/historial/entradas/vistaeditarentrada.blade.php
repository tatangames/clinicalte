@extends('adminlte::page')

@section('title', 'Historial Entradas - Editar')

@section('content_header')
    <h1></h1>
@stop

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
        /* ── Variables de color ── */
        :root {
            --color-primary:   #3b7dd8;
            --color-success:   #28a745;
            --color-danger:    #dc3545;
            --color-warning:   #ffc107;
            --color-border:    #dee2e6;
            --color-bg-light:  #f8f9fc;
            --color-label:     #495057;
            --color-header-bg: #3b7dd8;
            --color-header-txt:#ffffff;
            --radius:          6px;
            --shadow-card:     0 2px 8px rgba(0,0,0,.08);
        }

        /* ── Sección encabezado de página ── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }
        .section-header h2 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 600;
            color: #333;
        }

        /* ── Cards ── */
        .card-custom {
            background: #fff;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            margin-bottom: 22px;
        }
        .card-custom .card-head {
            background: var(--color-header-bg);
            color: var(--color-header-txt);
            padding: 10px 18px;
            border-radius: var(--radius) var(--radius) 0 0;
            font-weight: 600;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-custom .card-body-custom {
            padding: 20px 18px;
        }

        /* ── Labels ── */
        .control-label {
            font-size: .82rem;
            font-weight: 600;
            color: var(--color-label);
            margin-bottom: 4px;
            display: block;
        }
        .control-label .req { color: var(--color-danger); margin-left: 2px; }

        /* ── Inputs ── */
        .form-control {
            border-radius: var(--radius);
            border: 1px solid #ced4da;
            font-size: .88rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(59,125,216,.18);
        }
        .form-control:disabled {
            background: #f1f3f5;
            color: #6c757d;
        }

        /* ── Separador de secciones ── */
        .section-divider {
            border: none;
            border-top: 2px solid var(--color-bg-light);
            margin: 18px 0;
        }

        /* ── Buscador de producto ── */
        #matriz-busqueda td { padding: 0; border: none; }
        #matriz-busqueda { margin-bottom: 0; }

        /* ── Tabla de detalle ── */
        #matriz thead th {
            background: var(--color-header-bg);
            color: #fff;
            font-size: .82rem;
            font-weight: 600;
            border: none;
            padding: 10px 8px;
            white-space: nowrap;
        }
        #matriz tbody tr:hover { background: #eef3fc !important; }
        #matriz tbody td {
            vertical-align: middle;
            padding: 6px 8px;
            border-top: 1px solid #eee;
        }
        #matriz .form-control { font-size: .83rem; }

        /* ── Botones de acción ── */
        .btn-accion-guardar {
            background: var(--color-success);
            color: #fff;
            font-weight: 600;
            border-radius: var(--radius);
            padding: 8px 22px;
            font-size: .9rem;
            border: none;
            transition: background .2s, transform .1s;
        }
        .btn-accion-guardar:hover {
            background: #218838;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-agregar {
            background: var(--color-primary);
            color: #fff;
            font-weight: 600;
            border-radius: var(--radius);
            padding: 8px 20px;
            font-size: .88rem;
            border: none;
            transition: background .2s;
        }
        .btn-agregar:hover { background: #2c6ab8; color: #fff; }

        /* ── Badge de existencia / último costo ── */
        .info-badge {
            background: var(--color-bg-light);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 7px 12px;
            font-size: .88rem;
            color: #495057;
            min-height: 38px;
            display: flex;
            align-items: center;
        }

        /* ── Footer de acciones ── */
        .footer-acciones {
            display: flex;
            justify-content: flex-end;
            padding: 18px 0 30px;
            gap: 10px;
        }
    </style>

    <div id="divcontenedor">

        {{-- ── Cabecera + botón Atrás ── --}}
        <div class="section-header mb-3">
            <button type="button" onclick="vistaAtras()" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Atrás
            </button>
            <h2><i class="fas fa-edit mr-1" style="color:var(--color-primary)"></i> Editar Entrada de Artículo</h2>
        </div>

        {{-- ── Card: datos generales de la entrada ── --}}
        <div class="card-custom">
            <div class="card-head">
                <i class="fas fa-file-invoice"></i> Datos de la Entrada
            </div>
            <div class="card-body-custom">

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Fecha:</label>
                            <input type="date" class="form-control" id="fecha-editar" value="{{ $fechaFormat }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Tipo Factura</label>
                            <select id="select-tipofactura" class="form-control">
                                @foreach($arrayTipoFac as $item)
                                    <option value="{{ $item->id }}" {{ $infoEntrada->id_tipofactura == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fuente Financiamiento</label>
                            <select id="select-fuentefina" class="form-control">
                                @foreach($arrayFuente as $item)
                                    <option value="{{ $item->id }}" {{ $infoEntrada->id_fuentefina == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Proveedor</label>
                            <select id="select-proveedor" class="form-control">
                                @foreach($arrayProvee as $item)
                                    <option value="{{ $item->id }}" {{ $infoEntrada->id_proveedor == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">N° de Factura</label>
                            <input type="text" class="form-control" maxlength="100" id="numero-factura"
                                   value="{{ $infoEntrada->numero_factura }}">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Card: agregar producto ── --}}
        <div class="card-custom">
            <div class="card-head">
                <i class="fas fa-plus-circle"></i> Agregar Producto
            </div>
            <div class="card-body-custom">

                {{-- Buscador + Existencia + Último Costo --}}
                <div class="row align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="control-label">Buscar Producto <span class="req">*</span></label>
                        <table class="table" id="matriz-busqueda" data-toggle="table">
                            <tbody>
                            <tr>
                                <td>
                                    <input id="inputBuscador" data-idmedicamento='0' autocomplete="off"
                                           class='form-control' style='width:100%'
                                           onkeyup='buscarMaterial(this)' maxlength='300' type='text'
                                           placeholder="Escriba el nombre del producto…">
                                    <div class='droplista' id="midropmenu"
                                         style='position:absolute;z-index:9;width:75% !important;'></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-3">
                        <label class="control-label">Existencia</label>
                        <input type="text" disabled autocomplete="off" class="form-control" id="existencia">
                    </div>

                    <div class="col-md-3">
                        <label class="control-label">Último Costo</label>
                        <input type="text" disabled autocomplete="off" class="form-control" id="ultimo-costo">
                    </div>
                </div>

                <hr class="section-divider">

                {{-- Campos del ítem --}}
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="control-label">Cantidad <span class="req">*</span></label>
                        <input type="text" autocomplete="off" class="form-control" id="cantidad" placeholder="0">
                    </div>

                    <div class="col-md-3">
                        <label class="control-label">Lote <span class="req">*</span></label>
                        <input type="text" maxlength="100" autocomplete="off" class="form-control" id="lote">
                    </div>

                    <div class="col-md-3">
                        <label class="control-label">Fecha de Vencimiento <span class="req">*</span></label>
                        <input type="date" class="form-control" id="fecha-vencimiento">
                    </div>

                    <div class="col-md-2">
                        <label class="control-label">Precio Producto <span class="req">*</span></label>
                        <input type="text" autocomplete="off" class="form-control" id="precio-producto" placeholder="0.00">
                    </div>

                    <div class="col-md-2">
                        <label class="control-label">Precio Donación <span class="req">*</span></label>
                        <input type="text" autocomplete="off" class="form-control" id="precio-donacion" placeholder="0.00">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn-agregar" onclick="agregarFila();">
                        <i class="fas fa-plus mr-1"></i> Agregar Artículo
                    </button>
                </div>

            </div>
        </div>

        {{-- ── Card: detalle de artículos ── --}}
        <div class="card-custom">
            <div class="card-head">
                <i class="fas fa-list-alt"></i> Detalle de Artículos
            </div>

            <div style="overflow-x:auto; padding: 0 4px 4px;">
                <table class="table table-bordered mb-0" id="matriz" data-toggle="table">
                    <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th style="width:28%">Artículo</th>
                        <th style="width:10%">Precio</th>
                        <th style="width:8%">Cantidad</th>
                        <th style="width:10%">Lote</th>
                        <th style="width:14%">Fecha Vencimiento</th>
                        <th style="width:8%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($arrayDetalle as $item)
                        <tr>
                            <td>
                                <p id="fila" class="form-control mb-0" style="max-width:55px;text-align:center">
                                    {{ $item->contador }}
                                </p>
                            </td>
                            <td><input disabled value="{{ $item->nombre }}" class="form-control" type="text"></td>
                            <td><input disabled value="${{ $item->precio }}" class="form-control" type="text"></td>
                            <td><input disabled value="{{ $item->cantidad_fija }}" class="form-control" type="text"></td>
                            <td><input disabled value="{{ $item->lote }}" class="form-control" type="text"></td>
                            <td><input disabled value="{{ $item->fechaVencimiento }}" class="form-control" type="text"></td>
                            <td>
                                <button type="button" title="Editar" class="btn btn-warning btn-sm"
                                        style="color:white" onclick="infoEditarDecimales({{ $item->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Botón Actualizar Entrada ── --}}
        <div class="footer-acciones">
            <button type="button" class="btn-accion-guardar" onclick="preguntarGuardar()">
                <i class="fas fa-save mr-1"></i> Actualizar Entrada
            </button>
        </div>

        {{-- ── Modal Editar Detalle ── --}}
        <div class="modal fade" id="modalEditarDecimales">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius:var(--radius)">
                    <div class="modal-header" style="background:var(--color-header-bg);color:#fff;border-radius:var(--radius) var(--radius) 0 0">
                        <h5 class="modal-title mb-0"><i class="fas fa-edit mr-2"></i>Editar Registro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background:#f8f9fc">
                        <form id="formulario-decimales">
                            <input id="id-entramedi-decimal" type="hidden">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Precio</label>
                                        <input id="precio-edicion" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Precio Donación</label>
                                        <input id="precio-donacion-editar" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Lote</label>
                                        <input id="lote-edicion" maxlength="100" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Fecha Vencimiento</label>
                                        <input type="date" id="fechavencimiento-edicion" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="guardarEdicionFormato()">
                            Actualizar
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
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#select-fuentefina').select2({
                theme: "bootstrap-5",
                language: { noResults: () => "Búsqueda no encontrada" }
            });

            $('#select-proveedor').select2({
                theme: "bootstrap-5",
                language: { noResults: () => "Búsqueda no encontrada" }
            });

            window.seguroBuscador = true;
            window.txtContenedorGlobal = this;

            $(document).click(function(){ $(".droplista").hide(); });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        /* ───── BUSCADOR ───── */

        function buscarMaterial(e){
            if(!seguroBuscador) return;
            seguroBuscador = false;

            var row = $(e).closest('tr');
            txtContenedorGlobal = e;

            let texto = e.value;
            if(texto === '') $(e).attr('data-idmedicamento', 0);

            axios.post(urlAdmin+'/admin/buscar/nombre/medicamento', { query: texto })
                .then((response) => {
                    seguroBuscador = true;
                    $(row).find(".droplista").fadeIn().html(response.data);
                })
                .catch(() => { seguroBuscador = true; });
        }

        function modificarValor(edrop){
            $(txtContenedorGlobal).val($(edrop).text())
                .attr('data-idmedicamento', edrop.id);
            document.getElementById("existencia").value   = edrop.dataset.existencia;
            document.getElementById("ultimo-costo").value = edrop.dataset.ultimoprecio;
        }


        /* ───── AGREGAR FILA ───── */

        function agregarFila(){
            var lote          = document.getElementById('lote').value;
            var fechaVenc     = document.getElementById('fecha-vencimiento').value;
            var precioProducto= document.getElementById('precio-producto').value;
            var cantidad      = document.getElementById('cantidad').value;
            var precioDonacion= document.getElementById('precio-donacion').value;
            var inputBuscador = document.querySelector('#inputBuscador');

            var reglaEntero   = /^[0-9]\d*$/;
            var reglaDecimal  = /^([0-9]+\.?[0-9]{0,10})$/;

            if(inputBuscador.dataset.idmedicamento == 0)   { toastr.error("Producto es requerido"); return; }
            if(fechaVenc === '')    { toastr.error('Fecha Vencimiento no es válida'); return; }
            if(cantidad === '' || !cantidad.match(reglaEntero)) { toastr.error('Cantidad debe ser un número entero'); return; }
            if(cantidad < 0)        { toastr.error('Cantidad no debe ser negativa'); return; }
            if(cantidad > 9000000)  { toastr.error('Cantidad máximo 9 millones'); return; }
            if(lote === '')         { toastr.error('Lote es requerido'); return; }

            if(precioProducto === '' || !precioProducto.match(reglaDecimal)) { toastr.error('Precio Producto debe ser número decimal (10 decimales)'); return; }
            if(precioProducto < 0)      { toastr.error('Precio Producto no debe ser negativo'); return; }
            if(precioProducto > 9000000){ toastr.error('Precio Producto máximo 9 millones'); return; }

            if(precioDonacion === '' || !precioDonacion.match(reglaDecimal)) { toastr.error('Precio Donación debe ser número decimal (10 decimales)'); return; }
            if(precioDonacion < 0)      { toastr.error('Precio Donación no debe ser negativo'); return; }
            if(precioDonacion > 9000000){ toastr.error('Precio Donación máximo 9 millones'); return; }

            const fecha  = new Date(fechaVenc);
            const dia    = fecha.getDate();
            const mes    = fecha.getMonth() + 1;
            const anio   = fecha.getFullYear();
            let fechaFmt = dia + "/" + mes + "/" + anio;

            var nomProducto = document.getElementById('inputBuscador').value;
            var nFilas = $('#matriz >tbody >tr').length + 1;

            var markup = `<tr>
                <td><p id='fila${nFilas}' class='form-control mb-0' style='max-width:55px;text-align:center'>${nFilas}</p></td>
                <td><input name='arrayNombre[]' disabled data-idmedicamento='${inputBuscador.dataset.idmedicamento}' value='${nomProducto}' class='form-control' type='text'></td>
                <td>
                    <input name='arrayPrecio[]' data-precio='${precioProducto}' disabled value='$${precioProducto}' class='form-control' type='text'>
                    <input name='arrayPrecioDonacion[]' data-preciodonacion='${precioDonacion}' disabled value='$${precioDonacion}' class='form-control' type='hidden'>
                </td>
                <td><input name='arrayCantidad[]' disabled value='${cantidad}' class='form-control' type='text'></td>
                <td><input name='arrayLote[]' disabled value='${lote}' class='form-control' type='text'></td>
                <td><input name='arrayFecha[]' data-fecha='${fechaVenc}' disabled value='${fechaFmt}' class='form-control' type='text'></td>
                <td><button type='button' class='btn btn-danger btn-sm btn-block' onclick='borrarFila(this)'>Borrar</button></td>
            </tr>`;

            $("#matriz tbody").append(markup);

            Swal.fire({ position:'center', type:'success', title:'Agregado al Detalle', showConfirmButton:false, timer:1500 });

            $(txtContenedorGlobal).attr('data-idmedicamento','0');
            ['cantidad','fecha-vencimiento','precio-producto','precio-donacion','inputBuscador','existencia','ultimo-costo']
                .forEach(id => document.getElementById(id).value = '');
        }


        /* ───── TABLA ───── */

        function borrarFila(elemento){
            elemento.closest('tr').remove();
            setearFila();
        }

        function setearFila(){
            var table  = document.getElementById('matriz');
            var conteo = 0;
            for(var r = 1, n = table.rows.length; r < n; r++){
                conteo++;
                table.rows[r].cells[0].children[0].innerHTML = conteo;
            }
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background','#F1948A');
        }
        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background','white');
        }


        /* ───── GUARDAR ENTRADA ───── */

        function preguntarGuardar(){
            Swal.fire({
                title: '¿Actualizar Registro?', text: '',
                type: 'info', showCancelButton: true,
                confirmButtonColor: '#28a745', cancelButtonColor: '#d33',
                allowOutsideClick: false,
                confirmButtonText: 'Sí', cancelButtonText: 'Cancelar'
            }).then(result => { if(result.value) registrarMedicamento(); });
        }

        function registrarMedicamento(){
            var numFactura  = document.getElementById('numero-factura').value;
            var tipoFactura = document.getElementById('select-tipofactura').value;
            var fuenteFina  = document.getElementById('select-fuentefina').value;
            var proveedor   = document.getElementById('select-proveedor').value;
            var fecha       = document.getElementById('fecha-editar').value;

            if(!fecha)       { toastr.error('Fecha es requerida'); return; }
            if(!numFactura)  { toastr.error('Número de Factura es requerido'); return; }
            if(!tipoFactura) { toastr.error('Tipo Factura es requerido'); return; }
            if(!fuenteFina)  { toastr.error('Fuente Financiamiento es requerida'); return; }
            if(!proveedor)   { toastr.error('Proveedor es requerido'); return; }

            if($('#matriz > tbody > tr').length <= 0){
                toastr.error('Debe agregar al menos un producto'); return;
            }

            var arrayIdMedicamento  = $("input[name='arrayNombre[]']").map(function(){ return $(this).attr('data-idmedicamento'); }).get();
            var arrayCantidad       = $("input[name='arrayCantidad[]']").map(function(){ return $(this).val(); }).get();
            var arrayPrecio         = $("input[name='arrayPrecio[]']").map(function(){ return $(this).attr('data-precio'); }).get();
            var arrayLote           = $("input[name='arrayLote[]']").map(function(){ return $(this).val(); }).get();
            var arrayFecha          = $("input[name='arrayFecha[]']").map(function(){ return $(this).attr('data-fecha'); }).get();
            var arrayPrecioDonacion = $("input[name='arrayPrecioDonacion[]']").map(function(){ return $(this).attr('data-preciodonacion'); }).get();

            var reglaEntero  = /^[0-9]\d*$/;
            var reglaDecimal = /^([0-9]+\.?[0-9]{0,10})$/;

            colorBlancoTabla();

            for(var a = 0; a < arrayIdMedicamento.length; a++){
                let id  = arrayIdMedicamento[a];
                let can = arrayCantidad[a];
                let pre = arrayPrecio[a];
                let lot = arrayLote[a];
                let fec = arrayFecha[a];
                let don = arrayPrecioDonacion[a];

                if(id == 0)                        { colorRojoTabla(a); alertaMensaje('info','No encontrado','Fila #'+(a+1)+' - El Producto no se encontró. Borre la fila y búsquelo de nuevo.'); return; }
                if(!can || !can.match(reglaEntero)){ colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Cantidad inválida'); return; }
                if(can <= 0)                       { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Cantidad debe ser mayor a 0'); return; }
                if(can > 9000000)                  { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Cantidad máximo 9 millones'); return; }
                if(!pre || !pre.match(reglaDecimal)){ colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio inválido'); return; }
                if(pre <= 0)                       { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio debe ser mayor a 0'); return; }
                if(pre > 9000000)                  { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio máximo 9 millones'); return; }
                if(!don || !don.match(reglaDecimal)){ colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio Donación inválido'); return; }
                if(don < 0)                        { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio Donación no puede ser negativo'); return; }
                if(don > 9000000)                  { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Precio Donación máximo 9 millones'); return; }
                if(!lot)                           { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Lote requerido'); return; }
                if(!fec)                           { colorRojoTabla(a); toastr.error('Fila #'+(a+1)+' - Fecha requerida'); return; }
            }

            openLoading();

            const contenedorArray = arrayIdMedicamento.map((id, i) => ({
                infoIdMedicamento  : id,
                infoCantidad       : arrayCantidad[i],
                infoPrecio         : arrayPrecio[i],
                infoLote           : arrayLote[i],
                infoFecha          : arrayFecha[i],
                infoPrecioDonacion : arrayPrecioDonacion[i]
            }));

            let identrada = {{ $infoEntrada->id }};
            let formData  = new FormData();
            formData.append('contenedorArray', JSON.stringify(contenedorArray));
            formData.append('numFactura',  numFactura);
            formData.append('tipoFactura', tipoFactura);
            formData.append('fuenteFina',  fuenteFina);
            formData.append('proveedor',   proveedor);
            formData.append('identrada',   identrada);
            formData.append('fecha',       fecha);

            axios.post(urlAdmin+'/admin/registrar/actualizar/medicamento', formData)
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        Swal.fire({
                            title: '¡Entrada Actualizada!', text: '',
                            type: 'success', showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // ✅ Recarga la vista actual en lugar de ir atrás
                            location.reload();
                        });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(() => { toastr.error('Error al guardar'); closeLoading(); });
        }


        /* ───── MODAL EDITAR DETALLE ───── */

        function infoEditarDecimales(idmedientrada){
            openLoading();
            document.getElementById("formulario-decimales").reset();

            axios.post(urlAdmin+'/admin/modificar/entrada/medicamento/detalle', { id: idmedientrada })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditarDecimales').modal('show');
                        $('#id-entramedi-decimal').val(idmedientrada);
                        $('#precio-edicion').val(response.data.info.precio);
                        $('#precio-donacion-editar').val(response.data.info.precio_donacion);
                        $('#lote-edicion').val(response.data.info.lote);
                        $('#fechavencimiento-edicion').val(response.data.info.fecha_vencimiento);
                    } else {
                        toastr.error('Información no encontrada');
                    }
                })
                .catch(() => { closeLoading(); toastr.error('Información no encontrada'); });
        }

        function guardarEdicionFormato(){
            var id             = document.getElementById('id-entramedi-decimal').value;
            var precioProducto = document.getElementById('precio-edicion').value;
            var lote           = document.getElementById('lote-edicion').value;
            var fecha          = document.getElementById('fechavencimiento-edicion').value;
            var precioDonacion = document.getElementById('precio-donacion-editar').value;

            var reglaDecimal = /^([0-9]+\.?[0-9]{0,10})$/;

            if(!precioProducto || !precioProducto.match(reglaDecimal)) { toastr.error('Precio inválido (máx. 10 decimales)'); return; }
            if(precioProducto <= 0)       { toastr.error('Precio no puede ser cero o negativo'); return; }
            if(precioProducto > 9000000)  { toastr.error('Precio no debe superar 9 millones'); return; }

            if(!precioDonacion || !precioDonacion.match(reglaDecimal)) { toastr.error('Precio Donación inválido (máx. 10 decimales)'); return; }
            if(precioDonacion < 0)        { toastr.error('Precio Donación no puede ser negativo'); return; }
            if(precioDonacion > 9000000)  { toastr.error('Precio Donación no debe superar 9 millones'); return; }

            if(!lote)  { toastr.error('Lote es requerido'); return; }
            if(!fecha) { toastr.error('Fecha de Vencimiento es requerida'); return; }

            openLoading();
            var formData = new FormData();
            formData.append('id',              id);
            formData.append('precio',          precioProducto);
            formData.append('preciodonacion',  precioDonacion);
            formData.append('lote',            lote);
            formData.append('fechavencimiento',fecha);

            axios.post(urlAdmin+'/admin/actualizar/entrada/medicamento/detalle', formData)
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        Swal.fire({
                            title: '¡Actualizado!', type: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false,
                            confirmButtonText: 'Recargar'
                        }).then(() => location.reload());
                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(() => { toastr.error('Error al registrar'); closeLoading(); });
        }

        function vistaAtras(){
            window.location.href = "{{ url('/admin/historialentradas/index') }}";
        }

    </script>
@endsection
