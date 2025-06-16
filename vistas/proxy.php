<?php
// Configura el token de la API y el DNI
$token = 'apis-token-6397.Lmx17dsxx-Ohh00Fi-eeORJX6gQMMECC';
$dni = $_GET['dni'];

// Inicia la llamada a la API
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_HTTPHEADER => array(
        'Referer: https://apis.net.pe/consulta-dni-api',
        'Authorization: Bearer ' . $token
    ),
));

$response = curl_exec($curl);
curl_close($curl);

// Devuelve la respuesta JSON a tu frontend
header('Content-Type: application/json');
echo $response;
?>
