<?php
// FASE 6: Seguridad de API (Previene que otras webs manden datos aquí)
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inyección de dependencias segura
require_once dirname(__DIR__) . '/includes/config.php';

if (file_exists('whatsapp_webhook.php')) {
    require_once 'whatsapp_webhook.php'; 
} elseif (file_exists('whatsapp_webhook (deprecado).php')) {
    require_once 'whatsapp_webhook (deprecado).php'; 
}

$raw_data = file_get_contents("php://input");
$data = json_decode($raw_data, true);

if (isset($data['lesson_id']) && isset($_SESSION['user_id'])) {
    // FASE 6: Sanitización y validación estricta de tipos
    $lesson_id = filter_var($data['lesson_id'], FILTER_VALIDATE_INT);
    $stars_earned = isset($data['stars']) ? filter_var($data['stars'], FILTER_VALIDATE_INT) : 0;
    $user_id = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT); 
    
    if ($lesson_id === false || $user_id === false) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos detectados.']);
        exit;
    }

    $just_words = isset($data['just_words']) ? (bool)$data['just_words'] : false;
    $selected_words = isset($data['selected_words']) && is_array($data['selected_words']) ? json_encode($data['selected_words']) : null;

    try {
        if ($just_words) {
            $stmtInsert = $pdo->prepare("INSERT INTO progress (user_id, lesson_id, selected_words) VALUES (:uid, :lid, :words) ON DUPLICATE KEY UPDATE selected_words = :words2");
            $stmtInsert->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $stmtInsert->bindValue(':lid', $lesson_id, PDO::PARAM_INT);
            $stmtInsert->bindValue(':words', $selected_words, PDO::PARAM_STR);
            $stmtInsert->bindValue(':words2', $selected_words, PDO::PARAM_STR);
            $stmtInsert->execute();
            echo json_encode(['status' => 'success', 'message' => 'Palabras guardadas, iniciando playlist']);
            exit;
        }

        $stmtCheck = $pdo->prepare("SELECT is_completed FROM progress WHERE user_id = :uid AND lesson_id = :lid LIMIT 1");
        $stmtCheck->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmtCheck->bindValue(':lid', $lesson_id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $already_completed = $stmtCheck->fetchColumn();

        if (!$already_completed) {
            $pdo->beginTransaction(); // Transacción segura

            $stmtInsert = $pdo->prepare("UPDATE progress SET is_completed = 1, stars_earned = :stars WHERE user_id = :uid AND lesson_id = :lid");
            $stmtInsert->bindValue(':stars', $stars_earned, PDO::PARAM_INT);
            $stmtInsert->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $stmtInsert->bindValue(':lid', $lesson_id, PDO::PARAM_INT);
            $stmtInsert->execute();

            $stmtUpdateUser = $pdo->prepare("UPDATE users SET total_stars = total_stars + :stars WHERE id = :uid");
            $stmtUpdateUser->bindValue(':stars', $stars_earned, PDO::PARAM_INT);
            $stmtUpdateUser->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $stmtUpdateUser->execute();

            $pdo->commit();

            // === MAGIA WHATSAPP ===
            $stmtUser = $pdo->prepare("SELECT child_name, parent_phone FROM users WHERE id = :uid LIMIT 1");
            $stmtUser->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $stmtUser->execute();
            $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

            $stmtLesson = $pdo->prepare("SELECT title FROM lessons WHERE id = :lid LIMIT 1");
            $stmtLesson->bindValue(':lid', $lesson_id, PDO::PARAM_INT);
            $stmtLesson->execute();
            $lesson_title = $stmtLesson->fetchColumn();

            if ($userInfo && !empty($userInfo['parent_phone']) && function_exists('sendParentWhatsApp')) {
                sendParentWhatsApp($userInfo['parent_phone'], $userInfo['child_name'], $lesson_title, $stars_earned);
            }

            echo json_encode(['status' => 'success', 'message' => 'Estrellas sumadas y progreso seguro.']);
        } else {
            echo json_encode(['status' => 'info', 'message' => 'Lección ya completada anteriormente.']);
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("DB Error in save_progress: " . $e->getMessage()); // Log silencioso
        echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor guardando el progreso.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Petición rechazada: Sesión no válida.']);
}
?>