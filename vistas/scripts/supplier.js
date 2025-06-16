var tabla;

function init() {

    $('#formularioregistros').on('hidden.bs.modal', function () {
        $('#formulario')[0].reset();  // Resetea los campos del formulario
        $('#companyName').val('');  // Limpia los campos adicionales si es necesario
        $('#department').val('');
        $('#province').val('');
        $('#district').val('');
        $('#address').val('');
        $('#stateSunat').val('');
        $('#conditionSunat').val('');
    });

    // Evento para consultar RUC cuando se cambia el valor
    $("#RUC").on("change", function () {
        var ruc = $(this).val();
        if (ruc.length === 11) {
            consultarRUC(ruc);
        } else {
            alert("El RUC debe tener 11 dígitos.");
        }
    });

    // Evento para consultar RUC en la actualización
    $("#RUCUpdate").on("change", function () {
        var ruc = $(this).val();
        if (ruc.length === 11) {
            consultarRUCUpdate(ruc);
        } else {
            alert("El RUC debe tener 11 dígitos.");
        }
    });

    listar();

    // Evento para el formulario de guardar proveedor
    $("#formulario").on("submit", function (e) {
        guardar(e);
    });

    // Evento para el formulario de actualizar proveedor
    $("#formActualizar").on("submit", function (e) {
        actualizar(e);
    });
}

// Función para listar proveedores
function listar() {
    tabla = $("#tbllistado").DataTable({
        "ajax": {
            url: "/documenta/controlador/SupplierController.php?op=listar",
            type: "get",
            dataType: "json",
            error: function (e) {
                console.error("Error al listar proveedores:", e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10,
        "buttons": [
            'copy', 'excel', 'csv', 'pdf'
        ]
    });
}

// Función para consultar RUC
function consultarRUC(ruc) {
    $.ajax({
        url: 'proxyRUC',
        method: 'GET',
        data: { ruc: ruc },
        success: function (response) {
            try {
                var data = typeof response === "string" ? JSON.parse(response) : response;

                if (data.error) {
                    mostrarNotificacion(data.error, "error");
                } else {
                    // Asignar valores a los campos del formulario
                    $("#companyName").val(data.nombre);
                    $("#department").val(data.departamento);
                    $("#province").val(data.provincia);
                    $("#district").val(data.distrito);
                    $("#address").val(data.direccion);
                    $("#stateSunat").val(data.estado);
                    $("#conditionSunat").val(data.condicion);

                    // Actualizar los colores basados en los valores
                    setSunatFieldColors();
                }
            } catch (e) {
                console.error("Error al parsear el JSON: ", e);
                mostrarNotificacion("Error al procesar la respuesta del servidor.", "error");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", status, error);
            mostrarNotificacion("Error al consultar los datos del RUC.", "error");
        }
    });
}


// Función para consultar RUC al actualizar
function consultarRUCUpdate(ruc) {
    $.ajax({
        url: 'proxyRUC', // Cambia la ruta si es necesario
        method: 'GET',
        data: { ruc: ruc },
        success: function (response) {
            try {
                var data = typeof response === "string" ? JSON.parse(response) : response;

                if (data.error) {
                    mostrarNotificacion(data.error, "error");
                } else {
                    // Asignar valores a los campos del formulario de actualización
                    $("#companyNameUpdate").val(data.nombre);
                    $("#addressUpdate").val(data.direccion);
                    $("#departmentUpdate").val(data.departamento);
                    $("#provinceUpdate").val(data.provincia);
                    $("#districtUpdate").val(data.distrito);
                    $("#stateSunatUpdate").val(data.estado);
                    $("#conditionSunatUpdate").val(data.condicion);

                    // Actualizar los colores basados en los valores
                    setSunatFieldColors();
                }
            } catch (e) {
                console.error("Error al procesar la respuesta del servidor:", e);
                mostrarNotificacion("Error al procesar la respuesta del servidor.", "error");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", status, error);
            mostrarNotificacion("Error al consultar los datos del RUC.", "error");
        }
    });
}


// Función para guardar proveedor
function guardar(e) {
    e.preventDefault();
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "/documenta/controlador/SupplierController.php?op=guardar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#formularioregistros').modal('hide');
            mostrarNotificacion(response, "success");
            tabla.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error("Error al guardar proveedor:", status, error);
            mostrarNotificacion("Error al guardar el proveedor.", "error");
        }
    });
}

// Función para actualizar proveedor
function actualizar(e) {
    e.preventDefault();
    var formData = new FormData($("#formActualizar")[0]);

    $.ajax({
        url: "/documenta/controlador/SupplierController.php?op=editar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('#formularioActualizar').modal('hide');
            mostrarNotificacion(response, "success");
            tabla.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error("Error al actualizar proveedor:", status, error);
            mostrarNotificacion("Error al actualizar el proveedor.", "error");
        }
    });
}

// Función para activar proveedor
function activar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡El proveedor será activado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, activar!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/documenta/controlador/SupplierController.php?op=activar", { id: id }, function (response) {
                mostrarNotificacion(response, "success");
                tabla.ajax.reload();
            });
        }
    });
}

