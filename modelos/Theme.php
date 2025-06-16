<?php
// modelos/Theme.php

require_once "../config/Conexion.php";

class Theme
{
    // Obtener el tema actual del usuario
    public function getTheme($user_id)
    {
        global $conexion; // Asegúrate de que $conexion es accesible

        $sql = "SELECT theme FROM user_preferences WHERE user_id = ?";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($theme);
        if ($stmt->fetch()) {
            $stmt->close();
            return $theme;
        } else {
            // Si no existe, insertar el tema por defecto
            $stmt->close();
            $default_theme = 'default';
            $insert_sql = "INSERT INTO user_preferences (user_id, theme) VALUES (?, ?)";
            $insert_stmt = $conexion->prepare($insert_sql);
            if (!$insert_stmt) {
                return false;
            }
            $insert_stmt->bind_param("is", $user_id, $default_theme);
            $insert_stmt->execute();
            $insert_stmt->close();
            return $default_theme;
        }
    }

    // Actualizar el tema del usuario
    public function setTheme($user_id, $theme)
    {
        global $conexion; // Asegúrate de que $conexion es accesible

        $sql = "INSERT INTO user_preferences (user_id, theme) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE theme = VALUES(theme)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("is", $user_id, $theme);
        return $stmt->execute();
    }
}
?>
