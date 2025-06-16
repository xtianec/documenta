<?php
require_once "../modelos/DocumentNameSupplier.php";
session_start();

class DocumentNameSupplierController
{
    public function guardar()
    {
        $name = isset($_POST["documentName"]) ? $_POST["documentName"] : "";
        $description = isset($_POST["documentDescription"]) ? $_POST["documentDescription"] : "";

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'El nombre del documento es obligatorio.']);
            return;
        }

        $templatePath = null;
        if (isset($_FILES['documentTemplate']) && $_FILES['documentTemplate']['error'] == UPLOAD_ERR_OK) {
            $templatePath = $this->subirPlantilla();
            if (!$templatePath) {
                echo json_encode(['success' => false, 'message' => 'Error al subir la plantilla.']);
                return;
            }
        }

        $document = new DocumentNameSupplier();
        $result = $document->insertar($name, $description, $templatePath);

        echo json_encode($result);
    }

    public function listar()
    {
        $document = new DocumentNameSupplier();
        echo json_encode($document->listar());
    }

    public function obtener()
    {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $document = new DocumentNameSupplier();
        echo json_encode($document->obtener($id));
    }

    public function actualizar()
    {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $name = isset($_POST["name"]) ? $_POST["name"] : "";
        $description = isset($_POST["description"]) ? $_POST["description"] : "";

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            return;
        }

        $templatePath = null;
        if (isset($_FILES['documentTemplate']) && $_FILES['documentTemplate']['error'] == UPLOAD_ERR_OK) {
            $templatePath = $this->subirPlantilla();
            if (!$templatePath) {
                echo json_encode(['success' => false, 'message' => 'Error al subir la plantilla.']);
                return;
            }
        }

        $document = new DocumentNameSupplier();
        $result = $document->actualizar($id, $name, $description, $templatePath);

        echo json_encode($result);
    }

    public function eliminar()
    {
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        if ($id <= 0) {
            echo json_encode(['status' => false, 'message' => 'ID de documento inválido.']);
            return;
        }
    
        $document = new DocumentNameSupplier();
        $result = $document->eliminar($id);
    
        if ($result['success']) {
            echo json_encode(['status' => true, 'message' => 'Documento eliminado correctamente.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Error al eliminar el documento: ' . $result['error']]);
        }
    }
    

    private function subirPlantilla()
    {
        $file = $_FILES['documentTemplate'];
        $fileName = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf', 'doc', 'docx'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            return false;
        }

        $uploadDir = '../uploads/templates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('template_', true) . '.' . $fileExtension;
        $destinationPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            return $destinationPath;
        }

        return false;
    }
}

// Manejar solicitudes
if (isset($_GET["op"])) {
    $controller = new DocumentNameSupplierController();
    switch ($_GET["op"]) {
        case 'guardar':
            $controller->guardar();
            break;
        case 'listar':
            $controller->listar();
            break;
        case 'obtener':
            $controller->obtener();
            break;
        case 'actualizar':
            $controller->actualizar();
            break;
        case 'eliminar':
            $controller->eliminar();
            break;
    }
}
?>
