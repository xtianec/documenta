<?php
// Archivo: ../config/utilidades.php

// Función para limpiar cadenas de entrada
function limpiarCadena($str)
{
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    return $str;
}

// Función para registrar errores

if (!function_exists('logError')) {
    function logError($message) {
        // Asegúrate de que la ruta de logs es correcta y que el directorio existe
        $logFile = __DIR__ . '/../logs/errors.log';
        $logDir = dirname($logFile);

        // Crear el directorio de logs si no existe
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Intentar escribir en el archivo de logs
        if (file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND) === false) {
            error_log("No se pudo escribir en el archivo de logs: $logFile");
        }
    }
}
?>