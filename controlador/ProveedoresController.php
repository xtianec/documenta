<?php

require "../config/Conexion.php";

class ProveedoresController
{

    // Listar proveedores con el porcentaje de documentos subidos y aprobados
    public function listarProveedores() {
        global $conexion;
    
        // Query única para obtener proveedores y estadísticas de documentos subidos y aprobados
        $sqlProveedores = "
            SELECT 
                s.id, 
                s.RUC, 
                s.companyName, 
                s.contactEmailBusiness, 
                COUNT(ds.id) AS total_subidos, 
                SUM(CASE WHEN ds.state_id = 2 THEN 1 ELSE 0 END) AS total_aprobados,
                (SELECT COUNT(*) FROM documentnamesupplier) AS total_requeridos
            FROM suppliers s
            LEFT JOIN documentsupplier ds ON ds.supplier_id = s.id
            WHERE s.is_active = 1
            GROUP BY s.id
        ";
    
        $resultProveedores = $conexion->query($sqlProveedores);
        $proveedores = [];
    
        while ($row = $resultProveedores->fetch_assoc()) {
            // Calcular porcentajes directamente en PHP
            $totalRequeridos = $row['total_requeridos'];
            $totalSubidos = $row['total_subidos'];
            $totalAprobados = $row['total_aprobados'];
    
            $row['porcentaje_subidos'] = $totalRequeridos > 0 ? round(($totalSubidos / $totalRequeridos) * 100, 2) : 0;
            $row['porcentaje_aprobados'] = $totalRequeridos > 0 ? round(($totalAprobados / $totalRequeridos) * 100, 2) : 0;
            $proveedores[] = $row;
        }
    
        echo json_encode(['success' => true, 'proveedores' => $proveedores]);
    }
    

    // Listar proveedores

    // Obtener documentos subidos por un proveedor
    public function documentosProveedor()
    {
        global $conexion;
        $proveedor_id = intval($_POST['proveedor_id']);

        $sql = "SELECT 
                    ds.id AS document_id, 
                    dn.name AS document_name, 
                    ds.originalFileName, 
                    ds.documentFileName, 
                    ds.documentPath, 
                    ds.admin_observation, 
                    ds.admin_reviewed, 
                    ds.created_at, 
                    ds.updated_at, 
                    s.state_name AS document_state,
                    ds.state_id  -- Incluir el state_id para manejar los botones correctamente
                FROM documentsupplier ds
                JOIN documentnamesupplier dn ON ds.documentNameSupplier_id = dn.id
                JOIN document_states s ON ds.state_id = s.id
                WHERE ds.supplier_id = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $proveedor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $documentos = [];
        while ($row = $result->fetch_assoc()) {
            $documentos[] = $row;
        }

        echo json_encode(['success' => true, 'documentos' => $documentos]);
    }


    // Cambiar estado de un documento
    public function cambiarEstadoDocumento()
    {
        global $conexion;
        $document_id = intval($_POST['document_id']);
        $estado_id = intval($_POST['estado_id']);
        $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : NULL;

        $sql = "UPDATE documentsupplier 
                SET state_id = ?, admin_observation = ?, admin_reviewed = 1, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isi", $estado_id, $observacion, $document_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
    }

    public function obtenerPorcentajesProveedor() {
        global $conexion;
        $proveedor_id = intval($_POST['proveedor_id']);
    
        // Contar el total de documentos requeridos
        $sqlTotalDocs = "SELECT COUNT(*) AS total_requeridos FROM documentnamesupplier";
        $resultTotalDocs = $conexion->query($sqlTotalDocs);
        $totalRequeridos = $resultTotalDocs->fetch_assoc()['total_requeridos'];
    
        // Contar los documentos subidos por el proveedor
        $sqlDocsSubidos = "SELECT COUNT(*) AS total_subidos FROM documentsupplier WHERE supplier_id = $proveedor_id";
        $resultDocsSubidos = $conexion->query($sqlDocsSubidos);
        $totalSubidos = $resultDocsSubidos->fetch_assoc()['total_subidos'];
    
        // Contar los documentos aprobados por el proveedor (estado_id = 2 es "Aprobado")
        $sqlDocsAprobados = "SELECT COUNT(*) AS total_aprobados FROM documentsupplier WHERE supplier_id = $proveedor_id AND state_id = 2";
        $resultDocsAprobados = $conexion->query($sqlDocsAprobados);
        $totalAprobados = $resultDocsAprobados->fetch_assoc()['total_aprobados'];
    
        // Calcular los porcentajes
        $porcentajeSubidos = $totalRequeridos > 0 ? ($totalSubidos / $totalRequeridos) * 100 : 0;
        $porcentajeAprobados = $totalRequeridos > 0 ? ($totalAprobados / $totalRequeridos) * 100 : 0;
    
        echo json_encode([
            'success' => true,
            'porcentaje_subidos' => round($porcentajeSubidos, 2),
            'porcentaje_aprobados' => round($porcentajeAprobados, 2)
        ]);
    }
    
}

// Manejo de las acciones
if (isset($_GET['op'])) {
    $controller = new ProveedoresController();
    switch ($_GET['op']) {
        case 'listarProveedores':
            $controller->listarProveedores();
            break;
        case 'documentosProveedor':
            $controller->documentosProveedor();
            break;
        case 'cambiarEstadoDocumento':
            $controller->cambiarEstadoDocumento();
            break;
        case 'obtenerPorcentajesProveedor':
            $controller->obtenerPorcentajesProveedor();
            break;
    }
}
