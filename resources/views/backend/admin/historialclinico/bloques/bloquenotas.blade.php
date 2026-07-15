{{-- ── Bloque: Historial de Notas ── --}}

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="font-weight-bold mb-0">
        <i class="fas fa-sticky-note mr-1 text-warning"></i> Historial de Notas
    </h5>
    <button type="button" class="btn btn-success btn-sm" onclick="vistaNuevaNota()">
        <i class="fas fa-plus mr-1"></i> Nueva Nota
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tableNotasDt" class="table table-bordered table-striped table-sm mb-0">
            <thead class="thead-light">
            <tr>
                <th style="width: 30%">Fecha</th>
                <th style="width: 10%">Opciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($arrayNotas as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-xs"
                                onclick="informacionEditarNota({{ $dato->id }})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-xs"
                                onclick="modalBorrarNota({{ $dato->id }})">
                            <i class="fas fa-trash"></i> Borrar
                        </button>
                        <button type="button" class="btn btn-success btn-xs"
                                onclick="generarReporteNota({{ $dato->id }})">
                            <i class="fas fa-print"></i> Imprimir
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
        // Ordenación por fecha d-m-Y
        $.fn.dataTable.ext.type.order['date-dmy-pre'] = function (d) {
            var p = d.split('-');
            return p[2] + p[1] + p[0];
        };

        initDataTable('#tableNotasDt', { columnDefs: [{ type: 'date-dmy', targets: 0 }] });
    });
</script>
