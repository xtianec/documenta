    $(document).ready(function () {
    // Inicializa DataTable con los botones de exportación y Tablesaw para las columnas dinámicas
    function inicializarDataTable() {
        $('#documentTable').DataTable({
            dom: 'Blfrtip', // Define la estructura de botones
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print' // Botones de exportación
            ],
            lengthMenu: [10, 25, 50, 100],
            displayLength: 25,
            ajax: {
                url: "/documenta/controlador/AdminDocumentApplicantController.php?op=listarDocumentos",
                type: "GET",
                dataSrc: ''
            },
            columns: [
                { data: 'company_name' },
                { data: 'position_name' },
                { data: 'applicant_name' },
                { data: 'document_name' },
                {
                    data: 'document_path',
                    render: function (data) {
                        return `<a href="${data}" target="_blank">Ver Documento</a>`;
                    }
                },
                { data: 'created_at' },
                { data: 'uploaded_at' },
                {
                    data: 'state_name',
                    render: function (data) {
                        var estadoClass = '';
                        if (data === 'Aprobado') {
                            estadoClass = 'bg-aprobado';
                        } else if (data === 'Rechazado') {
                            estadoClass = 'bg-rechazado';
                        } else if (data === 'Pendiente de Corrección') {
                            estadoClass = 'bg-pendiente';
                        }
                        return `<span class="${estadoClass}">${data}</span>`;
                    }
                },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-primary btn-sm" onclick="marcarRevisado(${data})">Marcar Revisado</button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success">Evaluar</button>
                                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="evaluarDocumento('Aprobado', ${data})">Aprobar</a>
                                    <a class="dropdown-item" href="#" onclick="evaluarDocumento('Rechazado', ${data})">Rechazar</a>
                                    <a class="dropdown-item" href="#" onclick="evaluarDocumento('Pendiente de Corrección', ${data})">Pendiente de Corrección</a>
                                </div>
                            </div>`;
                    }
                }
            ],
            order: [[2, 'asc']], // Ordenar por el nombre del postulante
            // Callback que activa Tablesaw después de cargar la tabla
            initComplete: function(settings, json) {
                // Reinicializa Tablesaw después de que DataTable haya cargado
                $(document).trigger('enhance.tablesaw');
            }
        });
    }

    // Cargar el DataTable al cargar la página
    inicializarDataTable();

    // Recargar DataTable después de realizar cambios
    function recargarDocumentos() {
        $('#documentTable').DataTable().ajax.reload();
    }

    // Marcar documento como revisado
    window.marcarRevisado = function (id) {
        $.post("/documenta/controlador/AdminDocumentApplicantController.php?op=marcarRevisado", { document_id: id }, function (response) {
            try {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status) {
                    recargarDocumentos(); // Recargar los datos del DataTable
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: "Documento marcado como revisado.",
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: jsonResponse.message || "Error al marcar el documento como revisado.",
                    });
                }
            } catch (e) {
                console.error("Error procesando la respuesta JSON: ", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "Error al procesar la respuesta del servidor.",
                });
            }
        }).fail(function (xhr, status, error) {
            console.log("AJAX Error:", xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error en la solicitud AJAX.',
            });
        });
    };

    // Evaluar documento
    window.evaluarDocumento = function (estado, docId) {
        Swal.fire({
            title: `¿Estás seguro de que quieres ${estado} este documento?`,
            input: 'textarea',
            inputLabel: 'Observación (opcional)',
            inputPlaceholder: 'Escribe algo aquí...',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            preConfirm: (adminObservation) => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: '/documenta/controlador/AdminDocumentApplicantController.php?op=evaluarDocumento',
                        type: 'POST',
                        data: {
                            document_id: docId,
                            estado_documento: estado,
                            admin_observation: adminObservation
                        },
                        success: function (response) {
                            try {
                                var jsonResponse = JSON.parse(response);
                                if (jsonResponse.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Evaluación guardada correctamente.',
                                    });
                                    recargarDocumentos(); // Recargar los datos del DataTable
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: jsonResponse.message || "Error al guardar la evaluación.",
                                    });
                                }
                            } catch (e) {
                                console.error("Error procesando respuesta JSON: ", e);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: "Error al procesar la respuesta del servidor.",
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un error al procesar la evaluación.',
                            });
                            console.log("AJAX Error:", xhr.responseText);
                        }
                    });
                });
            }
        });
    };
});
