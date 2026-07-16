{{-- ── Bloque: Historial de Recetas ── --}}

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="font-weight-bold mb-0">
        <i class="fas fa-pills mr-1 text-success"></i> Historial de Recetas
    </h5>
    @if($existeReceta == 0)
        @can('boton.nueva.receta')
            <button type="button" class="btn btn-success btn-sm" onclick="vistaNuevaReceta()">
                <i class="fas fa-plus mr-1"></i> Nueva Receta
            </button>
        @endcan
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tableRecetasDt" class="table table-bordered table-striped table-sm mb-0">
            <thead class="thead-light">
            <tr>
                <th>Fecha</th>
                <th>Creado por</th>
                <th>Diagnóstico</th>
                <th>Estado</th>
                <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($arrayRecetas as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->nombreusuario }}</td>
                    <td>{{ $dato->descripcion_general }}</td>
                    <td>
                        @if($dato->estado == 1)
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($dato->estado == 2)
                            <span class="badge badge-success">Procesada</span>
                        @else
                            <span class="badge badge-danger">Denegada</span>
                        @endif
                    </td>
                    <td>
                        @if($dato->estado == 1)
                            <button type="button" class="btn btn-warning btn-xs"
                                    onclick="infoEditarReceta({{ $dato->id }})">
                                <i class="fas fa-edit"></i> Editar
                            </button>

                          <!-- UNICAMENTE SI ESTA PENDIENTE -->
                            <button type="button" class="btn btn-danger btn-xs"
                                        onclick="confirmarBorrarReceta({{ $dato->id }})">
                                    <i class="fas fa-trash-alt"></i> Borrar
                           </button>

                        @else
                            <button type="button" class="btn btn-info btn-xs"
                                    onclick="infoEditarReceta({{ $dato->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        @endif

                        <button type="button" class="btn btn-success btn-xs"
                                onclick="imprimirRecetaMedica({{ $dato->id }})">
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
        $.fn.dataTable.ext.type.order['date-dmy-pre'] = function (d) {
            var p = d.split('-');
            return p[2] + p[1] + p[0];
        };
        initDataTable('#tableRecetasDt', { columnDefs: [{ type: 'date-dmy', targets: 0 }] });
    });

    function imprimirRecetaMedica(idreceta) {
        window.open("{{ URL::to('admin/reporte/receta/paciente') }}/" + idreceta);
    }

    function confirmarBorrarReceta(idreceta) {
        Swal.fire({
            title: '¿Borrar receta?',
            text: 'Esta acción eliminará la receta permanentemente.',
            type: 'warning',
            showCancelButton: true,
            allowOutsideClick: false,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.value) {
                borrarReceta(idreceta);
            }
        });
    }

    function borrarReceta(idreceta) {
        openLoading();
        var formData = new FormData();
        formData.append('idreceta', idreceta);

        axios.post(urlAdmin + '/admin/recetas/borrar', formData)
            .then(function (response) {
                closeLoading();
                if (response.data.success === 1) {
                    toastr.success('Receta eliminada');
                    $('#tablaRecetas').load("{{ URL::to('/admin/historial/bloque/recetas') }}/" + IDCONSULTA);
                } else {
                    toastr.error('No se pudo eliminar la receta');
                }
            })
            .catch(function () {
                closeLoading();
                toastr.error('Error al eliminar la receta');
            });
    }
</script>
