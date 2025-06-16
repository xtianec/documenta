<?php
require "../config/Conexion.php";

class DocumentNameSupplier
{
    // Insertar un nuevo documento
    public function insertar($name, $description, $templatePath = null)
    {
        global $conexion;

        $name = $this->limpiarCadena($name);
        $description = $this->limpiarCadena($description);

        $sql = "INSERT INTO documentnamesupplier (name, description, templatePath) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sss", $name, $description, $templatePath);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $conexion->error];
        }
    }

    // Listar documentos
    public function listar()
    {
        global $conexion;

        $sql = "SELECT * FROM documentnamesupplier ORDER BY id DESC";
        $result = $conexion->query($sql);

        if ($result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return ['success' => true, 'data' => $data];
        } else {
            return ['success' => false, 'error' => $conexion->error];
        }
    }

    // Obtener documento por ID
    public function obtener($id)
    {
        global $conexion;

        $sql = "SELECT * FROM documentnamesupplier WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return ['success' => true, 'data' => $data];
        } else {
            return ['success' => false, 'error' => $conexion->error];
        }
    }

    // Actualizar un documento
    public function actualizar($id, $name, $description, $templatePath = null)
    {
        global $conexion;

        $name = $this->limpiarCadena($name);
        $description = $this->limpiarCadena($description);

        $sql = "UPDATE documentnamesupplier SET name = ?, description = ?";
        if ($templatePath) {
            $sql .= ", templatePath = ?";
        }
        $sql .= " WHERE id = ?";

        $stmt = $conexion->prepare($sql);
        if ($templatePath) {
            $stmt->bind_param("sssi", $name, $description, $templatePath, $id);
        } else {
            $stmt->bind_param("ssi", $name, $description, $id);
        }

        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $conexion->error];
        }
    }

    // Eliminar un documento
    public function eliminar($id)
    {
        global $conexion;
    
        $sql = "DELETE FROM documentnamesupplier WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
    
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $conexion->error];
        }
    }
    
    // Función para limpiar cadenas
    private function limpiarCadena($str)
    {
        global $conexion;
        $str = mysqli_real_escape_string($conexion, trim($str));
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>
