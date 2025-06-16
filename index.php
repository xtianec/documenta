<?php
// index.php

// Iniciar la sesión
session_start();

// Autoload de Composer (si es necesario)
require_once 'vendor/autoload.php';

// Incluir archivos de configuración
require_once 'config/global.php';
require_once 'config/Utilidades.php';

// Obtener la URL desde el parámetro 'url' o establecer 'home' por defecto
$url = isset($_GET['url']) ? $_GET['url'] : 'home';

// Separar la URL en segmentos
$url = explode('/', $url);

// Obtener el nombre del controlador (primer segmento)
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';

// Obtener el método (segundo segmento) o 'index' por defecto
$method = isset($url[1]) ? $url[1] : 'index';

// Obtener los parámetros adicionales
$params = array_slice($url, 2);

// Ruta al archivo del controlador
$controllerPath = 'controlador/' . $controllerName . '.php';

// Verificar si el archivo del controlador existe
if (file_exists($controllerPath)) {
    require_once $controllerPath;

    // Verificar si la clase del controlador existe
    if (class_exists($controllerName)) {
        $controller = new $controllerName();

        // Verificar si el método existe en el controlador
        if (method_exists($controller, $method)) {
            // Llamar al método con los parámetros
            call_user_func_array([$controller, $method], $params);
        } else {
            // Método no encontrado
            logError('Método no encontrado: ' . $controllerName . '->' . $method);
            echo 'Error 404: Método no encontrado';
        }
    } else {
        // Clase del controlador no encontrada
        logError('Controlador no encontrado: ' . $controllerName);
        echo 'Error 404: Controlador no encontrado';
    }
} else {
    // Archivo del controlador no encontrado
    logError('Archivo del controlador no encontrado: ' . $controllerPath);
    echo 'Error 404: Página no encontrada';
}
