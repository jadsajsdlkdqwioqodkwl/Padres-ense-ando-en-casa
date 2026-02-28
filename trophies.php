<?php
// trophies.php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($pdo, $user_id);

// Obtener todas las lecciones completadas por el usuario
$stmt = $pdo->prepare("
    SELECT l.content_data 
    FROM progress p 
    JOIN lessons l ON p.lesson_id = l.id 
    WHERE p.user_id = ? AND p.is_completed = 1
");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll();

$learned_items = [];

// Extraer vocabulario del JSON (soporta tanto el formato antiguo como el nuevo 'rounds')
foreach ($completed_lessons as $row) {
    $data = json_decode($row['content_data'], true);
    
    // Si tiene la estructura multironda nueva
    if (isset($data['rounds'])) {
        foreach ($data['rounds'] as $r) {
            if (isset($r['word'])) $learned_items[] = ['en' => $r['word'], 'es' => $r['translation'] ?? ''];
            if (isset($r['sentence'])) $learned_items[] = ['en' => implode(" ", $r['sentence']), 'es' => $r['translation'] ?? ''];
            if (isset($r['target_word'])) $learned_items[] = ['en' => $r['target_word'], 'es' => $r['translation'] ?? ''];
        }
    } else {
        // Soporte para JSON legado (1 sola palabra)
        if (isset($data['word'])) $learned_items[] = ['en' => $data['word'], 'es' => $data['translation'] ?? ''];
        if (isset($data['sentence'])) $learned_items[] = ['en' => implode(" ", $data['sentence']), 'es' => $data['translation'] ?? ''];
        if (isset($data['target_word'])) $learned_items[] = ['en' => $data['target_word'], 'es' => $data['translation'] ?? ''];
    }
}

// Eliminar duplicados
$learned_items = array_unique($learned_items, SORT_REGULAR);
$page_title = "Mis Trofeos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .trophy-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px; margin-top: 30px;
        }
        .trophy-card {
            background: var(--light); border-radius: 20px; padding: 20px;
            text-align: center; border: 2px solid var(--border-color);
            transition: 0.3s; position: relative; overflow: hidden;
        }
        .trophy-card:hover {
            transform: translateY(-5px); border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(255, 159, 67, 0.2);
        }
        .trophy-en { font-size: 22px; font-weight: bold; color: var(--primary); margin-bottom: 5px; }
        .trophy-es { color: var(--text-muted); font-size: 14px; }
        .trophy-icon { font-size: 30px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        
        <h1 style="font-size: 2.5rem;">🏆 Salón de la Fama</h1>
        <p style="font-size: 18px; color: var(--text-muted);">Aquí están todas las palabras y frases que <?php echo htmlspecialchars($user_info['child_name']); ?> ha conquistado.</p>

        <?php if (empty($learned_items)): ?>
            <div style="padding: 40px; background: #fffde7; border-radius: 20px; margin-top: 20px;">
                <h2>Aún no hay trofeos</h2>
                <p>¡Completa tu primera misión para desbloquear palabras aquí!</p>
            </div>
        <?php else: ?>
            <div class="trophy-grid">
                <?php foreach ($learned_items as $item): ?>
                    <div class="trophy-card" onclick="playTTS('<?php echo addslashes($item['en']); ?>')" style="cursor: pointer;" title="Toca para escuchar">
                        <div class="trophy-icon">⭐</div>
                        <div class="trophy-en"><?php echo htmlspecialchars($item['en']); ?></div>
                        <div class="trophy-es"><?php echo htmlspecialchars($item['es']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="assets/js/engine.js"></script>
</body>
</html>