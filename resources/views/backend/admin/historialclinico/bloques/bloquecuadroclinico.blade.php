{{-- ── Bloque: Historial de Cuadros Clínicos ── --}}

<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="font-weight-bold mb-0">
        <i class="fas fa-file-medical mr-1 text-info"></i> Historial de Cuadros Clínicos
    </h5>
    @if($haycuadro == 0)
        @can('boton.nuevo.historial.clinico')
            <button type="button" class="btn btn-success btn-sm" onclick="modalCuadroClinico()">
                <i class="fas fa-plus mr-1"></i> Nuevo Cuadro Clínico
            </button>
        @endcan
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tableCuadroClinicoDt" class="table table-bordered table-striped table-sm mb-0">
            <thead class="thead-light">
            <tr>
                <th style="width:10%">Fecha</th>
                <th style="width:15%">Tipo diagnóstico</th>
                <th style="width:12%">Creado por</th>
                <th style="width:8%">Opciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bloqueCuadroClinico as $dato)
                <tr>
                    <td>{{ $dato->fechaFormat }}</td>
                    <td>{{ $dato->nombreDiagnostico }}</td>
                    <td>{{ $dato->nombreUsuario }}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-xs"
                                onclick="informacionCuadroClinico({{ $dato->id }})">
                            <i class="fas fa-edit"></i> Editar
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
        initDataTable('#tableCuadroClinicoDt', { columnDefs: [{ type: 'date-euro', targets: 0 }] });
    });

    function modalCuadroClinico() {
        document.getElementById('select-tipo-diagnostico').selectedIndex = 0;
        $('#select-tipo-diagnostico').trigger('change');
        $('#modalNuevoHistoClinico').modal({ backdrop: 'static', keyboard: false });
    }

    function guardarNuevoCuadroClinico() {
        var tipoDiagnostico = document.getElementById('select-tipo-diagnostico').value;
        if (!tipoDiagnostico) { toastr.error('Tipo diagnóstico es requerido'); return; }

        var editorData = varGlobalEditorCuadro.getData();
        if (!editorData.trim()) { toastr.error('Descripción es requerida'); return; }

        openLoading();
        var formData = new FormData();
        formData.append('idconsulta',  {{ $idconsulta }});
        formData.append('diagnostico', tipoDiagnostico);
        formData.append('descripcion', editorData);

        axios.post(urlAdmin + '/admin/historial/nuevo/historialclinico', formData)
            .then(function (response) {
                closeLoading();
                if (response.data.success === 1) {
                    toastr.success('Registrado correctamente');
                    varGlobalEditorCuadro.setData('');
                    $('#modalNuevoHistoClinico').modal('hide');
                    recargarTablaCuadroClinico();
                } else {
                    toastr.error('Error al registrar');
                }
            })
            .catch(function () { toastr.error('Error al registrar'); closeLoading(); });
    }

    function informacionCuadroClinico(id) {
        openLoading();
        axios.post(urlAdmin + '/admin/historial/informacion/historialclinico', { id: id })
            .then(function (response) {
                closeLoading();
                if (response.data.success !== 1) { toastr.error('Información no encontrada'); return; }

                var d = response.data;
                $('#idCuadroClinico-editar').val(d.info.id);

                var sel = document.getElementById('select-tipo-diagnostico-editar');
                sel.options.length = 0;
                $.each(d.arraydiagnostico, function (k, v) {
                    sel.add(new Option(v.nombre, v.id, false, d.info.id_diagnostico == v.id));
                });
                $('#select-tipo-diagnostico-editar').trigger('change');

                varGlobalEditorCuadroEditar.setData(d.info.descripcion);
                $('#modalEditarHistoClinico').modal({ backdrop: 'static', keyboard: false });
            })
            .catch(function () { closeLoading(); toastr.error('Información no encontrada'); });
    }

    function actualizarCuadroClinico() {
        var tipoDiagnostico = document.getElementById('select-tipo-diagnostico-editar').value;
        if (!tipoDiagnostico) { toastr.error('Tipo diagnóstico es requerido'); return; }

        var editorData = varGlobalEditorCuadroEditar.getData();
        if (!editorData.trim()) { toastr.error('Descripción es requerida'); return; }

        openLoading();
        var formData = new FormData();
        formData.append('idCuadro',    document.getElementById('idCuadroClinico-editar').value);
        formData.append('diagnostico', tipoDiagnostico);
        formData.append('descripcion', editorData);

        axios.post(urlAdmin + '/admin/historial/actualizar/historialclinico', formData)
            .then(function (response) {
                closeLoading();
                if (response.data.success === 1) {
                    toastr.success('Actualizado correctamente');
                    varGlobalEditorCuadroEditar.setData('');
                    $('#modalEditarHistoClinico').modal('hide');
                    recargarTablaCuadroClinico();
                } else {
                    toastr.error('Error al actualizar');
                }
            })
            .catch(function () { toastr.error('Error al actualizar'); closeLoading(); });
    }
</script>
