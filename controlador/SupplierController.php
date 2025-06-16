<?php
require_once "../modelos/Supplier.php";

class SupplierController
{
    // Guardar proveedor
    public function guardar()
    {
        $supplier = new Supplier();

        // Obtener los datos desde el formulario
        $RUC = isset($_POST["RUC"]) ? limpiarCadena($_POST["RUC"]) : "";
        $companyName = isset($_POST["companyName"]) ? limpiarCadena($_POST["companyName"]) : "";
        $department = isset($_POST["department"]) ? limpiarCadena($_POST["department"]) : "";
        $province = isset($_POST["province"]) ? limpiarCadena($_POST["province"]) : "";
        $district = isset($_POST["district"]) ? limpiarCadena($_POST["district"]) : "";
        $address = isset($_POST["address"]) ? limpiarCadena($_POST["address"]) : "";
        $stateSunat = isset($_POST["stateSunat"]) ? limpiarCadena($_POST["stateSunat"]) : "";
        $conditionSunat = isset($_POST["conditionSunat"]) ? limpiarCadena($_POST["conditionSunat"]) : "";
        $contactNameBusiness = isset($_POST["contactNameBusiness"]) ? limpiarCadena($_POST["contactNameBusiness"]) : "";
        $contactEmailBusiness = isset($_POST["contactEmailBusiness"]) ? limpiarCadena($_POST["contactEmailBusiness"]) : "";
        $contactPhoneBusiness = isset($_POST["contactPhoneBusiness"]) ? limpiarCadena($_POST["contactPhoneBusiness"]) : "";

        // Inserción
        $rspta = $supplier->insertar($RUC, $companyName, $department, $province, $district, $address, $stateSunat, $conditionSunat, $contactNameBusiness, $contactEmailBusiness, $contactPhoneBusiness);

        // Mostrar respuesta
        echo $rspta ? "Proveedor registrado correctamente" : "No se pudo registrar el proveedor";
    }
    

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
    
    

    // Desactivar proveedor
    public function desactivar()
    {
        $supplier = new Supplier();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $supplier->desactivar($id);
        echo $rspta ? "Proveedor desactivado correctamente" : "No se pudo desactivar el proveedor";
    }

    // Activar proveedor
    public function activar()
    {
        $supplier = new Supplier();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $supplier->activar($id);
        echo $rspta ? "Proveedor activado correctamente" : "No se pudo activar el proveedor";
    }

    // Mostrar datos de un proveedor
    public function mostrar()
    {
        $supplier = new Supplier();
        $id = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
        $rspta = $supplier->mostrar($id);
        echo json_encode($rspta);
    }

    // Listar proveedores
    public function listar()
    {
        $supplier = new Supplier();
        $rspta = $supplier->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->id,
                "1" => $reg->RUC,
                "2" => $reg->companyName,
                "3" => $reg->stateSunat,
                "4" => $reg->conditionSunat,
                "5" => $reg->contactNameBusiness,
                "6" => $reg->contactEmailBusiness,
                "7" => $reg->contactPhoneBusiness,
                "8" => $reg->address,
                "9" => $reg->is_active ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>',
                "10" => $reg->is_active
                    ? '<button class="btn btn-warning" onclick="mostrar(' . $reg->id . ')"><i class="fa fa-pencil"></i></button>' .
                      ' <button class="btn btn-danger" onclick="desactivar(' . $reg->id . ')"><i class="fa fa-close"></i></button>'
                    : '<button class="btn btn-primary" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button>'
            );
        }

        

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
    }
}

// Verificar la operación y ejecutar el controlador correspondiente
if (isset($_GET["op"])) {
    $controller = new SupplierController();
    switch ($_GET["op"]) {
        case 'listar':
            $controller->listar();
            break;
        case 'guardar':
            $controller->guardar();
            break;
        case 'editar':
            $controller->actualizar();
            break;
        case 'mostrar':
            $controller->mostrar();
            break;
        case 'desactivar':
            $controller->desactivar();
            break;
        case 'activar':
            $controller->activar();
            break;
    }
}
