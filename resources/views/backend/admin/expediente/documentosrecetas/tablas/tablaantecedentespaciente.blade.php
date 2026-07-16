<style>
    .bloque-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
    }
    .bloque-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1rem;
        padding-bottom: 0.625rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .bloque-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e8f4fd;
        color: #1a7abf;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .bloque-titulo {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }
    .form-label-sm {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 3px;
        display: block;
    }

    /* Toggle */
    .switch { position: relative; display: inline-block; width: 36px; height: 20px; margin: 0 6px 0 0; vertical-align: middle; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; inset: 0; background: #ced4da; border-radius: 20px; transition: .25s; }
    .slider::before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .25s; }
    input:checked + .slider { background: #1a7abf; }
    input:checked + .slider::before { transform: translateX(16px); }
    .check-item { display: flex; align-items: center; gap: 6px; padding: 4px 0; font-size: 13px; color: #495057; }
</style>

@php
    $ant = $b1_antecedentes; // alias corto
@endphp

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

{{-- Bloques con checkboxes --}}
@php
    $bloquesCheck = [
        [
            'icon'   => 'fas fa-stethoscope',
            'titulo' => 'Antecedentes médicos',
            'items'  => $b1_arrayAntecedentesMedico,
            'nota'   => ['id' => 'notas_antecedente_medicos', 'valor' => $ant->nota_antecedente_medico ?? ''],
        ],
        [
            'icon'   => 'fas fa-exclamation-triangle',
            'titulo' => 'Complicaciones agudas en diabetes',
            'items'  => $b1_arrayComplicacionAguda,
            'nota'   => ['id' => 'notas_complicacion_diabetes', 'valor' => $ant->nota_complicaciones_diabetes ?? ''],
        ],
        [
            'icon'   => 'fas fa-heartbeat',
            'titulo' => 'Enfermedades crónicas',
            'items'  => $b1_arrayEnfermedadCronicas,
            'nota'   => ['id' => 'notas_enfermedad_cronica', 'valor' => $ant->nota_enfermedades_cronicas ?? ''],
        ],
        [
            'icon'   => 'fas fa-procedures',
            'titulo' => 'Antecedentes quirúrgicos',
            'items'  => $b1_arrayAntecedenteCronicos,
            'nota'   => ['id' => 'notas_antecedente_quirurgico', 'valor' => $ant->nota_antecedentes_quirurgicos ?? ''],
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
                @php $activo = $b1_arrayIdPacienteAntecedente->contains('id_antecedente_medico', $item->id) @endphp
                <div class="col-6 col-md-3">
                    <div class="check-item">
                        <label class="switch">
                            <input type="checkbox" data-valor="{{ $item->id }}" name="arrayCheckAntecedentes[]" {{ $activo ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                        {{ $item->nombre }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">
            <label class="form-label-sm">Notas</label>
            <textarea class="form-control form-control-sm" id="{{ $bloque['nota']['id'] }}" rows="3">{{ $bloque['nota']['valor'] }}</textarea>
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
            $gine = [
                ['id' => 'dato-menarquia',      'label' => 'Menarquía',       'valor' => $ant->menarquia       ?? ''],
                ['id' => 'dato-ciclomenstrual',  'label' => 'Ciclo menstrual', 'valor' => $ant->ciclo_menstrual ?? ''],
                ['id' => 'dato-pap',            'label' => 'PAP',             'valor' => $ant->pap             ?? ''],
                ['id' => 'dato-mamografia',     'label' => 'Mamografía',      'valor' => $ant->mamografia      ?? ''],
            ];
        @endphp

        @foreach($gine as $campo)
            <div class="col-md-3">
                <label class="form-label-sm">{{ $campo['label'] }}</label>
                <input type="text" maxlength="300" class="form-control form-control-sm"
                       id="{{ $campo['id'] }}" value="{{ $campo['valor'] }}">
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
<div class="text-right mb-2">
    <button type="button" class="btn btn-success btn-sm px-4" onclick="guardarAntecedentes()">
        <i class="fas fa-save mr-1"></i> Guardar antecedentes
    </button>
</div>
