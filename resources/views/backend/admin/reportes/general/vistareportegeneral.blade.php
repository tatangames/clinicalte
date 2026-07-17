@extends('adminlte::page')

@section('title', 'Reportes')

@section('content_header')
    <h1><i class="fas fa-chart-bar" style="color:#3b82f6; margin-right:8px"></i>Reportes</h1>
@stop

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

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

@section('css')
    <style>
        /* ── Grid de tarjetas ── */
        .reportes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 10px 0 20px;
        }

        /* ── Tarjeta base ── */
        .reporte-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .15s, box-shadow .15s;
            height: 100%;
        }
        .reporte-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,.12);
        }

        /* ── Header de tarjeta ── */
        .reporte-card-header {
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            color: #fff;
        }
        .reporte-card-header .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .reporte-card-header .card-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .reporte-card-header h5 {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            line-height: 1.3;
        }
        .btn-info-reglas {
            background: rgba(255,255,255,.22);
            border: none;
            color: #fff;
            width: 30px; height: 30px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s;
            font-size: 14px;
        }
        .btn-info-reglas:hover { background: rgba(255,255,255,.38); }

        /* Colores por tipo */
        .reporte-card.blue  .reporte-card-header { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
        .reporte-card.amber .reporte-card-header { background: linear-gradient(135deg, #b45309, #d97706); }
        .reporte-card.green .reporte-card-header { background: linear-gradient(135deg, #15803d, #22c55e); }
        .reporte-card.teal  .reporte-card-header { background: linear-gradient(135deg, #0f766e, #14b8a6); }

        /* ── Cuerpo de tarjeta ── */
        .reporte-card-body {
            padding: 20px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .reporte-card-body p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
            line-height: 1.6;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 14px;
        }

        /* Campos dentro de la tarjeta */
        .reporte-fields {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .reporte-fields .field-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .reporte-fields .field-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
            min-width: 130px;
        }
        .reporte-fields label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin: 0;
        }
        .reporte-fields .required-star { color: #ef4444; }
        .reporte-fields input[type="date"],
        .reporte-fields select {
            height: 36px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            padding: 0 10px;
            font-size: 13px;
            background: #f8fafc;
            color: #1e293b;
            width: 100%;
            transition: border-color .15s, box-shadow .15s;
        }
        .reporte-fields input[type="date"]:focus,
        .reporte-fields select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
            background: #fff;
        }

        /* select2 dentro de tarjeta */
        .reporte-fields .select2-container { width: 100% !important; }
        .reporte-fields .select2-container .select2-selection--single {
            height: 36px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #f8fafc;
        }
        .reporte-fields .select2-container .select2-selection__rendered {
            line-height: 36px;
            font-size: 13px;
            color: #1e293b;
        }
        .reporte-fields .select2-container .select2-selection__arrow { height: 34px; }

        /* ── Botón generar ── */
        .btn-generar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            color: #fff;
            align-self: flex-start;
        }
        .reporte-card.blue  .btn-generar { background: #2563eb; }
        .reporte-card.blue  .btn-generar:hover { background: #1d4ed8; }
        .reporte-card.amber .btn-generar { background: #d97706; }
        .reporte-card.amber .btn-generar:hover { background: #b45309; }
        .reporte-card.green .btn-generar { background: #16a34a; }
        .reporte-card.green .btn-generar:hover { background: #15803d; }
        .reporte-card.teal  .btn-generar { background: #0f766e; }
        .reporte-card.teal  .btn-generar:hover { background: #0d9488; }

        /* ── Checkbox personalizado (tarjeta 2) ── */
        .custom-existencia-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #f8fafc;
            transition: border-color .15s, background .15s;
        }
        .custom-existencia-check:hover { border-color: #d97706; }
        .custom-existencia-check .check-icon {
            width: 18px; height: 18px;
            border-radius: 5px;
            border: 2px solid #cbd5e1;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background .15s, border-color .15s;
        }
        .custom-existencia-check.checked .check-icon {
            background: #d97706;
            border-color: #d97706;
        }
        .custom-existencia-check .check-label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .divider { border-color: #e2e8f0; margin: 14px 0; }

        @media (max-width: 768px) {
            .reportes-grid        { grid-template-columns: 1fr; }
            .reporte-fields .field-row { flex-direction: column; }
        }
    </style>
@stop

@section('content')
    <div id="divcontenedor">
        <section class="content">
            <div class="container-fluid">

                {{-- ══ GRID DE TARJETAS ══ --}}
                <div class="reportes-grid">

                    {{-- TARJETA 1: INVENTARIO ACTUAL --}}
                    <div class="reporte-card blue">
                        <div class="reporte-card-header">
                            <div class="header-left">
                                <div class="card-icon"><i class="fas fa-boxes"></i></div>
                                <h5>Inventario Actual de Medicamentos</h5>
                            </div>
                        </div>
                        <div class="reporte-card-body">
                            <p>Existencias actuales calculadas como entradas menos salidas. Muestra código, nombre, entradas, salidas y stock disponible.</p>
                            <div class="reporte-fields">
                                <div class="field-row">
                                    <div class="field-item">
                                        <label>Mostrar</label>
                                        <select id="select-filtro-existencia">
                                            <option value="1">Solo con stock disponible (> 0)</option>
                                            <option value="0">Todos los medicamentos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="pdfExistencias()" class="btn-generar">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                    </div>

                    {{-- TARJETA 2: REPORTE POR FECHAS (FINAL v2) --}}
                    <div class="reporte-card amber">
                        <div class="reporte-card-header">
                            <div class="header-left">
                                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                                <h5>Reporte de Existencias por Fechas</h5>
                            </div>
                            <button type="button" class="btn-info-reglas" onclick="explicacionColumna()" title="Ver reglas aplicadas">
                                <i class="fas fa-question"></i>
                            </button>
                        </div>
                        <div class="reporte-card-body">
                            <p style="font-weight: bold; color: #92400e;">
                                Este reporte toma en cuenta las fechas para las columnas "Entregado Total" y "Total Desca. Fechas".
                            </p>
                            <div class="reporte-fields">
                                <div class="field-row">
                                    <div class="field-item">
                                        <label>Desde <span class="required-star">*</span></label>
                                        <input type="date" autocomplete="off" id="fecha2-desde">
                                    </div>
                                    <div class="field-item">
                                        <label>Hasta <span class="required-star">*</span></label>
                                        <input type="date" autocomplete="off" id="fecha2-hasta">
                                    </div>
                                </div>
                                <div class="field-row">
                                    <div class="field-item">
                                        <div class="custom-existencia-check" id="checkExistencia" onclick="toggleCheck()" title="Solo mostrar artículos con existencia mayor a 0">
                                            <div class="check-icon" id="checkIcon">
                                                <i class="fas fa-check" id="checkMark" style="display:none; color:#fff; font-size:11px;"></i>
                                            </div>
                                            <span class="check-label" id="checkLabel">Solo Existencia &gt; 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="verificar2()" class="btn-generar">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                    </div>



                </div>{{-- /.reportes-grid --}}

                {{-- TARJETA 3: MOVIMIENTOS POR MEDICAMENTO --}}
                <div class="reporte-card green">
                    <div class="reporte-card-header">
                        <div class="header-left">
                            <div class="card-icon"><i class="fas fa-exchange-alt"></i></div>
                            <h5>Movimientos por Medicamento</h5>
                        </div>
                    </div>
                    <div class="reporte-card-body">
                        <p>Detalle de entradas y salidas por receta de un medicamento específico. El rango de fechas es opcional.</p>
                        <div class="reporte-fields">
                            <div class="field-row">
                                <div class="field-item" style="min-width: 220px;">
                                    <label>Medicamento <span class="required-star">*</span></label>
                                    <select class="select2" id="mov-medicamento" style="width:100%">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($materiales as $m)
                                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="field-item">
                                    <label>Desde (opcional)</label>
                                    <input type="date" autocomplete="off" id="mov-desde">
                                </div>
                                <div class="field-item">
                                    <label>Hasta (opcional)</label>
                                    <input type="date" autocomplete="off" id="mov-hasta">
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="reporteMovimientosMedicamento()" class="btn-generar">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </button>
                    </div>
                </div>


            </div>
        </section>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        $(function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar medicamento...',
                allowClear: true,
                width: '100%',
            });
        });

        /* ── Tarjeta 1: Inventario actual ── */
        function pdfExistencias() {
            var filtro = document.getElementById('select-filtro-existencia').value;
            window.open(urlAdmin + '/admin/existencia/pdf/generar?soloConStock=' + filtro);
        }

        /* ── Tarjeta 2: Reporte por fechas (checkbox personalizado) ── */
        var soloExistencia = false;

        function toggleCheck() {
            soloExistencia = !soloExistencia;
            var check = document.getElementById('checkExistencia');
            var mark = document.getElementById('checkMark');

            if (soloExistencia) {
                check.classList.add('checked');
                mark.style.display = 'inline-block';
            } else {
                check.classList.remove('checked');
                mark.style.display = 'none';
            }
        }

        function verificar2() {
            let fechaDesde = document.getElementById("fecha2-desde").value;
            let fechaHasta = document.getElementById("fecha2-hasta").value;

            if (fechaDesde === '') {
                toastr.error('Fecha Desde es requerido');
                return;
            }
            if (fechaHasta === '') {
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            var fecha1 = new Date(fechaDesde);
            var fecha2 = new Date(fechaHasta);

            if (fecha1 > fecha2) {
                toastr.error('Fecha Desde no puede ser mayor que Fecha Hasta');
                return;
            }

            let filtro = soloExistencia ? '1' : '0';
            window.open(urlAdmin + '/admin/pdf/reporte/finalv2/' + fechaDesde + '/' + fechaHasta + '/' + filtro);
        }

        /* ── Modal de reglas aplicadas (Tarjeta 2) ── */
        function explicacionColumna() {

            let mensaje = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm text-left mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width:35%">Columna</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>COSTO</strong></td>
                        <td>Precio unitario del medicamento (costo normal).</td>
                    </tr>
                    <tr>
                        <td><strong>COSTO DONA</strong></td>
                        <td>Precio unitario del medicamento por donación.</td>
                    </tr>
                    <tr>
                        <td><strong>CANTIDAD INICIAL</strong></td>
                        <td>Cantidad con la que ingresó el lote al sistema.</td>
                    </tr>
                    <tr>
                        <td><strong>ENTREGADO</strong></td>
                        <td>Cantidad total entregada acumulada hasta la fecha final del intervalo.</td>
                    </tr>
                    <tr>
                        <td><strong>ENTREGADO TOTAL</strong></td>
                        <td>Cantidad entregada únicamente dentro del rango de fechas del reporte.</td>
                    </tr>
                    <tr>
                        <td><strong>EXISTENCIA</strong></td>
                        <td>Cantidad disponible en bodega (Cantidad Inicial - Entregado acumulado).</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL DESCARGADO</strong></td>
                        <td>Costo × Entregado acumulado hasta la fecha final.</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL DESCARGADO DONA</strong></td>
                        <td>Costo Dona × Entregado acumulado hasta la fecha final.</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL DESCA. FECHAS</strong></td>
                        <td>Costo × Entregado Total (solo dentro del rango de fechas).</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL DESCA. DONA FECHAS</strong></td>
                        <td>Costo Dona × Entregado Total (solo dentro del rango de fechas).</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL EXISTENCIA</strong></td>
                        <td>Costo × Existencia disponible.</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL EXISTENCIA DONA</strong></td>
                        <td>Costo Dona × Existencia disponible.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `;

            Swal.fire({
                title: '<i class="fas fa-info-circle text-primary"></i> Información de las Columnas',
                html: mensaje,
                width: '900px',
                icon: 'info',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#28a745',
                allowOutsideClick: false,
                customClass: {
                    htmlContainer: 'p-0'
                }
            });
        }

        /* ── Tarjeta 3: Movimientos por medicamento ── */
        function reporteMovimientosMedicamento() {
            let medicamentoId = document.getElementById('mov-medicamento').value;
            let desde = document.getElementById('mov-desde').value;
            let hasta = document.getElementById('mov-hasta').value;

            if (!medicamentoId) {
                toastr.error('Debe seleccionar un medicamento');
                return;
            }

            if ((desde && !hasta) || (!desde && hasta)) {
                toastr.error('Debe indicar ambas fechas (Desde y Hasta) o dejarlas vacías');
                return;
            }

            if (desde && hasta) {
                var fecha1 = new Date(desde);
                var fecha2 = new Date(hasta);
                if (fecha1 > fecha2) {
                    toastr.error('Fecha Desde no puede ser mayor que Fecha Hasta');
                    return;
                }
            }

            var url = urlAdmin + '/admin/pdf/movimientos/' + medicamentoId;
            if (desde && hasta) {
                url += '/' + desde + '/' + hasta;
            }

            window.open(url);
        }
    </script>
@endsection
