<?php
// app/process_payment.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token'])) {
    echo json_encode(['success' => false, 'message' => 'Falta el token de pago.']);
    exit;
}

// Validación de datos
if (!isset($data['child_name']) || !isset($data['parent_phone']) || empty(trim($data['child_name'])) || empty(trim($data['parent_phone']))) {
    echo json_encode(['success' => false, 'message' => 'Faltan los datos del cliente.']);
    exit;
}

$child_name = trim($data['child_name']);
$parent_phone = preg_replace('/[^0-9]/', '', $data['parent_phone']);

// Verificamos si el usuario ya existe
$stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
$stmtCheck->execute([$parent_phone]);
$user = $stmtCheck->fetch();

if ($user) {
    // Si ya existe, simplemente le damos acceso (Vitalicio)
    $user_id = $user['id'];
    $msg = '¡Bienvenido de vuelta! Tu cuenta de acceso vitalicio ya está activa.';
} else {
    // Crear usuario nuevo con acceso vitalicio
    $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars) VALUES (?, ?, 0)");
    $stmtInsert->execute([$child_name, $parent_phone]);
    $user_id = $pdo->lastInsertId();
    $msg = '¡Cuenta VITALICIA creada con éxito! Bienvenid@ a My World.';
}

// Iniciar sesión automáticamente
$_SESSION['user_id'] = $user_id;

echo json_encode(['success' => true, 'message' => $msg]);
?>