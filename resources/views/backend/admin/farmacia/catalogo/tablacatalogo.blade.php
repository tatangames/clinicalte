<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 10%">Código</th>
                                <th style="width: 20%">Nombre</th>
                                <th style="width: 10%">Línea</th>
                                <th style="width: 10%">Sub Línea</th>
                                <th style="width: 6%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($arrayCatalogo as $dato)
                                <tr>
                                    <td>{{ $dato->codigo_articulo }}</td>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->linea?->nombre }}</td>
                                    <td>{{ $dato->subLinea?->nombre }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="infoEditar({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
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


