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
    /* Tabla limpia */
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
</style>

<div class="bloque-card">
    <div class="bloque-header">
        <div class="bloque-icon"><i class="fas fa-weight"></i></div>
        <p class="bloque-titulo">Historial de antropometría</p>
    </div>

    <div class="table-responsive">
        <table id="tablaHistAntrop" class="table table-hover dt-tabla">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Creado por</th>
                <th>F.C (lpm)</th>
                <th>T/A (mmHg)</th>
                <th>Peso lb</th>
                <th>Peso kg</th>
                <th>Talla (cm)</th>
                <th class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bloqueAntropSv as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->horaFormat }}</td>
                    <td>{{ $dato->nomusuario }}</td>
                    <td>{{ $dato->frecuencia_cardiaca }}</td>
                    <td>{{ $dato->presion_arterial }}</td>
                    <td>{{ $dato->peso_libra }}</td>
                    <td>{{ $dato->peso_kilo }}</td>
                    <td>{{ $dato->estatura }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary"
                                onclick="verAntropometria({{ $dato->id }})">
                            <i class="fas fa-eye mr-1"></i> Ver
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
        $('#tablaHistAntrop').DataTable(dtConfig({
            columnDefs: [{ type: 'date-euro', targets: 0 }],
            order: [[0, 'desc']],
        }));
    });

    function verAntropometria(id) {
        window.location.href = "{{ url('/admin/vista/visualizar/antropometria/exped') }}/" + id;
    }
</script>
