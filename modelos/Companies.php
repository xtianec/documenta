<?php
require_once '../config/Conexion.php';

class Companies {

    // Método para insertar una nueva empresa
    public function insertar($company_name, $ruc, $description)
    {
        $company_name = limpiarCadena($company_name);
        $ruc = limpiarCadena($ruc);
        $description = limpiarCadena($description);

        // Verificar si el RUC ya existe
        $sql_verificar = "SELECT id FROM companies WHERE ruc = ?";
        $params_verificar = [$ruc];
        $rspta_verificar = ejecutarConsulta($sql_verificar, $params_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // RUC ya existe
        }

        $sql = "INSERT INTO companies (company_name, ruc, description) VALUES (?, ?, ?)";
        $params = [$company_name, $ruc, $description];
        return ejecutarConsulta($sql, $params);
    }

    // Método para editar una empresa existente
    public function editar($id, $company_name, $ruc, $description)
    {
        $id = limpiarCadena($id);
        $company_name = limpiarCadena($company_name);
        $ruc = limpiarCadena($ruc);
        $description = limpiarCadena($description);

        // Verificar si el RUC ya existe en otra empresa
        $sql_verificar = "SELECT id FROM companies WHERE ruc = ? AND id != ?";
        $params_verificar = [$ruc, $id];
        $rspta_verificar = ejecutarConsulta($sql_verificar, $params_verificar);
        if ($rspta_verificar && $rspta_verificar->num_rows > 0) {
            return false; // RUC ya existe en otra empresa
        }

        $sql = "UPDATE companies SET company_name = ?, ruc = ?, description = ? WHERE id = ?";
        $params = [$company_name, $ruc, $description, $id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para desactivar una empresa
    public function desactivar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE companies SET is_active = 0 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para activar una empresa
    public function activar($id)
    {
        $id = limpiarCadena($id);
        $sql = "UPDATE companies SET is_active = 1 WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Método para mostrar una empresa específica
    public function mostrar($id)
    {
        $id = limpiarCadena($id);
        $sql = "SELECT * FROM companies WHERE id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Método para listar todas las empresas
    public function listar()
    {
        $sql = "SELECT * FROM companies";
        return ejecutarConsulta($sql);
    }

    // Método para listar empresas activas
    public function listarEmpresas()
    {
        $sql = "SELECT id, company_name FROM companies WHERE is_active = 1 ORDER BY company_name ASC";
        $result = ejecutarConsulta($sql);
        $empresas = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $empresas[] = $row;
            }
        }

        return $empresas;
    }

    // Método para obtener una lista de empresas activas para un select
    public function select()
    {
        $sql = "SELECT * FROM companies WHERE is_active = 1 ORDER BY company_name ASC";
        return ejecutarConsulta($sql);
    }

    // Método para verificar si un RUC ya existe
    public function verificarRuc($ruc, $id = null)
    {
        $ruc = limpiarCadena($ruc);
        if ($id) {
            $id = limpiarCadena($id);
            $sql = "SELECT id FROM companies WHERE ruc = ? AND id != ?";
            $params = [$ruc, $id];
        } else {
            $sql = "SELECT id FROM companies WHERE ruc = ?";
            $params = [$ruc];
        }
        $result = ejecutarConsulta($sql, $params);
        if ($result && $result->num_rows > 0) {
            return true; // RUC ya existe
        }
        return false; // RUC no existe
    }

    
}
?>
