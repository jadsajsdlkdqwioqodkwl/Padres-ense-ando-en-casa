<?php
// Seguro para evitar choques de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/config.php';

// Forzamos el header JSON limpio
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token'])) {
    echo json_encode(['success' => false, 'message' => 'Falta el token de pago.']);
    exit;
}

$modo_prueba = true; 

if ($modo_prueba) {
    $new_expire_date = date('Y-m-d H:i:s', strtotime('+31 days'));

    // CASO 1: RENOVACIÓN (El padre ya está logueado en renovar.php)
    if (isset($_SESSION['user_id']) && isset($data['is_renewal']) && $data['is_renewal'] === true) {
        $stmtUpdate = $pdo->prepare("UPDATE users SET subscription_expires_at = ? WHERE id = ?");
        $stmtUpdate->execute([$new_expire_date, $_SESSION['user_id']]);
        
        echo json_encode(['success' => true, 'message' => '¡Renovación exitosa! Tienes 31 días más.']);
        exit;
    } 
    
    // CASO 2: USUARIO NUEVO (Viene desde landing.php)
    else {
        if (!isset($data['child_name']) || !isset($data['parent_phone']) || empty(trim($data['child_name'])) || empty(trim($data['parent_phone']))) {
            echo json_encode(['success' => false, 'message' => 'Faltan los datos del cliente.']);
            exit;
        }

        $child_name = trim($data['child_name']);
        $parent_phone = trim($data['parent_phone']);

        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
        $stmtCheck->execute([$parent_phone]);
        $user = $stmtCheck->fetch();

        if ($user) {
            // Renovar si el usuario ya existe
            $stmtUpdate = $pdo->prepare("UPDATE users SET subscription_expires_at = ? WHERE id = ?");
            $stmtUpdate->execute([$new_expire_date, $user['id']]);
            $user_id = $user['id'];
        } else {
            // Crear usuario nuevo
            $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars, subscription_expires_at) VALUES (?, ?, 0, ?)");
            $stmtInsert->execute([$child_name, $parent_phone, $new_expire_date]);
            $user_id = $pdo->lastInsertId();
        }

        $_SESSION['user_id'] = $user_id;
        echo json_encode(['success' => true, 'message' => '¡Cuenta creada con éxito! Bienvenid@.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'El banco rechazó la tarjeta.']);
}
?>