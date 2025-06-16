<?php
// controlador/UserDocumentsController.php

require "../config/Conexion.php";
require_once "../modelos/Companies.php"; // Nuevo modelo para Empresas
require_once "../modelos/Jobs.php"; // Nuevo modelo para Puestos

class UserDocumentsController
{

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
    // Listar usuarios con porcentajes de documentos subidos y aprobados
    public function listarUsuarios()
    {
        $this->verificarSuperadmin();

        // Obtener los filtros de fecha si existen
        $start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;
        $company_id = isset($_GET['company_id']) && !empty($_GET['company_id']) ? intval($_GET['company_id']) : null;
        $job_id = isset($_GET['job_id']) && !empty($_GET['job_id']) ? intval($_GET['job_id']) : null;

        // Base de la consulta
        $sqlUsers = "SELECT u.id, u.username, u.email, u.names, u.lastname, u.job_id,
                            ud.photo,
                            c.company_name, j.position_name
                     FROM users u
                     LEFT JOIN user_details ud ON u.id = ud.user_id
                     LEFT JOIN companies c ON u.company_id = c.id
                     LEFT JOIN jobs j ON u.job_id = j.id
                     WHERE u.is_active = 1";

        $paramsUsers = [];

        // Aplicar filtros si se proporcionan
        if ($company_id) {
            $sqlUsers .= " AND u.company_id = ?";
            $paramsUsers[] = $company_id;
        }

        if ($job_id) {
            $sqlUsers .= " AND u.job_id = ?";
            $paramsUsers[] = $job_id;
        }

        if ($start_date && $end_date) {
            $sqlUsers .= " AND u.created_at BETWEEN ? AND ?";
            $paramsUsers[] = $start_date . ' 00:00:00';
            $paramsUsers[] = $end_date . ' 23:59:59';
        }

        $resultUsers = ejecutarConsulta($sqlUsers, $paramsUsers);
        if (!$resultUsers) {
            // Error en la consulta
            echo json_encode(['success' => false, 'message' => 'Error al obtener usuarios.']);
            exit;
        }

        $usuarios = [];

        while ($user = $resultUsers->fetch_assoc()) {
            $userId = $user['id'];
            $jobId = $user['job_id'];

            // Obtener total de documentos obligatorios y opcionales requeridos para el puesto del usuario
            $sqlTotalDocs = "SELECT 
                                SUM(CASE WHEN md.document_type = 'obligatorio' THEN 1 ELSE 0 END) AS total_mandatory,
                                SUM(CASE WHEN md.document_type = 'opcional' THEN 1 ELSE 0 END) AS total_optional
                             FROM mandatory_documents md
                             WHERE md.position_id = ? AND md.is_active = 1";
            $paramsTotalDocs = [$jobId];
            $resultTotalDocs = ejecutarConsulta($sqlTotalDocs, $paramsTotalDocs);
            if (!$resultTotalDocs) {
                echo json_encode(['success' => false, 'message' => 'Error al obtener documentos requeridos.']);
                exit;
            }
            $totals = $resultTotalDocs->fetch_assoc();
            $totalMandatory = $totals['total_mandatory'] ?? 0;
            $totalOptional = $totals['total_optional'] ?? 0;

            // Obtener documentos subidos por el usuario dentro del rango de fechas si se proporcionaron
            $sqlUploadedDocs = "SELECT 
                                    SUM(CASE WHEN d.document_type = 'obligatorio' THEN 1 ELSE 0 END) AS total_subidos_mandatory,
                                    SUM(CASE WHEN d.document_type = 'opcional' THEN 1 ELSE 0 END) AS total_subidos_optional,
                                    SUM(CASE WHEN d.document_type = 'obligatorio' AND d.state_id = 2 THEN 1 ELSE 0 END) AS total_aprobados_mandatory,
                                    SUM(CASE WHEN d.document_type = 'opcional' AND d.state_id = 2 THEN 1 ELSE 0 END) AS total_aprobados_optional
                                 FROM documents d
                                 WHERE d.user_id = ?";

            $paramsUploadedDocs = [$userId];

            if ($start_date && $end_date) {
                $sqlUploadedDocs .= " AND d.uploaded_at BETWEEN ? AND ?";
                $paramsUploadedDocs[] = $start_date . ' 00:00:00';
                $paramsUploadedDocs[] = $end_date . ' 23:59:59';
            }

            $resultUploadedDocs = ejecutarConsulta($sqlUploadedDocs, $paramsUploadedDocs);
            if (!$resultUploadedDocs) {
                echo json_encode(['success' => false, 'message' => 'Error al obtener documentos subidos.']);
                exit;
            }
            $counts = $resultUploadedDocs->fetch_assoc();
            $totalSubidosMandatory = $counts['total_subidos_mandatory'] ?? 0;
            $totalSubidosOptional = $counts['total_subidos_optional'] ?? 0;
            $totalAprobadosMandatory = $counts['total_aprobados_mandatory'] ?? 0;
            $totalAprobadosOptional = $counts['total_aprobados_optional'] ?? 0;

            // Calcular porcentajes
            $porcentajeSubidosMandatory = $totalMandatory > 0 ? round(($totalSubidosMandatory / $totalMandatory) * 100, 2) : 0;
            $porcentajeSubidosOptional = $totalOptional > 0 ? round(($totalSubidosOptional / $totalOptional) * 100, 2) : 0;
            $porcentajeAprobadosMandatory = $totalMandatory > 0 ? round(($totalAprobadosMandatory / $totalMandatory) * 100, 2) : 0;
            $porcentajeAprobadosOptional = $totalOptional > 0 ? round(($totalAprobadosOptional / $totalOptional) * 100, 2) : 0;

            // Agregar al array de usuarios
            $user['porcentaje_subidos_mandatory'] = $porcentajeSubidosMandatory;
            $user['porcentaje_subidos_optional'] = $porcentajeSubidosOptional;
            $user['porcentaje_aprobados_mandatory'] = $porcentajeAprobadosMandatory;
            $user['porcentaje_aprobados_optional'] = $porcentajeAprobadosOptional;

            $usuarios[] = $user;
        }

        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
    }

