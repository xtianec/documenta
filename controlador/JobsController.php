<?php
require_once "../modelos/Jobs.php";

$jobs = new Jobs();

$job_id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
$position_name = isset($_POST["position_name"]) ? limpiarCadena($_POST["position_name"]) : "";
$area_id = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";

switch ($_GET["op"]) {
    case 'guardar':
        $rspta = $jobs->insertar($position_name, $area_id);
        if ($rspta) {
            echo "Puesto de trabajo registrado correctamente";
        } else {
            echo "El puesto de trabajo ya existe en esta área o ocurrió un error";
        }
        break;

    case 'editar':
        $rspta = $jobs->editar($job_id, $position_name, $area_id);
        if ($rspta) {
            echo "Puesto de trabajo actualizado correctamente";
        } else {
            echo "El puesto de trabajo ya existe en esta área o ocurrió un error";
        }
        break;

    case 'desactivar':
        $rspta = $jobs->desactivar($job_id);
        echo $rspta ? "Puesto de trabajo desactivado correctamente" : "No se pudo desactivar el puesto de trabajo";
        break;

    case 'activar':
        $rspta = $jobs->activar($job_id);
        echo $rspta ? "Puesto de trabajo activado correctamente" : "No se pudo activar el puesto de trabajo";
        break;

    case 'mostrar':
        $rspta = $jobs->mostrar($job_id);
        echo json_encode($rspta);
        break;
        case 'listar':
            $company_id = isset($_GET['company_id']) ? limpiarCadena($_GET['company_id']) : "";
            $area_id = isset($_GET['area_id']) ? limpiarCadena($_GET['area_id']) : "";
            $position_id = isset($_GET['position_id']) ? limpiarCadena($_GET['position_id']) : "";
        
            $rspta = $jobs->listarFiltrado($company_id, $area_id, $position_id);
            $data = array();
        
            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0" => $reg->id,
                    "1" => htmlspecialchars($reg->position_name),
                    "2" => htmlspecialchars($reg->area_name),
                    "3" => htmlspecialchars($reg->company_name),
                    "4" => ($reg->is_active) ? '<span class="badge badge-success">Activado</span>' : '<span class="badge badge-danger">Desactivado</span>',
                    "5" => ($reg->is_active) ?
                        '<button class="btn btn-warning btn-xs btn-edit-job" data-id="' . $reg->id . '">
                            <i class="fa fa-edit"></i>
                        </button>' . ' ' .
                        '<button class="btn btn-danger btn-xs btn-desactivar-job" data-id="' . $reg->id . '">
                            <i class="fa fa-window-close"></i>
                        </button>' :
                        '<button class="btn btn-warning btn-xs btn-edit-job" data-id="' . $reg->id . '">
                            <i class="fa fa-edit"></i>
                        </button>' . ' ' .
                        '<button class="btn btn-primary btn-xs btn-activar-job" data-id="' . $reg->id . '">
                            <i class="fa fa-check-square"></i>
                        </button>',
                );
            }
        
            $results = array(
                "aaData" => $data
            );
            echo json_encode($results);
            break;
        

    case 'verificar_puesto':
        $position_name_verificar = isset($_POST["position_name"]) ? limpiarCadena($_POST["position_name"]) : "";
        $area_id_verificar = isset($_POST["area_id"]) ? limpiarCadena($_POST["area_id"]) : "";
        $id_verificar = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : null;

        $rspta = $jobs->verificarPuesto($position_name_verificar, $area_id_verificar, $id_verificar);
        echo $rspta ? "Puesto ya existe" : "Puesto disponible";
        break;

    // Nueva Operación para Listar Puestos por Área
    case 'listar_por_area':
        $area_id_listar = isset($_GET["area_id"]) ? limpiarCadena($_GET["area_id"]) : "";
        if (empty($area_id_listar)) {
            echo json_encode([]);
            exit;
        }

        $rspta = $jobs->listar_por_area($area_id_listar);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "id" => $reg->id,
                "position_name" => htmlspecialchars($reg->position_name)
            );
        }

        echo json_encode($data);
        break;
}
?>
