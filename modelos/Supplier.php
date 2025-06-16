<?php
require "../config/Conexion.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

class Supplier
{
    // Listar todos los proveedores
    public function listar()
    {
        $sql = "SELECT s.*, sd.contactNameAccouting, sd.contactEmailAccouting, sd.contactPhoneAccouting 
        FROM suppliers s
        LEFT JOIN supplierdetail sd ON s.id = sd.supplier_id";
        return ejecutarConsulta($sql);
    }

    // Insertar un nuevo proveedor
    public function insertar($RUC, $companyName, $department, $province, $district, $address, $stateSunat, $conditionSunat, $contactNameBusiness, $contactEmailBusiness, $contactPhoneBusiness)
    {
        $password = bin2hex(random_bytes(4)); // Generar una contraseña aleatoria
        $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña

        // Escapar el hash de la contraseña
        $password_hashed_escaped = addslashes($password_hashed);

        // Sanitizar los demás inputs
        $RUC = limpiarCadena($RUC);
        $companyName = limpiarCadena($companyName);
        $department = limpiarCadena($department);
        $province = limpiarCadena($province);
        $district = limpiarCadena($district);
        $address = limpiarCadena($address);
        $stateSunat = limpiarCadena($stateSunat);
        $conditionSunat = limpiarCadena($conditionSunat);
        $contactNameBusiness = limpiarCadena($contactNameBusiness);
        $contactEmailBusiness = limpiarCadena($contactEmailBusiness);
        $contactPhoneBusiness = limpiarCadena($contactPhoneBusiness);

        $sql = "INSERT INTO suppliers (RUC, companyName, department, province, district, address, stateSunat, conditionSunat, contactNameBusiness, contactEmailBusiness, contactPhoneBusiness, password, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
        $params = [$RUC, $companyName, $department, $province, $district, $address, $stateSunat, $conditionSunat, $contactNameBusiness, $contactEmailBusiness, $contactPhoneBusiness, $password_hashed_escaped];

        $result = ejecutarConsulta($sql, $params);

        if ($result) {
            // Enviar correo con la contraseña generada
            if (!$this->enviarCorreo($contactEmailBusiness, $RUC, $companyName, $password)) {
                return "Proveedor creado, pero el correo no se pudo enviar";
            }
            return "Proveedor registrado correctamente y correo enviado";
        }

        return "No se pudo registrar el proveedor";
    }

    // Autenticar usuario
   

    // Editar un proveedor existente
    // Editar proveedor
    public function editar($id, $RUC, $companyName, $department, $province, $district, $address, $stateSunat, $conditionSunat, $contactNameBusiness, $contactEmailBusiness, $contactPhoneBusiness)
    {
        $sql = "UPDATE suppliers
                SET RUC = ?,
                    companyName = ?,
                    department = ?,
                    province = ?,
                    district = ?,
                    address = ?,
                    stateSunat = ?,
                    conditionSunat = ?,
                    contactNameBusiness = ?,
                    contactEmailBusiness = ?,
                    contactPhoneBusiness = ?
                WHERE id = ?";

        $params = [$RUC, $companyName, $department, $province, $district, $address, $stateSunat, $conditionSunat, $contactNameBusiness, $contactEmailBusiness, $contactPhoneBusiness, $id];
        return ejecutarConsulta($sql, $params);
    }



    // Desactivar proveedor
    public function desactivar($id)
    {
        $sql = "UPDATE suppliers SET is_active = '0' WHERE id = ?";
        return ejecutarConsulta($sql, [$id]);
    }

    // Activar proveedor
    public function activar($id)
    {
        $sql = "UPDATE suppliers SET is_active = '1' WHERE id = ?";
        return ejecutarConsulta($sql, [$id]);
    }

    // Mostrar datos de un proveedor específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM suppliers WHERE id = ?";
        return ejecutarConsultaSimpleFila($sql, [$id]);
    }

    // Listar documentos por proveedor
    public function listarDocumentos($supplier_id)
    {
        $sql = "SELECT ds.*, d.name as document_name, s.state_name
                FROM document_supplier ds
                LEFT JOIN document_name_supplier d ON ds.documentNameSupplier_id = d.id
                LEFT JOIN document_states s ON ds.state_id = s.id
                WHERE ds.supplier_id = ?";
        return ejecutarConsulta($sql, [$supplier_id]);
    }

    // Insertar un documento del proveedor
    public function insertarDocumento($supplier_id, $documentNameSupplier_id, $documentFileName, $documentPath, $originalFileName, $state_id)
    {
        $sql = "INSERT INTO document_supplier (supplier_id, documentNameSupplier_id, documentFileName, documentPath, originalFileName, state_id, admin_reviewed)
                VALUES (?, ?, ?, ?, ?, ?, '0')";
        $params = [$supplier_id, $documentNameSupplier_id, $documentFileName, $documentPath, $originalFileName, $state_id];
        return ejecutarConsulta($sql, $params);
    }

    // Enviar correo con credenciales al proveedor
    private function enviarCorreo($email, $RUC, $companyName, $password)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'mail.exclusivehosting.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'cristhian.espino@andinaenergy.com';
            $mail->Password = 'AndinaEn@BP1zAPx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('cristhian.espino@andinaenergy.com', 'Andina Energy');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Cuenta Creada - Información de Acceso';
            $mail->CharSet = 'UTF-8';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #007bff;'>¡Bienvenido $companyName!</h2>
                <p style='font-size: 14px;'>Tu cuenta ha sido creada exitosamente.</p>
                <p style='font-size: 16px;'><strong>Usuario (RUC):</strong> $RUC</p>
                <p style='font-size: 16px;'><strong>Contraseña:</strong> $password</p>
                <p style='font-size: 12px; color: #555;'>Por favor, cambia tu contraseña la primera vez que inicies sesión.</p>
                <div style='margin-top: 20px;'>
                    <a href='https://andinaenergy.com/' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Iniciar sesión</a>
                </div>
                <p style='font-size: 12px; color: #888; margin-top: 20px;'>Si tienes alguna pregunta o necesitas ayuda, contáctanos.</p>
                <p style='font-size: 12px; color: #888;'>Gracias,<br>Equipo de Andina Energy</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Error al enviar mensaje: {$mail->ErrorInfo}";
            return false;
        }
    }
    public function autenticar($ruc, $password)
    {
        $ruc = limpiarCadena($ruc);

        $sql = "SELECT * FROM suppliers WHERE RUC = ? AND is_active = 1";
        $result = ejecutarConsultaSimpleFila($sql, [$ruc]);

        if ($result) {
            // Obtener el hash almacenado y eliminar las barras invertidas
            $stored_password_hashed = stripslashes($result['password']);

            if (password_verify($password, $stored_password_hashed)) {
                return $result;
            }
        }
        return false;
    }

    

    // Método para registrar el login del proveedor
    public function registrarLogin($supplier_id)
    {
        $supplier_id = (int)$supplier_id;
        $sql = "INSERT INTO supplier_access_log (supplier_id, access_time) VALUES (?, NOW())";
        return ejecutarConsulta($sql, [$supplier_id]);
    }

    // Obtener los detalles del proveedor por su ID
    public function obtenerProveedorPorId($supplier_id)
    {
        $sql = "SELECT * FROM suppliers WHERE id = ?";
        return ejecutarConsultaSimpleFila($sql, [$supplier_id]);
    }


    // Clean input function

}
