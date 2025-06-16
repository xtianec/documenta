<?php
// controlador/DocumentEvaluationController.php

require "../config/Conexion.php";
require_once "../modelos/DocumentApplicant.php";
require_once "../modelos/Experience.php";
require_once "../modelos/Companies.php"; // Nuevo modelo para Empresas
require_once "../modelos/Jobs.php"; // Nuevo modelo para Puestos

class DocumentEvaluationController
{
    // Constructor para iniciar la sesión
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Método para verificar si el usuario es superadministrador
    private function verificarSuperadmin()
    {
        if (
            !isset($_SESSION['user_type']) ||
            $_SESSION['user_type'] !== 'user' ||
            !isset($_SESSION['user_role']) ||
            $_SESSION['user_role'] !== 'superadmin'
        ) {
            echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
            exit();
        }
    }

    // Listar postulantes con Server-Side Processing
    public function listarApplicants()
    {
        $this->verificarSuperadmin();

        // Parámetros de DataTables
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        // Ordenamiento
        $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';
        $orderColumn = 'c.company_name'; // Predeterminado
        $columns = [
            0 => 'c.company_name',
            1 => 'j.position_name',
            2 => 'a.names',
            3 => 'a.email'
            // Añade más columnas según tu necesidad
        ];
        if (isset($columns[$orderColumnIndex])) {
            $orderColumn = $columns[$orderColumnIndex];
        }

        // Obtener los filtros si existen
        $start_date = isset($_POST['start_date']) && !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $company_id = isset($_POST['company_id']) && !empty($_POST['company_id']) ? intval($_POST['company_id']) : null;
        $job_id = isset($_POST['job_id']) && !empty($_POST['job_id']) ? intval($_POST['job_id']) : null;

        $documentApplicant = new DocumentApplicant();
        $totalRecords = $documentApplicant->contarTodos($start_date, $end_date, $searchValue, $company_id, $job_id);
        $documents = $documentApplicant->listarTodosServerSide($start, $length, $orderColumn, $orderDir, $start_date, $end_date, $searchValue, $company_id, $job_id);

        if (!empty($documents)) {
            echo json_encode([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords, // Puede ajustarse si se maneja correctamente la búsqueda
                'data' => $documents
            ]);
        } else {
            echo json_encode([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    // Obtener lista de Empresas
    public function obtenerEmpresas()
    {
        $this->verificarSuperadmin();

        $company = new Companies();
        $empresas = $company->listarEmpresas();

        if (!empty($empresas)) {
            echo json_encode([
                'success' => true,
                'empresas' => $empresas
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron empresas.']);
        }
    }

    // Obtener lista de Puestos por Empresa
    public function obtenerPuestosPorEmpresa()
    {
        $this->verificarSuperadmin();

        $company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;

        if ($company_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de empresa inválido.']);
            exit();
        }

        $job = new Jobs();
        $puestos = $job->listarPuestosPorEmpresa($company_id);

        if (!empty($puestos)) {
            echo json_encode([
                'success' => true,
                'puestos' => $puestos
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron puestos para esta empresa.']);
        }
    }

    // Obtener nombre del postulante
    public function obtenerNombreApplicant()
    {
        $this->verificarSuperadmin();

        $applicant_id = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

        if ($applicant_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $documentApplicant = new DocumentApplicant();
        $nombre = $documentApplicant->obtenerNombreApplicant($applicant_id);

        if ($nombre) {
            echo json_encode([
                'success' => true,
                'nombre' => $nombre
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Postulante no encontrado.']);
        }
    }

    // Obtener documentos subidos por un postulante
    public function documentosApplicant()
    {
        $this->verificarSuperadmin();

        $applicant_id = isset($_POST['applicant_id']) ? intval($_POST['applicant_id']) : 0;

        if ($applicant_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $documentApplicant = new DocumentApplicant();
        $documents = $documentApplicant->obtenerDocumentosPorUsuario($applicant_id);

        if (!empty($documents)) {
            // Separar los documentos en CV y Otros
            $cv_documents = [];
            $other_documents = [];
            foreach ($documents as $doc) {
                if (stripos($doc['document_name'], 'cv') !== false) {
                    $cv_documents[] = $doc;
                } else {
                    $other_documents[] = $doc;
                }
            }

            echo json_encode([
                'success' => true,
                'cv_documents' => $cv_documents,
                'other_documents' => $other_documents
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron documentos para este postulante.']);
        }
    }

    // Cambiar estado de un documento
    public function cambiarEstadoDocumento()
    {
        $this->verificarSuperadmin();

        $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
        $estado_id = isset($_POST['estado_id']) ? intval($_POST['estado_id']) : 0;
        $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : NULL;

        // Validar que el estado_id sea válido
        $valid_estados = [2, 3, 4]; // 2: Aprobado, 3: Rechazado, 4: Por Corregir
        if (!in_array($estado_id, $valid_estados)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido.']);
            exit();
        }

        $documentApplicant = new DocumentApplicant();
        $document = $documentApplicant->mostrar($document_id);

        if (!$document) {
            echo json_encode(['success' => false, 'message' => 'Documento no encontrado.']);
            exit();
        }

        // Actualizar el estado del documento
        $result = $documentApplicant->evaluarDocumento($document_id, $observacion, $estado_id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
    }

    // Obtener experiencia educativa de un postulante
    public function obtenerEducacion()
    {
        $this->verificarSuperadmin();

        $applicantId = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

        if ($applicantId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $experience = new Experience();
        $educaciones = $experience->mostrarEducacion($applicantId);

        echo json_encode([
            'success' => true,
            'educaciones' => $educaciones
        ]);
    }

    // Obtener experiencia laboral de un postulante
    public function obtenerTrabajo()
    {
        $this->verificarSuperadmin();

        $applicantId = isset($_GET['applicant_id']) ? intval($_GET['applicant_id']) : 0;

        if ($applicantId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de postulante inválido.']);
            exit();
        }

        $experience = new Experience();
        $trabajos = $experience->mostrarTrabajo($applicantId);

        echo json_encode([
            'success' => true,
            'trabajos' => $trabajos
        ]);
    }

    // Servir imagen optimizada (opcional)
    public function servirImagen()
    {
        $path = isset($_GET['path']) ? $_GET['path'] : '';
        $imagePath = "../" . $path;
        if (file_exists($imagePath)) {
            $info = getimagesize($imagePath);
            header("Content-Type: " . $info['mime']);
            header("Cache-Control: public, max-age=86400"); // Caché por un día
            readfile($imagePath);
            exit;
        } else {
            // Imagen por defecto
            readfile('/rh/app/template/images/default_photo.png');
            exit;
        }
    }

    // Listar todos los postulantes para DataTables (si es necesario)
    // Puedes agregar más métodos según tus necesidades
}

// Manejo de las acciones
if (isset($_GET['op'])) {
    $controller = new DocumentEvaluationController();
    switch ($_GET['op']) {
        case 'listarApplicants':
            $controller->listarApplicants();
            break;
        case 'obtenerEmpresas':
            $controller->obtenerEmpresas();
            break;
        case 'obtenerPuestosPorEmpresa':
            $controller->obtenerPuestosPorEmpresa();
            break;
        case 'documentosApplicant':
            $controller->documentosApplicant();
            break;
        case 'cambiarEstadoDocumento':
            $controller->cambiarEstadoDocumento();
            break;
        case 'obtenerEducacion':
            $controller->obtenerEducacion();
            break;
        case 'obtenerTrabajo':
            $controller->obtenerTrabajo();
            break;
        case 'obtenerNombreApplicant':
            $controller->obtenerNombreApplicant();
            break;
        case 'servirImagen':
            $controller->servirImagen();
            break;
        // Otros casos si los hay
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
?>
