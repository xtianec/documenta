// theme.js

$(document).ready(function() {
    // Función para establecer el tema
    function setTheme(themeName) {
        var themePath = '/documenta/app/template/css/colors/' + themeName + '.css';
        $('#theme').attr('href', themePath);

        // Actualizar la clase 'working' en el sidebar
        $('#themecolors a').removeClass('working');
        $('#themecolors a[data-theme="' + themeName + '"]').addClass('working');
    }

    // Obtener el tema guardado en localStorage (si existe)
    var savedTheme = localStorage.getItem('selectedTheme');
    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        setTheme('default'); // Tema por defecto si no hay ninguno guardado
    }

    // Manejar el clic en las opciones de tema
    $('#themecolors a').on('click', function(e) {
        e.preventDefault();
        var selectedTheme = $(this).data('theme');
        setTheme(selectedTheme);
        // Guardar la selección en localStorage
        localStorage.setItem('selectedTheme', selectedTheme);
    });

    // Función para abrir y cerrar el Right Sidebar
    function toggleRightSidebar() {
        $('.right-sidebar').toggleClass('open');
        $('body').toggleClass('sidebar-open'); // Opcional: deshabilita el scroll del body cuando el sidebar está abierto
    }

    // Manejar el clic en el botón de toggle
    $('.right-side-toggle').on('click', function(e) {
        e.preventDefault();
        toggleRightSidebar();
    });
});
