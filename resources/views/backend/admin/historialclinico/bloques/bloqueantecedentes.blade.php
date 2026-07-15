@php $ant = $b1_antecedentes; @endphp

<style>
    .bloque-card { background:#fff; border:1px solid #e9ecef; border-radius:10px; padding:1.25rem 1.5rem; margin-bottom:1rem; }
    .bloque-header { display:flex; align-items:center; gap:10px; margin-bottom:1rem; padding-bottom:.625rem; border-bottom:1px solid #f0f0f0; }
    .bloque-icon { width:32px; height:32px; border-radius:8px; background:#e8f4fd; color:#1a7abf; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .bloque-titulo { font-size:13px; font-weight:600; color:#495057; margin:0; }
    .form-label-sm { font-size:12px; font-weight:500; color:#6c757d; margin-bottom:3px; display:block; }
    .switch { position:relative; display:inline-block; width:36px; height:20px; margin:0 6px 0 0; vertical-align:middle; flex-shrink:0; }
    .switch input { opacity:0; width:0; height:0; }
    .slider { position:absolute; cursor:pointer; inset:0; background:#ced4da; border-radius:20px; transition:.25s; }
    .slider::before { position:absolute; content:""; height:14px; width:14px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.25s; }
    input:checked + .slider { background:#1a7abf; }
    input:checked + .slider::before { transform:translateX(16px); }
    .check-item { display:flex; align-items:center; gap:6px; padding:4px 0; font-size:13px; color:#495057; }
</style>

{{-- 1. Datos generales --}}
<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-notes-medical"></i></div>
        <p class="bloque-titulo">Datos generales</p>
    </div>
    <div class="row">
        <div class="col-md-8">
            <label class="form-label-sm">Antecedentes familiares</label>
            <textarea class="form-control form-control-sm" rows="4" id="text-antecedentes-editar">{{ $ant->antecedentes_familiares ?? '' }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label-sm">Tipeo sanguíneo</label>
            <select class="form-control form-control-sm" id="select-tipeo-sanguineo">
                <option value="">Seleccionar...</option>
                @foreach($b1_arrayTipeoSanguineo as $item)
                    <option value="{{ $item->id }}" {{ ($ant && $ant->id_tipeo_sanguineo == $item->id) ? 'selected' : '' }}>
                        {{ $item->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label-sm">Alergias</label>
            <textarea class="form-control form-control-sm" rows="3" id="text-alergias-editar">{{ $ant->alergias ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label-sm">Medicamentos actuales</label>
            <textarea class="form-control form-control-sm" rows="3" id="text-medicamento-actual-editar">{{ $ant->medicamentos_actuales ?? '' }}</textarea>
        </div>
    </div>
</div>

{{-- 2-5. Bloques con checkboxes (estructura idéntica) --}}
@php
    $bloquesCheck = [
        [
            'icon'   => 'fas fa-stethoscope',
            'titulo' => 'Antecedentes médicos',
            'items'  => $b1_arrayAntecedentesMedico,
            'notaId' => 'notas_antecedente_medicos',
            'notaVal'=> $ant->nota_antecedente_medico ?? '',
        ],
        [
            'icon'   => 'fas fa-exclamation-triangle',
            'titulo' => 'Complicaciones agudas en diabetes',
            'items'  => $b1_arrayComplicacionAguda,
            'notaId' => 'notas_complicacion_diabetes',
            'notaVal'=> $ant->nota_complicaciones_diabetes ?? '',
        ],
        [
            'icon'   => 'fas fa-heartbeat',
            'titulo' => 'Enfermedades crónicas',
            'items'  => $b1_arrayEnfermedadCronicas,
            'notaId' => 'notas_enfermedad_cronica',
            'notaVal'=> $ant->nota_enfermedades_cronicas ?? '',
        ],
        [
            'icon'   => 'fas fa-procedures',
            'titulo' => 'Antecedentes quirúrgicos',
            'items'  => $b1_arrayAntecedenteCronicos,
            'notaId' => 'notas_antecedente_quirurgico',
            'notaVal'=> $ant->nota_antecedentes_quirurgicos ?? '',
        ],
    ];
@endphp

@foreach($bloquesCheck as $bloque)
    <div class="bloque-card">
        <div class="bloque-header">
            <div class="bloque-icon"><i class="{{ $bloque['icon'] }}"></i></div>
            <p class="bloque-titulo">{{ $bloque['titulo'] }}</p>
        </div>
        <div class="row">
            @foreach($bloque['items'] as $item)
                <div class="col-6 col-md-3">
                    <div class="check-item">
                        <label class="switch">
                            <input type="checkbox"
                                   data-valor="{{ $item->id }}"
                                   name="arrayCheckAntecedentes[]"
                                {{ $b1_arrayIdPacienteAntecedente->contains('id_antecedente_medico', $item->id) ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                        {{ $item->nombre }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">
            <label class="form-label-sm">Notas</label>
            <textarea class="form-control form-control-sm" id="{{ $bloque['notaId'] }}" rows="3">{{ $bloque['notaVal'] }}</textarea>
        </div>
    </div>
@endforeach

{{-- 6. Otros antecedentes --}}
<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-clipboard-list"></i></div>
        <p class="bloque-titulo">Otros antecedentes</p>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label class="form-label-sm">Antecedentes oftalmológicos</label>
            {{-- ✓ corregido: antes leía del id quirúrgico por error --}}
            <textarea class="form-control form-control-sm" rows="3" id="notas_antecedente_oftamologico">{{ $ant->antecedentes_oftalmologicos ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label-sm">Antecedentes deportivos</label>
            <textarea class="form-control form-control-sm" rows="3" id="notas_antecedente_deportivos">{{ $ant->antecedentes_deportivos ?? '' }}</textarea>
        </div>
    </div>
</div>

{{-- 7. Antecedentes ginecológicos --}}
<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-venus"></i></div>
        <p class="bloque-titulo">Antecedentes ginecológicos</p>
    </div>
    <div class="row">
        @php
            $camposGine = [
                ['id' => 'dato-menarquia',      'label' => 'Menarquía',       'valor' => $ant->menarquia       ?? ''],
                ['id' => 'dato-ciclomenstrual',  'label' => 'Ciclo menstrual', 'valor' => $ant->ciclo_menstrual ?? ''],
                ['id' => 'dato-pap',             'label' => 'PAP',             'valor' => $ant->pap             ?? ''],
                ['id' => 'dato-mamografia',      'label' => 'Mamografía',      'valor' => $ant->mamografia      ?? ''],
            ];
        @endphp
        @foreach($camposGine as $c)
            <div class="col-md-3">
                <label class="form-label-sm">{{ $c['label'] }}</label>
                <input type="text" maxlength="300" class="form-control form-control-sm"
                       id="{{ $c['id'] }}" value="{{ $c['valor'] }}">
            </div>
        @endforeach
    </div>
    <div class="mt-3">
        <label class="form-label-sm">Otros detalles</label>
        <textarea class="form-control form-control-sm" id="otros-detalles" rows="3"
                  placeholder="Otros detalles">{{ $ant->otros ?? '' }}</textarea>
    </div>
</div>

{{-- Botón guardar --}}
@can('boton.guardar.antecedentes')
    <div class="text-right mb-3">
        <button type="button" class="btn btn-success btn-sm px-4" onclick="guardarAntecedentes()">
            <i class="fas fa-save mr-1"></i> Guardar antecedentes
        </button>
    </div>
@endcan

<script>
    function guardarAntecedentes() {

        /* Checkboxes marcados */
        var datosCheckboxes = [];
        document.querySelectorAll('input[name="arrayCheckAntecedentes[]"]').forEach(function (cb) {
            if (cb.checked) datosCheckboxes.push({ estado: true, valorAdicional: cb.dataset.valor });
        });

        /* Leer campos — cada var mapea 1:1 con su id en el DOM */
        var campos = {
            textAntecedenteFami:        'text-antecedentes-editar',
            textAlergia:                'text-alergias-editar',
            textMedicamento:            'text-medicamento-actual-editar',
            selectSanguineo:            'select-tipeo-sanguineo',
            notaAnteceMedico:           'notas_antecedente_medicos',
            notaCompliDiabete:          'notas_complicacion_diabetes',
            notaEnfermCronica:          'notas_enfermedad_cronica',
            notaAnteceQuirur:           'notas_antecedente_quirurgico',
            notaAnteceOftamo:           'notas_antecedente_oftamologico',  // ← key corregido
            notaAnteceDeportivo:        'notas_antecedente_deportivos',
            datoMenarquia:              'dato-menarquia',
            datoCicloMenstr:            'dato-ciclomenstrual',
            datoPap:                    'dato-pap',
            datoMamografia:             'dato-mamografia',
            otrosDetalles:              'otros-detalles',
        };

        /* Validar que todos los elementos existen antes de enviar */
        for (var key in campos) {
            if (!document.getElementById(campos[key])) {
                console.warn('Campo no encontrado:', campos[key]);
            }
        }

        openLoading();

        var formData = new FormData();
        formData.append('idpaciente',   {{ $b1_infoPaciente->id }});
        formData.append('datocheckbox', JSON.stringify(datosCheckboxes));

        for (var key in campos) {
            var el = document.getElementById(campos[key]);
            formData.append(key, el ? el.value : '');
        }

        axios.post(urlAdmin + '/admin/historial/antecedente/actualizacion', formData)
            .then(function (response) {
                closeLoading();
                if (response.data.success === 1) {
                    toastr.success('Actualizado correctamente');
                } else {
                    toastr.error('Error al registrar');
                }
            })
            .catch(function () {
                toastr.error('Error al registrar');
                closeLoading();
            });
    }
</script>
