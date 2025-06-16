var tabla;

$(document).ready(function () {
    Init();
});

function Init() {
    listar(); // Inicializar la tabla con DataTables
}

// Función listar
function listar() {
    tabla = $('#proveedoresTable').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f>>>><"col-md-12"rt><"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm mr-1' },
            { extend: 'csv', className: 'btn btn-secondary btn-sm mr-1' },
            { extend: 'excel', className: 'btn btn-secondary btn-sm mr-1' },
            { extend: 'print', className: 'btn btn-secondary btn-sm' }
        ],
        language: {
            paginate: { 
                previous: '<svg width="1em" height="1em" viewBox="0 0 16 16" class="fa fa-chevron-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>', 
                next: '<svg width="1em" height="1em" viewBox="0 0 16 16" class="fa fa-chevron-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>' 
            },
            info: "Mostrando página _PAGE_ de _PAGES_",
            search: '<svg width="1em" height="1em" viewBox="0 0 16 16" class="fa fa-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1-1.414 0l-3.263-3.263a1 1 0 1 1 1.414-1.414l3.263 3.263a1 1 0 0 1 0 1.414z"/><path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zm0 1a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13z"/></svg>',
            searchPlaceholder: "Buscar...",
            lengthMenu: "Mostrar _MENU_ registros",
        },
        lengthMenu: [10, 20, 50],
        pageLength: 10,
        ajax: {
            url: '/documenta/controlador/ProveedoresController.php?op=listarProveedores',
            type: "GET",
            dataType: "json",
            dataSrc: 'proveedores',  // Asegurado
            error: function (e) {
                console.error("Error al cargar los datos: ", e.responseText);
            }
        },
        columns: [
            { "data": "RUC" },
            { "data": "companyName" },
            { "data": "contactEmailBusiness" },
            { 
                "data": "porcentaje_subidos",
                "render": function(data, type, row) {
                    return `
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100">${data}%</div>
                        </div>`;
                },
                "className": "porcentaje-subidos",
                "orderable": false
            },
            { 
                "data": "porcentaje_aprobados",
                "render": function(data, type, row) {
                    return `
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100">${data}%</div>
                        </div>`;
                },
                "className": "porcentaje-aprobados",
                "orderable": false
            },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button class="btn btn-primary btn-sm btn-ver-documentos" data-id="${row.id}" data-nombre="${row.companyName}">
                            <i class="fa fa-eye"></i> Ver Documentos
                        </button>`;
                },
                "orderable": false,
                "searchable": false
            }
        ],
        rowCallback: function(row, data) {
            $(row).attr('data-id', data.id);
        },
        responsive: true,
        destroy: true,
        order: [[1, "asc"]]
    });
}

// Cuando el administrador hace clic en "Ver Documentos"
$(document).on('click', '.btn-ver-documentos', function () {
    var proveedorId = $(this).data('id');
    var proveedorNombre = $(this).data('nombre');
    $('#proveedorNombre').text(proveedorNombre);
    cargarDocumentosProveedor(proveedorId);
    $('#modalDocumentosProveedor').modal('show');
});

// Cargar los documentos subidos por el proveedor
function cargarDocumentosProveedor(proveedorId) {
    $.ajax({
        url: '/documenta/controlador/ProveedoresController.php?op=documentosProveedor',
        method: 'POST',
        data: { proveedor_id: proveedorId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                var html = '<ul class="list-group">';
                data.documentos.forEach(function (doc) {
                    var documentName = doc.document_name ? doc.document_name : "Nombre no disponible";
                    var stateName = doc.document_state ? doc.document_state : "Estado desconocido";
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center" id="documento_${doc.document_id}">
                            <div>
                                <p class="mb-1"><strong>${documentName}</strong> (${stateName})</p>
                                <p class="mb-1"><a href="${doc.documentPath}" target="_blank">${doc.originalFileName}</a></p>
                            </div>`;
                    
                    // Botones de acción según estado
                    html += '<div class="document-action-buttons">';
                    if (doc.state_id == 1) {  // Estado Subido
                        html += `
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-success btn-aprobar" data-id="${doc.document_id}" data-proveedor="${proveedorId}" title="Aprobar"><i class="fa fa-check-circle"></i></button>
                                <button class="btn btn-warning btn-solicitar-correccion" data-id="${doc.document_id}" data-proveedor="${proveedorId}" title="Solicitar Corrección"><i class="fa fa-pencil-square"></i></button>
                                <button class="btn btn-danger btn-rechazar" data-id="${doc.document_id}" data-proveedor="${proveedorId}" title="Rechazar"><i class="fa fa-window-close"></i></button>
                            </div>`;
                    } else {
                        html += `
                            <button class="btn btn-warning btn-corregir btn-sm" data-id="${doc.document_id}" data-proveedor="${proveedorId}" title="Corregir">
                                <i class="fa fa-pencil-square-o"></i>
                            </button>`;
                    }

                    html += '</div>'; // cierre de document-action-buttons
                    html += `</li>`;
                });
                html += '</ul>';
                $('#documentosProveedorContainer').html(html);
            } else {
                $('#documentosProveedorContainer').html('<p class="text-danger">No se pudieron cargar los documentos.</p>');
            }
        },
        error: function (e) {
            console.error("Error al cargar los documentos: ", e.responseText);
            $('#documentosProveedorContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
        }
    });
}

