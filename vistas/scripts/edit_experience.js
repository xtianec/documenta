$(document).ready(function () {
    cargarExperiencia(); // Cargar experiencias existentes al cargar la página

    // Cargar experiencia educativa y laboral
    function cargarExperiencia() {
        // Cargar experiencia educativa
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=mostrarEducacion",
            type: "POST",
            success: function (response) {
                let data = JSON.parse(response);
                $("#tablaExperienciaEducativa tbody").empty();
                if (data.length > 0) {
                    data.forEach(item => {
                        agregarFilaEducacion(item.id, item.institution, item.education_type, item.start_date, item.end_date, item.duration);
                    });
                } else {
                    $("#tablaExperienciaEducativa tbody").append('<tr><td colspan="6" class="text-center">No se encontraron datos.</td></tr>');
                }
            }
        });

        // Cargar experiencia laboral
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=mostrarTrabajo",
            type: "POST",
            success: function (response) {
                let data = JSON.parse(response);
                $("#tablaExperienciaLaboral tbody").empty();
                if (data.length > 0) {
                    data.forEach(item => {
                        agregarFilaTrabajo(item.id, item.company, item.position, item.start_date, item.end_date);
                    });
                } else {
                    $("#tablaExperienciaLaboral tbody").append('<tr><td colspan="5" class="text-center">No se encontraron datos.</td></tr>');
                }
            }
        });
    }

    // Función para agregar filas de experiencia educativa con datos cargados
    function agregarFilaEducacion(id, institution = '', education_type = '', start_date = '', end_date = '', duration = '') {
        let fila = `
        <tr>
            <td><input type="hidden" name="education_id[]" value="${id}"><input type="text" class="form-control" name="institution[]" value="${institution}" required></td>
            <td><input type="text" class="form-control" name="education_type[]" value="${education_type}" required></td>
            <td><input type="date" class="form-control" name="start_date_education[]" value="${start_date}" required></td>
            <td><input type="date" class="form-control" name="end_date_education[]" value="${end_date}" required></td>
            <td><input type="number" class="form-control" name="duration_education[]" value="${duration}" min="1" required></td>
            <td><button type="button" class="btn btn-danger btnRemoveRow"><i class="fa fa-trash-o"></i></button></td>
        </tr>`;
        $("#tablaExperienciaEducativa tbody").append(fila);
    }

    // Función para agregar filas de experiencia laboral con datos cargados
    function agregarFilaTrabajo(id, company = '', position = '', start_date = '', end_date = '') {
        let fila = `
        <tr>
            <td><input type="hidden" name="work_id[]" value="${id}"><input type="text" class="form-control" name="company[]" value="${company}" required></td>
            <td><input type="text" class="form-control" name="position[]" value="${position}" required></td>
            <td><input type="date" class="form-control" name="start_date_work[]" value="${start_date}" required></td>
            <td><input type="date" class="form-control" name="end_date_work[]" value="${end_date}" required></td>
            <td><button type="button" class="btn btn-danger btnRemoveRow"><i class="fa fa-trash-o"></i></button></td>
        </tr>`;
        $("#tablaExperienciaLaboral tbody").append(fila);
    }

    // Eliminar una fila
    $(document).on('click', '.btnRemoveRow', function () {
        $(this).closest('tr').remove();
    });

    // Guardar cambios de experiencia educativa
    $("#formEducation").on("submit", function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=guardarCambios",
            type: "POST",
            data: formData,
            success: function (response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status) {
                    alert("Experiencia educativa actualizada correctamente.");
                } else {
                    alert("Error al actualizar: " + jsonResponse.message);
                }
            },
        });
    });

    // Guardar cambios de experiencia laboral
    $("#formWork").on("submit", function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=guardarCambios",
            type: "POST",
            data: formData,
            success: function (response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status) {
                    alert("Experiencia laboral actualizada correctamente.");
                } else {
                    alert("Error al actualizar: " + jsonResponse.message);
                }
            },
        });
    });
});
