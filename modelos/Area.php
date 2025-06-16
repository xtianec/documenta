<?php
require_once '../config/Conexion.php';

class Area {

    // Método para insertar una nueva área
    public function insertar($area_name, $company_id)
    {
        $area_name = limpiarCadena($area_name);
        $company_id = limpiarCadena($company_id);

        // Verificar si el área ya existe en la empresa
        $sql_verificar = "SELECT id FROM areas WHERE area_name = '$area_name' AND company_id = '$company_id'";
        $rspta_verificar = ejecutarConsulta($sql_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // Área ya existe
        }

        $sql = "INSERT INTO areas (area_name, company_id) VALUES ('$area_name', '$company_id')";
        return ejecutarConsulta($sql);
    }

    // Método para editar una área existente
    public function editar($id, $area_name, $company_id)
    {
        $id = limpiarCadena($id);
        $area_name = limpiarCadena($area_name);
        $company_id = limpiarCadena($company_id);

        // Verificar si el área ya existe en la empresa
        $sql_verificar = "SELECT id FROM areas WHERE area_name = '$area_name' AND company_id = '$company_id' AND id != '$id'";
        $rspta_verificar = ejecutarConsulta($sql_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // Área ya existe en otra entrada
        }

        $sql = "UPDATE areas SET area_name = '$area_name', company_id = '$company_id' WHERE id = '$id'";
        return ejecutarConsulta($sql);
    }

    // Método para desactivar una área
    public function desactivar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE areas SET is_active = 0 WHERE id = '$id'";
        return ejecutarConsulta($sql);
    }

    // Método para activar una área
    public function activar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE areas SET is_active = 1 WHERE id = '$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar una área específica
    public function mostrar($id)
    {
        $id = limpiarCadena($id);
        $sql = "SELECT * FROM areas WHERE id = '$id'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para listar todas las áreas
    public function listar()
    {
        $sql = "SELECT a.*, c.company_name FROM areas a INNER JOIN companies c ON a.company_id = c.id";
        return ejecutarConsulta($sql);
    }

    // Método para obtener una lista de áreas activas para un select
    public function select($company_id = null)
    {
        if ($company_id) {
            $company_id = limpiarCadena($company_id);
            $sql = "SELECT * FROM areas WHERE is_active = 1 AND company_id = '$company_id'";
        } else {
            $sql = "SELECT * FROM areas WHERE is_active = 1";
        }
        return ejecutarConsulta($sql);
    }

    // Método para verificar si un área ya existe
    public function verificarArea($area_name, $company_id, $id = null)
    {
        $area_name = limpiarCadena($area_name);
        $company_id = limpiarCadena($company_id);
        if ($id) {
            $id = limpiarCadena($id);
            $sql = "SELECT id FROM areas WHERE area_name = '$area_name' AND company_id = '$company_id' AND id != '$id'";
        } else {
            $sql = "SELECT id FROM areas WHERE area_name = '$area_name' AND company_id = '$company_id'";
        }
        $result = ejecutarConsulta($sql);
        if ($result && $result->num_rows > 0) {
            return true; // Área ya existe
        }
        return false; // Área no existe
    }

    // **Nueva Función para Listar Áreas por Empresa**
    public function listar_por_empresa($company_id)
    {
        $company_id = limpiarCadena($company_id);
        $sql = "SELECT id, area_name FROM areas WHERE is_active = 1 AND company_id = '$company_id' ORDER BY area_name ASC";
        return ejecutarConsulta($sql);
    }
}
?>
