<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../modelos/DocumentSupplier.php";
session_start(); // Asegurarse de que la sesión esté iniciada

class DocumentSupplierController
{
    // Subir un nuevo documento
    public function subir()
    {
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $supplier_id = $_SESSION['supplier_id'];
        $documentNameSupplier_id = isset($_POST['documentNameSupplier_id']) ? intval($_POST['documentNameSupplier_id']) : 0;
        $state_id = 1; // Estado inicial
        $admin_reviewed = 0; // Indica si el administrador ha revisado el documento

        // Validar que se haya seleccionado un tipo de documento
        if ($documentNameSupplier_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de documento inválido.']);
            return;
        }

        // Validar que se haya subido un archivo
        if (!isset($_FILES['documentFile']) || $_FILES['documentFile']['error'] != UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un archivo válido.']);
            return;
        }

        // Validar y procesar el archivo
        $file = $_FILES['documentFile'];
        $originalFileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmpPath = $file['tmp_name'];

        // Validar tamaño del archivo (por ejemplo, máximo 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 5MB).']);
            return;
        }

        // Validar tipo de archivo permitido (por ejemplo, PDF)
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido.']);
            return;
        }

        // Generar un nombre único para el archivo
        $newFileName = uniqid('doc_', true) . '.' . $fileExtension;

        // Definir la ruta donde se guardará el archivo
        $uploadDir = '../uploads/supplier/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destinationPath = $uploadDir . $newFileName;

        // Mover el archivo a la ubicación final
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            // Preparar los datos para insertar en la base de datos
            $data = [
                'supplier_id' => $supplier_id,
                'documentNameSupplier_id' => $documentNameSupplier_id,
                'documentFileName' => $newFileName,
                'documentPath' => $destinationPath,
                'originalFileName' => $originalFileName,
                'state_id' => $state_id,
                'admin_reviewed' => $admin_reviewed,
            ];

            $documentSupplier = new DocumentSupplier();
            $result = $documentSupplier->insertarOActualizar($data);

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Documento subido exitosamente.',
                    'documentPath' => $destinationPath,
                    'originalFileName' => $originalFileName
                ]);
            } else {
                // Eliminar el archivo si hubo un error al insertar en la base de datos
                if (file_exists($destinationPath)) {
                    unlink($destinationPath);
                }
                echo json_encode(['success' => false, 'message' => 'Error al guardar el documento: ' . $result['error']]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo al directorio de destino.']);
        }
    }

    // Listar documentos disponibles para subir
    public function listarDocumentos()
    {
        $documentSupplier = new DocumentSupplier();
        $result = $documentSupplier->listarDocumentosDisponibles();

        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener los documentos: ' . $result['error']]);
        }
    }

    // Obtener el estado de los documentos subidos por el proveedor
    public function obtenerEstadoDocumentos()
    {
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $supplier_id = $_SESSION['supplier_id'];
        $documentSupplier = new DocumentSupplier();
        $result = $documentSupplier->obtenerEstadoDocumentos($supplier_id);

        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el estado de los documentos: ' . $result['error']]);
        }
    }

    public function eliminar()
    {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }
    
        $supplier_id = $_SESSION['supplier_id'];
        $documentNameSupplier_id = isset($_POST['documentNameSupplier_id']) ? intval($_POST['documentNameSupplier_id']) : 0;
    
        if ($documentNameSupplier_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de documento inválido.']);
            return;
        }
    
        $documentSupplier = new DocumentSupplier();
        $result = $documentSupplier->eliminarDocumento($supplier_id, $documentNameSupplier_id);
    
        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Documento eliminado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el documento: ' . $result['error']]);
        }
    }
    
}

// Manejar las solicitudes
// Manejar las solicitudes
// Manejar las solicitudes
if (isset($_GET['op'])) {
    $controller = new DocumentSupplierController();
    switch ($_GET['op']) {
        case 'subir':
            $controller->subir();
            break;
        case 'listarDocumentos':
            $controller->listarDocumentos();
            break;
        case 'obtenerEstadoDocumentos':
            $controller->obtenerEstadoDocumentos();
            break;
        case 'eliminar':
            $controller->eliminar();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
