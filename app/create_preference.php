<?php
// app/create_preference.php
require_once '../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

// Leer los datos enviados por JS
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['phone'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
    exit;
}

$name = trim($data['name']);
$phone = trim($data['phone']);
$is_bump = isset($data['bump']) && $data['bump'] === true;

// Credenciales de Prueba (Cámbialas a Producción cuando estés listo)
$access_token = "APP_USR-3157555154327509-031319-5abc27c624037a097c816f574baeee44-3256090307";

$price = $is_bump ? 19.99 : 14.99;
$external_reference = $is_bump ? 'bump' : 'standard';

// URL dinámica asegurando que enviamos el nombre y teléfono por GET
$base_url = "https://myworldingles.simpledomai123n.online";
$back_url_success = $base_url . "/register_success.php?parent_name=" . urlencode($name) . "&parent_phone=" . urlencode($phone);
$back_url_failure = $base_url . "/landing.php";

$preference_data = [
    "items" => [
        [
            "id" => "MW-001",
            "title" => "My World - Acceso Vitalicio",
            "description" => "Acceso de por vida a la plataforma My World",
            "quantity" => 1,
            "unit_price" => $price,
            "currency_id" => "PEN"
        ]
    ],
    "payer" => [
        "name" => $name,
        "phone" => [
            "area_code" => "51",
            "number" => $phone
        ]
    ],
    "back_urls" => [
        "success" => $back_url_success,
        "failure" => $back_url_failure,
        "pending" => $back_url_failure
    ],
    "auto_return" => "approved",
    "external_reference" => $external_reference,
    "statement_descriptor" => "MY WORLD"
];

$ch = curl_init("https://api.mercadopago.com/checkout/preferences");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $access_token,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 || $http_code == 201) {
    $mp_data = json_decode($response, true);
    if (isset($mp_data['init_point'])) {
        echo json_encode(['success' => true, 'init_point' => $mp_data['init_point']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Respuesta inesperada de Mercado Pago.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con Mercado Pago.', 'details' => $response]);
}
?>