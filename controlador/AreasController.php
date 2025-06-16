<?php
require_once "../modelos/Area.php";

$area = new Area();

// Obtener todos los parámetros POST necesarios
$area_id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
$area_name = isset($_POST["area_name"]) ? limpiarCadena($_POST["area_name"]) : "";
$company_id = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";

switch ($_GET["op"]) {
    case 'guardar':
        $rspta = $area->insertar($area_name, $company_id);
        if ($rspta) {
            echo "Área registrada correctamente";
        } else {
            echo "El área ya existe en esta empresa o ocurrió un error";
        }
        break;

    case 'editar':
        $rspta = $area->editar($area_id, $area_name, $company_id);
        if ($rspta) {
            echo "Área actualizada correctamente";
        } else {
            echo "El área ya existe en esta empresa o ocurrió un error";
        }
        break;

    case 'desactivar':
        $rspta = $area->desactivar($area_id);
        echo $rspta ? "Área desactivada correctamente" : "No se pudo desactivar el área";
        break;

    case 'activar':
        $rspta = $area->activar($area_id);
        echo $rspta ? "Área activada correctamente" : "No se pudo activar el área";
        break;

    case 'mostrar':
        $rspta = $area->mostrar($area_id);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $area->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id,
                "1" => htmlspecialchars($reg->area_name),
                "2" => htmlspecialchars($reg->company_name),
                "3" => ($reg->is_active) ? '<span class="badge badge-success">Activado</span>' : '<span class="badge badge-danger">Desactivado</span>',
                "4" => ($reg->is_active) ?
                    '<button class="btn btn-warning btn-xs btn-edit-area" data-id="' . $reg->id . '">
                        <i class="fa fa-edit"></i>
                    </button>' . ' ' .
                    '<button class="btn btn-danger btn-xs btn-desactivar-area" data-id="' . $reg->id . '">
                        <i class="fa fa-window-close"></i>
                    </button>' :
                    '<button class="btn btn-warning btn-xs btn-edit-area" data-id="' . $reg->id . '">
                        <i class="fa fa-edit"></i>
                    </button>' . ' ' .
                    '<button class="btn btn-primary btn-xs btn-activar-area" data-id="' . $reg->id . '">
                        <i class="fa fa-check-square"></i>
                    </button>',
            );
        }

        $results = array(
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'verificar_area':
        $area_name_verificar = isset($_POST["area_name"]) ? limpiarCadena($_POST["area_name"]) : "";
        $company_id_verificar = isset($_POST["company_id"]) ? limpiarCadena($_POST["company_id"]) : "";
        $id_verificar = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : null;

        $rspta = $area->verificarArea($area_name_verificar, $company_id_verificar, $id_verificar);
        echo $rspta ? "Área ya existe" : "Área disponible";
        break;

    // Nueva Operación para Listar Áreas por Empresa
    case 'listar_por_empresa':
        $company_id_listar = isset($_GET["company_id"]) ? limpiarCadena($_GET["company_id"]) : "";
        if (empty($company_id_listar)) {
            echo json_encode([]);
            exit;
        }
    
        $rspta = $area->listar_por_empresa($company_id_listar);
        $data = array();
    
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "id" => $reg->id,
                "area_name" => htmlspecialchars($reg->area_name)
            );
        }
    
        echo json_encode($data);
        break;
    
}
?>
