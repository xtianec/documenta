$(document).ready(function () {
    cargarExperiencias(); // Cargar las experiencias al cargar la página

    // Función para cargar experiencias educativas y laborales
    function cargarExperiencias() {
        // Cargar experiencia educativa
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=mostrarEducacion",
            type: "POST",
            success: function (response) {
                console.log(response); // Verificar la respuesta en la consola
                let data = JSON.parse(response);
                let rows = '';
                if (data.length > 0) {
                    data.forEach(function (item) {
                        rows += `<tr>
                            <td>${item.institution}</td>
                            <td>${item.education_type}</td>
                            <td>${item.start_date}</td>
                            <td>${item.end_date}</td>
                            <td>${item.duration}</td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center">No se encontraron datos de experiencia educativa.</td></tr>';
                }

                $('#tablaExperienciaEducativa').html(rows);
            },
            error: function () {
                console.error("Error al cargar experiencia educativa.");
            }
        });

        
        // Cargar experiencia laboral
        $.ajax({
            url: "/documenta/controlador/ExperienceController.php?op=mostrarTrabajo",
            type: "POST",
            success: function (response) {
                console.log(response); // Verificar la respuesta en la consola
                let data = JSON.parse(response);
                let rows = '';

                if (data.length > 0) {
                    data.forEach(function (item) {
                        rows += `<tr>
                            <td>${item.company}</td>
                            <td>${item.position}</td>
                            <td>${item.start_date}</td>
                            <td>${item.end_date}</td>
                        </tr>`;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center">No se encontraron datos de experiencia laboral.</td></tr>';
                }

                $('#tablaExperienciaLaboral').html(rows);
            },
            error: function () {
                console.error("Error al cargar experiencia laboral.");
            }
        });
    }
});
