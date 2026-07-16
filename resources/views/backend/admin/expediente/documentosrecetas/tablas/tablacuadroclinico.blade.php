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
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .bloque-icon {
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
    .bloque-titulo {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }
    .dt-tabla thead th {
        background: #f8f9fa;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 2px solid #e9ecef !important;
        white-space: nowrap;
    }
    .dt-tabla tbody tr:hover { background: #f5faff; }
    .dt-tabla tbody td { font-size: 13px; vertical-align: middle; }
    /* La descripción puede tener HTML largo — limitar visualmente */
    .desc-cell { max-width: 420px; }
</style>

<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-file-medical-alt"></i></div>
        <p class="bloque-titulo">Cuadro clínico</p>
    </div>

    <div class="table-responsive">
        <table id="tablaHistCuadro" class="table table-hover dt-tabla">
            <thead>
            <tr>
                <th style="width:10%">Fecha</th>
                <th style="width:15%">Tipo diagnóstico</th>
                <th style="width:13%">Creado por</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bloqueCuadroClinico as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->nombreDiagnostico }}</td>
                    <td>{{ $dato->doctor }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('#tablaHistCuadro').DataTable(dtConfig({
            columnDefs: [
                { type: 'date-euro', targets: 0 },
                { orderable: false, targets: 3 },  // descripción HTML no ordenable
            ],
            order: [[0, 'desc']],
        }));
    });
</script>
