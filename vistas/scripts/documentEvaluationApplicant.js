// admin/scripts/documentEvaluationApplicant.js

$(document).ready(function () {
    // Cargar Empresas al iniciar la página
    cargarEmpresas();

    // Inicializar el DataTable con Server-Side Processing y filtros personalizados
    var tabla = $('#applicantsTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '/documenta/controlador/DocumentEvaluationController.php?op=listarApplicants',
            type: "POST",
            dataType: "json",
            data: function (d) {
                // Añadir filtros personalizados
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.company_id = $('#companySelect').val();
                d.job_id = $('#positionSelect').val();
            },
            error: function (e) {
                console.error("Error al cargar los datos: ", e.responseText);
                Toastify({
                    text: "Error al cargar los postulantes.",
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
            { "data": "job_name" },
            {
                "data": "photo",
                "render": function (data) {
                    let photoUrl = data && data !== 'NULL'
                        ? data
                        : '/documenta/app/template/images/default_user.jpg'; // Ruta de la imagen por defecto
                    return `<img src="${photoUrl}" alt="Foto del Postulante" class="img-thumbnail photo-clickable" style="width: 50px; height: 50px; cursor: pointer;" loading="lazy">`;
                },
                "orderable": false
            },
            {
                "data": null,
                "render": function (data) {
                    return `${data.names} ${data.lastname}`;
                }
            },
            { "data": "email" },
            {
                "data": "porcentaje_subidos_cv",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-info');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_subidos_other",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-warning');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_aprobados_cv",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-success');
                },
                "orderable": false
            },
            {
                "data": "porcentaje_aprobados_other",
                "render": function (data) {
                    return crearBarraProgreso(data, 'bg-secondary');
                },
                "orderable": false
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm ver-detalles" data-id="${row.id}" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                },
                "orderable": false
            }
        ],
        order: [[0, "asc"]],
        language: {
            // Opciones de idioma para DataTables
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        responsive: true,
        deferRender: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
    });

    // Evento cuando se cambia la Empresa
    $('#companySelect').on('change', function () {
        var companyId = $(this).val();
        if (companyId) {
            cargarPuestos(companyId);
            $('#positionSelect').prop('disabled', false);
        } else {
            $('#positionSelect').html('<option value="">Todos los Puestos</option>');
            $('#positionSelect').prop('disabled', true);
        }
    });

    // Evento de envío del formulario de filtros
    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        tabla.ajax.reload();
    });

    // Evento del botón de resetear filtros
    $('#resetFilter').on('click', function () {
        $('#filtroForm')[0].reset();
        $('#positionSelect').html('<option value="">Todos los Puestos</option>');
        $('#positionSelect').prop('disabled', true);
        tabla.ajax.reload();
    });

    // Evento para ver detalles en el DataTable
    $('#applicantsTable tbody').on('click', '.ver-detalles', function () {
        var data = tabla.row($(this).parents('tr')).data();
        cargarDetallesApplicant(data.id);
    });
});

/**
 * Función para cargar las empresas en el select de Empresa
 */
function cargarEmpresas() {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=obtenerEmpresas',
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

/**
 * Función para cargar los puestos en el select de Puesto basado en la empresa seleccionada
 * @param {number} companyId - ID de la empresa seleccionada
 */
function cargarPuestos(companyId) {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=obtenerPuestosPorEmpresa',
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

/**
 * Función para crear una barra de progreso
 * @param {number} valor - Porcentaje de progreso
 * @param {string} clase - Clase de Bootstrap para el color
 * @returns {string} - HTML de la barra de progreso
 */
function crearBarraProgreso(valor, clase) {
    // Asegúrate de que el porcentaje esté entre 0 y 100
    valor = Math.min(Math.max(valor, 0), 100);
    return `
        <div class="progress">
            <div class="progress-bar ${clase}" role="progressbar" style="width: ${valor}%" aria-valuenow="${valor}" aria-valuemin="0" aria-valuemax="100">
                ${valor}%
            </div>
        </div>
    `;
}

/**
 * Función para mostrar una imagen en el modal
 * @param {string} src - Ruta de la imagen
 */
function mostrarImagenModal(src) {
    $('#modalImage').attr('src', src);
    $('#imageModal').modal('show');
}

/**
 * Función para cargar los detalles del postulante en el modal
 * @param {number} applicantId - ID del postulante
 */
function cargarDetallesApplicant(applicantId) {
    // Limpiar contenedores
    $('#documentosApplicantContainer').html('<p>Cargando documentos...</p>');
    $('#educacionApplicantContainer').html('<p>Cargando experiencia educativa...</p>');
    $('#trabajoApplicantContainer').html('<p>Cargando experiencia laboral...</p>');
    $('#applicantNombre').text('');

    // Obtener el nombre del postulante
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=obtenerNombreApplicant',
        method: 'GET',
        data: { applicant_id: applicantId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                $('#applicantNombre').text(data.nombre);
            } else {
                $('#applicantNombre').text('Desconocido');
            }
        },
        error: function (e) {
            console.error("Error al obtener el nombre del postulante: ", e.responseText);
            $('#applicantNombre').text('Desconocido');
        }
    });

    // Cargar Documentos
    cargarDocumentosApplicant(applicantId);

    // Cargar Experiencia Educativa
    cargarEducacionApplicant(applicantId);

    // Cargar Experiencia Laboral
    cargarTrabajoApplicant(applicantId);
}

