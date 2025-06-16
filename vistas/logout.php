<?php
// logout.php

session_start();
require_once "../modelos/User.php";

// Determinar la URL de redirección por defecto
$redirectUrl = "login";

// Verificar si existe la variable 'user_type' en la sesión
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    // Opcional: Registrar el logout dependiendo del tipo de usuario
    if ($userId) {
        $user = new User();
        $user->registrarLogout($userId);
    }

    // Determinar la URL de redirección según el tipo de usuario
    switch ($userType) {
        case 'user':
            $redirectUrl = "login";
            break;
        case 'applicant':
            $redirectUrl = "login_postulantes";
            break;
        case 'supplier':
            $redirectUrl = "login_supplier";
            break;
        // Agrega más casos si tienes más tipos de usuarios
        default:
            $redirectUrl = "login";
    }
}

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de login correspondiente
header("Location: " . $redirectUrl);
exit();
?>
