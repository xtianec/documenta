<?php
// Configura el token de la API y el DNI
$token = 'apis-token-6397.Lmx17dsxx-Ohh00Fi-eeORJX6gQMMECC';

// Validar el DNI recibido
$dni = isset($_GET['dni']) ? $_GET['dni'] : '';
if (!preg_match('/^\d{8}$/', $dni)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'DNI inválido']);
    exit;
}

// Inicia la llamada a la API
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 1,
    CURLOPT_HTTPHEADER => array(
        'Referer: https://apis.net.pe/consulta-dni-api',
        'Authorization: Bearer ' . $token
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($response === false) {
    $error = curl_error($curl);
    curl_close($curl);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error al conectarse con la API',
        'details' => $error
    ]);
    exit;
}

curl_close($curl);

if ($httpCode !== 200) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error en la consulta del DNI',
        'code' => $httpCode
    ]);
    exit;
}

// Devuelve la respuesta JSON a tu frontend
header('Content-Type: application/json');
echo $response;
?>