/**
 * Función para cargar los documentos subidos por el postulante
 * @param {number} applicantId - ID del postulante
 */
function cargarDocumentosApplicant(applicantId) {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=documentosApplicant',
        method: 'POST',
        data: { applicant_id: applicantId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                renderizarDocumentosApplicant(data.cv_documents, data.other_documents, '#documentosApplicantContainer', applicantId);
            } else {
                console.error(data.message);
                $('#documentosApplicantContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar los documentos: ", e.responseText);
            $('#documentosApplicantContainer').html('<p class="text-danger">Ocurrió un error al cargar los documentos.</p>');
        }
    });
}

/**
 * Renderizar los documentos en el contenedor especificado
 * @param {Array} cv_documents - Lista de CVs
 * @param {Array} other_documents - Lista de otros documentos
 * @param {string} containerSelector - Selector del contenedor
 * @param {number} applicantId - ID del postulante
 */
function renderizarDocumentosApplicant(cv_documents, other_documents, containerSelector, applicantId) {
    var html = '';

    // Documentos de CV
    html += '<h5>CV</h5>';
    if (cv_documents.length === 0) {
        html += '<p>No hay CVs subidos.</p>';
    } else {
        html += '<ul class="list-group mb-3">';
        cv_documents.forEach(function (doc) {
            html += generarItemDocumento(doc, applicantId);
        });
        html += '</ul>';
    }

    // Otros Documentos
    html += '<h5>Otros Documentos</h5>';
    if (other_documents.length === 0) {
        html += '<p>No hay otros documentos subidos.</p>';
    } else {
        html += '<ul class="list-group">';
        other_documents.forEach(function (doc) {
            html += generarItemDocumento(doc, applicantId);
        });
        html += '</ul>';
    }

    $(containerSelector).html(html);
}

/**
 * Generar el HTML para un item de documento
 * @param {Object} doc - Objeto del documento
 * @param {number} applicantId - ID del postulante
 * @returns {string} - HTML del item
 */
function generarItemDocumento(doc, applicantId) {
    return `
        <li class="list-group-item" id="documento_${doc.document_id}">
            <div>
                <p><strong>${doc.original_file_name}</strong> (${doc.state_name})</p>
                <p><a href="${doc.document_path}" target="_blank" class="text-primary">Ver Documento</a></p>
                <p><strong>Observación del Usuario:</strong> ${doc.user_observation || 'Sin observación'}</p>
            </div>
            <div class="mt-2">
                <!-- Textarea para observación individual -->
                <div class="form-group">
                    <label for="observacion_${doc.document_id}">Observación del Administrador:</label>
                    <textarea class="form-control admin-observation" id="observacion_${doc.document_id}" rows="2">${doc.admin_observation || ''}</textarea>
                </div>
                <!-- Botones de acción -->
                <button class="btn btn-success btn-sm btn-aprobar" data-id="${doc.document_id}" data-applicant="${applicantId}" title="Aprobar">
                    <i class="fas fa-check"></i> Aprobar
                </button>
                <button class="btn btn-warning btn-sm btn-solicitar-correccion" data-id="${doc.document_id}" data-applicant="${applicantId}" title="Solicitar Corrección">
                    <i class="fas fa-edit"></i> Solicitar Corrección
                </button>
                <button class="btn btn-danger btn-sm btn-rechazar" data-id="${doc.document_id}" data-applicant="${applicantId}" title="Rechazar">
                    <i class="fas fa-times"></i> Rechazar
                </button>
            </div>
        </li>`;
}

/**
 * Función para cargar la experiencia educativa del postulante
 * @param {number} applicantId - ID del postulante
 */
function cargarEducacionApplicant(applicantId) {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=obtenerEducacion',
        method: 'GET',
        data: { applicant_id: applicantId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                renderizarEducacionApplicant(data.educaciones, '#educacionApplicantContainer');
            } else {
                console.error(data.message);
                $('#educacionApplicantContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar la experiencia educativa: ", e.responseText);
            $('#educacionApplicantContainer').html('<p class="text-danger">Ocurrió un error al cargar la experiencia educativa.</p>');
        }
    });
}

/**
 * Renderizar la experiencia educativa en el contenedor especificado
 * @param {Array} educaciones - Lista de experiencias educativas
 * @param {string} containerSelector - Selector del contenedor
 */
