// scripts/jobs.js

$(document).ready(function () {
    InitJobs();
});

function InitJobs() {
    listarJobs();
    configurarFiltrosExternos();
    verificarJobEnAgregar();
    verificarJobEnActualizar();

    $('#company_select').on('change', function () {
        var company_id = $(this).val();
        cargarAreas(company_id, '#area_select');
    });

    $('#company_selectUpdate').on('change', function () {
        var company_id = $(this).val();
        cargarAreas(company_id, '#area_selectUpdate');
    });


    // Limpieza de formularios al cerrar los modales
    $('#formularioJob').on('hidden.bs.modal', function () {
        $('#formularioJobForm')[0].reset();
        $('#position_name').removeClass('is-invalid');
        $('#positionFeedback').text("");
        $('#area_select').empty().append('<option value="">Seleccione un área</option>');
    });

    $('#formularioActualizarJob').on('hidden.bs.modal', function () {
        $('#formularioActualizarJobForm')[0].reset();
        $('#position_nameUpdate').removeClass('is-invalid');
        $('#positionUpdateFeedback').text("");
        $('#area_selectUpdate').empty().append('<option value="">Seleccione un área</option>');
    });
}

// Función de debounce para limitar la frecuencia de ejecución
function debounce(func, delay) {
    let debounceTimer;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
}

// Función para listar Puestos de Trabajo en el DataTable
function listarJobs() {
    tablaJobs = $('#tbllistadoJobs').DataTable({
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
        "lengthMenu": [10, 20, 50, 100],
        "pageLength": 10,
        "ajax": {
            url: '/documenta/controlador/JobsController.php?op=listar',
            type: "GET",
            dataType: "json",
            data: function (d) {
                d.company_id = $('#filter_company').val();
                d.area_id = $('#filter_area').val();
                d.position_id = $('#filter_job').val();
            },
            beforeSend: function () {
                $('#loadingIndicator').show();
            },
            complete: function () {
                $('#loadingIndicator').hide();
            },
            error: function (e) {
                console.log(e.responseText);
                $('#loadingIndicator').hide();
            }
        },
        "destroy": true,
        "order": [[0, "desc"]], // Ordenar por ID descendente
        "columnDefs": [
            {
                "targets": [0], // ID
                "visible": false, // Ocultar la columna ID
                "searchable": false
            },
            {
                "targets": [4], // Estado
                "orderable": false, // Deshabilitar ordenamiento en esta columna
                "searchable": false
            },
            {
                "targets": [5], // Opciones
                "orderable": false, // Deshabilitar ordenamiento
                "searchable": false
            }
        ]
    });
}

// Función para configurar los Filtros Externos con Debounce
function configurarFiltrosExternos() {
    // Debounce de 300ms para evitar recargas excesivas
    const reloadTable = debounce(function () {
        tablaJobs.ajax.reload();
    }, 300);

    // Filtro por Empresa
    $('#filter_company').on('change', function () {
        var company_id = $(this).val();
        cargarAreas(company_id, '#filter_area');
        $('#filter_job').empty().append('<option value="">Todos los Puestos</option>');
        reloadTable();
    });

    // Filtro por Área
    $('#filter_area').on('change', function () {
        var area_id = $(this).val();
        cargarJobs(area_id, '#filter_job');
        reloadTable();
    });

    // Filtro por Puesto de Trabajo
    $('#filter_job').on('change', function () {
        reloadTable();
    });
}

// Función para cargar Áreas Dinámicamente en los Filtros
function cargarAreas(company_id, selector, selected_area_id = null) {
    if (!company_id) {
        $(selector).empty().append('<option value="">Seleccione un área</option>');
        return;
    }

    $.ajax({
        url: '/documenta/controlador/AreasController.php?op=listar_por_empresa',
        type: 'GET',
        data: { company_id: company_id },
        dataType: 'json',
        success: function (data) {
            $(selector).empty().append('<option value="">Seleccione un área</option>');
            $.each(data, function (index, area) {
                var selected = (selected_area_id && area.id == selected_area_id) ? 'selected' : '';
                $(selector).append('<option value="' + area.id + '" ' + selected + '>' + area.area_name + '</option>');
            });
        },
        error: function (e) {
            console.log(e.responseText);
            $(selector).empty().append('<option value="">Seleccione un área</option>');
        }
    });
}


