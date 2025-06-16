$(document).ready(function () {
    // Inicializar dropify
    $('.dropify').dropify({
        messages: {
            'default': 'Arrastra y suelta un archivo aquí o haz clic',
            'replace': 'Arrastra y suelta o haz clic para reemplazar',
            'remove': 'Eliminar',
            'error': 'Ooops, algo salió mal.'
        }
    });

    // Cargar documentos al cargar la página
    cargarDocumentos();

    // Manejar el envío del formulario para registrar un nuevo documento
    $("#formDocumentRegister").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: "/documenta/controlador/DocumentNameSupplierController.php?op=guardar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    // Mostrar notificación de éxito con el mensaje personalizado
                    Toastify({
                        text: "Documento guardado correctamente.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745"
                    }).showToast();
                    
                    // Reiniciar el formulario
                    $("#formDocumentRegister")[0].reset();
                    
                    // Recargar la tabla de documentos
                    cargarDocumentos();
                } else {
                    // Mostrar mensaje de error
                    Toastify({
                        text: jsonResponse.message,
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            },
            error: function () {
                // Mostrar mensaje de error si la solicitud falla
                Toastify({
                    text: "Error al registrar el documento.",
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    });

    // Función para cargar documentos
    function cargarDocumentos() {
        $.ajax({
            url: "/documenta/controlador/DocumentNameSupplierController.php?op=listar",
            type: "GET",
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    var documentos = data.data;
                    var tbody = '';
                    documentos.forEach(function (documento) {
                        tbody += '<tr>';
                        tbody += '<td>' + documento.id + '</td>';
                        tbody += '<td>' + documento.name + '</td>';
                        tbody += '<td>' + (documento.description || '') + '</td>';
                        tbody += '<td>' + (documento.templatePath ? '<a href="' + documento.templatePath + '" target="_blank">Descargar Plantilla</a>' : 'No disponible') + '</td>';
                        tbody += '<td>';
                        tbody += '<button class="btn btn-primary btn-sm editarDocumento" data-id="' + documento.id + '"><i class="fa fa-edit"></i> Editar</button> ';
                        tbody += '<button class="btn btn-danger btn-sm eliminarDocumento" data-id="' + documento.id + '"><i class="fa fa-trash"></i> Eliminar</button>';
                        tbody += '</td>';
                        tbody += '</tr>';
                    });
                    $("#documentTable tbody").html(tbody);
                } else {
                    $("#documentTable tbody").html('<tr><td colspan="5" class="text-center">No hay documentos registrados.</td></tr>');
                }
            },
            error: function () {
                $("#documentTable tbody").html('<tr><td colspan="5" class="text-center">Error al cargar los documentos.</td></tr>');
            }
        });
    }

    // Manejar la edición de documentos
    $(document).on('click', '.editarDocumento', function () {
        var id = $(this).data('id');
        $.ajax({
            url: "/documenta/controlador/DocumentNameSupplierController.php?op=obtener",
            type: "POST",
            data: { id: id },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#editDocumentoId').val(data.data.id);
                    $('#editDocumentName').val(data.data.name);
                    $('#editDocumentDescription').val(data.data.description);
                    $('#modalEditarDocumento').modal('show');
                }
            }
        });
    });

    // Manejar el envío del formulario para editar un documento
    $("#formEditarDocumento").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "/documenta/controlador/DocumentNameSupplierController.php?op=actualizar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    Toastify({
                        text: "Documento editado correctamente",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745"
                    }).showToast();
                    $('#modalEditarDocumento').modal('hide');
                    cargarDocumentos();
                }
            }
        });
    });

    // Manejar la eliminación de documentos con confirmación usando SweetAlert
    $(document).on('click', '.eliminarDocumento', function () {
        var id = $(this).data('id');

        swal({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este documento.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "/documenta/controlador/DocumentNameSupplierController.php?op=eliminar",
                    type: "POST",
                    data: { id: id },
                    success: function (response) {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            // Mostrar notificación de éxito
                            Toastify({
                                text: "Documento eliminado exitosamente.",
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#28a745"
                            }).showToast();
                            
                            // Recargar la lista de documentos
                            cargarDocumentos();
                        } else {
                            Toastify({
                                text: jsonResponse.message,
                                duration: 5000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545"
                            }).showToast();
                        }
                    },
                    error: function () {
                        Toastify({
                            text: "Error al intentar eliminar el documento.",
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545"
                        }).showToast();
                    }
                });
            }
        });
    });

});
