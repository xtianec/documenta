<?php
require "../config/Conexion.php";

class SupplierDetails
{
    // Insertar detalles del proveedor
    public function insertar($supplier_id, $contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide)
    {
        $supplier_id = limpiarCadena($supplier_id);
        $contactNameAccouting = limpiarCadena($contactNameAccouting);
        $contactEmailAccouting = limpiarCadena($contactEmailAccouting);
        $contactPhoneAccouting = limpiarCadena($contactPhoneAccouting);
        $Provide = limpiarCadena($Provide);

        $sql = "INSERT INTO supplierdetail (supplier_id, contactNameAccouting, contactEmailAccouting, contactPhoneAccouting, Provide)
                VALUES (?, ?, ?, ?, ?)";
        $params = [$supplier_id, $contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide];
        return ejecutarConsulta($sql, $params);
    }

    // Actualizar detalles del proveedor
    public function actualizar($id, $contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide)
    {
        global $conexion; // Asegúrate de tener acceso a la conexión a la base de datos
    
        $id = limpiarCadena($id);
        $contactNameAccouting = limpiarCadena($contactNameAccouting);
        $contactEmailAccouting = limpiarCadena($contactEmailAccouting);
        $contactPhoneAccouting = limpiarCadena($contactPhoneAccouting);
        $Provide = limpiarCadena($Provide);
    
        $sql = "UPDATE supplierdetail
                SET contactNameAccouting = ?, contactEmailAccouting = ?, contactPhoneAccouting = ?, Provide = ?
                WHERE id = ?";

        $params = [$contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide, $id];
        $result = ejecutarConsulta($sql, $params);
    
        if ($result) {
            return ['success' => true];
        } else {
            $error = $conexion->error; // Capturamos el error de la base de datos
            return ['success' => false, 'error' => $error];
        }
    }
    

    // Mostrar detalles de un proveedor
    public function mostrar($supplier_id)
    {
        $supplier_id = limpiarCadena($supplier_id);

        $sql = "SELECT * FROM supplierdetail WHERE supplier_id = ?";
        return ejecutarConsultaSimpleFila($sql, [$supplier_id]);
    }
}
?>
