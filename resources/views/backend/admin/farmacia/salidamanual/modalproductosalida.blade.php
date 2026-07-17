{{-- ── Bloque parcial: lotes disponibles del producto seleccionado ── --}}

<style>
    .lote-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        margin-bottom: .85rem;
        overflow: hidden;
        transition: box-shadow .15s;
    }
    .lote-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.11); }

    .lote-card-head {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 8px;
        padding: .65rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-size: 12px;
    }
    .lote-meta { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }

    .lote-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 2px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .lote-pill-blue   { background: #e8f1fb; color: #1a6fc4; }
    .lote-pill-green  { background: #e6f5ef; color: #1a8a5a; }
    .lote-pill-amber  { background: #fef3c7; color: #d97706; }
    .lote-pill-gray   { background: #f1f3f5; color: #495057; }

    .lote-card-body {
        padding: .8rem 1rem;
        display: flex; align-items: center; gap: 12px;
    }
    .lote-input-label {
        font-size: 11px; font-weight: 700;
        color: #6c757d; text-transform: uppercase;
        letter-spacing: .04em; white-space: nowrap;
    }
    .lote-input {
        width: 140px;
        border-radius: 7px !important;
        border: 1px solid #dee2e6 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        transition: border-color .15s, box-shadow .15s;
    }
    .lote-input:focus {
        border-color: #1a6fc4 !important;
        box-shadow: 0 0 0 3px rgba(26,111,196,.12) !important;
        outline: none;
    }

    /* Stock disponible visual */
    .lote-stock {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 600; color: #1a8a5a;
    }
    .lote-stock-zero { color: #c0392b; }

    /* Sin existencias */
    .lote-empty {
        text-align: center; padding: 2rem 1rem; color: #adb5bd;
    }
    .lote-empty i { font-size: 1.8rem; display: block; margin-bottom: .4rem; }
</style>

@if($conteo === 0)

    <div class="lote-empty">
        <i class="fas fa-box-open"></i>
        Sin existencias disponibles para este producto.
    </div>

@else

    <div style="margin-bottom:.5rem; font-size:11px; font-weight:700;
                color:#6c757d; text-transform:uppercase; letter-spacing:.05em;">
        {{ count($resultado) }} {{ count($resultado) == 1 ? 'lote disponible' : 'lotes disponibles' }} — ingresa la cantidad a retirar por lote
    </div>

    {{-- ✅ $resultado en lugar de $arraySalidas --}}
    @foreach($resultado as $dato)
        <div class="lote-card">

            {{-- Encabezado con metadatos del lote --}}
            <div class="lote-card-head">
                <div class="lote-meta">

                    <span class="lote-pill lote-pill-gray">
                        <i class="fas fa-barcode" style="font-size:9px"></i>
                        Lote: <strong>{{ $dato->lote }}</strong>
                    </span>

                    <span class="lote-pill lote-pill-blue">
                        <i class="fas fa-file-invoice" style="font-size:9px"></i>
                        Factura: {{ $dato->numero_factura }}
                    </span>

                    <span class="lote-pill lote-pill-amber">
                        <i class="fas fa-calendar-times" style="font-size:9px"></i>
                        Vence: {{ $dato->fechaVencimiento }}
                    </span>

                    <span class="lote-pill lote-pill-gray">
                        <i class="fas fa-calendar-plus" style="font-size:9px"></i>
                        Entrada: {{ $dato->fechaEntrada }}
                    </span>

                    @if($dato->precio)
                        <span class="lote-pill lote-pill-gray">
                            <i class="fas fa-tag" style="font-size:9px"></i>
                            {{ $dato->precio }}
                        </span>
                    @endif

                </div>

                {{-- ✅ stockReal en lugar de cantidad --}}
                <span class="lote-stock">
                    <i class="fas fa-boxes" style="font-size:11px"></i>
                    {{ $dato->stockReal }} disponibles
                </span>
            </div>

            {{-- Input de cantidad --}}
            <div class="lote-card-body">
                <span class="lote-input-label">Cantidad a retirar:</span>
                <input class="form-control lote-input"
                       name="arraysalida[]"
                       type="number"
                       min="1"
                       max="{{ $dato->stockReal }}"
                       step="1"
                       placeholder="0"
                       value=""
                       data-identrada="{{ $dato->identradadetalle }}"
                       data-nombremedi="{{ $dato->nombre }}"
                       data-maxcantidad="{{ $dato->stockReal }}"
                       data-stock="{{ $dato->stockReal }}"
                       data-fechavencimiento="{{ $dato->fechaVencimiento }}"
                       data-fechaentrada="{{ $dato->fechaEntrada }}"
                       data-lote="{{ $dato->lote }}"
                       oninput="verificarInputCantidad(this)"
                       onkeyup="this.value=this.value.replace(/[^\d]/,'')">
            </div>

        </div>
    @endforeach

@endif

<script>
    $(document).ready(function () {
        closeLoading();

        @if($conteo > 0)
        document.getElementById('btnAgregarFila').style.display = 'block';
        @else
        document.getElementById('btnAgregarFila').style.display = 'none';
        @endif
    });

    function verificarInputCantidad(el) {
        var valor       = parseInt(el.value);
        var maxCantidad = parseInt(el.getAttribute('data-maxcantidad'));

        if (!isNaN(valor) && valor > maxCantidad) {
            toastr.info('Máximo disponible: ' + maxCantidad + ' unidades');
            el.value = maxCantidad;
        }
    }
</script>
