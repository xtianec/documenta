<?php
// ../controlador/LoginPostulanteController.php

require_once "../modelos/Applicant.php";
require_once "../config/Conexion.php"; // Asegúrate de que la ruta sea correcta
require_once "../config/Utilidades.php";

class LoginPostulanteController
{
    public function verificar()
    {
        header('Content-Type: application/json'); // Forzar respuesta JSON

        try {
            $applicant = new Applicant();

            $username = isset($_POST['username']) ? limpiarCadena($_POST['username']) : "";
            $password = isset($_POST['password']) ? limpiarCadena($_POST['password']) : "";

            // Validar que los campos no estén vacíos
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Por favor, rellena todos los campos.']);
                return;
            }

            // Intentar autenticar el usuario
            $result = $applicant->autenticar($username, $password);

            if ($result) {
                // Inicia sesión si la autenticación fue exitosa
                session_start();
                session_regenerate_id(true); // Regenerar el ID de sesión para seguridad
                $_SESSION['applicant_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['user_role'] = 'postulante'; // Establecer el rol en español
                $_SESSION['user_type'] = 'applicant'; // Indicar que es un postulante
                $_SESSION['names'] = $result['names']; // Almacenar el nombre completo

                // Registrar el login
                $applicant->registrarLogin($result['id']);

                $response = [
                    'success' => true,
                    'role' => 'postulante', // Coincidir con 'user_role'
                    'type' => 'applicant'
                ];
            } else {
                $response = ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];
            }

        } catch (Exception $e) {
            logError($e->getMessage()); // Registra el error en el archivo de log
            $response = [
                'success' => false,
                'message' => 'Error interno del servidor. Por favor, inténtalo de nuevo.'
            ];
        }

        echo json_encode($response);
        exit();
    }
}

// Manejador de las solicitudes AJAX
if (isset($_GET['op'])) {
    $controller = new LoginPostulanteController();
    switch ($_GET['op']) {
        case 'verificar':
            $controller->verificar();
            break;
    }
}

// Asegúrate de que la función limpiarCadena está definida correctamente

?>
