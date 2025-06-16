// vistas/scripts/user_documents.js

$(document).ready(function () {
    // Cargar Empresas al iniciar la página
    cargarEmpresas();

    // Inicializar DataTable
    var tabla = $('#usuariosTable').DataTable({
        ajax: {
            url: '/documenta/controlador/UserDocumentsController.php?op=listarUsuarios',
            type: "GET",
            dataType: "json",
            dataSrc: function (json) {
                if (json.success) {
                    return json.usuarios;
                } else {
                    console.error(json.message);
                    Toastify({
                        text: json.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        stopOnFocus: true,
                    }).showToast();
                    return [];
                }
            },
            data: function (d) {
                // Obtener los valores de los filtros
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.company_id = $('#companySelect').val();
                d.job_id = $('#positionSelect').val();
            },
            error: function (e) {
                console.error("Error al cargar los datos: ", e.responseText);
                Toastify({
                    text: "Error al cargar los usuarios.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    stopOnFocus: true,
                }).showToast();
            }
        },
        columns: [
            { "data": "company_name" },
            { "data": "position_name" },
            { "data": "names" },
            { "data": "lastname" },
            {
                "data": null,
                "render": function (data) {
                    let photoUrl = data.photo && data.photo !== 'NULL'
                        ? data.photo
                        : '/documenta/app/template/images/default_user.jpg'; // Ruta de la imagen por defecto
                    return `<img src="${photoUrl}" alt="Foto del Usuario" class="img-thumbnail photo-clickable" style="width: 50px; height: 50px; cursor: pointer;" loading="lazy">`;
                },
                "orderable": false
            },
            
            
            {
                "data": "porcentaje_subidos_mandatory",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_subidos_optional",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_aprobados_mandatory",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_aprobados_optional",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            {
                "data": null,
                "render": function (data) {
                    return `
                        <button class="btn btn-primary btn-sm btn-ver-documentos" data-id="${data.id}" data-nombre="${data.names} ${data.lastname}">
                            <i class="fa fa-eye"></i> Ver Documentos
                        </button>`;
                },
                "orderable": false
            }
        ],
        language: {
            // Opciones de idioma (personalizar según sea necesario)
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"

        },
        responsive: true, // Asegúrate de que esta opción esté habilitada
        deferRender: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[1, "asc"]] // Ordenar por nombre
    });

    // Manejar el envío del formulario de filtros
    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        tabla.ajax.reload();
    });

    // Manejar el reset del filtro
    $('#resetFilter').on('click', function () {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#companySelect').val('');
        $('#positionSelect').html('<option value="">Todos los Puestos</option>').prop('disabled', true);
        tabla.ajax.reload();
    });

    // Evento cuando se cambia la Empresa
    $('#companySelect').on('change', function () {
        var companyId = $(this).val();
        if (companyId) {
            cargarPuestos(companyId);
            $('#positionSelect').prop('disabled', false);
        } else {
            $('#positionSelect').html('<option value="">Todos los Puestos</option>').prop('disabled', true);
        }
    });
});

function listarUsuarios() {
    // Ya no es necesario, ya inicializamos la DataTable una vez
}

