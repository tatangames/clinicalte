<style>
    /* ── Badge estado ── */
    .badge-denegado {
        background: #fde8e6;
        color: #c0392b;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    /* ── Nota truncada con tooltip ── */
    .nota-cell {
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: default;
    }
</style>

<div class="d-flex align-items-center mb-3" style="gap:8px;">
    <span style="
        width:30px; height:30px; border-radius:8px;
        background:#fde8e6; color:#c0392b;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:14px; flex-shrink:0;">
        <i class="fas fa-ban"></i>
    </span>
    <span style="font-size:13px; font-weight:700; color:#343a40;">
        Recetas Denegadas
    </span>
    <span style="
        margin-left:auto;
        background:#fde8e6; color:#c0392b;
        padding:2px 10px; border-radius:20px;
        font-size:11px; font-weight:700;">
        {{ count($arrayRecetas) }} {{ count($arrayRecetas) == 1 ? 'registro' : 'registros' }}
    </span>
</div>

<div class="card" style="border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08);">
    <div class="card-body p-0">
        <table id="tabla" class="table table-bordered table-striped table-sm mb-0"
               style="font-size:13px;">
            <thead style="background:#f8f9fa;">
            <tr>
                <th style="width:10%;">Fecha receta</th>
                <th style="width:10%;">Fecha denegada</th>
                <th style="width:18%;">Recetado por</th>
                <th style="width:18%;">Paciente</th>
                <th style="width:22%;">Motivo de denegación</th>
                <th style="width:10%; text-align:center;">Opciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($arrayRecetas as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->fechaEstadoFormat }}</td>
                    <td>{{ $dato->nombreuser }}</td>
                    <td>{{ $dato->nombrepaciente }}</td>
                    <td>
                            <span class="nota-cell" title="{{ $dato->nota_denegada }}">
                                {{ $dato->nota_denegada ?? '—' }}
                            </span>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button type="button"
                                class="btn btn-info btn-xs"
                                style="color:white;"
                                onclick="imprimirRecetaMedica({{ $dato->id }})"
                                title="Imprimir receta">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </td>
                </tr>
            @empty
                {{-- DataTables muestra su propio mensaje vacío --}}
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    closeLoading();

    $(function () {
        $("#tabla").DataTable(dtConfig({
            columnDefs: [
                { type: 'date-euro', targets: [0, 1] },
                { orderable: false, targets: 5 }
            ]
        }));
    });

    function imprimirRecetaMedica(idreceta) {
        window.open("{{ URL::to('admin/reporte/receta/paciente') }}/" + idreceta);
    }
</script>
