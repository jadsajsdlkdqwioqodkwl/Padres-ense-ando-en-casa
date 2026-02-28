<?php
// includes/config.php está un nivel arriba, por eso usamos '../'
require_once '../includes/config.php';

// Leemos los datos JSON que mandó el fetch de JavaScript desde controls.php
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['lesson_id']) && isset($data['stars'])) {
    $lesson_id = (int)$data['lesson_id'];
    $stars_earned = (int)$data['stars'];
    // Si no hay sesión, usamos el usuario ID 1 por defecto (Explorador)
    $user_id = $_SESSION['user_id'] ?? 1; 

    try {
        // 1. Verificamos si ya había completado esta lección para no duplicar estrellas
        $stmtCheck = $pdo->prepare("SELECT is_completed FROM progress WHERE user_id = ? AND lesson_id = ?");
        $stmtCheck->execute([$user_id, $lesson_id]);
        $already_completed = $stmtCheck->fetchColumn();

        if (!$already_completed) {
            // 2. Insertamos el progreso en la tabla
            $stmtInsert = $pdo->prepare("INSERT INTO progress (user_id, lesson_id, is_completed, stars_earned) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE is_completed = 1, stars_earned = ?");
            $stmtInsert->execute([$user_id, $lesson_id, $stars_earned, $stars_earned]);

            // 3. Le sumamos las estrellas a su perfil global
            $stmtUpdateUser = $pdo->prepare("UPDATE users SET total_stars = total_stars + ? WHERE id = ?");
            $stmtUpdateUser->execute([$stars_earned, $user_id]);

            echo json_encode(['status' => 'success', 'message' => 'Estrellas sumadas correctamente']);
        } else {
            echo json_encode(['status' => 'info', 'message' => 'Lección ya completada anteriormente']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
}
?>