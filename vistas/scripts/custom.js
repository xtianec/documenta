// ../app/template/js/theme-switcher.js

$(document).ready(function () {

    // Manejar el cambio de tema al hacer clic en una opción
    $('#themecolors a').on('click', function () {
        var theme = $(this).data('theme'); // Obtener el valor de data-theme

        // **Actualizar la ruta al archivo CSS del tema**
        var themePath = '/documenta/app/template/css/colors/' + theme + '.css'; // Ruta correcta al archivo CSS

        var $this = $(this); // Guardar referencia a 'this'

        // Verificar si el archivo CSS existe antes de aplicarlo
        $.ajax({
            url: themePath,
            type: 'HEAD',
            success: function() {
                // Cambiar el atributo href del enlace de tema
                $('#theme').attr('href', themePath);

                // Eliminar la clase 'working' de todos los enlaces de tema
                $('#themecolors a').removeClass('working');

                // Agregar la clase 'working' al enlace de tema seleccionado
                $this.addClass('working');

                // Guardar el tema seleccionado en localStorage
                localStorage.setItem('selectedTheme', theme);
            },
            error: function() {
                alert("El tema seleccionado no está disponible.");
            }
        });
    });

    // Cargar el tema guardado al iniciar la página
    var savedTheme = localStorage.getItem('selectedTheme');
    if (savedTheme) {
        var themePath = '/documenta/app/template/css/colors/' + savedTheme + '.css'; // Ruta correcta al archivo CSS

        $('#theme').attr('href', themePath);

        // Actualizar la clase 'working' en los enlaces de tema
        $('#themecolors a').removeClass('working');
        $('#themecolors a[data-theme="' + savedTheme + '"]').addClass('working');
    }

    // Manejar el toggle del Right Sidebar
    $('.right-side-toggle').on('click', function () {
        $('.right-sidebar').toggleClass('shw-rside');
    });
});
