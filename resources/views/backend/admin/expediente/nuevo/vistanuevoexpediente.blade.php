@extends('adminlte::page')

@section('title', 'Nuevo Expediente')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Nuevo Expediente</h1>
        </div>
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
        .edad-badge {
            display: inline-block;
            background: #e8f4fd;
            color: #1a7abf;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            min-width: 80px;
            text-align: center;
            margin-top: 2px;
        }
        .file-drop {
            border: 1.5px dashed #ced4da;
            border-radius: 8px;
            padding: 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .file-drop:hover { border-color: #1a7abf; background: #f5faff; }
        .file-drop .drop-icon { font-size: 24px; color: #adb5bd; margin-bottom: 4px; }
        .file-drop .drop-text { font-size: 12px; color: #6c757d; }
        .btn-registrar {
            background: #28a745;
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
        .btn-registrar:hover { background: #218838; color: #fff; }
    </style>

    <div class="py-2">

        <form id="formulario" autocomplete="off">

            {{-- SECCIÓN 1: Identificación --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-id-badge"></i></div>
                    <p class="seccion-titulo">Identificación</p>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label-sm"># Expediente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="numero-expediente-nuevo" maxlength="100" placeholder="EXP-2025-001">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label-sm">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="nombre-nuevo" maxlength="150" placeholder="Nombres del paciente">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Apellidos</label>
                        <input type="text" class="form-control form-control-sm" id="apellido-nuevo" maxlength="150" placeholder="Apellidos del paciente">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: Datos personales --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-user"></i></div>
                    <p class="seccion-titulo">Datos personales</p>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label-sm">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="fecha-nacimiento" onchange="calcularEdad()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Edad calculada</label>
                        <div><span class="edad-badge" id="edad">—</span></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Sexo <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" id="select-sexo">
                            <option value="">Seleccione...</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Tipo de paciente <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" id="select-tipopaciente">
                            @foreach($arrayTipoPaciente as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-sm">Estado civil <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" id="select-civil">
                            @foreach($arrayEstadoCivil as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label-sm">Profesión</label>
                        <select class="form-control form-control-sm" id="select-profesion">
                            @foreach($arrayProfesion as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: Documento --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-file-alt"></i></div>
                    <p class="seccion-titulo">Documento de identidad</p>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label-sm">Tipo de documento <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" id="select-documento">
                            @foreach($arrayTipoDocumento as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">
                            Número de documento
                        </label>
                        <input type="text"
                               class="form-control form-control-sm"
                               id="numero-documento"
                               maxlength="100">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: Contacto --}}
            <div class="seccion-card">
                <div class="seccion-header">
                    <div class="seccion-icon"><i class="fas fa-phone"></i></div>
                    <p class="seccion-titulo">Contacto</p>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label-sm">Teléfono</label>
                        <input type="text" class="form-control form-control-sm" id="telefono" maxlength="10" placeholder="2200-0000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Celular</label>
                        <input type="text" class="form-control form-control-sm" id="celular" maxlength="10" placeholder="7000-0000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Correo electrónico</label>
                        <input type="text" class="form-control form-control-sm" id="correo" maxlength="150" placeholder="paciente@correo.com">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8">
                        <label class="form-label-sm">Dirección</label>
                        <textarea class="form-control form-control-sm" id="direccion" rows="2" maxlength="500" placeholder="Calle, colonia, municipio..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Referido por</label>
                        <input type="text" class="form-control form-control-sm" id="referido" maxlength="300" placeholder="Nombre del referente">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 5: Foto + botón --}}
            <div class="row">
                <div class="col-md-5">
                    <div class="seccion-card h-100">
                        <div class="seccion-header">
                            <div class="seccion-icon"><i class="fas fa-image"></i></div>
                            <p class="seccion-titulo">Foto del paciente <small class="text-muted font-weight-normal">(opcional)</small></p>
                        </div>
                        <label class="file-drop d-block" for="documento">
                            <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <div class="drop-text">Haz clic para subir una imagen<br><span class="text-muted">JPG, PNG — máx. recomendado 2 MB</span></div>
                        </label>
                        <input type="file" id="documento" class="d-none" accept="image/jpeg,image/jpg,image/png">
                        <small class="text-muted d-block mt-2" id="nombre-archivo"></small>
                    </div>
                </div>
                <div class="col-md-7 d-flex align-items-end justify-content-center">
                    <div class="seccion-card w-100 d-flex align-items-center justify-content-center mb-0">
                        <button type="button" class="btn-registrar" onclick="registrar()">
                            <i class="fas fa-save"></i> Registrar expediente
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
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>



        $('#select-profesion').select2({
            theme: "bootstrap-5",
            language: { noResults: () => "Búsqueda no encontrada" },
        });

        document.getElementById('documento').addEventListener('change', function () {
            const nombre = this.files[0] ? this.files[0].name : '';
            document.getElementById('nombre-archivo').textContent = nombre;
        });

        function calcularEdad() {
            const fechaNacimiento = document.getElementById('fecha-nacimiento').value;
            const badge = document.getElementById('edad');

            if (!fechaNacimiento) { badge.textContent = '—'; return; }

            const hoy = new Date();
            const cumple = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - cumple.getFullYear();
            const m = hoy.getMonth() - cumple.getMonth();

            if (m < 0 || (m === 0 && hoy.getDate() < cumple.getDate())) edad--;

            if (edad === 0) {
                const meses = Math.floor((hoy - cumple) / (1000 * 60 * 60 * 24 * 30.44));
                badge.textContent = meses + ' meses';
            } else {
                badge.textContent = edad + ' años';
            }
        }

        function registrar() {
            Swal.fire({
                title: 'Registrar expediente',
                text: '¿Confirmas el registro del nuevo paciente?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then(({ value }) => { if (value) nuevoPaciente(); });
        }

        function nuevoPaciente() {

            const campos = {
                nombre:          document.getElementById('nombre-nuevo').value.trim(),
                apellido:        document.getElementById('apellido-nuevo').value.trim(),
                numexpediente:   document.getElementById('numero-expediente-nuevo').value.trim(),
                tipopaciente:    document.getElementById('select-tipopaciente').value,
                fechanacimiento: document.getElementById('fecha-nacimiento').value,
                sexopaciente:    document.getElementById('select-sexo').value,
                estadocivil:     document.getElementById('select-civil').value,
                tipodocumento:   document.getElementById('select-documento').value,
                numdocumento:    document.getElementById('numero-documento').value,
                profesion:       document.getElementById('select-profesion').value,
                telefono:        document.getElementById('telefono').value.trim(),
                celular:         document.getElementById('celular').value.trim(),
                correo:          document.getElementById('correo').value.trim(),
                direccion:       document.getElementById('direccion').value.trim(),
                referido:        document.getElementById('referido').value.trim(),
            };

            const validaciones = [
                { campo: campos.numexpediente,   msg: '# Expediente es requerido' },
                { campo: campos.nombre,          msg: 'El nombre es requerido' },
                { campo: campos.fechanacimiento, msg: 'La fecha de nacimiento es requerida' },
                { campo: campos.sexopaciente,    msg: 'El sexo es requerido' },
                { campo: campos.estadocivil,     msg: 'El estado civil es requerido' },
                { campo: campos.tipodocumento,   msg: 'El tipo de documento es requerido' },
            ];

            for (const v of validaciones) {
                if (!v.campo) { toastr.error(v.msg); return; }
            }

            const formData = new FormData();
            const archivo = document.getElementById('documento');
            if (archivo.files[0]) formData.append('documento', archivo.files[0]);

            Object.entries(campos).forEach(([k, v]) => formData.append(k, v));

            openLoading();

            axios.post(urlAdmin + '/admin/expediente/registro', formData)
                .then(({ data }) => {
                    closeLoading();

                    if (data.success === 1) {
                        Swal.fire({
                            title: 'Expediente duplicado',
                            text: `El expediente "${campos.numexpediente}" ya está registrado.`,
                            type: 'warning',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Entendido'
                        });
                    } else if (data.success === 2) {
                        toastr.success('Expediente registrado correctamente');
                        limpiarFormulario();
                    } else {
                        toastr.error('Ocurrió un error al registrar. Intenta de nuevo.');
                    }

                })
                .catch(() => {
                    closeLoading();
                    toastr.error('Error de conexión. Verifica tu red e intenta de nuevo.');
                });
        }

        function limpiarFormulario() {
            document.getElementById('formulario').reset();
            document.getElementById('edad').textContent = '—';
            document.getElementById('nombre-archivo').textContent = '';
            ['select-tipopaciente', 'select-civil', 'select-documento', 'select-profesion'].forEach(id => {
                document.getElementById(id).selectedIndex = 0;
                $('#' + id).trigger('change');
            });
        }
    </script>
@endsection
