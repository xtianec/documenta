$(document).ready(function () {
    // Inicializar validación de formularios
    inicializarValidacionFormularios();

    // Cargar los datos personales si ya existen
    cargarDatosPersonales();

    // Manejar la selección de archivo
    $('#photo').on('change', function () {
        var fileName = this.files[0] ? this.files[0].name : 'No se ha seleccionado ninguna foto';
        $('#file-name').text(fileName);

        // Previsualizar la foto seleccionada
        var file = this.files[0];
        var preview = $('#previewPhoto');
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            preview.attr('src', '').hide();
        }
    });

    // Calcular edad automáticamente al cambiar la fecha de nacimiento
    // Calcular edad automáticamente al cambiar la fecha de nacimiento
    $("#birth_date").on("change", function () {
        var birthDate = new Date(this.value);
        var today = new Date();
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age > 0 && age < 120) {
            $("#age").val(age);
        } else {
            $("#age").val('');
        }
    });


    // Formulario para registrar o actualizar datos
    $("#formUserDetails").on("submit", function (e) {
        e.preventDefault();
        var form = this;
        if (!validarFormulario(form)) return;
        var formData = new FormData(form);

        var url = "/documenta/controlador/UserDetailsController.php?op=guardarOActualizar";

        enviarFormulario(url, formData, function (jsonResponse) {
            if (jsonResponse.status) {
                Toastify({
                    text: jsonResponse.message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
                cargarDatosPersonales();
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
        });
    });

    // Mostrar formulario de actualización al hacer clic en "Editar Perfil"
    $("#btnEditarPerfil").on("click", function () {
        $("#datosRegistrados").fadeOut(function () {
            $("#formUserDetails").fadeIn();
            cargarDatosEnFormulario();
        });
    });

    // Funciones auxiliares
    function inicializarValidacionFormularios() {
        var forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    }

    function validarFormulario(form) {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
        return true;
    }

    function enviarFormulario(url, formData, onSuccess) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    onSuccess(jsonResponse);
                } catch (e) {
                    Toastify({
                        text: "Respuesta inválida del servidor.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            },
            error: function () {
                Toastify({
                    text: "Error al procesar la solicitud.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }

    function cargarDatosPersonales() {
        $.ajax({
            url: "/documenta/controlador/UserDetailsController.php?op=mostrar",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status === false) {
                    // Mostrar el formulario si no hay datos
                    $("#formUserDetails").fadeIn();
                    $("#datosRegistrados").hide();
                } else {
                    // Mostrar los datos registrados
                    $("#formUserDetails").hide();
                    $("#datosRegistrados").fadeIn();

                    // Mostrar la foto del usuario
                    if (data.data.photo) {
                        $("#verPhoto").attr("src", data.data.photo).show();
                    } else {
                        $("#verPhoto").attr("src", "/documenta/template/images/default_photo.png").show();
                    }

                    // Llenar los datos
                    $("#verNombre").text(data.data.full_name || "Nombre Completo");
                    $("#verPersonContactName").text(data.data.person_contact_name || "No registrado");
                    $("#verPersonContactPhone").text(data.data.person_contact_phone || "No registrado");
                    $("#verPhone").text(data.data.phone || "No registrado");
                    $("#verEmail").text(data.data.email || "No registrado");
                    $("#verCountry").text(data.data.country || "No registrado");
                    $("#verEntryDate").text(data.data.entry_date || "No registrado");
                    $("#verBirthDate").text(data.data.birth_date || "No registrado");
                    $("#verAge").text(data.data.age || "No registrado");

                    $("#verBloodType").text(data.data.blood_type || "No registrado");
                    $("#verAllergies").text(data.data.allergies || "No registrado");
                }
            },
            error: function () {
                Toastify({
                    text: "Error al cargar los datos personales.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }

    function cargarDatosEnFormulario() {
        $.ajax({
            url: "/documenta/controlador/UserDetailsController.php?op=mostrar",
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status === true) {
                    $("#person_contact_name").val(data.data.person_contact_name);
                    $("#person_contact_phone").val(data.data.person_contact_phone);
                    $("#phone").val(data.data.phone);
                    $("#email").val(data.data.email);
                    $("#country").val(data.data.country);
                    $("#entry_date").val(data.data.entry_date);
                    $("#birth_date").val(data.data.birth_date);
                    $("#blood_type").val(data.data.blood_type);
                    $("#allergies").val(data.data.allergies);
                    $("#age").val(data.data.age);

                    // Mostrar la foto actual en la previsualización
                    if (data.data.photo) {
                        $("#previewPhoto").attr("src", data.data.photo).show();
                    } else {
                        $("#previewPhoto").hide();
                    }
                } else {
                    Toastify({
                        text: "Error al cargar los datos para actualizar.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                }
            },
            error: function () {
                Toastify({
                    text: "Error al cargar los datos para actualizar.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }
});
