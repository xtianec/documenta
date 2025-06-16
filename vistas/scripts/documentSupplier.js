$(document).ready(function () {
    var documentosCache = [];
    var totalDocuments = 0;
    var uploadedDocuments = 0;

    // Cargar y renderizar los documentos al cargar la página
    cargarDocumentosDisponibles();

    function cargarDocumentosDisponibles() {
        $.ajax({
            url: '/documenta/controlador/DocumentSupplierController.php?op=listarDocumentos',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    documentosCache = data.data;
                    totalDocuments = data.data.length;
                    obtenerEstadoDocumentos(data.data);
                } else {
                    console.error("Error al obtener los documentos:", data.message);
                    mostrarNotificacion("Error al obtener los documentos: " + data.message, "error");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                mostrarNotificacion("Error en la solicitud al servidor.", "error");
            }
        });
    }

    function obtenerEstadoDocumentos(documentos) {
        $.ajax({
            url: '/documenta/controlador/DocumentSupplierController.php?op=obtenerEstadoDocumentos',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    uploadedDocuments = 0;
                    renderizarDocumentos(documentos, data.data);
                } else {
                    console.error("Error al obtener el estado de los documentos:", data.message);
                    mostrarNotificacion("Error al obtener el estado de los documentos: " + data.message, "error");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                mostrarNotificacion("Error en la solicitud al servidor.", "error");
            }
        });
    }

    function renderizarDocumentos(documentos, estadoDocumentos) {
        var container = $('#document-container');
        container.empty();
    
        documentos.forEach(function (doc) {
            var documentId = doc.id;
            var archivoSubido = estadoDocumentos[documentId];
    
            var estado = archivoSubido ? 'subido' : 'pendiente';
            var cardClass = estado === 'subido' ? 'card-outline-inverse' : 'card-outline-danger';
            var iconClass = estado === 'subido' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
            var estadoTexto = estado === 'subido' ? 'Subido Correctamente' : 'Pendiente de Subida';
            var estadoColor = estado === 'subido' ? 'text-success' : 'text-danger';
            var archivoHTML = '';
    
            // Mostrar información del archivo subido si ya se ha subido uno
            if (archivoSubido) {
                uploadedDocuments++;
                archivoHTML = `
                    <div class="archivo-subido mb-3">
                        <p><strong>Archivo subido:</strong> <a href="${archivoSubido.documentPath}" target="_blank">${archivoSubido.originalFileName}</a></p>
                        <p>Sube un nuevo archivo si deseas reemplazar el actual.</p>
                        <button type="button" class="btn btn-danger btn-delete mt-2" data-id="${documentId}">
                            <i class="fa fa-trash"></i> Eliminar
                        </button>
                    </div>
                    
                `;
            }
    
            // Si el documento tiene una plantilla, mostrarla para descargar
            var plantillaHTML = '';
            if (doc.templatePath && doc.templatePath !== '') {
                plantillaHTML = `
                    <p><strong>Registrar descargar esta plantilla y subirlo escaneado:</strong> </p><p>
                    <a href="${doc.templatePath}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-download"></i> Descargar Plantilla
                    </a></p>
                `;
            }
    
            var card = `
                <div class="col-md-6 col-lg-3">
                    <div class="card ${cardClass} mb-4 shadow-sm h-100" id="card_${documentId}">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="m-b-0 text-white">
                                <i class="${iconClass}" id="icon_${documentId}"></i> ${doc.name}
                            </h4>
                        </div>  
                        <div class="card-body d-flex flex-column">
                            <p class="card-text text-muted">${doc.description || 'Sube el documento requerido a continuación.'}</p>
                            ${plantillaHTML} <!-- Mostrar enlace de plantilla si está disponible -->
                            <p class="estado-documento ${estadoColor}"><strong>${estadoTexto}</strong></p>
                            ${archivoHTML}
                            
                            <div class="form-group mt-auto">
                                <label for="file_${documentId}" class="form-label"><i class="fa fa-upload"></i> Selecciona un archivo</label>
                                <input type="file" class="form-control-file dropify" id="file_${documentId}" name="file_${documentId}">
                            </div>
                            <button type="button" class="btn btn-primary btn-upload mt-auto" data-id="${documentId}">
                                <i class="fa fa-upload"></i> ${archivoSubido ? 'Reemplazar Archivo' : 'Subir Archivo'}
                            </button>
                            <div id="uploadStatus_${documentId}" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    
        // Inicializar Dropify (subida de archivos con drag & drop)
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastra y suelta aquí, o haz clic para seleccionar',
                'replace': 'Arrastra y suelta o haz clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Oops, algo salió mal.'
            },
            error: {
                'fileSize': 'El archivo es muy grande (máx. 5MB).',
                'fileExtension': 'Este tipo de archivo no está permitido.'
            }
        });
    
        actualizarBarraProgreso();
    }
    
    

    $(document).on('click', '.btn-upload', function () {
        var documentId = $(this).data('id');
        var fileInput = $(`#file_${documentId}`)[0];
        var file = fileInput.files[0];
    
        if (!file) {
            mostrarNotificacion("Por favor, selecciona un archivo para subir.", "warning");
            return;
        }
    
        var formData = new FormData();
        formData.append('documentFile', file);
        formData.append('documentNameSupplier_id', documentId);
    
        $.ajax({
            url: '/documenta/controlador/DocumentSupplierController.php?op=subir',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(`.btn-upload[data-id="${documentId}"]`).html('<div class="spinner-border spinner-border-sm" role="status"></div> Subiendo...');
                $(`#uploadStatus_${documentId}`).html('<div class="spinner-border text-info" role="status"><span class="sr-only">Subiendo...</span></div>');
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    mostrarNotificacion(data.message, 'success');
                    $(`#uploadStatus_${documentId}`).html('<span class="text-success">Subida exitosa</span>');
    
                    // Cambiar la clase del card a inverse (verde)
                    var card = $(`#card_${documentId}`);
                    card.removeClass('card-outline-danger').addClass('card-outline-inverse');
    
                    // Cambiar el ícono del card a check-circle (icono verde)
                    var icon = $(`#icon_${documentId}`);
                    icon.removeClass('fa-exclamation-circle').addClass('fa-check-circle');
    
                    $(`.btn-upload[data-id="${documentId}"]`).html('<i class="fa fa-cloud-upload"></i> Reemplazar Documento');
    
                    var archivoHTML = `
                        <div class="archivo-subido">
                            <p><strong>Archivo subido:</strong> <a href="${data.documentPath}" target="_blank">${data.originalFileName}</a></p>
                            <p>Puedes seleccionar un nuevo archivo para reemplazarlo o eliminarlo.</p>
                            <button type="button" class="btn btn-danger btn-delete mt-2" data-id="${documentId}">
                                <i class="fa fa-trash-o"></i> Eliminar Documento
                            </button>
                        </div>
                    `;
                    var cardBody = $(`#file_${documentId}`).closest('.card-body');
                    cardBody.find('.archivo-subido').remove();
                    cardBody.prepend(archivoHTML);
    
                    // Incrementar el progreso al subir un documento
                    uploadedDocuments++;
                    actualizarBarraProgreso();
                } else {
                    mostrarNotificacion(data.message, 'error');
                    $(`#uploadStatus_${documentId}`).html('<span class="text-danger">Error al subir</span>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                mostrarNotificacion('Error en la solicitud al servidor.', 'error');
                $(`#uploadStatus_${documentId}`).html('<span class="text-danger">Error al subir</span>');
            }
        });
    });
    
    $(document).on('click', '.btn-delete', function () {
        var documentId = $(this).data('id');
    
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto una vez eliminado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controlador/DocumentSupplierController.php?op=eliminar',
                    method: 'POST',
                    data: { documentNameSupplier_id: documentId },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', 'El documento ha sido eliminado exitosamente.', 'success');
                            obtenerEstadoDocumentos(documentosCache);
    
                            // Cambiar el card a danger (rojo) al eliminar
                            var card = $(`#card_${documentId}`);
                            card.removeClass('card-outline-inverse').addClass('card-outline-danger');
    
                            // Cambiar el ícono del card a exclamation-circle (icono rojo)
                            var icon = $(`#icon_${documentId}`);
                            icon.removeClass('fa-check-circle').addClass('fa-exclamation-circle');
    
                            // Decrementar el progreso al eliminar un documento
                            uploadedDocuments--;
                            actualizarBarraProgreso();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Error', 'Error en la solicitud al servidor.', 'error');
                    }
                });
            }
        });
    });
    
    function actualizarBarraProgreso() {
        var progresoPorcentaje = (uploadedDocuments / totalDocuments) * 100;
        $('#overallProgress').css({
            'width': progresoPorcentaje + '%',
            'height': '30px',                  // Altura de la barra de progreso
            'background-color': '#28a745',      // Color verde de fondo
            'color': '#fff',                    // Texto de color blanco
            'font-weight': 'bold',              // Texto en negrita
            'font-size': '16px',                // Texto más grande
            'line-height': '30px',              // Alineación vertical centrada, igual a la altura
            'text-align': 'center',             // Alineación horizontal centrada
            'box-shadow': '0 4px 6px rgba(0, 0, 0, 0.1)',  // Sombra moderna para la barra
            'display': 'block',                 // Asegura que el progreso se comporte como un bloque
        });
        $('#overallProgress').attr('aria-valuenow', progresoPorcentaje);
        $('#overallProgress').text(progresoPorcentaje.toFixed(2) + '% Completado');
    }
    

    function mostrarNotificacion(mensaje, tipo) {
        var backgroundColor;

        switch (tipo) {
            case 'success':
                backgroundColor = '#4caf50';
                break;
            case 'error':
                backgroundColor = '#f44336';
                break;
            case 'warning':
                backgroundColor = '#ff9800';
                break;
            default:
                backgroundColor = '#2196f3';
        }

        Toastify({
            text: mensaje,
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: backgroundColor
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    }
});