function renderizarEducacionApplicant(educaciones, containerSelector) {
    var html = '';

    if (educaciones.length === 0) {
        html += '<p>No hay experiencias educativas registradas.</p>';
    } else {
        html += '<ul class="list-group">';
        educaciones.forEach(function (edu) {
            html += `
                <li class="list-group-item">
                    <strong>${edu.institution}</strong> - ${edu.education_type}<br>
                    <small>${formatDate(edu.start_date)} - ${formatDate(edu.end_date)}</small><br>
                    <small>Duración: ${edu.duration} ${edu.duration_unit}</small><br>
                    ${edu.file_path ? `<a href="${edu.file_path}" target="_blank" class="text-primary">Ver Archivo</a>` : 'Sin Archivo'}
                </li>
            `;
        });
        html += '</ul>';
    }

    $(containerSelector).html(html);
}

/**
 * Función para cargar la experiencia laboral del postulante
 * @param {number} applicantId - ID del postulante
 */
function cargarTrabajoApplicant(applicantId) {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=obtenerTrabajo',
        method: 'GET',
        data: { applicant_id: applicantId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                renderizarTrabajoApplicant(data.trabajos, '#trabajoApplicantContainer');
            } else {
                console.error(data.message);
                $('#trabajoApplicantContainer').html(`<p class="text-danger">${data.message}</p>`);
            }
        },
        error: function (e) {
            console.error("Error al cargar la experiencia laboral: ", e.responseText);
            $('#trabajoApplicantContainer').html('<p class="text-danger">Ocurrió un error al cargar la experiencia laboral.</p>');
        }
    });
}

/**
 * Renderizar la experiencia laboral en el contenedor especificado
 * @param {Array} trabajos - Lista de experiencias laborales
 * @param {string} containerSelector - Selector del contenedor
 */
function renderizarTrabajoApplicant(trabajos, containerSelector) {
    var html = '';

    if (trabajos.length === 0) {
        html += '<p>No hay experiencias laborales registradas.</p>';
    } else {
        html += '<ul class="list-group">';
        trabajos.forEach(function (trab) {
            html += `
                <li class="list-group-item">
                    <strong>${trab.company}</strong> - ${trab.position}<br>
                    <small>${formatDate(trab.start_date)} - ${formatDate(trab.end_date)}</small><br>
                    ${trab.file_path ? `<a href="${trab.file_path}" target="_blank" class="text-primary">Ver Archivo</a>` : 'Sin Archivo'}
                </li>
            `;
        });
        html += '</ul>';
    }

    $(containerSelector).html(html);
}

/**
 * Formatear la fecha en formato DD/MM/YYYY
 * @param {string} dateStr - Fecha en formato YYYY-MM-DD
 * @returns {string} - Fecha formateada
 */
function formatDate(dateStr) {
    if (!dateStr) return 'Presente';
    var date = new Date(dateStr);
    if (isNaN(date.getTime())) return 'Desconocido';
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Maximizar imagen al hacer clic
$(document).on('click', '.photo-clickable', function () {
    var imageUrl = $(this).attr('src');
    $('#modalImage').attr('src', imageUrl);
    $('#imageModal').modal('show');
});

// Al hacer clic en "Ver Detalles"
$(document).on('click', '.ver-detalles', function () {
    var applicantId = $(this).data('id');
    cargarDetallesApplicant(applicantId);
    $('#modalDetallesApplicant').modal('show');
});

// Eventos para cambiar estado de documentos
$(document).on('click', '.btn-aprobar, .btn-solicitar-correccion, .btn-rechazar', function () {
    var documentId = $(this).data('id');
    var applicantId = $(this).data('applicant');
    var estadoId;
    var accion;

    if ($(this).hasClass('btn-aprobar')) {
        estadoId = 2; // Aprobado
        accion = 'aprobar';
    } else if ($(this).hasClass('btn-solicitar-correccion')) {
        estadoId = 4; // Por Corregir
        accion = 'solicitar corrección';
    } else if ($(this).hasClass('btn-rechazar')) {
        estadoId = 3; // Rechazado
        accion = 'rechazar';
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
    Swal.fire({
        title: `¿Estás seguro de ${accion} este documento?`,
        text: `Una vez confirmada, la acción no podrá deshacerse.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoDocumento(documentId, estadoId, applicantId, observacion);
        }
    });
});

/**
 * Función para cambiar el estado del documento
 * @param {number} documentId - ID del documento
 * @param {number} estadoId - Nuevo estado
 * @param {number} applicantId - ID del postulante
 * @param {string} observacion - Observación del administrador
 */
function cambiarEstadoDocumento(documentId, estadoId, applicantId, observacion) {
    $.ajax({
        url: '/documenta/controlador/DocumentEvaluationController.php?op=cambiarEstadoDocumento',
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
                cargarDocumentosApplicant(applicantId);
                actualizarApplicantsTable();

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

/**
 * Función para actualizar el DataTable de postulantes
 */
function actualizarApplicantsTable() {
    var table = $('#applicantsTable').DataTable();
    table.ajax.reload(null, false); // false para no resetear la paginación
}
