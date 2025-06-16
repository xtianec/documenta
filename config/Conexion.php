<?php 
require_once "global.php";
require_once "Utilidades.php";

$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');

// Muestra posible error en la conexión
if (mysqli_connect_errno()) {
    printf("Falló en la conexión con la base de datos: %s\n", mysqli_connect_error());
    exit();
}

if (!function_exists('ejecutarConsulta')) {

    function ejecutarConsulta($sql, $params = [])
    {
        global $conexion;
        
        // Preparar la consulta
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            logError("Error en la preparación de la consulta: " . $conexion->error . " - SQL: $sql");
            return false;
        }
        
        // Vincular los parámetros si existen
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Modificar según los tipos de los parámetros
            $stmt->bind_param($types, ...$params);  // Desempaquetar el array de parámetros
        }
    
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Para `SELECT`, devolver el resultado
            if (strpos($sql, 'SELECT') === 0) {
                return $stmt->get_result();
            }
            return true; // Para `UPDATE`, `DELETE` o `INSERT`, solo devolver éxito
        } else {
            logError("Error en la consulta: " . $stmt->error . " - SQL: $sql");
            return false;
        }
    }
    function ejecutarConsultaSimpleFila($sql, $params = [])
{
    global $conexion;  // Asegúrate de tener acceso a la conexión a la base de datos

    // Prepara la consulta
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        logError("Error en la preparación de la consulta: " . $conexion->error . " - SQL: $sql");
        return null;
    }

    // Si hay parámetros, los ligamos a la consulta preparada
    if (!empty($params)) {
        // Determinar los tipos de datos
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        if (!$stmt->bind_param($types, ...$params)) {
            logError("Error en bind_param: " . $stmt->error . " - SQL: $sql");
            return null;
        }
    }

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        logError("Error en la ejecución de la consulta: " . $stmt->error . " - SQL: $sql");
        return null;
    }

    // Obtén el resultado
    $resultado = $stmt->get_result();
    if ($resultado === false) {
        logError("Error al obtener el resultado: " . $stmt->error . " - SQL: $sql");
        return null;
    }

    // Verifica si se obtuvo una fila y devuélvela como un array asociativo
    if ($fila = $resultado->fetch_assoc()) {
        return $fila;
    } else {
        return null;  // Devuelve null si no hay resultados
    }
}

    
    function ejecutarConsulta_retornarID($sql, $params = [])
    {
        global $conexion;

        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error en la preparación de la consulta: " . $conexion->error);
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            if (!$stmt->bind_param($types, ...$params)) {
                error_log("Error en bind_param: " . $stmt->error);
                return false;
            }
        }

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        } else {
            error_log("Error en la consulta: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    if (!function_exists('limpiarCadena')) {
        function limpiarCadena($str)
        {
            global $conexion;
            $str = mysqli_real_escape_string($conexion, trim($str));
            return htmlspecialchars($str);
        }
    }

    function ejecutarConsultaArray($sql, $params = [])
    {
        $result = ejecutarConsulta($sql, $params);
        if ($result === false) {
            return false;
        }

        $resultArray = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $resultArray[] = $row;
            }
        }

        return $resultArray;
    }

}
?>

