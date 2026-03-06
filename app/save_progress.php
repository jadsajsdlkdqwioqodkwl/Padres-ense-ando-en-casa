<?php
// Seguro para evitar choques de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('whatsapp_webhook.php')) {
    require_once 'whatsapp_webhook.php'; 
} elseif (file_exists('whatsapp_webhook (deprecado).php')) {
    require_once 'whatsapp_webhook (deprecado).php'; 
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['lesson_id']) && isset($_SESSION['user_id'])) {
    $lesson_id = (int)$data['lesson_id'];
    $stars_earned = isset($data['stars']) ? (int)$data['stars'] : 0;
    $user_id = $_SESSION['user_id']; 
    
    $selected_words = isset($data['selected_words']) ? json_encode($data['selected_words']) : null;
    $just_words = isset($data['just_words']) ? $data['just_words'] : false;

    try {
        // AÑADIDO: Si solo estamos guardando las palabras antes de empezar a jugar
        if ($just_words) {
            $stmtInsert = $pdo->prepare("INSERT INTO progress (user_id, lesson_id, selected_words) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE selected_words = ?");
            $stmtInsert->execute([$user_id, $lesson_id, $selected_words, $selected_words]);
            echo json_encode(['status' => 'success', 'message' => 'Palabras guardadas, iniciando playlist']);
            exit;
        }

        // Si ya terminó los 5 juegos, guardamos todo y damos estrellas
        $stmtCheck = $pdo->prepare("SELECT is_completed FROM progress WHERE user_id = ? AND lesson_id = ?");
        $stmtCheck->execute([$user_id, $lesson_id]);
        $already_completed = $stmtCheck->fetchColumn();

        if (!$already_completed) {
            $stmtInsert = $pdo->prepare("UPDATE progress SET is_completed = 1, stars_earned = ? WHERE user_id = ? AND lesson_id = ?");
            $stmtInsert->execute([$stars_earned, $user_id, $lesson_id]);

            $stmtUpdateUser = $pdo->prepare("UPDATE users SET total_stars = total_stars + ? WHERE id = ?");
            $stmtUpdateUser->execute([$stars_earned, $user_id]);

            // === MAGIA WHATSAPP ===
            $stmtUser = $pdo->prepare("SELECT child_name, parent_phone FROM users WHERE id = ?");
            $stmtUser->execute([$user_id]);
            $userInfo = $stmtUser->fetch();

            $stmtLesson = $pdo->prepare("SELECT title FROM lessons WHERE id = ?");
            $stmtLesson->execute([$lesson_id]);
            $lesson_title = $stmtLesson->fetchColumn();

            if ($userInfo && !empty($userInfo['parent_phone'])) {
                sendParentWhatsApp($userInfo['parent_phone'], $userInfo['child_name'], $lesson_title, $stars_earned);
            }

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