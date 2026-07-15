/**
 * Configuración base compartida para todos los DataTables del sistema.
 * Uso: $("#miTabla").DataTable(dtConfig({ columnDefs: [...] }));
 */
function dtConfig(overrides = {}) {
    return Object.assign({
        paging:      true,
        lengthChange: true,
        searching:   true,
        ordering:    true,
        info:        true,
        autoWidth:   false,
        responsive:  true,
        pagingType:  'full_numbers',
        lengthMenu:  [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todo']],
        language: {
            sProcessing:   'Procesando...',
            sLengthMenu:   'Mostrar _MENU_ registros',
            sZeroRecords:  'No se encontraron resultados',
            sEmptyTable:   'Sin datos disponibles',
            sInfo:         'Registros del _START_ al _END_ de _TOTAL_',
            sInfoEmpty:    'Registros del 0 al 0 de 0',
            sInfoFiltered: '(filtrado de _MAX_ registros)',
            sSearch:       'Buscar:',
            sLoadingRecords: 'Cargando...',
            oPaginate: {
                sFirst:    'Primero',
                sLast:     'Último',
                sNext:     'Siguiente',
                sPrevious: 'Anterior',
            },
        },
    }, overrides);
}
