<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token']) || !isset($data['child_name']) || !isset($data['parent_phone'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

$token = $data['token'];
$child_name = trim($data['child_name']);
$parent_phone = trim($data['parent_phone']);

// =========================================================================
// MODO DE PRUEBAS ACTIVADO (Bypass de Culqi)
// =========================================================================
$modo_prueba = true; 

if ($modo_prueba) {
    
    // 1. Verificar si el usuario ya existe por su WhatsApp
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
    $stmtCheck->execute([$parent_phone]);
    $user = $stmtCheck->fetch();

    $new_expire_date = date('Y-m-d H:i:s', strtotime('+31 days'));

    if ($user) {
        // Usuario existe (Renovación): Actualizamos la fecha de caducidad
        $stmtUpdate = $pdo->prepare("UPDATE users SET subscription_expires_at = ? WHERE id = ?");
        $stmtUpdate->execute([$new_expire_date, $user['id']]);
        $user_id = $user['id'];
    } else {
        // Usuario Nuevo: Lo creamos
        $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars, subscription_expires_at) VALUES (?, ?, 0, ?)");
        $stmtInsert->execute([$child_name, $parent_phone, $new_expire_date]);
        $user_id = $pdo->lastInsertId();
    }

    // 2. Iniciar sesión automáticamente
    session_start();
    $_SESSION['user_id'] = $user_id;

    echo json_encode(['success' => true, 'message' => '¡Modo Prueba! Pago simulado exitoso y cuenta activada.']);

} else {
    // Aquí irá el código real de Culqi cuando estés listo
    echo json_encode(['success' => false, 'message' => 'El banco rechazó la tarjeta.']);
}
?>