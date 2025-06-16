<?php
require_once "../modelos/SupplierDetails.php";
session_start(); // Asegurarse de que la sesión esté iniciada

class SupplierDetailsController
{
    // Guardar detalles del proveedor
    public function guardar()
    {
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $supplier_id = $_SESSION['supplier_id'];
        $contactNameAccouting = isset($_POST["contactNameAccouting"]) ? limpiarCadena($_POST["contactNameAccouting"]) : "";
        $contactEmailAccouting = isset($_POST["contactEmailAccouting"]) ? limpiarCadena($_POST["contactEmailAccouting"]) : "";
        $contactPhoneAccouting = isset($_POST["contactPhoneAccouting"]) ? limpiarCadena($_POST["contactPhoneAccouting"]) : "";
        $Provide = isset($_POST["Provide"]) ? limpiarCadena($_POST["Provide"]) : "";

        $supplierDetails = new SupplierDetails();
        $result = $supplierDetails->insertar($supplier_id, $contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide);

        if ($result['success']) {
            echo json_encode([
                'status' => true,
                'message' => "Datos guardados correctamente"
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => "Error al guardar los datos: " . $result['error']
            ]);
        }
    }

    // Actualizar detalles del proveedor
    public function actualizar()
    {
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $supplier_id = $_SESSION['supplier_id'];
        $contactNameAccouting = isset($_POST["contactNameAccoutingUpdate"]) ? limpiarCadena($_POST["contactNameAccoutingUpdate"]) : "";
        $contactEmailAccouting = isset($_POST["contactEmailAccoutingUpdate"]) ? limpiarCadena($_POST["contactEmailAccoutingUpdate"]) : "";
        $contactPhoneAccouting = isset($_POST["contactPhoneAccoutingUpdate"]) ? limpiarCadena($_POST["contactPhoneAccoutingUpdate"]) : "";
        $Provide = isset($_POST["ProvideUpdate"]) ? limpiarCadena($_POST["ProvideUpdate"]) : "";

        $supplierDetails = new SupplierDetails();
        $existingDetails = $supplierDetails->mostrar($supplier_id);

        if ($existingDetails) {
            $result = $supplierDetails->actualizar($existingDetails['id'], $contactNameAccouting, $contactEmailAccouting, $contactPhoneAccouting, $Provide);
            if ($result['success']) {
                echo json_encode([
                    'status' => true,
                    'message' => "Datos actualizados correctamente"
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => "Error al actualizar los datos: " . $result['error']
                ]);
            }
        } else {
            echo json_encode([
                'status' => false,
                'message' => "No se encontraron datos para actualizar"
            ]);
        }
    }

    // Mostrar detalles de un proveedor
    public function mostrar()
    {
        if (!isset($_SESSION['supplier_id'])) {
            echo json_encode(['status' => false, 'message' => 'Sesión no iniciada.']);
            return;
        }

        $supplier_id = $_SESSION['supplier_id'];
        $supplierDetails = new SupplierDetails();
        $result = $supplierDetails->mostrar($supplier_id);

        if ($result) {
            echo json_encode([
                'status' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => "No se encontraron datos registrados"
            ]);
        }
    }
}

// Verificar la operación solicitada
if (isset($_GET["op"])) {
    $controller = new SupplierDetailsController();
    switch ($_GET["op"]) {
        case 'guardar':
            $controller->guardar();
            break;
        case 'actualizar':
            $controller->actualizar();
            break;
        case 'mostrar':
            $controller->mostrar();
            break;
    }
}

// Asegurarse de que la función limpiarCadena no se redeclare
if (!function_exists('limpiarCadena')) {
    function limpiarCadena($str) {
        $str = trim($str);
        $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        return $str;
    }
}
?>
