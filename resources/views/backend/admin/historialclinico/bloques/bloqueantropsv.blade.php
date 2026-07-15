{{-- ── Bloque: Historial de Antropometría ── --}}

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="font-weight-bold mb-0">
        <i class="fas fa-weight mr-1 text-primary"></i> Historial de Antropometría
    </h5>
    @if($btnAntrosV == 0)
        @can('boton.guardar.antropometria')
            <button type="button" class="btn btn-success btn-sm" onclick="vistaNuevaAntropologia()">
                <i class="fas fa-plus mr-1"></i> Nueva Antropometría
            </button>
        @endcan
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tablaAntropSvDt" class="table table-bordered table-striped table-sm mb-0">
            <thead class="thead-light">
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Creado por</th>
                <th>F.C</th>
                <th>T/A</th>
                <th>Peso (lb)</th>
                <th>Peso (kg)</th>
                <th>Talla</th>
                <th>Opciones</th>
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
                    <td>
                        <button type="button" class="btn btn-primary btn-xs"
                                onclick="vistaVisualizarAntropologia({{ $dato->id }})">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="button" class="btn btn-danger btn-xs"
                                onclick="confirmarBorrarAntropometria({{ $dato->id }})">
                            <i class="fas fa-trash"></i> Borrar
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
        initDataTable('#tablaAntropSvDt', { columnDefs: [{ type: 'date-euro', targets: 0 }] });
    });

    function vistaVisualizarAntropologia(idantrop) {
        window.location.href = "{{ url('/admin/vista/visualizar/antropometria') }}/" + idantrop;
    }

    function confirmarBorrarAntropometria(id) {
        Swal.fire({
            title: '¿Borrar antropometría?',
            type: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then(function (result) {
            if (result.value) {
                borrarAntropometria(id);
            }
        });
    }

    function borrarAntropometria(id) {
        openLoading();
        var formData = new FormData();
        formData.append('id', id);

        axios.post(urlAdmin + '/admin/historial/borrar/antropometria', formData)
            .then(function (response) {
                closeLoading();
                if (response.data.success === 1) {
                    toastr.success('Borrado correctamente');
                    recargarTablaAntropometria();
                } else {
                    toastr.error('Error al borrar');
                }
            })
            .catch(function () { toastr.error('Error al borrar'); closeLoading(); });
    }
</script>
