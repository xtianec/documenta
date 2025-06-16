// scripts/companies.js

$(document).ready(function () {
    InitCompanies();
});

function InitCompanies() {
    listarCompanies();
    verificarRucEnAgregar();
    verificarRucEnActualizar();

    // Limpiar el formulario al cerrar el modal de agregar empresa
    $('#formulario').on('hidden.bs.modal', function () {
        $('#formulario')[0].reset();
        $('#ruc').removeClass('is-invalid');
        $('#rucFeedback').text("");
    });

    // Limpiar el formulario al cerrar el modal de actualizar empresa
    $('#formularioActualizar').on('hidden.bs.modal', function () {
        $('#formularioActualizarEmpresa')[0].reset();
        $('#rucUpdate').removeClass('is-invalid');
        $('#rucUpdateFeedback').text("");
    });
}

// Función listar Empresas
function listarCompanies() {
    tablaCompanies = $('#tbllistadoCompanies').DataTable({
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
            url: '/documenta/controlador/CompaniesController.php?op=listar',
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "destroy": true,
        "order": [[0, "desc"]] // Ordenar (columna, orden)
    });
}

// Función mostrar Empresa para editar
function mostrarEmpresa(id) {
    $.post('/documenta/controlador/CompaniesController.php?op=mostrar', { id: id }, function (data) {
        data = JSON.parse(data);
        $('#idUpdate').val(data.id);
        $('#company_nameUpdate').val(data.company_name);
        $('#rucUpdate').val(data.ruc);
        $('#descriptionUpdate').val(data.description);
        $('#formularioActualizar').modal('show'); // Mostrar el modal de actualización
    }).fail(function () {
        Toastify({
            text: "Error al obtener los datos de la empresa",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            className: "toast-progress",
        }).showToast();
    });
}
// Función guardar Empresa
function guardarEmpresa() {
    var company_name = $('#company_name').val().trim();
    var ruc = $('#ruc').val().trim();
    var description = $('#description').val().trim();

    // Validación adicional en JavaScript
    var rucPattern = /^\d{11}$/;
    if (!rucPattern.test(ruc)) {
        Toastify({
            text: "Ingrese un RUC válido de 11 dígitos",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            className: "toast-progress",
        }).showToast();
        return;
    }

    if (company_name === "" || ruc === "" || description === "") {
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

    // Verificar RUC único antes de guardar
    $.post('/documenta/controlador/CompaniesController.php?op=verificar_ruc', { ruc: ruc }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "ruc ya existe") {
            $('#ruc').addClass('is-invalid');
            $('#rucFeedback').text("RUC ya existe. Por favor, ingrese un RUC diferente.");
            Toastify({
                text: "El RUC ya existe. Por favor, ingrese un RUC diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#ruc').removeClass('is-invalid');
            $('#rucFeedback').text("");

            // Proceder a guardar la empresa
            $.post('/documenta/controlador/CompaniesController.php?op=guardar', { company_name: company_name, ruc: ruc, description: description }, function (response) {
                response = response.trim();
                if (response === "Datos registrados correctamente") {
                    $('#formulario').modal('hide');
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    $('#tbllistadoCompanies').DataTable().ajax.reload(); // Recargar la tabla
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

// Función actualizar Empresa
function actualizarEmpresa() {
    var id = $('#idUpdate').val().trim();
    var company_name = $('#company_nameUpdate').val().trim();
    var ruc = $('#rucUpdate').val().trim();
    var description = $('#descriptionUpdate').val().trim();

    // Validación adicional en JavaScript
    var rucPattern = /^\d{11}$/;
    if (!rucPattern.test(ruc)) {
        Toastify({
            text: "Ingrese un RUC válido de 11 dígitos",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            className: "toast-progress",
        }).showToast();
        return;
    }

    if (company_name === "" || ruc === "" || description === "") {
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

    // Verificar RUC único antes de actualizar
    $.post('/documenta/controlador/CompaniesController.php?op=verificar_ruc', { ruc: ruc, id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "ruc ya existe") {
            $('#rucUpdate').addClass('is-invalid');
            $('#rucUpdateFeedback').text("RUC ya existe. Por favor, ingrese un RUC diferente.");
            Toastify({
                text: "El RUC ya existe. Por favor, ingrese un RUC diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#rucUpdate').removeClass('is-invalid');
            $('#rucUpdateFeedback').text("");

            // Proceder a actualizar la empresa
            $.post('/documenta/controlador/CompaniesController.php?op=editar', { id: id, company_name: company_name, ruc: ruc, description: description }, function (response) {
                response = response.trim();
                if (response === "Datos actualizados correctamente") {
                    $('#formularioActualizar').modal('hide');
                    Toastify({
                        text: response,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    $('#tbllistadoCompanies').DataTable().ajax.reload(); // Recargar la tabla
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
function confirmarDesactivacionCompany(id) {
    Swal.fire({
        title: '¿Está seguro de desactivar el registro?',
        text: "¡Este registro se dará de baja hasta que se vuelva a activar!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡desactívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            desactivarCompany(id);
        }
    });
}

function desactivarCompany(id) {
    $.post('/documenta/controlador/CompaniesController.php?op=desactivar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Desactivado', 'El registro fue desactivado correctamente.', 'success');
            tablaCompanies.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo desactivar el registro.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Función para confirmar activación usando SweetAlert2
function confirmarActivacionCompany(id) {
    Swal.fire({
        title: '¿Está seguro de activar el registro?',
        text: "Este registro se activará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡actívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            activarCompany(id);
        }
    });
}

function activarCompany(id) {
    $.post('/documenta/controlador/CompaniesController.php?op=activar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Activado', 'El registro fue activado correctamente.', 'success');
            tablaCompanies.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo activar el registro.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Configurar eventos para los botones de editar, activar y desactivar
$(document).on('click', '.btn-edit', function () {
    var id = $(this).data('id');
    mostrarEmpresa(id);
});

$(document).on('click', '.btn-desactivar', function () {
    var id = $(this).data('id');
    confirmarDesactivacionCompany(id);
});

$(document).on('click', '.btn-activar', function () {
    var id = $(this).data('id');
    confirmarActivacionCompany(id);
});

// Función para verificar RUC único en el formulario de agregar
function verificarRucEnAgregar() {
    $('#ruc').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var ruc = $(this).val().trim();
        if (ruc.length !== 11) {
            $('#ruc').addClass('is-invalid');
            $('#rucFeedback').text("Ingrese un RUC válido de 11 dígitos.");
            return;
        }

        $.post('/documenta/controlador/CompaniesController.php?op=verificar_ruc', { ruc: ruc }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "ruc ya existe") {
                $('#ruc').addClass('is-invalid');
                $('#rucFeedback').text("RUC ya existe. Por favor, ingrese un RUC diferente.");
            } else {
                $('#ruc').removeClass('is-invalid');
                $('#rucFeedback').text("");
            }
        });
    });
}

// Función para verificar RUC único en el formulario de actualizar
function verificarRucEnActualizar() {
    $('#rucUpdate').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var ruc = $(this).val().trim();
        var id = $('#idUpdate').val().trim();
        if (ruc.length !== 11) {
            $('#rucUpdate').addClass('is-invalid');
            $('#rucUpdateFeedback').text("Ingrese un RUC válido de 11 dígitos.");
            return;
        }

        $.post('/documenta/controlador/CompaniesController.php?op=verificar_ruc', { ruc: ruc, id: id }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "ruc ya existe") {
                $('#rucUpdate').addClass('is-invalid');
                $('#rucUpdateFeedback').text("RUC ya existe. Por favor, ingrese un RUC diferente.");
            } else {
                $('#rucUpdate').removeClass('is-invalid');
                $('#rucUpdateFeedback').text("");
            }
        });
    });
}
