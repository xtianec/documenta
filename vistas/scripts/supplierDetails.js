$(document).ready(function () {
    // Cargar los datos del proveedor si ya existen
    cargarDatosProveedor();

    // Formulario para registrar los detalles del proveedor
    $("#formSupplierDetailsRegister").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData($("#formSupplierDetailsRegister")[0]);

        $.ajax({
            url: "/documenta/controlador/SupplierDetailsController.php?op=guardar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status) {
                    Toastify({
                        text: jsonResponse.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745"
                    }).showToast();
                    cargarDatosProveedor();
                } else {
                    Toastify({
                        text: jsonResponse.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            }
        });
    });

    // Mostrar formulario de actualización al hacer clic en "Editar Detalles"
    $("#btnEditarDetalles").on("click", function () {
        $("#datosRegistrados").hide();
        $("#formSupplierDetailsUpdate").show();
        
        // Llenar el formulario de actualización con los datos actuales
        cargarDatosEnFormularioDeActualizacion();
    });

    // Formulario para actualizar los detalles del proveedor
    $("#formSupplierDetailsUpdate").on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData($("#formSupplierDetailsUpdate")[0]);

        $.ajax({
            url: "/documenta/controlador/SupplierDetailsController.php?op=actualizar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status) {
                    Toastify({
                        text: jsonResponse.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745"
                    }).showToast();
                    cargarDatosProveedor();
                } else {
                    Toastify({
                        text: jsonResponse.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            }
        });
    });

    // Función para cargar los detalles del proveedor en el panel de detalles
    function cargarDatosProveedor() {
        $.ajax({
            url: "/documenta/controlador/SupplierDetailsController.php?op=mostrar",
            type: "POST",
            success: function (response) {
                console.log("Respuesta del servidor:", response);
                var data = JSON.parse(response);
                if (data.status === false) {
                    // Mostrar el formulario de registro si no hay datos
                    $("#formSupplierDetailsRegister").fadeIn();
                    $("#formSupplierDetailsUpdate").hide();
                    $("#datosRegistrados").hide();
                } else if (data.status === true) {
                    // Mostrar los datos y el botón de editar si ya existen datos
                    $("#formSupplierDetailsRegister").hide();
                    $("#formSupplierDetailsUpdate").hide();
                    $("#datosRegistrados").fadeIn();
    
                    var supplierData = data.data;
    
                    // Llenar los datos registrados
                    $("#verContactName").text(supplierData.contactNameAccouting);
                    $("#verContactEmail").text(supplierData.contactEmailAccouting);
                    $("#verContactPhone").text(supplierData.contactPhoneAccouting);
                    $("#verProvide").text(supplierData.Provide);
                }
            },
            error: function () {
                alert("Error al cargar los detalles del proveedor.");
            }
        });
    }
    

    // Función para cargar los datos en el formulario de actualización
    function cargarDatosEnFormularioDeActualizacion() {
        $.ajax({
            url: "/documenta/controlador/SupplierDetailsController.php?op=mostrar",
            type: "POST",
            success: function (response) {
                console.log("Respuesta del servidor:", response);
                var data = JSON.parse(response);
                if (data && data.status === true) {
                    var supplierData = data.data;
                    // Ahora accedemos a los campos desde 'supplierData'
                    $("#contactNameAccoutingUpdate").val(supplierData.contactNameAccouting);
                    $("#contactEmailAccoutingUpdate").val(supplierData.contactEmailAccouting);
                    $("#contactPhoneAccoutingUpdate").val(supplierData.contactPhoneAccouting);
                    $("#ProvideUpdate").val(supplierData.Provide);
                } else {
                    alert("No se encontraron datos para actualización.");
                }
            },
            error: function () {
                alert("Error al cargar los datos para actualización.");
            }
        });
    }
    
    

    // Mostrar el perfil cuando se hace clic en "Regresar a Detalles"
    $("#btnBackToDetails").on("click", function () {
        $("#formSupplierDetailsUpdate").hide();
        $("#datosRegistrados").fadeIn();
    });
});
