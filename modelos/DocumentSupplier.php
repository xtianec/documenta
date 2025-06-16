<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../config/Conexion.php";

class DocumentSupplier
{
    // Constructor
    public function __construct() {}

    // Insertar un nuevo documento
    public function insertar($data)
    {
        try {
            global $conexion;

            $sql = "INSERT INTO documentsupplier (supplier_id, documentNameSupplier_id, documentFileName, documentPath, originalFileName, state_id, admin_reviewed, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param(
                "iisssii",
                $data['supplier_id'],
                $data['documentNameSupplier_id'],
                $data['documentFileName'],
                $data['documentPath'],
                $data['originalFileName'],
                $data['state_id'],
                $data['admin_reviewed']
            );

            if ($stmt->execute()) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Insertar o actualizar documento
    public function insertarOActualizar($data)
    {
        try {
            global $conexion;

            // Verificar si ya existe un documento para este proveedor y tipo de documento
            $sqlCheck = "SELECT id, documentPath FROM documentsupplier WHERE supplier_id = ? AND documentNameSupplier_id = ?";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->bind_param("ii", $data['supplier_id'], $data['documentNameSupplier_id']);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows > 0) {
                // Ya existe, actualizar el registro
                $row = $resultCheck->fetch_assoc();
                $documentId = $row['id'];
                $oldDocumentPath = $row['documentPath'];

                // Eliminar el archivo anterior
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }

                // Actualizar el registro
                $sqlUpdate = "UPDATE documentsupplier SET documentFileName = ?, documentPath = ?, originalFileName = ?, state_id = ?, admin_reviewed = ?, updated_at = NOW()
                              WHERE id = ?";
                $stmtUpdate = $conexion->prepare($sqlUpdate);
                $stmtUpdate->bind_param(
                    "sssiii",
                    $data['documentFileName'],
                    $data['documentPath'],
                    $data['originalFileName'],
                    $data['state_id'],
                    $data['admin_reviewed'],
                    $documentId
                );

                if ($stmtUpdate->execute()) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => $stmtUpdate->error];
                }
            } else {
                // No existe, insertar nuevo registro
                return $this->insertar($data);
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Listar documentos disponibles
    public function listarDocumentosDisponibles()
    {
        try {
            global $conexion;
    
            // Selecciona también la ruta del template
            $sql = "SELECT id, name, description, templatePath FROM documentnamesupplier ORDER BY id ASC";
    
            $result = $conexion->query($sql);
    
            if ($result) {
                $documents = [];
                while ($row = $result->fetch_assoc()) {
                    $documents[] = $row;
                }
    
                return ['success' => true, 'data' => $documents];
            } else {
                return ['success' => false, 'error' => $conexion->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    

    // Obtener estado de los documentos subidos por el proveedor
    public function obtenerEstadoDocumentos($supplier_id)
    {
        try {
            global $conexion;

            $sql = "SELECT documentNameSupplier_id AS document_id, documentFileName, documentPath, originalFileName
                    FROM documentsupplier
                    WHERE supplier_id = ?";

            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                return ['success' => false, 'error' => $conexion->error];
            }
            $stmt->bind_param("i", $supplier_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $documents = [];
            while ($row = $result->fetch_assoc()) {
                $documents[$row['document_id']] = $row;
            }

            return ['success' => true, 'data' => $documents];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function eliminarDocumento($supplier_id, $documentNameSupplier_id)
    {
        try {
            global $conexion;

            // Obtener información del documento a eliminar
            $sqlGet = "SELECT id, documentPath FROM documentsupplier WHERE supplier_id = ? AND documentNameSupplier_id = ?";
            $stmtGet = $conexion->prepare($sqlGet);
            if (!$stmtGet) {
                return ['success' => false, 'error' => $conexion->error];
            }
            $stmtGet->bind_param("ii", $supplier_id, $documentNameSupplier_id);
            $stmtGet->execute();
            $resultGet = $stmtGet->get_result();

            if ($resultGet->num_rows > 0) {
                $row = $resultGet->fetch_assoc();
                $documentId = $row['id'];
                $documentPath = $row['documentPath'];

                // Eliminar el registro de la base de datos
                $sqlDelete = "DELETE FROM documentsupplier WHERE id = ?";
                $stmtDelete = $conexion->prepare($sqlDelete);
                if (!$stmtDelete) {
                    return ['success' => false, 'error' => $conexion->error];
                }
                $stmtDelete->bind_param("i", $documentId);
                if ($stmtDelete->execute()) {
                    // Eliminar el archivo del sistema de archivos
                    if (file_exists($documentPath)) {
                        unlink($documentPath);
                    }
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => $stmtDelete->error];
                }
            } else {
                return ['success' => false, 'error' => 'Documento no encontrado.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