function cargarEmpresas() {
    $.ajax({
        url: '/documenta/controlador/UserDocumentsController.php?op=obtenerEmpresas', // Actualizado
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                var options = '<option value="">Todas las Empresas</option>';
                data.empresas.forEach(function (empresa) {
                    options += `<option value="${empresa.id}">${empresa.company_name}</option>`;
                });
                $('#companySelect').html(options);
            } else {
                console.error(data.message);
                Toastify({
                    text: "Error al cargar las empresas.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        },
        error: function (e) {
            console.error("Error al cargar las empresas: ", e.responseText);
            Toastify({
                text: "Error al comunicarse con el servidor.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
            }).showToast();
        }
    });
}

function cargarPuestos(companyId) {
    $.ajax({
        url: '/documenta/controlador/UserDocumentsController.php?op=obtenerPuestosPorEmpresa', // Actualizado
        method: 'GET',
        data: { company_id: companyId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                var options = '<option value="">Todos los Puestos</option>';
                data.puestos.forEach(function (puesto) {
                    options += `<option value="${puesto.id}">${puesto.position_name}</option>`;
                });
                $('#positionSelect').html(options);
            } else {
                console.error(data.message);
                $('#positionSelect').html('<option value="">Todos los Puestos</option>');
                Toastify({
                    text: "Error al cargar los puestos.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        },
        error: function (e) {
            console.error("Error al cargar los puestos: ", e.responseText);
            $('#positionSelect').html('<option value="">Todos los Puestos</option>');
            Toastify({
                text: "Error al comunicarse con el servidor.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
            }).showToast();
        }
    });
}

function crearBarraProgreso(valor, clase) {
    return `
        <div class="progress" style="height: 20px;">
            <div class="progress-bar ${clase}" role="progressbar" style="width: ${valor}%" aria-valuenow="${valor}" aria-valuemin="0" aria-valuemax="100">${valor}%</div>
        </div>`;
}

// Maximizar imagen al hacer clic
$(document).on('click', '.photo-clickable', function () {
    var imageUrl = $(this).attr('src');
    $('#modalImage').attr('src', imageUrl);
    $('#imageModal').modal('show');
});

// Al hacer clic en "Ver Documentos"
$(document).on('click', '.btn-ver-documentos', function () {
    var userId = $(this).data('id');
    var userName = $(this).data('nombre');
    $('#usuarioNombre').text(userName);
    cargarDocumentosUsuario(userId);
    $('#modalDocumentosUsuario').modal('show');
});

// Cargar los documentos subidos por el usuario
function cargarDocumentosUsuario(userId) {
    // Obtener los valores de los filtros actuales
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
        url: '/documenta/controlador/UserDocumentsController.php?op=documentosUsuario',
        method: 'POST',
        data: {
            user_id: userId,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // Separar documentos por tipo
                var documentosObligatorios = data.documentos.filter(doc => doc.document_type === 'obligatorio');
                var documentosOpcionales = data.documentos.filter(doc => doc.document_type === 'opcional');

                // Renderizar documentos
                renderizarDocumentos(documentosObligatorios, '#documentosObligatoriosContainer', userId);
                renderizarDocumentos(documentosOpcionales, '#documentosOpcionalesContainer', userId);
            } else {
                console.error(data.message);
                $('#documentosObligatoriosContainer, #documentosOpcionalesContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar los documentos: ", e.responseText);
            $('#documentosObligatoriosContainer, #documentosOpcionalesContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
        }
    });
}

function renderizarDocumentos(documentos, containerSelector, userId) {
    var html = '';
    if (documentos.length === 0) {
        html = '<p>No hay documentos para mostrar.</p>';
    } else {
        html = '<ul class="list-group">';
        documentos.forEach(function (doc) {
            html += `
                <li class="list-group-item" id="documento_${doc.document_id}">
                    <div>
                        <p><strong>${doc.document_name}</strong> (${doc.state_name})</p>
                        <p><a href="${doc.document_path}" target="_blank">Ver Documento</a></p>
                    </div>
                    <div class="mt-2">
                        <!-- Textarea para observación individual -->
                        <div class="form-group">
                            <label for="observacion_${doc.document_id}">Observación:</label>
                            <textarea class="form-control admin-observation" id="observacion_${doc.document_id}" rows="2">${doc.admin_observation || ''}</textarea>
                        </div>
                        <!-- Botones de acción -->
                        <button class="btn btn-success btn-sm btn-aprobar" data-id="${doc.document_id}" data-user="${userId}">Aprobar</button>
                        <button class="btn btn-warning btn-sm btn-solicitar-correccion" data-id="${doc.document_id}" data-user="${userId}">Solicitar Corrección</button>
                        <button class="btn btn-danger btn-sm btn-rechazar" data-id="${doc.document_id}" data-user="${userId}">Rechazar</button>
                    </div>
                </li>`;
        });
        html += '</ul>';
    }
    $(containerSelector).html(html);
}

// Eventos para cambiar estado de documentos
$(document).on('click', '.btn-aprobar, .btn-solicitar-correccion, .btn-rechazar', function () {
    var documentId = $(this).data('id');
    var userId = $(this).data('user');
    var estadoId;

    if ($(this).hasClass('btn-aprobar')) {
        estadoId = 2; // Aprobado
    } else if ($(this).hasClass('btn-solicitar-correccion')) {
        estadoId = 4; // Por Corregir
    } else if ($(this).hasClass('btn-rechazar')) {
        estadoId = 3; // Rechazado
    }

    var observacion = $(`#observacion_${documentId}`).val();

    // Validar que la observación no esté vacía
    if (!observacion.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Observación requerida',
            text: 'Por favor, ingresa una observación antes de continuar.',
        });
        return;
    }

    // Confirmación antes de proceder
    var accionTexto = '';
    switch (estadoId) {
        case 2:
            accionTexto = 'aprobar';
            break;
        case 3:
            accionTexto = 'rechazar';
            break;
        case 4:
            accionTexto = 'solicitar corrección';
            break;
        default:
            accionTexto = 'realizar esta acción';
    }

    Swal.fire({
        title: `¿Estás seguro de ${accionTexto} este documento?`,
        text: `Una vez confirmada, la acción no podrá deshacerse.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, estadoId, userId, observacion);
        }
    });
});

// Función para cambiar el estado del documento
function cambiarEstadoDocumento(documentId, estadoId, userId, observacion) {
    $.ajax({
        url: '/documenta/controlador/UserDocumentsController.php?op=cambiarEstadoDocumento',
        method: 'POST',
        data: {
            document_id: documentId,
            estado_id: estadoId,
            observacion: observacion
        },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // Actualizar el modal y el DataTable
                cargarDocumentosUsuario(userId);
                actualizarUsuariosTable();

                // Notificación
                let toastColor = "";
                let toastText = "";
                switch (estadoId) {
                    case 2:
                        toastColor = "#28a745"; // Verde
                        toastText = "Documento aprobado correctamente.";
                        break;
                    case 3:
                        toastColor = "#dc3545"; // Rojo
                        toastText = "Documento rechazado.";
                        break;
                    case 4:
                        toastColor = "#ffc107"; // Amarillo
                        toastText = "Documento marcado para corrección.";
                        break;
                    default:
                        toastColor = "#6c757d"; // Gris
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
            } else {
                console.error(data.message);
                Toastify({
                    text: "Error: " + data.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
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
                backgroundColor: "#dc3545",
                stopOnFocus: true,
            }).showToast();
        }
    });
}

// Función para actualizar el DataTable de usuarios
function actualizarUsuariosTable() {
    var table = $('#usuariosTable').DataTable();
    table.ajax.reload(null, false); // false para no resetear la paginación
}
