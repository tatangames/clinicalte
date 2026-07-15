@extends('adminlte::page')

@section('title', 'Nueva Antropometría')

@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown" role="button">
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
    <section class="content pt-3">
        <div class="container-fluid">

            {{-- Encabezado --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <button type="button" class="btn btn-warning btn-sm" style="color: white" onclick="vistaHistorialClinico()">
                    <i class="fas fa-arrow-left mr-1"></i> Atrás
                </button>

            </div>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-weight mr-2"></i> Registro de Antropometría y Signos Vitales
                    </h5>
                </div>

                <div class="card-body">
                    <form id="formulario-antropometria">

                        {{-- BLOQUE 1: Fecha --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="font-weight-bold">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha-antro">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- BLOQUE 2: Signos vitales --}}
                        <p class="font-weight-bold text-muted small text-uppercase mb-2">
                            <i class="fas fa-heartbeat mr-1 text-danger"></i> Signos vitales
                        </p>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Frecuencia cardíaca <small class="text-muted">(lpm)</small></label>
                                <input type="text" maxlength="150" class="form-control" id="frecuencia-cardia-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Frecuencia respiratoria <small class="text-muted">(rpm)</small></label>
                                <input type="text" maxlength="150" class="form-control" id="frecuencia-respiratoria-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Presión arterial <small class="text-muted">(mmHg)</small></label>
                                <input type="text" maxlength="150" class="form-control" id="presion-arterial-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Temperatura <small class="text-muted">(°C)</small></label>
                                <input type="text" class="form-control" id="temperatura-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>SpO2</label>
                                <input type="text" onkeypress="return validaNumero(event)" class="form-control" id="sp02-antro" autocomplete="off">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- BLOQUE 3: Antropometría --}}
                        <p class="font-weight-bold text-muted small text-uppercase mb-2">
                            <i class="fas fa-ruler-vertical mr-1 text-primary"></i> Antropometría
                        </p>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Peso <small class="text-muted">(lb)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" onkeyup="calcularImcDesdeLb()"
                                       class="form-control" id="peso-libra-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Peso <small class="text-muted">(kg)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" onkeyup="calcularImcDesdeKg()"
                                       class="form-control" id="peso-kilo-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Estatura <small class="text-muted">(cm)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" onkeyup="calcularImcDesdeLb()"
                                       class="form-control" id="estatura-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>IMC</label>
                                <input type="text" disabled class="form-control font-weight-bold" id="imc-antro">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Resultado IMC</label>
                                <input type="text" disabled class="form-control font-weight-bold" id="resultado-imc-antro">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Perím. abdominal <small class="text-muted">(cm)</small></label>
                                <input type="text" class="form-control" id="perim-abdominal-antro" value="N/A" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Perím. cefálico <small class="text-muted">(cm)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" class="form-control" value="N/A"
                                       id="perimetro-cefalico-antro" autocomplete="off">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- BLOQUE 4: ICC --}}
                        <p class="font-weight-bold text-muted small text-uppercase mb-2">
                            <i class="fas fa-circle-notch mr-1 text-warning"></i> Índice cintura-cadera (ICC)
                        </p>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Perímetro de cintura <small class="text-muted">(cm)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" onchange="calcularIcc()"
                                       class="form-control" id="perimetro-cintura-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Perímetro de cadera <small class="text-muted">(cm)</small></label>
                                <input type="text" onkeypress="return validaNumero(event)" onchange="calcularIcc()"
                                       class="form-control" id="perimetro-cadera-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>ICC</label>
                                <input type="text" disabled class="form-control font-weight-bold" id="icc-antro">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Riesgo mujer</label>
                                <input type="text" disabled class="form-control font-weight-bold" id="riesgo-mujer-antro">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Riesgo hombre</label>
                                <input type="text" disabled class="form-control font-weight-bold" id="riesgo-hombre-antro">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Gasto energético basal</label>
                                <input type="text" onkeypress="return validaNumero(event)"
                                       class="form-control" id="gasto-energetico-antro" autocomplete="off">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- BLOQUE 5: Capilares --}}
                        <p class="font-weight-bold text-muted small text-uppercase mb-2">
                            <i class="fas fa-tint mr-1 text-danger"></i> Mediciones capilares
                        </p>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Glucometría capilar</label>
                                <input type="text" onkeypress="return validaNumero(event)" class="form-control"
                                       id="glucometria-capilar-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Glicohemoglobina capilar</label>
                                <input type="text" onkeypress="return validaNumero(event)" class="form-control"
                                       id="glicohemoglobina-antro" autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Cetonas capilares</label>
                                <input type="text" onkeypress="return validaNumero(event)" class="form-control"
                                       id="cetona-capilar-antro" autocomplete="off">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Otros detalles --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Otros detalles</label>
                            <textarea class="form-control" rows="3" id="otros-detalles-antro"></textarea>
                        </div>

                    </form>
                </div>

                <div class="card-footer text-right">
                    <button type="button" class="btn btn-success" onclick="saveAntropometriaSv()">
                        <i class="fas fa-save mr-1"></i> Guardar antropometría
                    </button>
                </div>
            </div>

        </div>
    </section>
@stop

