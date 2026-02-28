<?php
// app/save_progress.php
require_once '../includes/config.php';
require_once 'whatsapp_webhook.php'; // Tu archivo de conexión a Evolution API

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['lesson_id']) && isset($data['stars']) && isset($_SESSION['user_id'])) {
    $lesson_id = (int)$data['lesson_id'];
    $stars_earned = (int)$data['stars'];
    $user_id = $_SESSION['user_id']; 

    try {
        $stmtCheck = $pdo->prepare("SELECT is_completed FROM progress WHERE user_id = ? AND lesson_id = ?");
        $stmtCheck->execute([$user_id, $lesson_id]);
        $already_completed = $stmtCheck->fetchColumn();

        if (!$already_completed) {
            // Guardar progreso
            $stmtInsert = $pdo->prepare("INSERT INTO progress (user_id, lesson_id, is_completed, stars_earned) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE is_completed = 1, stars_earned = ?");
            $stmtInsert->execute([$user_id, $lesson_id, $stars_earned, $stars_earned]);

            // Sumar estrellas al global
            $stmtUpdateUser = $pdo->prepare("UPDATE users SET total_stars = total_stars + ? WHERE id = ?");
            $stmtUpdateUser->execute([$stars_earned, $user_id]);

            // === MAGIA WHATSAPP ===
            // Obtener datos del niño y del padre para el mensaje
            $stmtUser = $pdo->prepare("SELECT child_name, parent_phone FROM users WHERE id = ?");
            $stmtUser->execute([$user_id]);
            $userInfo = $stmtUser->fetch();

            $stmtLesson = $pdo->prepare("SELECT title FROM lessons WHERE id = ?");
            $stmtLesson->execute([$lesson_id]);
            $lesson_title = $stmtLesson->fetchColumn();

            // Si hay un número registrado, enviamos el WhatsApp
            if ($userInfo && !empty($userInfo['parent_phone'])) {
                sendParentWhatsApp($userInfo['parent_phone'], $userInfo['child_name'], $lesson_title, $stars_earned);
            }
            // ======================

            echo json_encode(['status' => 'success', 'message' => 'Estrellas sumadas y WhatsApp enviado']);
        } else {
            echo json_encode(['status' => 'info', 'message' => 'Lección ya completada anteriormente']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos o sesión no iniciada']);
}
?>