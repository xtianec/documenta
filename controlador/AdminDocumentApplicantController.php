<?php
require_once "../modelos/DocumentApplicant.php";

class AdminDocumentApplicantController
{

    // Listar documentos pendientes de evaluación
    public function listarDocumentos()
    {
        $documentModel = new DocumentApplicant();
        $documents = $documentModel->listarTodos();

        if ($documents && count($documents) > 0) {
            echo json_encode($documents);
        } else {
            echo json_encode(['status' => false, 'message' => 'No se encontraron documentos.']);
        }
    }

    // Marcar documento como revisado
    public function marcarRevisado()
    {
        if (!isset($_POST['document_id'])) {
            echo json_encode(['status' => false, 'message' => 'ID de documento no proporcionado.']);
            return;
        }
    
        $document_id = $_POST['document_id'];
        $documentModel = new DocumentApplicant();
    
        // Ejecutar la función que marca el documento como revisado
        $result = $documentModel->marcarRevisado($document_id);
    
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Documento marcado como revisado.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Error al marcar el documento como revisado.']);
        }
    }
    


    // Evaluar documento
    // Evaluar documento
    public function evaluarDocumento()
    {
        if (!isset($_POST['document_id']) || !isset($_POST['estado_documento'])) {
            echo json_encode(['status' => false, 'message' => 'Datos incompletos para evaluar el documento.']);
            return;
        }
    
        $document_id = $_POST['document_id'];
        $admin_observation = isset($_POST['admin_observation']) ? $_POST['admin_observation'] : '';
        $estado_documento = $_POST['estado_documento'];
    
        // Determinar el estado en base a la opción seleccionada
        $state_id = ($estado_documento == 'Aprobado') ? 3 : ($estado_documento == 'Rechazado' ? 4 : 5);  // Aprobado=3, Rechazado=4, Pendiente de corrección=5
    
        $documentModel = new DocumentApplicant();
        $result = $documentModel->evaluarDocumento($document_id, $admin_observation, $state_id);
    
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Evaluación guardada correctamente.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Error al guardar la evaluación.']);
        }
    }

}

// Procesar las solicitudes
if (isset($_GET['op'])) {
    $adminDocumentController = new AdminDocumentApplicantController();

    switch ($_GET['op']) {
        case 'listarDocumentos':
            $adminDocumentController->listarDocumentos();
            break;
        case 'marcarRevisado':
            $adminDocumentController->marcarRevisado();
            break;
        case 'evaluarDocumento':
            $adminDocumentController->evaluarDocumento();
            break;
    }
}