@section('js')
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>
        const IDCONSULTA = {{ $idconsulta }};

        $(document).ready(function () {
            document.getElementById('fecha-antro').value = new Date().toJSON().slice(0, 10);
        });

        /* ─── Navegación ─── */
        function vistaHistorialClinico() {
            window.location.href = "{{ url('/admin/historial/clinico/vista') }}/" + IDCONSULTA;
        }

        /* ─── IMC ─── */
        function clasificarImc(imc) {
            if (imc < 16)                    return 'Delgadez severa';
            if (imc <= 16.99)                return 'Delgadez moderada';
            if (imc <= 18.49)                return 'Delgadez leve';
            if (imc <= 24.99)                return 'Normal';
            if (imc <= 29.99)                return 'Preobeso';
            if (imc <= 34.99)                return 'Obesidad leve';
            if (imc <= 39.99)                return 'Obesidad media';
            return 'Obesidad mórbida';
        }

        function actualizarImc(pesoKg) {
            var estatura = parseFloat($('#estatura-antro').val());
            if (!pesoKg || !estatura) return;

            var imc = pesoKg / Math.pow(estatura / 100, 2);
            $('#imc-antro').val(imc.toFixed(2));
            $('#resultado-imc-antro').val(clasificarImc(imc));
        }

        function calcularImcDesdeLb() {
            var lb = parseFloat($('#peso-libra-antro').val());
            if (!lb) return;
            var kg = lb / 2.2046;
            $('#peso-kilo-antro').val(kg.toFixed(2));
            actualizarImc(kg);
        }

        function calcularImcDesdeKg() {
            var kg = parseFloat($('#peso-kilo-antro').val());
            if (!kg) return;
            $('#peso-libra-antro').val((kg * 2.2046).toFixed(2));
            actualizarImc(kg);
        }

        /* ─── ICC ─── */
        function clasificarIcc(valor, umbrales) {
            if (valor < umbrales[0])                          return { nivel: 'Bajo',     color: 'green'  };
            if (valor <= umbrales[1])                         return { nivel: 'Moderado', color: 'orange' };
            return                                                   { nivel: 'Alto',     color: 'red'    };
        }

        function calcularIcc() {
            var cintura = parseFloat($('#perimetro-cintura-antro').val());
            var cadera  = parseFloat($('#perimetro-cadera-antro').val());

            if (!cintura || !cadera) { $('#icc-antro').val(0); return; }

            var icc = (cintura / cadera).toFixed(2);
            $('#icc-antro').val(icc);

            var mujer  = clasificarIcc(parseFloat(icc), [0.8,  0.85]);
            var hombre = clasificarIcc(parseFloat(icc), [0.95, 1.0 ]);

            $('#riesgo-mujer-antro').val(mujer.nivel).css({ color: mujer.color, 'font-weight': 'bold' });
            $('#riesgo-hombre-antro').val(hombre.nivel).css({ color: hombre.color, 'font-weight': 'bold' });
        }

        /* ─── Validación numérica ─── */
        function validaNumero(e) {
            var tecla = e.keyCode || e.which;
            if (tecla === 8) return true;
            return /[0-9.]/.test(String.fromCharCode(tecla));
        }

        /* ─── Guardar ─── */
        function saveAntropometriaSv() {
            Swal.fire({
                title: '¿Guardar antropometría?',
                type: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then(function (result) {
                if (result.value) {
                    guardarAntropometria();
                }
            });
        }

        function guardarAntropometria() {
            var fecha = document.getElementById('fecha-antro').value;
            if (!fecha) { toastr.error('Fecha es requerida'); return; }

            var campos = {
                idconsulta:         IDCONSULTA,
                fecha:              fecha,
                freCardiaca:        'frecuencia-cardia-antro',
                freRespiratoria:    'frecuencia-respiratoria-antro',
                presionArterial:    'presion-arterial-antro',
                temperatura:        'temperatura-antro',
                perimetroAbdominal: 'perim-abdominal-antro',
                perimetroCefalico:  'perimetro-cefalico-antro',
                pesoLibra:          'peso-libra-antro',
                pesoKilo:           'peso-kilo-antro',
                estatura:           'estatura-antro',
                imc:                'imc-antro',
                resultadoImc:       'resultado-imc-antro',
                glucometria:        'glucometria-capilar-antro',
                glicohemoglobina:   'glicohemoglobina-antro',
                cetona:             'cetona-capilar-antro',
                sp02:               'sp02-antro',
                perimetroCintura:   'perimetro-cintura-antro',
                perimetroCadera:    'perimetro-cadera-antro',
                icc:                'icc-antro',
                riesgoMujer:        'riesgo-mujer-antro',
                riesgoHombre:       'riesgo-hombre-antro',
                gastoEnergetico:    'gasto-energetico-antro',
                otrosDetalles:      'otros-detalles-antro',
            };

            openLoading();
            var formData = new FormData();

            for (var key in campos) {
                var val = campos[key];
                // Si es string es un id de elemento DOM, si no es valor directo
                formData.append(key, typeof val === 'string' && val !== 'idconsulta'
                    ? (document.getElementById(val)?.value ?? '')
                    : val
                );
            }

            // idconsulta y fecha ya los metemos como valor directo
            formData.set('idconsulta', IDCONSULTA);
            formData.set('fecha', fecha);

            axios.post(urlAdmin + '/admin/historial/registrar/antropometria', formData)
                .then(function (response) {
                    closeLoading();

                    if (response.data.success === 1) {
                        Swal.fire({
                            title: 'Registro duplicado',
                            text: 'Ya existe un registro para esta consulta.',
                            type: 'warning',
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then(function () {
                            vistaHistorialClinico();
                        });

                    } else if (response.data.success === 2) {
                        Swal.fire({
                            title: 'Guardado correctamente',
                            type: 'success',
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then(function () {
                            vistaHistorialClinico();
                        });

                    } else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
        }
    </script>
@endsection