    // Obtener documentos subidos por un usuario
    public function documentosUsuario()
    
    {
        $this->verificarSuperadmin();

        // Obtener los filtros de fecha si existen
        $user_id = intval($_POST['user_id']);
        $start_date = isset($_POST['start_date']) && !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? $_POST['end_date'] : null;

        $sql = "SELECT 
                    d.id AS document_id,
                    d.document_name,
                    d.document_path,
                    d.admin_observation,
                    d.admin_reviewed,
                    d.uploaded_at,
                    d.state_id,
                    s.state_name,
                    d.document_type
                FROM documents d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.user_id = ?";

        $params = [$user_id];

        if ($start_date && $end_date) {
            $sql .= " AND d.uploaded_at BETWEEN ? AND ?";
            $params[] = $start_date . ' 00:00:00';
            $params[] = $end_date . ' 23:59:59';
        }

        $result = ejecutarConsulta($sql, $params);
        if (!$result) {
            // Error en la consulta
            echo json_encode(['success' => false, 'message' => 'Error al obtener documentos del usuario.']);
            exit;
        }

        $documentos = [];
        while ($row = $result->fetch_assoc()) {
            $documentos[] = $row;
        }

        echo json_encode(['success' => true, 'documentos' => $documentos]);
    }

    // Cambiar estado de un documento
    public function cambiarEstadoDocumento()
    {
        $this->verificarSuperadmin();

        $document_id = intval($_POST['document_id']);
        $estado_id = intval($_POST['estado_id']);
        $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : NULL;

        // Validar que el estado_id sea válido
        $valid_estados = [2, 3, 4]; // 2: Aprobado, 3: Rechazado, 4: Por Corregir
        if (!in_array($estado_id, $valid_estados)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido.']);
            exit;
        }

        // Verificar si el documento existe
        $sqlCheck = "SELECT user_id FROM documents WHERE id = ?";
        $resultCheck = ejecutarConsulta($sqlCheck, [$document_id]);
        if (!$resultCheck || $resultCheck->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Documento no encontrado.']);
            exit;
        }
        $document = $resultCheck->fetch_assoc();
        $user_id = $document['user_id'];

        // Actualizar el estado del documento
        $sql = "UPDATE documents 
                SET state_id = ?, admin_observation = ?, admin_reviewed = 1, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $params = [$estado_id, $observacion, $document_id];
        $result = ejecutarConsulta($sql, $params);
        if ($result) {
            // Registrar en el historial
            $sqlHistory = "INSERT INTO document_history (document_id, state_id, changed_at) VALUES (?, ?, CURRENT_TIMESTAMP)";
            $paramsHistory = [$document_id, $estado_id];
            ejecutarConsulta($sqlHistory, $paramsHistory);

            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
    }

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
}

// Manejo de las acciones
if (isset($_GET['op'])) {
    $controller = new UserDocumentsController();
    switch ($_GET['op']) {
        case 'listarUsuarios':
            $controller->listarUsuarios();
            break;
        case 'documentosUsuario':
            $controller->documentosUsuario();
            break;
        case 'cambiarEstadoDocumento':
            $controller->cambiarEstadoDocumento();
            break;
            // Otros casos si los hay
        case 'obtenerPuestosPorEmpresa':
            $controller->obtenerPuestosPorEmpresa();
            break;
        case 'obtenerEmpresas':
            $controller->obtenerEmpresas();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Operación no válida.']);
            break;
    }
}
