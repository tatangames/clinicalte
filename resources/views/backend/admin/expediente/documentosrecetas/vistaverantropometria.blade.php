@extends('adminlte::page')

@section('title', 'Antropometría')

@section('content_header')
    <div class="d-flex align-items-center">
        <button type="button"
                onclick="vistaExpedientes()"
                class="btn btn-sm btn-warning font-weight-bold" style="color: white">
            <i class="fas fa-arrow-left mr-1"></i> Atrás
        </button>

        <h1 class="flex-grow-1 text-center mb-0">Antropometría</h1>

        <div style="width: 90px;"></div>
    </div>
@stop

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
        .form-label-sm {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 4px;
            display: block;
        }
        .campo-calculado {
            background: #f8f9fa !important;
            color: #495057;
            font-weight: 600;
        }
        .btn-actualizar {
            background: #1a7abf;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-actualizar:hover { background: #155f99; color: #fff; }

        /* ── Ficha paciente inline ── */
        .ficha-badge {
            display: inline-block;
            background: #e8f4fd;
            color: #1a7abf;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>

    <div class="py-2">

        {{-- Ficha del paciente --}}
        <div class="seccion-card">
            <div class="seccion-header">
                <div class="seccion-icon"><i class="fas fa-id-card"></i></div>
                <p class="seccion-titulo">Paciente</p>
            </div>
            <p class="mb-1" style="font-weight:700; font-size:15px; color:#212529;">{{ $nombreCompleto }}</p>
            <span class="ficha-badge"><i class="fas fa-folder mr-1"></i>Visualizar Antropometría</span>
        </div>

        <form id="formulario-antropometria" autocomplete="off">

            {{-- SECCIÓN 1: Signos vitales --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-heartbeat"></i></div>
                    <p class="seccion-titulo">Signos vitales</p>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label-sm">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="fecha-antro"
                               value="{{ $infoAntrop->fecha }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Frec. Cardiaca (lpm)</label>
                        <input type="text" class="form-control form-control-sm" id="frecuencia-cardia-antro"
                               value="{{ $infoAntrop->frecuencia_cardiaca }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Frec. Respiratoria (rpm)</label>
                        <input type="text" class="form-control form-control-sm" id="frecuencia-respiratoria-antro"
                               value="{{ $infoAntrop->frecuencia_respiratoria }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Presión Arterial (mmHg)</label>
                        <input type="text" class="form-control form-control-sm" id="presion-arterial-antro"
                               value="{{ $infoAntrop->presion_arterial }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Temperatura (°C)</label>
                        <input type="text" class="form-control form-control-sm" id="temperatura-antro"
                               value="{{ $infoAntrop->temperatura }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">SpO₂</label>
                        <input type="text" class="form-control form-control-sm" id="sp02-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->spo2 }}">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: Antropometría + IMC --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-weight"></i></div>
                    <p class="seccion-titulo">Antropometría e IMC</p>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label-sm">Peso (lb)</label>
                        <input type="text" class="form-control form-control-sm" id="peso-libra-antro"
                               onkeypress="return validaNumero(event);"
                               onkeyup="calcularImcDesdeLb();"
                               value="{{ $infoAntrop->peso_libra }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Peso (kg)</label>
                        <input type="text" class="form-control form-control-sm" id="peso-kilo-antro"
                               onkeypress="return validaNumero(event);"
                               onkeyup="calcularImcDesdeKg();"
                               value="{{ $infoAntrop->peso_kilo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Estatura (cm)</label>
                        <input type="text" class="form-control form-control-sm" id="estatura-antro"
                               onkeypress="return validaNumero(event);"
                               onkeyup="calcularImcDesdeLb();"
                               value="{{ $infoAntrop->estatura }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">IMC</label>
                        <input type="text" class="form-control form-control-sm campo-calculado" id="imc-antro"
                               disabled value="{{ $infoAntrop->imc }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Resultado IMC</label>
                        <input type="text" class="form-control form-control-sm campo-calculado" id="resultado-imc-antro"
                               disabled value="{{ $infoAntrop->resultado_imc }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-2">
                        <label class="form-label-sm">Perím. Abdominal (cm)</label>
                        <input type="text" class="form-control form-control-sm" id="perim-abdominal-antro"
                               value="{{ $infoAntrop->perim_abdominal }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Perím. Cefálico (cm)</label>
                        <input type="text" class="form-control form-control-sm" id="perimetro-cefalico-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->perim_cefalico }}">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: ICC --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-ruler-combined"></i></div>
                    <p class="seccion-titulo">Índice cintura-cadera (ICC)</p>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label-sm">Perím. Cintura (cm)</label>
                        <input type="text" class="form-control form-control-sm" id="perimetro-cintura-antro"
                               onkeypress="return validaNumero(event);"
                               onchange="calcularIndice();"
                               value="{{ $infoAntrop->perim_cintura }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Perím. Cadera (cm)</label>
                        <input type="text" class="form-control form-control-sm" id="perimetro-cadera-antro"
                               onkeypress="return validaNumero(event);"
                               onchange="calcularIndice();"
                               value="{{ $infoAntrop->perim_cadera }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">ICC</label>
                        <input type="text" class="form-control form-control-sm campo-calculado" id="icc-antro"
                               disabled value="{{ $infoAntrop->icc }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Riesgo Mujer</label>
                        <input type="text" class="form-control form-control-sm campo-calculado" id="riesgo-mujer-antro"
                               disabled value="{{ $infoAntrop->riesgo_mujer }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Riesgo Hombre</label>
                        <input type="text" class="form-control form-control-sm campo-calculado" id="riesgo-hombre-antro"
                               disabled value="{{ $infoAntrop->riesgo_hombre }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Gasto Energético Basal</label>
                        <input type="text" class="form-control form-control-sm" id="gasto-energetico-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->gasto_energetico_basal }}">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: Mediciones capilares --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-tint"></i></div>
                    <p class="seccion-titulo">Mediciones capilares</p>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label-sm">Glucometría Capilar</label>
                        <input type="text" class="form-control form-control-sm" id="glucometria-capilar-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->glucometria_capilar }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Glicohemoglobina Capilar</label>
                        <input type="text" class="form-control form-control-sm" id="glicohemoglobina-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->glicohemoglibona_capilar }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Cetonas Capilares</label>
                        <input type="text" class="form-control form-control-sm" id="cetona-capilar-antro"
                               onkeypress="return validaNumero(event);"
                               value="{{ $infoAntrop->cetona_capilar }}">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 5: Notas + botón --}}
            <div class="row">
                <div class="col-md-8">
                    <div class="seccion-card h-100">
                        <div class="seccion-header">
                            <div class="seccion-icon"><i class="fas fa-sticky-note"></i></div>
                            <p class="seccion-titulo">Otros detalles</p>
                        </div>
                        <textarea class="form-control form-control-sm" id="otros-detalles-antro"
                                  rows="3">{{ $infoAntrop->nota_adicional }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <div class="seccion-card w-100 d-flex align-items-center justify-content-end mb-0">
                        <button type="button" class="btn-actualizar" onclick="actualizarAntropometria()">
                            <i class="fas fa-save"></i> Actualizar antropometría
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>

@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/datatables-config.js') }}"></script>

    <script>

        // ── Validación numérica ──
        function validaNumero(e) {
            const tecla = document.all ? e.keyCode : e.which;
            if (tecla === 8) return true;
            return /[0-9.]/.test(String.fromCharCode(tecla));
        }

        // ── IMC desde libras ──
        function calcularImcDesdeLb() {
            const lb      = parseFloat($('#peso-libra-antro').val()) || 0;
            const kg      = lb / 2.2046;
            const estatura = parseFloat($('#estatura-antro').val()) || 0;

            $('#peso-kilo-antro').val(kg.toFixed(2));

            if (kg > 0 && estatura > 0) {
                const imc = kg / Math.pow(estatura / 100, 2);
                $('#imc-antro').val(imc.toFixed(2));
                $('#resultado-imc-antro').val(clasificarImc(imc));
            }
        }

        // ── IMC desde kilos ──
        function calcularImcDesdeKg() {
            const kg      = parseFloat($('#peso-kilo-antro').val()) || 0;
            const estatura = parseFloat($('#estatura-antro').val()) || 0;

            $('#peso-libra-antro').val((kg * 2.2046).toFixed(2));

            if (kg > 0 && estatura > 0) {
                const imc = kg / Math.pow(estatura / 100, 2);
                $('#imc-antro').val(imc.toFixed(2));
                $('#resultado-imc-antro').val(clasificarImc(imc));
            }
        }

        // ── Clasificación IMC ──
        function clasificarImc(imc) {
            if (imc < 16)                        return 'Delgadez severa';
            if (imc >= 16   && imc <= 16.99)     return 'Delgadez moderada';
            if (imc >= 17   && imc <= 18.49)     return 'Delgadez leve';
            if (imc >= 18.5 && imc <= 24.99)     return 'Normal';
            if (imc >= 25   && imc <= 29.99)     return 'Preobeso';
            if (imc >= 30   && imc <= 34.99)     return 'Obesidad leve';
            if (imc >= 35   && imc <= 39.99)     return 'Obesidad media';
            return 'Obesidad mórbida';
        }

        // ── ICC ──
        function calcularIndice() {
            const cintura = parseFloat($('#perimetro-cintura-antro').val()) || 0;
            const cadera  = parseFloat($('#perimetro-cadera-antro').val())  || 0;

            if (cintura <= 0 || cadera <= 0) { $('#icc-antro').val(0); return; }

            const icc = (cintura / cadera).toFixed(2);
            $('#icc-antro').val(icc);

            // Riesgo mujer
            let mujer, colorM;
            if      (icc < 0.8)              { mujer = 'Bajo';     colorM = 'green'; }
            else if (icc > 0.8 && icc <= 0.85) { mujer = 'Moderado'; colorM = 'orange'; }
            else                             { mujer = 'Alto';     colorM = 'red'; }

            // Riesgo hombre
            let hombre, colorH;
            if      (icc < 0.95)             { hombre = 'Bajo';     colorH = 'green'; }
            else if (icc > 0.95 && icc <= 1) { hombre = 'Moderado'; colorH = 'orange'; }
            else                             { hombre = 'Alto';     colorH = 'red'; }

            $('#riesgo-mujer-antro').val(mujer).css({ color: colorM, fontWeight: 'bold' });
            $('#riesgo-hombre-antro').val(hombre).css({ color: colorH, fontWeight: 'bold' });
        }

        // ── Guardar ──
        function actualizarAntropometria() {

            const fecha = document.getElementById('fecha-antro').value;
            if (!fecha) { toastr.warning('La fecha es requerida'); return; }

            const datos = {
                idmodal:            {{ $infoAntrop->id }},
                fecha,
                freCardiaca:        document.getElementById('frecuencia-cardia-antro').value,
                freRespiratoria:    document.getElementById('frecuencia-respiratoria-antro').value,
                presionArterial:    document.getElementById('presion-arterial-antro').value,
                temperatura:        document.getElementById('temperatura-antro').value,
                perimetroAbdominal: document.getElementById('perim-abdominal-antro').value,
                perimetroCefalico:  document.getElementById('perimetro-cefalico-antro').value,
                pesoLibra:          document.getElementById('peso-libra-antro').value,
                pesoKilo:           document.getElementById('peso-kilo-antro').value,
                estatura:           document.getElementById('estatura-antro').value,
                imc:                document.getElementById('imc-antro').value,
                resultadoImc:       document.getElementById('resultado-imc-antro').value,
                glucometria:        document.getElementById('glucometria-capilar-antro').value,
                glicohemoglobina:   document.getElementById('glicohemoglobina-antro').value,
                cetona:             document.getElementById('cetona-capilar-antro').value,
                sp02:               document.getElementById('sp02-antro').value,
                perimetroCintura:   document.getElementById('perimetro-cintura-antro').value,
                perimetroCadera:    document.getElementById('perimetro-cadera-antro').value,
                icc:                document.getElementById('icc-antro').value,
                riesgoMujer:        document.getElementById('riesgo-mujer-antro').value,
                riesgoHombre:       document.getElementById('riesgo-hombre-antro').value,
                gastoEnergetico:    document.getElementById('gasto-energetico-antro').value,
                otrosDetalles:      document.getElementById('otros-detalles-antro').value,
            };

            const formData = new FormData();
            Object.entries(datos).forEach(([k, v]) => formData.append(k, v));

            openLoading();

            axios.post(urlAdmin + '/admin/historial/actualizar/antropometria', formData)
                .then(({ data }) => {
                    closeLoading();
                    if (data.success === 1) {
                        Swal.fire({
                            title: 'Actualizado correctamente',
                            type: 'success',
                            confirmButtonColor: '#1a7abf',
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then(function () {

                        });
                    } else {
                        toastr.error('Error al actualizar. Intenta de nuevo.');
                    }
                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error de conexión. Verifica tu red e intenta de nuevo.');
                });
        }

        function vistaExpedientes() {
            window.location.href = "{{ url('/admin/buscarexpediente/index') }}";
        }

    </script>
@endsection