// Función para cargar Puestos Dinámicamente según Área Seleccionada
function cargarJobs(area_id, selector, selected_job_id = null) {
    if (area_id === "") {
        $(selector).empty().append('<option value="">Todos los Puestos</option>');
        return;
    }

    $.ajax({
        url: '/documenta/controlador/JobsController.php?op=listar_por_area',
        type: 'GET',
        data: { area_id: area_id },
        dataType: 'json',
        success: function (data) {
            $(selector).empty().append('<option value="">Todos los Puestos</option>');
            $.each(data, function (index, job) {
                var selected = (selected_job_id && job.id == selected_job_id) ? 'selected' : '';
                $(selector).append('<option value="' + job.id + '" ' + selected + '>' + job.position_name + '</option>');
            });
        },
        error: function (e) {
            console.log(e.responseText);
            $(selector).empty().append('<option value="">Todos los Puestos</option>');
        }
    });
}

// Función mostrar puesto para editar
function mostrarJob(id) {
    $.post('/documenta/controlador/JobsController.php?op=mostrar', { id: id }, function (data) {
        data = JSON.parse(data);
        $('#job_idUpdate').val(data.id);
        $('#position_nameUpdate').val(data.position_name);
        $('#company_selectUpdate').val(data.company_id);
        cargarAreas(data.company_id, '#area_selectUpdate', data.area_id);
        $('#formularioActualizarJob').modal('show'); // Mostrar el modal de actualización

        // Remover cualquier clase de error previa
        $('#position_nameUpdate').removeClass('is-invalid');
        $('#positionUpdateFeedback').text("");
    }).fail(function () {
        Toastify({
            text: "Error al obtener los datos del puesto de trabajo",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            className: "toast-progress",
        }).showToast();
    });
}

