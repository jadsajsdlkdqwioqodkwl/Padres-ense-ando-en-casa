<?php
require_once '../includes/config.php';

// Esta función recibe los datos y dispara la petición CURL a tu Evolution API
function sendParentWhatsApp($parent_phone, $child_name, $lesson_title, $stars) {
    // IMPORTANTE: Asegúrate de que 'evolution_exchange' sea el nombre real de tu instancia en Evolution API
    $evolution_url = 'http://143.198.158.33:8080/message/sendText/evolution_exchange'; 
    $api_key = 'Prosegur3143%'; 
    
    // Formatear el número (quita cualquier símbolo raro)
    $number = preg_replace('/[^0-9]/', '', $parent_phone);

    $message = "🎉 ¡Hola! Tu hijo/a *{$child_name}* acaba de completar la lección *{$lesson_title}* y ha ganado *{$stars} estrellas* ⭐. ¡Felicítalo de nuestra parte!";

    $data = [
        "number" => $number,
        "options" => [
            "delay" => 1200, // Simula que está escribiendo
            "presence" => "composing" 
        ],
        "textMessage" => [
            "text" => $message
        ]
    ];

    $ch = curl_init($evolution_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'apikey: ' . $api_key
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code === 200 || $http_code === 201;
}
?>