// Función para desactivar proveedor
function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡El proveedor será desactivado!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/documenta/controlador/SupplierController.php?op=desactivar", { id: id }, function (response) {
                mostrarNotificacion(response, "success");
                tabla.ajax.reload();
            });
        }
    });
}

// Función para mostrar datos de un proveedor para editar
function mostrar(id) {
    $.post("/documenta/controlador/SupplierController.php?op=mostrar", { id: id }, function (data) {
        data = JSON.parse(data);

        // Poblamos los campos del formulario de actualización
        $("#idUpdate").val(data.id);  // Aquí debe llenarse el ID del registro
        $("#RUCUpdate").val(data.RUC);
        $("#companyNameUpdate").val(data.companyName);
        $("#departmentUpdate").val(data.department);
        $("#provinceUpdate").val(data.province);
        $("#districtUpdate").val(data.district);
        $("#addressUpdate").val(data.address);
        $("#stateSunatUpdate").val(data.stateSunat);
        $("#conditionSunatUpdate").val(data.conditionSunat);
        $("#contactNameBusinessUpdate").val(data.contactNameBusiness);
        $("#contactEmailBusinessUpdate").val(data.contactEmailBusiness);
        $("#contactPhoneBusinessUpdate").val(data.contactPhoneBusiness);

        // Abrimos el modal para editar
        $('#formularioActualizar').modal('show');
        setSunatFieldColors();
        Update
    });
}


// Función para cambiar el color de los campos en base al estado y condición del SUNAT
function setSunatFieldColors() {
    const estado = $("#stateSunat").val().toLowerCase();
    const condicion = $("#conditionSunat").val().toLowerCase();
    const estadoUpdate = $("#stateSunatUpdate").val().toLowerCase();
    const condicionUpdate = $("#conditionSunatUpdate").val().toLowerCase();

    // Estado de Contribuyente
    if (estado.includes("activo")) {
        $("#stateSunat").css({ "background-color": "green", "color": "white" });
    } else {
        $("#stateSunat").css({ "background-color": "red", "color": "white" });
    }

    // Condición de Contribuyente
    if (condicion.includes("habido")) {
        $("#conditionSunat").css({ "background-color": "green", "color": "white" });
    } else {
        $("#conditionSunat").css({ "background-color": "red", "color": "white" });
    }

    if (estadoUpdate.includes("activo")) {
        $("#stateSunatUpdate").css({ "background-color": "green", "color": "white" });
    } else {
        $("#stateSunatUpdate").css({ "background-color": "red", "color": "white" });
    }

    // Condición de Contribuyente
    if (condicionUpdate.includes("habido")) {
        $("#conditionSunatUpdate").css({ "background-color": "green", "color": "white" });
    } else {
        $("#conditionSunatUpdate").css({ "background-color": "red", "color": "white" });
    }
}

// Inicializar la aplicación
init();

// Función de notificación usando Toastify y SweetAlert
function mostrarNotificacion(mensaje, tipo) {
    if (tipo === "success") {
        Toastify({
            text: mensaje,
            duration: 3000,
            backgroundColor: "green",
            className: "info",
        }).showToast();
    } else {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: mensaje,
            confirmButtonColor: "#d33",
        });
    }
}
