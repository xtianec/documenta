// scripts/areas.js

$(document).ready(function () {
    InitAreas();
});

function InitAreas() {
    listarAreas();
    verificarAreaEnAgregar();
    verificarAreaEnActualizar();

    // Limpiar el formulario al cerrar el modal de agregar área
    $('#formularioArea').on('hidden.bs.modal', function () {
        $('#formularioAreaForm')[0].reset();
        $('#area_name').removeClass('is-invalid');
        $('#areaFeedback').text("");
    });

    // Limpiar el formulario al cerrar el modal de actualizar área
    $('#formularioActualizarArea').on('hidden.bs.modal', function () {
        $('#formularioActualizarAreaForm')[0].reset();
        $('#area_nameUpdate').removeClass('is-invalid');
        $('#areaUpdateFeedback').text("");
    });
}

// Función listar áreas
function listarAreas() {
    tablaAreas = $('#tbllistadoAreas').DataTable({
        dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f>>>><"col-md-12"rt><"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary' },
            { extend: 'csv', className: 'btn btn-secondary' },
            { extend: 'excel', className: 'btn btn-secondary' },
            { extend: 'print', className: 'btn btn-secondary' }
        ],
        "language": {
            "paginate": {
                "previous": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M15 18l-6-6 6-6"/></svg>',
                "next": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M9 6l6 6-6 6"/></svg>'
            },
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "search": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M21 21l-6-6"/></svg>',
            "searchPlaceholder": "Buscar...",
            "lengthMenu": "Resultados: _MENU_",
        },
        "lengthMenu": [10, 10, 20, 50],
        "pageLength": 10,
        "ajax": {
            url: '/documenta/controlador/AreasController.php?op=listar',
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "destroy": true,
        "order": [[0, "desc"]], // Ordenar (columna, orden)
        "columnDefs": [
            {
                "targets": [0], // ID
                "visible": false, // Ocultar la columna ID
                "searchable": false
            }
        ]
    });
}
// Función mostrar área
function mostrarArea(id) {
    $.post('/documenta/controlador/AreasController.php?op=mostrar', { id: id }, function (data) {
        data = JSON.parse(data);
        $('#area_idUpdate').val(data.id);
        $('#area_nameUpdate').val(data.area_name);
        $('#company_idUpdate').val(data.company_id);
        cargarAreas(data.company_id, '#area_selectUpdate', data.area_id);
        $('#formularioActualizarArea').modal('show'); // Mostrar el modal de actualización

        // Remover cualquier clase de error previa
        $('#area_nameUpdate').removeClass('is-invalid');
        $('#areaUpdateFeedback').text("");
    }).fail(function () {
        Toastify({
            text: "Error al obtener los datos del área",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            className: "toast-progress",
        }).showToast();
    });
}


// Función guardar área
function guardarArea() {
    var area_name = $('#area_name').val().trim();
    var company_id = $('#company_id').val().trim();

    // Validación adicional en JavaScript
    if (area_name === "" || company_id === "") {
        Toastify({
            text: "Complete todos los campos requeridos",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#ffc107",
            className: "toast-progress",
        }).showToast();
        return;
    }

    // Verificar área única antes de guardar
    $.post('/documenta/controlador/AreasController.php?op=verificar_area', { area_name: area_name, company_id: company_id }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "área ya existe") {
            $('#area_name').addClass('is-invalid');
            $('#areaFeedback').text("Área ya existe en esta empresa. Por favor, ingrese un nombre diferente.");
            Toastify({
                text: "El área ya existe en esta empresa. Por favor, ingrese un nombre diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#area_name').removeClass('is-invalid');
            $('#areaFeedback').text("");

            // Proceder a guardar el área
            $.post('/documenta/controlador/AreasController.php?op=guardar', { area_name: area_name, company_id: company_id }, function (response) {
                response = response.trim();
                if (response === "Área registrada correctamente") {
                    $('#formularioArea').modal('hide');
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    $('#tbllistadoAreas').DataTable().ajax.reload(); // Recargar la tabla
                } else {
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        className: "toast-progress",
                    }).showToast();
                }
            });
        }
    });
}

