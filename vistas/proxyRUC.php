<?php
$token = 'apis-token-6397.Lmx17dsxx-Ohh00Fi-eeORJX6gQMMECC';

// Validar el RUC recibido
$ruc = isset($_GET['ruc']) ? $_GET['ruc'] : '';
if (!preg_match('/^\d{11}$/', $ruc)) {
    echo json_encode(['error' => 'RUC inválido']);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 1,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token
    ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

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

if ($httpcode === 200) {
    header('Content-Type: application/json');
    echo $response;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error en la consulta del RUC',
        'code' => $httpcode
    ]);
}

?>
