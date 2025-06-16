<?php
require_once "../config/Conexion.php";

class UserDetails
{
    // Insertar datos personales del usuario
    public function insertar($user_id, $params, $photo_url)
    {
        $sql = "INSERT INTO user_details (
                    user_id,
                    person_contact_name,
                    person_contact_phone,
                    phone,
                    email,
                    country,
                    entry_date,
                    birth_date,
                    age,
                    blood_type,
                    allergies,
                    photo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array_merge([$user_id], $params, [$photo_url]);
        return ejecutarConsulta($sql, $params);
    }

    // Actualizar datos personales del usuario
    public function actualizar($user_id, $params, $photo_url)
    {
        $sql = "UPDATE user_details SET
                    person_contact_name = ?,
                    person_contact_phone = ?,
                    phone = ?,
                    email = ?,
                    country = ?,
                    entry_date = ?,
                    birth_date = ?,
                    age = ?,
                    blood_type = ?,
                    allergies = ?,
                    photo = ?
                WHERE user_id = ?";
        $params = array_merge($params, [$photo_url, $user_id]);
        return ejecutarConsulta($sql, $params);
    }

    // Mostrar detalles personales de un usuario
    public function mostrar($user_id)
    {
        $sql = "SELECT ud.*, u.names, u.lastname, u.surname
                FROM user_details ud
                INNER JOIN users u ON ud.user_id = u.id
                WHERE ud.user_id = ?";
        $user = ejecutarConsultaSimpleFila($sql, [$user_id]);

        if ($user) {
            $full_name = "{$user['names']} {$user['lastname']} {$user['surname']}";
            $user['full_name'] = $full_name;
            return $user;
        } else {
            return null;
        }
    }
    // Mostrar detalles personales de un usuario

}
?>