// Aprobar documento
$(document).on('click', '.btn-aprobar', function () {
    var documentId = $(this).data('id');
    var proveedorId = $(this).data('proveedor');
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este documento será aprobado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, 2, proveedorId);  // Estado: aprobado
        }
    });
});

// Solicitar corrección
$(document).on('click', '.btn-solicitar-correccion', function () {
    var documentId = $(this).data('id');
    var proveedorId = $(this).data('proveedor');
    Swal.fire({
        title: '¿Solicitar corrección?',
        text: "Este documento será marcado para corrección.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, solicitar corrección',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, 4, proveedorId);  // Estado: por corregir
        }
    });
});

// Rechazar documento
$(document).on('click', '.btn-rechazar', function () {
    var documentId = $(this).data('id');
    var proveedorId = $(this).data('proveedor');
    Swal.fire({
        title: '¿Rechazar documento?',
        text: "Este documento será rechazado.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, 3, proveedorId);  // Estado: rechazado
        }
    });
});

// Corregir documento - muestra los 3 botones de acción sin cerrar el modal
$(document).on('click', '.btn-corregir', function () {
    var documentId = $(this).data('id');
    var proveedorId = $(this).data('proveedor');

    // Reemplazar el botón "Corregir" con los 3 botones de acción
    var $documentItem = $(`#documento_${documentId}`);
    var actionButtonsHtml = `
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-success btn-aprobar" data-id="${documentId}" data-proveedor="${proveedorId}" title="Aprobar"><i class="fa fa-check-circle"></i></button>
            <button class="btn btn-warning btn-solicitar-correccion" data-id="${documentId}" data-proveedor="${proveedorId}" title="Solicitar Corrección"><i class="fa fa-pencil-square"></i></button>
            <button class="btn btn-danger btn-rechazar" data-id="${documentId}" data-proveedor="${proveedorId}" title="Rechazar"><i class="fa fa-window-close"></i></button>
        </div>`;
    
    $documentItem.find('.document-action-buttons').html(actionButtonsHtml);

    // Mostrar una notificación al usuario
    Toastify({
        text: "Seleccione una acción para el documento.",
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: "#17a2b8",  // Azul para info
        stopOnFocus: true,
    }).showToast();
})  

