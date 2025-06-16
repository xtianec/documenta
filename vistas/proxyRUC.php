<?php
$token = 'apis-token-6397.Lmx17dsxx-Ohh00Fi-eeORJX6gQMMECC';
$ruc = $_GET['ruc'];

// Verifica que el RUC sea válido
if (strlen($ruc) !== 11 || !is_numeric($ruc)) {
    echo json_encode(['error' => 'RUC inválido']);
    exit;
}

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token
    ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpcode === 200) {
    header('Content-Type: application/json');
    echo $response;
} else {
    echo json_encode(['error' => 'Error en la consulta del RUC', 'code' => $httpcode]);
}

?>