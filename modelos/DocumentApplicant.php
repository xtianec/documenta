<?php
// modelos/DocumentApplicant.php

require_once "../config/Conexion.php";

class DocumentApplicant
{
    // Insertar documento en la base de datos
    public function insertar($applicant_id, $document_name, $original_file_name, $generated_file_name, $document_path, $user_observation = null)
    {
        $state_id = 1; // Estado 'Subido'
        $sql = "INSERT INTO documents_applicants 
                (applicant_id, document_name, original_file_name, generated_file_name, document_path, state_id, user_observation, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $params = [$applicant_id, $document_name, $original_file_name, $generated_file_name, $document_path, $state_id, $user_observation];
        return ejecutarConsulta($sql, $params);
    }

    // Listar todos los documentos de un postulante
    public function listar($applicant_id)
    {
        $sql = "SELECT 
                    d.id, 
                    d.applicant_id,
                    d.document_name,
                    d.original_file_name,
                    d.generated_file_name,
                    d.document_path,
                    d.created_at,
                    d.uploaded_at,
                    d.state_id,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.applicant_id = ?
                ORDER BY d.created_at DESC";
        $params = [$applicant_id];
        return ejecutarConsulta($sql, $params);
    }

    // Eliminar documento por ID
    public function eliminar($id)
    {
        $sql = "DELETE FROM documents_applicants WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar detalles de un documento por ID
    public function mostrar($id)
    {
        $sql = "SELECT 
                    d.id, 
                    d.applicant_id,
                    d.document_name,
                    d.original_file_name,
                    d.generated_file_name,
                    d.document_path,
                    d.created_at,
                    d.uploaded_at,
                    d.state_id,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Evaluar el documento: Aprobar, Rechazar, Solicitar Corrección
    public function evaluarDocumento($id, $admin_observation, $state_id)
    {
        // Asegurarse de que el campo 'reviewed_at' exista en la tabla 'documents_applicants'
        $sql = "UPDATE documents_applicants 
                SET admin_observation = ?, state_id = ?, admin_reviewed = 1, reviewed_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $params = [$admin_observation, $state_id, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Obtener el nombre completo del postulante
    public function obtenerNombreApplicant($applicant_id)
    {
        $sql = "SELECT CONCAT(a.names, ' ', a.lastname, ' ', a.surname) AS nombre 
                FROM applicants a 
                WHERE a.id = ?";
        $params = [$applicant_id];
        $result = ejecutarConsultaSimpleFila($sql, $params);
        return $result ? $result['nombre'] : false;
    }

    /**
     * Obtener documentos por usuario con filtros
     * @param int $user_id - ID del postulante
     * @param string|null $start_date - Fecha de inicio
     * @param string|null $end_date - Fecha de fin
     * @return array - Lista de documentos
     */
    public function obtenerDocumentosPorUsuario($user_id, $start_date = '', $end_date = '')
    {
        $sql = "SELECT 
                    d.id AS document_id,
                    d.document_name,
                    d.original_file_name,
                    d.document_path,
                    d.user_observation,
                    d.admin_observation,
                    d.admin_reviewed,
                    d.uploaded_at,
                    d.reviewed_at,
                    d.state_id,
                    s.state_name
                FROM documents_applicants d
                JOIN document_states s ON d.state_id = s.id
                WHERE d.applicant_id = ?";

        $params = [$user_id];

        if (!empty($start_date) && !empty($end_date)) {
            $sql .= " AND d.created_at BETWEEN ? AND ?";
            $params[] = $start_date . ' 00:00:00';
            $params[] = $end_date . ' 23:59:59';
        }

        $sql .= " ORDER BY d.created_at DESC";

        $result = ejecutarConsulta($sql, $params);
        if ($result) {
            $documentos = [];
            while ($row = $result->fetch_assoc()) {
                $documentos[] = $row;
            }
            return $documentos;
        } else {
            return [];
        }
    }

    /**
     * Contar total de postulantes para Server-Side Processing con filtros
     * @param string|null $start_date
     * @param string|null $end_date
     * @param string $search
     * @param int|null $company_id
     * @param int|null $job_id
     * @return int - Total de registros
     */
    public function contarTodos($start_date = '', $end_date = '', $search = '', $company_id = null, $job_id = null)
    {
        $sql = "SELECT COUNT(DISTINCT a.id) as total
                FROM documents_applicants d
                JOIN applicants a ON d.applicant_id = a.id
                JOIN jobs j ON a.job_id = j.id
                JOIN companies c ON a.company_id = c.id
                LEFT JOIN applicants_details ad ON a.id = ad.applicant_id
                WHERE 1=1";

        $params = [];

        if (!empty($start_date) && !empty($end_date)) {
            $sql .= " AND d.created_at BETWEEN ? AND ?";
            $params[] = $start_date . " 00:00:00";
            $params[] = $end_date . " 23:59:59";
        }

        if (!empty($company_id)) {
            $sql .= " AND c.id = ?";
            $params[] = $company_id;
        }

        if (!empty($job_id)) {
            $sql .= " AND j.id = ?";
            $params[] = $job_id;
        }

        if (!empty($search)) {
            $sql .= " AND (c.company_name LIKE ? OR j.position_name LIKE ? OR a.names LIKE ? OR a.lastname LIKE ? OR a.email LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        $result = ejecutarConsultaSimpleFila($sql, $params);
        return $result ? intval($result['total']) : 0;
    }

    /**
     * Listar postulantes con Server-Side Processing y filtros
     * @param int $start
     * @param int $length
     * @param string $orderColumn
     * @param string $orderDir
     * @param string|null $start_date
     * @param string|null $end_date
     * @param string $search
     * @param int|null $company_id
     * @param int|null $job_id
     * @return array - Lista de postulantes
     */
    public function listarTodosServerSide($start, $length, $orderColumn, $orderDir, $start_date = '', $end_date = '', $search = '', $company_id = null, $job_id = null)
    {
        $sql = "SELECT 
                    a.id,
                    c.company_name,
                    j.position_name,
                    a.names,
                    a.lastname,
                    a.username,
                    a.email,
                    ad.photo,
                    COUNT(CASE WHEN d.document_name LIKE '%cv%' THEN 1 END) as total_uploaded_cv,
                    COUNT(CASE WHEN d.document_name NOT LIKE '%cv%' THEN 1 END) as total_uploaded_other,
                    SUM(CASE WHEN d.state_id = 2 AND d.document_name LIKE '%cv%' THEN 1 ELSE 0 END) as total_approved_cv,
                    SUM(CASE WHEN d.state_id = 2 AND d.document_name NOT LIKE '%cv%' THEN 1 ELSE 0 END) as total_approved_other
                FROM documents_applicants d
                JOIN applicants a ON d.applicant_id = a.id
                JOIN jobs j ON a.job_id = j.id
                JOIN companies c ON a.company_id = c.id
                LEFT JOIN applicants_details ad ON a.id = ad.applicant_id
                WHERE 1=1";

        $params = [];

        if (!empty($start_date) && !empty($end_date)) {
            $sql .= " AND d.created_at BETWEEN ? AND ?";
            $params[] = $start_date . " 00:00:00";
            $params[] = $end_date . " 23:59:59";
        }

        if (!empty($company_id)) {
            $sql .= " AND c.id = ?";
            $params[] = $company_id;
        }

        if (!empty($job_id)) {
            $sql .= " AND j.id = ?";
            $params[] = $job_id;
        }

        if (!empty($search)) {
            $sql .= " AND (c.company_name LIKE ? OR j.position_name LIKE ? OR a.names LIKE ? OR a.lastname LIKE ? OR a.email LIKE ?)";
            $searchParam = "%" . $search . "%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        $sql .= " GROUP BY a.id, c.company_name, j.position_name, a.names, a.lastname, a.username, a.email, ad.photo
                  ORDER BY $orderColumn $orderDir
                  LIMIT ?, ?";
        $params[] = $start;
        $params[] = $length;

        $result = ejecutarConsulta($sql, $params);
        $applicantsList = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Calcular porcentajes
                $porcentaje_subidos_cv = $row['total_uploaded_cv'] > 0 ? min(($row['total_uploaded_cv'] / $row['total_uploaded_cv']) * 100, 100) : 0;
                $porcentaje_aprobados_cv = $row['total_uploaded_cv'] > 0 ? round(min(($row['total_approved_cv'] / $row['total_uploaded_cv']) * 100, 100), 2) : 0;
                $porcentaje_subidos_other = $row['total_uploaded_other'] > 0 ? round(min(($row['total_uploaded_other'] / $row['total_uploaded_other']) * 100, 100), 2) : 0;
                $porcentaje_aprobados_other = $row['total_uploaded_other'] > 0 ? round(min(($row['total_approved_other'] / $row['total_uploaded_other']) * 100, 100), 2) : 0;

                $applicantsList[] = [
                    'company_name' => $row['company_name'],
                    'job_name' => $row['position_name'],
                    'id' => $row['id'],
                    'names' => $row['names'],
                    'lastname' => $row['lastname'],
                    'username' => $row['username'] ?? 'N/A',
                    'email' => $row['email'] ?? 'N/A',
                    'photo' => $row['photo'] ?? null,
                    'porcentaje_subidos_cv' => $porcentaje_subidos_cv,
                    'porcentaje_subidos_other' => $porcentaje_subidos_other,
                    'porcentaje_aprobados_cv' => $porcentaje_aprobados_cv,
                    'porcentaje_aprobados_other' => $porcentaje_aprobados_other
                ];
            }
        }

        return $applicantsList;
    }
}
?>
