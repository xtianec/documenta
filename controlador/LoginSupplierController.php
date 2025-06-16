<?php
// Archivo: ../controlador/LoginSupplierController.php

require_once "../config/Conexion.php"; // Incluir Conexion.php
require_once "../modelos/Supplier.php";

class LoginSupplierController
{
    public function verificar()
    {
        header('Content-Type: application/json');

        try {
            $supplier = new Supplier();

            $ruc = isset($_POST['username']) ? limpiarCadena($_POST['username']) : "";
            $password = isset($_POST['password']) ? trim($_POST['password']) : "";

            if (empty($ruc) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Por favor, rellena todos los campos.']);
                return;
            }

            $result = $supplier->autenticar($ruc, $password);

            if ($result) {
                session_start();
                session_regenerate_id(true); // Regenerar el ID de sesión para seguridad
                $_SESSION['supplier_id'] = $result['id'];
                $_SESSION['RUC'] = $result['RUC'];
                $_SESSION['companyName'] = $result['companyName'];
                $_SESSION['user_role'] = 'proveedor'; // Establecer el rol en español
                $_SESSION['user_type'] = 'supplier'; // Indicar que es un proveedor

                // Registrar el login en la tabla supplier_access_log
                $login_success = $supplier->registrarLogin($result['id']);

                if (!$login_success) {
                    echo json_encode(['success' => false, 'message' => 'Error al registrar el acceso.']);
                    return;
                }

                $response = [
                    'success' => true,
                    'role' => 'proveedor', // Coincidir con 'user_role'
                    'type' => 'supplier'
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
    $controller = new LoginSupplierController();
    switch ($_GET['op']) {
        case 'verificar':
            $controller->verificar();
            break;
    }
}

// Función para registrar errores en el archivo de log
function logError($message) {
    file_put_contents('../logs/errors.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}
?>
