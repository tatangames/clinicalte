<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 15%">Fecha</th>
                                <th style="width: 20%">Factura</th>
                                <th style="width: 20%">Proveedor</th>
                                <th style="width: 20%">Fuente Financiamiento</th>
                                <th style="width: 15%">Tipo Factura</th>
                                <th style="width: 10%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($arrayEntradas as $dato)
                                <tr>
                                    <td>{{ $dato->fecha->format('d-m-Y') }}</td>
                                    <td>{{ $dato->numero_factura }}</td>
                                    <td>{{ $dato->proveedor?->nombre }}</td>
                                    <td>{{ $dato->fuenteFinanciamiento?->nombre }}</td>
                                    <td>{{ $dato->tipoFactura?->nombre }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="infoEditar({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Ver"></i>&nbsp; Editar
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