// Función actualizar área
function actualizarArea() {
    var id = $('#area_idUpdate').val().trim();
    var area_name = $('#area_nameUpdate').val().trim();
    var company_id = $('#company_idUpdate').val().trim();

    // Validación adicional en JavaScript
    if (area_name === "" || company_id === "") {
        Toastify({
            text: "Complete todos los campos requeridos",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#ffc107",
            className: "toast-progress",
        }).showToast();
        return;
    }

    // Verificar área única antes de actualizar
    $.post('/documenta/controlador/AreasController.php?op=verificar_area', { area_name: area_name, company_id: company_id, id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "área ya existe") {
            $('#area_nameUpdate').addClass('is-invalid');
            $('#areaUpdateFeedback').text("Área ya existe en esta empresa. Por favor, ingrese un nombre diferente.");
            Toastify({
                text: "El área ya existe en esta empresa. Por favor, ingrese un nombre diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#area_nameUpdate').removeClass('is-invalid');
            $('#areaUpdateFeedback').text("");

            // Proceder a actualizar el área
            $.post('/documenta/controlador/AreasController.php?op=editar', { id: id, area_name: area_name, company_id: company_id }, function (response) {
                response = response.trim();
                if (response === "Área actualizada correctamente") {
                    $('#formularioActualizarArea').modal('hide');
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    $('#tbllistadoAreas').DataTable().ajax.reload(); // Recargar la tabla
                } else {
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        className: "toast-progress",
                    }).showToast();
                }
            });
        }
    });
}

// Función para confirmar desactivación usando SweetAlert2
function confirmarDesactivacionArea(id) {
    Swal.fire({
        title: '¿Está seguro de desactivar el área?',
        text: "¡Este área se dará de baja hasta que se vuelva a activar!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡desactívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            desactivarArea(id);
        }
    });
}

function desactivarArea(id) {
    $.post('/documenta/controlador/AreasController.php?op=desactivar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Desactivado', 'El área fue desactivada correctamente.', 'success');
            tablaAreas.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo desactivar el área.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Función para confirmar activación usando SweetAlert2
function confirmarActivacionArea(id) {
    Swal.fire({
        title: '¿Está seguro de activar el área?',
        text: "Este área se activará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡actívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            activarArea(id);
        }
    });
}

function activarArea(id) {
    $.post('/documenta/controlador/AreasController.php?op=activar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Activado', 'El área fue activada correctamente.', 'success');
            tablaAreas.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo activar el área.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Configurar eventos para los botones de editar, activar y desactivar
$(document).on('click', '.btn-edit-area', function () {
    var id = $(this).data('id');
    mostrarArea(id);
});

$(document).on('click', '.btn-desactivar-area', function () {
    var id = $(this).data('id');
    confirmarDesactivacionArea(id);
});

$(document).on('click', '.btn-activar-area', function () {
    var id = $(this).data('id');
    confirmarActivacionArea(id);
});

// Función para verificar área único en el formulario de agregar
function verificarAreaEnAgregar() {
    $('#area_name').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var area_name = $(this).val().trim();
        var company_id = $('#company_id').val().trim();
        if (area_name === "" || company_id === "") {
            // Remover clases y mensajes si los campos no están completos
            $('#area_name').removeClass('is-invalid');
            $('#areaFeedback').text("");
            return;
        }

        $.post('/documenta/controlador/AreasController.php?op=verificar_area', { area_name: area_name, company_id: company_id }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "área ya existe") {
                // Mostrar feedback de error
                $('#area_name').addClass('is-invalid');
                $('#areaFeedback').text("Área ya existe en esta empresa. Por favor, ingrese un nombre diferente.");
            } else {
                // Remover feedback de error
                $('#area_name').removeClass('is-invalid');
                $('#areaFeedback').text("");
            }
        });
    });
}

// Función para verificar área único en el formulario de actualizar
function verificarAreaEnActualizar() {
    $('#area_nameUpdate').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var area_name = $(this).val().trim();
        var company_id = $('#company_idUpdate').val().trim();
        var id = $('#area_idUpdate').val().trim();
        if (area_name === "" || company_id === "") {
            // Remover clases y mensajes si los campos no están completos
            $('#area_nameUpdate').removeClass('is-invalid');
            $('#areaUpdateFeedback').text("");
            return;
        }

        $.post('/documenta/controlador/AreasController.php?op=verificar_area', { area_name: area_name, company_id: company_id, id: id }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "área ya existe") {
                // Mostrar feedback de error
                $('#area_nameUpdate').addClass('is-invalid');
                $('#areaUpdateFeedback').text("Área ya existe en esta empresa. Por favor, ingrese un nombre diferente.");
            } else {
                // Remover feedback de error
                $('#area_nameUpdate').removeClass('is-invalid');
                $('#areaUpdateFeedback').text("");
            }
        });
    });
}
