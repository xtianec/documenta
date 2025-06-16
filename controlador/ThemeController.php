<?php
// controlador/ThemeController.php

require_once "../config/Conexion.php";
require_once "../modelos/Theme.php";

class ThemeController
{
    private $themeModel;

    public function __construct()
    {
        $this->themeModel = new Theme();
    }

    // Obtener el tema del usuario
    public function getTheme()
    {
        header('Content-Type: application/json');
        session_start();

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $theme = $this->themeModel->getTheme($user_id);
        if ($theme !== false) {
            echo json_encode(['success' => true, 'theme' => $theme]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el tema.']);
        }
    }

    // Establecer el tema del usuario
    public function setTheme()
    {
        header('Content-Type: application/json');
        session_start();

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
            exit();
        }

        if (!isset($_POST['theme'])) {
            echo json_encode(['success' => false, 'message' => 'Tema no proporcionado.']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $theme = $_POST['theme'];

        // Validar que el tema es uno de los permitidos
        $allowed_themes = ['default', 'green', 'red', 'blue', 'purple', 'megna', 'default-dark', 'green-dark', 'red-dark', 'blue-dark', 'purple-dark', 'megna-dark'];
        if (!in_array($theme, $allowed_themes)) {
            echo json_encode(['success' => false, 'message' => 'Tema no válido.']);
            exit();
        }

        $result = $this->themeModel->setTheme($user_id, $theme);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Tema actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el tema.']);
        }
    }
}

// Manejar las solicitudes AJAX
if (isset($_GET['op'])) {
    $controller = new ThemeController();
    switch ($_GET['op']) {
        case 'getTheme':
            $controller->getTheme();
            break;
        case 'setTheme':
            $controller->setTheme();
            break;
    }
}
?>
