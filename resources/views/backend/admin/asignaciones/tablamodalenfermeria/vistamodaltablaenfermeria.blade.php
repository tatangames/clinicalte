<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tablaEnfermeria" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 15%">HORA</th>
                                <th style="width: 20%">PACIENTE</th>
                                <th style="width: 15%">RAZON USO</th>
                                <th style="width: 15%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayTablaEnfermeria as $dato)
                                <tr>
                                    <td>{{ $dato->horaFormat }}</td>
                                    <td>{{ $dato->nombrepaciente }}</td>
                                    <td>{{ $dato->razonUso }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="infoAsignarAsalaPaciente({{ $dato->id }})">
                                          Asignar
                                        </button>

                                        <button type="button" class="btn btn-danger btn-xs" onclick="infoModalEliminarPaciente({{ $dato->id }})">
                                          Eliminar
                                        </button>

                                        <button type="button" class="btn btn-xs" style="color: white; background-color: #ffa616" onclick="infoModalEditarSalas({{ $dato->id }})">
                                          Editar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(function () { initDataTable("#tablaEnfermeria"); });
</script>
