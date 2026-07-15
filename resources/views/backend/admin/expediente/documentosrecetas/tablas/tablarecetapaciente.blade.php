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

    /* Badges de estado */
    .estado-badge {
        display: inline-block;
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .estado-pendiente  { background: #fff3cd; color: #856404; }
    .estado-procesada  { background: #d1e7dd; color: #0a3622; }
    .estado-denegada   { background: #f8d7da; color: #842029; }
</style>

<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-prescription"></i></div>
        <p class="bloque-titulo">Historial de recetas</p>
    </div>

    <div class="table-responsive">
        <table id="tablaHistRecetas" class="table table-hover dt-tabla">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Creado por</th>
                <th>Diagnóstico</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($arrayRecetas as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->nombreusuario }}</td>
                    <td>{{ $dato->descripcion_general }}</td>
                    <td class="text-center">
                        @if($dato->estado == 1)
                            <span class="estado-badge estado-pendiente">Pendiente</span>
                        @elseif($dato->estado == 2)
                            <span class="estado-badge estado-procesada">Procesada</span>
                        @else
                            <span class="estado-badge estado-denegada">Denegada</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-info"
                                style="color:#fff" onclick="imprimirReceta({{ $dato->id }})">
                            <i class="fas fa-print mr-1"></i> Imprimir
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('#tablaHistRecetas').DataTable(dtConfig({
            columnDefs: [{ type: 'date-euro', targets: 0 }],
            order: [[0, 'desc']],
        }));
    });

    function imprimirReceta(id) {
        window.open("{{ URL::to('admin/reporte/receta/paciente') }}/" + id);
    }
</script>