// Función guardar puesto
function guardarJob() {
    var position_name = $('#position_name').val().trim();
    var company_id = $('#company_select').val().trim();
    var area_id = $('#area_select').val().trim();

    // Validación adicional en JavaScript
    if (position_name === "" || company_id === "" || area_id === "") {
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

    // Verificar puesto único antes de guardar
    $.post('/documenta/controlador/JobsController.php?op=verificar_puesto', { position_name: position_name, area_id: area_id }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "puesto ya existe") {
            $('#position_name').addClass('is-invalid');
            $('#positionFeedback').text("Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.");
            Toastify({
                text: "El puesto ya existe en esta área. Por favor, ingrese un nombre diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#position_name').removeClass('is-invalid');
            $('#positionFeedback').text("");

            // Proceder a guardar el puesto
            $.post('/documenta/controlador/JobsController.php?op=guardar', { position_name: position_name, area_id: area_id }, function (response) {
                response = response.trim().toLowerCase();
                if (response === "puesto de trabajo registrado correctamente") {
                    $('#formularioJob').modal('hide');
                    Toastify({
                        text: "Puesto de trabajo registrado correctamente",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    tablaJobs.ajax.reload(); // Recargar la tabla
                } else {
                    Toastify({
                        text: "El puesto de trabajo ya existe en esta área o ocurrió un error",
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

// Función actualizar puesto
function actualizarJob() {
    var id = $('#job_idUpdate').val().trim();
    var position_name = $('#position_nameUpdate').val().trim();
    var company_id = $('#company_selectUpdate').val().trim();
    var area_id = $('#area_selectUpdate').val().trim();

    // Validación adicional en JavaScript
    if (position_name === "" || company_id === "" || area_id === "") {
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

    // Verificar puesto único antes de actualizar
    $.post('/documenta/controlador/JobsController.php?op=verificar_puesto', { position_name: position_name, area_id: area_id, id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response === "puesto ya existe") {
            $('#position_nameUpdate').addClass('is-invalid');
            $('#positionUpdateFeedback').text("Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.");
            Toastify({
                text: "El puesto ya existe en esta área. Por favor, ingrese un nombre diferente.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545",
                className: "toast-progress",
            }).showToast();
        } else {
            // Remover feedback de error si está presente
            $('#position_nameUpdate').removeClass('is-invalid');
            $('#positionUpdateFeedback').text("");

            // Proceder a actualizar el puesto
            $.post('/documenta/controlador/JobsController.php?op=editar', { id: id, position_name: position_name, area_id: area_id }, function (response) {
                response = response.trim().toLowerCase();
                if (response === "puesto de trabajo actualizado correctamente") {
                    $('#formularioActualizarJob').modal('hide');
                    Toastify({
                        text: "Puesto de trabajo actualizado correctamente",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        className: "toast-progress",
                    }).showToast();
                    tablaJobs.ajax.reload(); // Recargar la tabla
                } else {
                    Toastify({
                        text: "El puesto de trabajo ya existe en esta área o ocurrió un error",
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

// Configurar eventos para los botones de editar, activar y desactivar
$(document).on('click', '.btn-edit-job', function () {
    var id = $(this).data('id');
    mostrarJob(id);
});

$(document).on('click', '.btn-desactivar-job', function () {
    var id = $(this).data('id');
    confirmarDesactivacionJob(id);
});

$(document).on('click', '.btn-activar-job', function () {
    var id = $(this).data('id');
    confirmarActivacionJob(id);
});

// Función para confirmar desactivación usando SweetAlert2
function confirmarDesactivacionJob(id) {
    Swal.fire({
        title: '¿Está seguro de desactivar el puesto de trabajo?',
        text: "¡Este puesto se dará de baja hasta que se vuelva a activar!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡desactívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            desactivarJob(id);
        }
    });
}

function desactivarJob(id) {
    $.post('/documenta/controlador/JobsController.php?op=desactivar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Desactivado', 'El puesto de trabajo fue desactivado correctamente.', 'success');
            tablaJobs.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo desactivar el puesto de trabajo.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Función para confirmar activación usando SweetAlert2
function confirmarActivacionJob(id) {
    Swal.fire({
        title: '¿Está seguro de activar el puesto de trabajo?',
        text: "Este puesto se activará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, ¡actívalo!',
        cancelButtonText: 'No, cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            activarJob(id);
        }
    });
}

function activarJob(id) {
    $.post('/documenta/controlador/JobsController.php?op=activar', { id: id }, function (response) {
        response = response.trim().toLowerCase();
        if (response.includes("correctamente")) {
            Swal.fire('Activado', 'El puesto de trabajo fue activado correctamente.', 'success');
            tablaJobs.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
        } else {
            Swal.fire('Error', 'No se pudo activar el puesto de trabajo.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
    });
}

// Función para verificar puesto único en el formulario de agregar
function verificarJobEnAgregar() {
    $('#position_name').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var position_name = $(this).val().trim();
        var area_id = $('#area_select').val().trim();
        if (position_name === "" || area_id === "") {
            // Remover clases y mensajes si los campos no están completos
            $('#position_name').removeClass('is-invalid');
            $('#positionFeedback').text("");
            return;
        }

        $.post('/documenta/controlador/JobsController.php?op=verificar_puesto', { position_name: position_name, area_id: area_id }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "puesto ya existe") {
                // Mostrar feedback de error
                $('#position_name').addClass('is-invalid');
                $('#positionFeedback').text("Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.");
                Toastify({
                    text: "El puesto ya existe en esta área. Por favor, ingrese un nombre diferente.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    className: "toast-progress",
                }).showToast();
            } else {
                // Remover feedback de error
                $('#position_name').removeClass('is-invalid');
                $('#positionFeedback').text("");
            }
        });
    });
}

// Función para verificar puesto único en el formulario de actualizar
function verificarJobEnActualizar() {
    $('#position_nameUpdate').on('input change', function () { // Usamos 'input' para detectar cualquier cambio
        var position_name = $(this).val().trim();
        var area_id = $('#area_selectUpdate').val().trim();
        var id = $('#job_idUpdate').val().trim();
        if (position_name === "" || area_id === "") {
            // Remover clases y mensajes si los campos no están completos
            $('#position_nameUpdate').removeClass('is-invalid');
            $('#positionUpdateFeedback').text("");
            return;
        }

        $.post('/documenta/controlador/JobsController.php?op=verificar_puesto', { position_name: position_name, area_id: area_id, id: id }, function (response) {
            response = response.trim().toLowerCase();
            if (response === "puesto ya existe") {
                // Mostrar feedback de error
                $('#position_nameUpdate').addClass('is-invalid');
                $('#positionUpdateFeedback').text("Puesto ya existe en esta área. Por favor, ingrese un nombre diferente.");
                Toastify({
                    text: "El puesto ya existe en esta área. Por favor, ingrese un nombre diferente.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    className: "toast-progress",
                }).showToast();
            } else {
                // Remover feedback de error
                $('#position_nameUpdate').removeClass('is-invalid');
                $('#positionUpdateFeedback').text("");
            }
        });
    });
}
