<?php
// navbar.php

// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar variables por defecto
$name = 'Usuario';
$display_role = '';

// Verificar si el usuario ha iniciado sesión y determinar el tipo y rol
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

    switch ($user_type) {
        case 'user':
            // Para usuarios normales, obtener el nombre completo
            $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Usuario';
            $display_role = ucfirst(htmlspecialchars($user_role)); // 'Superadmin', 'Adminrh', etc.
            break;
        case 'applicant':
            // Para postulantes, obtener el nombre
            $name = isset($_SESSION['names']) ? $_SESSION['names'] : 'Postulante';
            $display_role = 'Postulante';
            break;
        case 'supplier':
            // Para proveedores, obtener el nombre de la empresa
            $name = isset($_SESSION['companyName']) ? $_SESSION['companyName'] : 'Proveedor';
            $display_role = 'Proveedor';
            break;
        default:
            $name = 'Usuario';
            $display_role = '';
    }
}
?>

<!-- Inicio del HTML de la Navbar -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- Navbar Header -->
        <div class="navbar-header">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <b>
                    <!-- Dark Logo icon -->
                    <img src="/documenta/app/template/images/LOGO_FONDO_TRANSPARENTE.png" alt="homepage" class="dark-logo" style="height: 40px;" />
                    <!-- Light Logo icon -->
                    <img src="/documenta/app/template/images/LOGO_FONDO_NEGRO_TRANSPARENTE.png" alt="homepage" class="light-logo" style="height: 40px;" />
                </b>
                <!-- Logo text -->
                <span>
                    <!-- dark Logo text -->
                    <img src="/documenta/app/template/images/TEXTO_FONDO_TRANSPARENTE.png" alt="homepage" class="dark-logo" style="height: 40px; " />
                    <!-- Light Logo text -->
                    <img src="/documenta/app/template/images/TEXTO_FONDO_NEGRO_TRANSPARENTE.png" class="light-logo" alt="homepage" style="height: 40px;" />
                </span>

            </a>
            <!-- Botón Toggler para el navbar en dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapseContent"
                aria-controls="navbarCollapseContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <!-- Fin del Navbar Header -->

        <!-- Contenido colapsable -->
        <div class="collapse navbar-collapse" id="navbarCollapseContent">
            <!-- Menú izquierdo -->
            <ul class="navbar-nav mr-auto">
                <!-- Botón para alternar el sidebar en dispositivos de escritorio -->
                <li class="nav-item">
                    <a class="sidebartoggler nav-link waves-effect waves-light d-none d-md-block" href="javascript:void(0)">
                        <i class="ti-menu"></i>
                    </a>

                </li>
                <!-- Puedes agregar otros elementos aquí si lo deseas -->
            </ul>
            <!-- Menú derecho -->
            <ul class="navbar-nav ml-auto">
                <!-- Elementos del menú derecho -->
                <li class="nav-item dropdown">
                    <?php if (isset($_SESSION['user_type'])): ?>
                        <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" id="navbarDropdownUser" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Bienvenido, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($display_role); ?>)
                        </a>
                        <div class="dropdown-menu dropdown-menu-right animated flipInY" aria-labelledby="navbarDropdownUser">
                            <a href="logout" class="dropdown-item">Cerrar Sesión</a>
                        </div>
                    <?php else: ?>
                        <a class="nav-link" href="login">Iniciar Sesión</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
        <!-- Fin del contenido colapsable -->
    </nav>
</header>
<!-- Fin del HTML de la Navbar -->