// Función para cambiar el estado del documento con colores de Toastify
function cambiarEstadoDocumento(documentId, estadoId, proveedorId) {
    // Opcional: Puedes solicitar una observación al usuario si es necesario
    // Por simplicidad, aquí no se incluye una observación
    var observacion = null; // Placeholder para observaciones

    $.ajax({
        url: '/documenta/controlador/ProveedoresController.php?op=cambiarEstadoDocumento',
        method: 'POST',
        data: { 
            document_id: documentId, 
            estado_id: estadoId, 
            observacion: observacion 
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                let toastColor = "";
                let toastText = "";
                switch (estadoId) {
                    case 1:
                        toastColor = "#17a2b8";  // Azul para corregido
                        toastText = "Documento marcado como corregido.";
                        break;
                    case 2:
                        toastColor = "#28a745";  // Verde para aprobado
                        toastText = "Documento aprobado correctamente.";
                        break;
                    case 3:
                        toastColor = "#dc3545";  // Rojo para rechazado
                        toastText = "Documento rechazado.";
                        break;
                    case 4:
                        toastColor = "#ffc107";  // Amarillo para por corregir
                        toastText = "Documento marcado para corrección.";
                        break;
                    default:
                        toastColor = "#6c757d";  // Gris para desconocido
                        toastText = "Estado del documento actualizado.";
                }

                Toastify({
                    text: toastText,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: toastColor,
                    stopOnFocus: true,
                }).showToast();

                actualizarEstadoDocumento(documentId, estadoId);
                actualizarPorcentajesProveedor(proveedorId);
            } else {
                Toastify({
                    text: "Error al actualizar el estado del documento.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",  // Rojo para error
                    stopOnFocus: true,
                }).showToast();
            }
        },
        error: function (e) {
            console.error("Error en la solicitud AJAX: ", e.responseText);
            Toastify({
                text: "Error al comunicarse con el servidor.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",  // Rojo para error
                stopOnFocus: true,
            }).showToast();
        }
    });
}

// Actualizar estado de documento en la interfaz
function actualizarEstadoDocumento(documentId, estadoId) {
    var nuevoEstado = '';
    switch (estadoId) {
        case 1:
            nuevoEstado = 'Corregido';
            break;
        case 2:
            nuevoEstado = 'Aprobado';
            break;
        case 3:
            nuevoEstado = 'Rechazado';
            break;
        case 4:
            nuevoEstado = 'Por Corregir';
            break;
        default:
            nuevoEstado = 'Desconocido';
    }
    $(`#documento_${documentId} p strong`).text(nuevoEstado);

    // Actualizar los botones de acción según el nuevo estado
    var proveedorId = $(`#documento_${documentId}`).find('.btn-aprobar, .btn-solicitar-correccion, .btn-rechazar, .btn-corregir').data('proveedor');

    if (estadoId == 1 || estadoId == 4) { // Corregido o Por Corregir
        var actionButtonsHtml = `
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-success btn-aprobar" data-id="${documentId}" data-proveedor="${proveedorId}" title="Aprobar"><i class="fa fa-check-circle"></i></button>
                <button class="btn btn-warning btn-solicitar-correccion" data-id="${documentId}" data-proveedor="${proveedorId}" title="Solicitar Corrección"><i class="fa fa-pencil-square"></i></button>
                <button class="btn btn-danger btn-rechazar" data-id="${documentId}" data-proveedor="${proveedorId}" title="Rechazar"><i class="fa fa-window-close"></i></button>
            </div>`;
        $(`#documento_${documentId} .document-action-buttons`).html(actionButtonsHtml);
    } else if (estadoId == 2 || estadoId == 3) { // Aprobado o Rechazado
        $(`#documento_${documentId} .document-action-buttons`).html('');
    }
}

// Actualizar los porcentajes del proveedor
function actualizarPorcentajesProveedor(proveedorId) {
    $.ajax({
        url: '/documenta/controlador/ProveedoresController.php?op=obtenerPorcentajesProveedor',
        method: 'POST',
        data: { proveedor_id: proveedorId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                var row = $(`#proveedoresTable tr[data-id='${proveedorId}']`);
                // Actualizar barra de Documentos Subidos
                row.find('.porcentaje-subidos').html(`
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: ${data.porcentaje_subidos}%" aria-valuenow="${data.porcentaje_subidos}" aria-valuemin="0" aria-valuemax="100">${data.porcentaje_subidos}%</div>
                    </div>
                `);
                // Actualizar barra de Documentos Aprobados
                row.find('.porcentaje-aprobados').html(`
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${data.porcentaje_aprobados}%" aria-valuenow="${data.porcentaje_aprobados}" aria-valuemin="0" aria-valuemax="100">${data.porcentaje_aprobados}%</div>
                    </div>
                `);
            } else {
                console.error("Error al obtener los porcentajes del proveedor.");
            }
        },
        error: function (e) {
            console.error("Error en la solicitud AJAX: ", e.responseText);
        }
    });
}
