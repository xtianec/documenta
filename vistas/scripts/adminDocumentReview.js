$(document).ready(function () {
    cargarDocumentos();

    $('#reviewFormObligatorios').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/documenta/controlador/AdminDocumentController.php?op=aprobarDocumentos',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Documentos obligatorios aprobados exitosamente.');
            },
            error: function (error) {
                console.error("Error al aprobar los documentos obligatorios:", error);
            }
        });
    });

    $('#reviewFormOpcionales').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/documenta/controlador/AdminDocumentController.php?op=aprobarDocumentos',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Documentos opcionales aprobados exitosamente.');
            },
            error: function (error) {
                console.error("Error al aprobar los documentos opcionales:", error);
            }
        });
    });

    function cargarDocumentos() {
        $('#document-review-list-obligatorios').empty();
        $('#document-review-list-opcionales').empty();

        $.ajax({
            url: '/documenta/controlador/AdminDocumentController.php?op=listarDocumentosParaRevisar',
            method: 'GET',
            success: function (response) {
                var documentos = JSON.parse(response);
                renderizarDocumentos(documentos);
            },
            error: function (error) {
                console.error("Error al cargar los documentos:", error);
            }
        });
    }

    function renderizarDocumentos(documentos) {
        documentos.forEach(function (doc) {
            var documentHtml = `
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">${doc.documentName} (${doc.document_type})</label>
                    <div class="col-sm-8">
                        <input type="file" class="dropify" disabled
                            data-id="${doc.document_id}" 
                            data-type="${doc.document_type}" 
                            data-category-id="${doc.document_id}" 
                            data-default-file="${doc.document_path}"
                            data-show-remove="false"
                        />
                    </div>
                </div>
            `;

            if (doc.document_type === 'obligatorio') {
                $('#document-review-list-obligatorios').append(documentHtml);
            } else {
                $('#document-review-list-opcionales').append(documentHtml);
            }
        });

        $('.dropify').dropify(); // Inicializa Dropify para los nuevos elementos añadidos
    }

    function rechazarDocumentos() {
        // Aquí puedes implementar la lógica para rechazar los documentos
        alert('Documentos rechazados.');
    }
});
