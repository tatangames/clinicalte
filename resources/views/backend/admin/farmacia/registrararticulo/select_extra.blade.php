{{--
    Partial: _select_extra.blade.php
    Uso:
        @include('backend.partials._select_extra', [
            'selectId'  => 'select-envase',
            'items'     => $arrayEnvase,
            'tipoModal' => 1,
        ])
--}}
<div class="input-group">
    <select class="form-control" id="{{ $selectId }}">
        <option value="">Seleccionar Opción</option>
        @foreach($items as $item)
            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
        @endforeach
    </select>
    <button type="button" class="btn"
            style="background-color:#ffa616"
            onclick="verModalExtraInformacion({{ $tipoModal }})">
        <i class="fas fa-plus" style="color:white"></i>
    </button>
</div>
