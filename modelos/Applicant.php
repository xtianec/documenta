<?php 
require_once "../config/Conexion.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

class Applicant
{
    // Listar todos los postulantes
    public function listar()
    {
        $sql = "SELECT a.*, c.company_name, jp.position_name, ar.area_name
                FROM applicants a
                LEFT JOIN companies c ON a.company_id = c.id
                LEFT JOIN jobs jp ON a.job_id = jp.id
                LEFT JOIN areas ar ON a.area_id = ar.id
                ORDER BY a.id DESC";
        return ejecutarConsulta($sql);
    }

    // Insertar un nuevo postulante
    public function insertar($company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names)
    {
        // Verificar si el DNI ya existe
        if ($this->usernameExiste($username)) {
            return "El DNI ya está en uso. Por favor, verifica.";
        }

        // Generar contraseña aleatoria
        $password = bin2hex(random_bytes(4)); // 8 caracteres
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo postulante utilizando consultas preparadas
        $sql = "INSERT INTO applicants (company_id, area_id, job_id, username, password, email, lastname, surname, names, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
        $params = [$company_id, $area_id, $job_id, $username, $password_hashed, $email, $lastname, $surname, $names];

        $result = ejecutarConsulta($sql, $params);

        if ($result) {
            // Enviar el correo con las credenciales
            if (!$this->enviarCorreo($email, $username, $names, $password)) {
                return "Postulante creado, pero no se pudo enviar el correo.";
            }
            return "Postulante registrado correctamente y correo enviado.";
        }

        return "No se pudo registrar el postulante.";
    }

    // Editar un postulante existente
    public function editar($id, $company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names)
    {
        // Verificar si el nuevo DNI ya existe en otro postulante
        if ($this->usernameExisteExceptoId($username, $id)) {
            return "El DNI ya está en uso por otro postulante. Por favor, verifica.";
        }

        // Actualizar el postulante utilizando consultas preparadas
        $sql = "UPDATE applicants 
                SET company_id = ?, 
                    area_id = ?,
                    job_id = ?, 
                    username = ?, 
                    email = ?, 
                    lastname = ?, 
                    surname = ?, 
                    names = ? 
                WHERE id = ?";
        $params = [$company_id, $area_id, $job_id, $username, $email, $lastname, $surname, $names, $id];

        return ejecutarConsulta($sql, $params);
    }

    // Desactivar postulante
    public function desactivar($id)
    {
        $sql = "UPDATE applicants SET is_active = '0' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Activar postulante
    public function activar($id)
    {
        $sql = "UPDATE applicants SET is_active = '1' WHERE id = ?";
        $params = [$id];
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar datos de un postulante específico
    public function mostrar($id)
    {
        $sql = "SELECT * FROM applicants WHERE id = ?";
        $params = [$id];
        return ejecutarConsultaSimpleFila($sql, $params);
    }

    // Listar empresas para el select
    public function listarEmpresas()
    {
        $sql = "SELECT id, company_name FROM companies WHERE is_active = 1";
        return ejecutarConsulta($sql);
    }

    // Listar áreas para el select por empresa
    public function listarAreasPorEmpresa($company_id)
    {
        $sql = "SELECT id, area_name FROM areas WHERE company_id = ?";
        $params = [$company_id];
        return ejecutarConsulta($sql, $params);
    }

    // Listar puestos activos para el select
    public function listarPuestosActivos()
    {
        $sql = "SELECT id, position_name FROM jobs WHERE is_active = 1";
        return ejecutarConsulta($sql);
    }

    // Listar puestos por área
    public function listarPuestosPorArea($area_id)
    {
        $sql = "SELECT id, position_name FROM jobs WHERE area_id = ? AND is_active = 1";
        $params = [$area_id];
        return ejecutarConsulta($sql, $params);
    }

    // Enviar correo con las credenciales
    private function enviarCorreo($email, $username, $names, $password)
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
                <h2 style='color: #007bff;'>¡Gracias por postular a Andina Energy, $names!</h2>
                <p style='font-size: 14px;'>Nos complace informarte que tu cuenta ha sido creada con éxito.</p>
                <p style='font-size: 16px;'><strong>Usuario:</strong> $username</p>
                <p style='font-size: 16px;'><strong>Contraseña:</strong> $password</p>
                <p style='font-size: 12px; color: #555;'>Por favor, cambia tu contraseña la primera vez que inicies sesión para asegurar tu cuenta.</p>
                <div style='margin-top: 20px;'>
                    <a href='https://andinaenergy.com/' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Iniciar sesión</a>
                </div>
                <p style='font-size: 12px; color: #888; margin-top: 20px;'>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
                <p style='font-size: 12px; color: #888;'>Gracias,<br>Equipo de Andina Energy</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log error instead of echoing to prevent breaking JSON response
            error_log("Error al enviar correo: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Autenticar postulante
    public function autenticar($username, $password)
    {
        $sql = "SELECT * FROM applicants WHERE username = ? AND is_active = 1";
        $params = [$username];
        $result = ejecutarConsultaSimpleFila($sql, $params);

        if ($result && password_verify($password, $result['password'])) {
            return $result;
        } else {
            return false;
        }
    }

    // Registrar login del postulante
    public function registrarLogin($applicantId)
    {
        $sql = "INSERT INTO applicant_access_logs (applicant_id, access_time) VALUES (?, NOW())";
        $params = [$applicantId];
        return ejecutarConsulta($sql, $params);
    }

    // Registrar logout del postulante
    public function registrarLogout($applicantId)
    {
        $sql = "UPDATE applicant_access_logs SET logout_time = NOW() 
                WHERE applicant_id = ? AND logout_time IS NULL";
        $params = [$applicantId];
        return ejecutarConsulta($sql, $params);
    }

    // Obtener historial de acceso
    public function obtenerHistorialAcceso($applicantId)
    {
        $sql = "SELECT access_time, logout_time FROM applicant_access_logs 
                WHERE applicant_id = ? 
                ORDER BY access_time DESC";
        $params = [$applicantId];
        return ejecutarConsulta($sql, $params);
    }

    // Verificar si el username (DNI) ya existe, excepto para un ID dado (usado en editar)
    private function usernameExisteExceptoId($username, $id)
    {
        $sql = "SELECT COUNT(*) as count FROM applicants WHERE username = ? AND id != ?";
        $params = [$username, $id];
        $result = ejecutarConsultaSimpleFila($sql, $params);
        return $result['count'] > 0;
    }

    // Verificar si el username (DNI) ya existe
    private function usernameExiste($username)
    {
        $sql = "SELECT COUNT(*) as count FROM applicants WHERE username = ?";
        $params = [$username];
        $result = ejecutarConsultaSimpleFila($sql, $params);
        return $result['count'] > 0;
    }
}
?>
