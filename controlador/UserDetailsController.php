<?php
require_once "../modelos/UserDetails.php";
session_start();

class UserDetailsController
{
    public function guardarOActualizar()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Obtener y limpiar los datos del formulario
        $person_contact_name   = isset($_POST["person_contact_name"]) ? $this->limpiarCadena($_POST["person_contact_name"]) : null;
        $person_contact_phone  = isset($_POST["person_contact_phone"]) ? $this->limpiarCadena($_POST["person_contact_phone"]) : null;
        $phone                 = isset($_POST["phone"]) ? $this->limpiarCadena($_POST["phone"]) : null;
        $email                 = isset($_POST["email"]) ? $this->limpiarCadena($_POST["email"]) : null;
        $country               = isset($_POST["country"]) ? $this->limpiarCadena($_POST["country"]) : null;
        $entry_date            = isset($_POST["entry_date"]) ? $this->limpiarCadena($_POST["entry_date"]) : null;
        $birth_date            = isset($_POST["birth_date"]) ? $this->limpiarCadena($_POST["birth_date"]) : null;
        $blood_type            = isset($_POST["blood_type"]) ? $this->limpiarCadena($_POST["blood_type"]) : null;
        $allergies             = isset($_POST["allergies"]) ? $this->limpiarCadena($_POST["allergies"]) : null;
        $photo                 = isset($_FILES["photo"]) ? $_FILES["photo"] : null;

        // Calcular la edad automáticamente
        $age = $this->calcularEdad($birth_date);

        // Validaciones
        $errors = $this->validarDatos($phone, $email, $country, $birth_date);

        // Procesar la foto
        $photo_url = $this->procesarFoto($photo, $user_id, $errors);

        if (!empty($errors)) {
            echo json_encode(['status' => false, 'message' => implode(" ", $errors)]);
            return;
        }

        // Preparar los parámetros
        $params = [
            $person_contact_name,
            $person_contact_phone,
            $phone,
            $email,
            $country,
            $entry_date,
            $birth_date,
            $age,
            $blood_type,
            $allergies
        ];

        $userDetails = new UserDetails();

        // Verificar si el registro ya existe
        $existingRecord = $userDetails->mostrar($user_id);

        if ($existingRecord) {
            // Actualizar
            $result = $userDetails->actualizar($user_id, $params, $photo_url);
            $message = $result ? "Datos actualizados correctamente." : "Error al actualizar los datos.";
        } else {
            // Insertar
            $result = $userDetails->insertar($user_id, $params, $photo_url);
            $message = $result ? "Datos guardados correctamente." : "Error al guardar los datos.";
        }

        echo json_encode([
            'status' => $result ? true : false,
            'message' => $message
        ]);
    }

    public function mostrar()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $userDetails = new UserDetails();
        $result = $userDetails->mostrar($user_id);

        if ($result) {
            echo json_encode([
                'status' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => "No se encontraron datos registrados."
            ]);
        }
    }

    // Función para limpiar cadenas y evitar inyecciones
    private function limpiarCadena($cadena)
    {
        return htmlspecialchars(strip_tags(trim($cadena)));
    }

    // Función para calcular la edad
    private function calcularEdad($birth_date)
    {
        if ($birth_date) {
            $birthDate = new DateTime($birth_date);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
            return $age;
        }
        return null;
    }

    // Función para validar datos
    private function validarDatos($phone, $email, $country, $birth_date)
    {
        $errors = [];

        if (empty($phone)) {
            $errors[] = "El teléfono personal es obligatorio.";
        } elseif (!preg_match('/^\d{7,15}$/', $phone)) {
            $errors[] = "El teléfono personal debe contener entre 7 y 15 dígitos.";
        }

        if (empty($email)) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }

        if (empty($country)) {
            $errors[] = "La nacionalidad es obligatoria.";
        }

        if (empty($birth_date)) {
            $errors[] = "La fecha de nacimiento es obligatoria.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
            $errors[] = "La fecha de nacimiento no es válida.";
        }

        return $errors;
    }

    // Función para procesar la foto
    private function procesarFoto($photo, $user_id, &$errors, $isUpdate = false)
    {
        $photo_url = null;
        if ($photo && $photo['error'] != UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if ($photo['error'] != UPLOAD_ERR_OK) {
                $errors[] = "Error al subir la foto.";
            } elseif (!in_array($photo['type'], $allowed_types)) {
                $errors[] = "Tipo de archivo de foto no permitido. Solo se aceptan JPG, PNG y GIF.";
            } else {
                // Procesar la subida de la foto
                $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $server_path = $_SERVER['DOCUMENT_ROOT'] . '/documenta/uploads/user_photos/' . $filename; // Ajusta la ruta según tu estructura
                $web_path = '/documenta/uploads/user_photos/' . $filename; // Ruta accesible vía web

                // Asegurarse de que el directorio exista
                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/documenta/uploads/user_photos/')) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/documenta/uploads/user_photos/', 0755, true);
                }

                // Mover el archivo
                if (move_uploaded_file($photo['tmp_name'], $server_path)) {
                    $photo_url = $web_path;
                } else {
                    $errors[] = "Error al mover la foto al servidor.";
                }
            }
        } elseif ($isUpdate) {
            // Obtener la foto actual si no se subió una nueva
            $userDetails = new UserDetails();
            $userData = $userDetails->mostrar($user_id);
            $photo_url = $userData['photo'];
        }

        return $photo_url;
    }

    
}

if (isset($_GET["op"])) {
    $controller = new UserDetailsController();
    switch ($_GET["op"]) {
        case 'guardarOActualizar':
            $controller->guardarOActualizar();
            break;
            
        case 'mostrar':
            $controller->mostrar();
            break;
        default:
            echo json_encode(['status' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
