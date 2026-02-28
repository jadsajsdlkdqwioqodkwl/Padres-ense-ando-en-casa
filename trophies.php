<?php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($pdo, $user_id);

$stmt = $pdo->prepare("SELECT l.content_data FROM progress p JOIN lessons l ON p.lesson_id = l.id WHERE p.user_id = ? AND p.is_completed = 1");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll();

$learned_items = [];

foreach ($completed_lessons as $row) {
    $data = json_decode($row['content_data'], true);
    if (isset($data['rounds'])) {
        foreach ($data['rounds'] as $r) {
            // Busca la fonética, si no está, usa la palabra en inglés
            $ph = $r['phonetic'] ?? $r['word'] ?? $r['target_word'] ?? $r['color_name'] ?? '';
            
            if (isset($r['word'])) $learned_items[] = ['en' => $r['word'], 'es' => $r['translation'] ?? '', 'ph' => $ph];
            if (isset($r['sentence'])) $learned_items[] = ['en' => implode(" ", $r['sentence']), 'es' => $r['translation'] ?? '', 'ph' => $r['phonetic'] ?? implode(" ", $r['sentence'])];
            if (isset($r['target_word'])) $learned_items[] = ['en' => $r['target_word'], 'es' => $r['translation'] ?? '', 'ph' => $ph];
            if (isset($r['color_name'])) $learned_items[] = ['en' => $r['color_name'], 'es' => $r['translation'] ?? '', 'ph' => $ph];
        }
    } else {
        $ph = $data['phonetic'] ?? $data['word'] ?? $data['target_word'] ?? '';
        if (isset($data['word'])) $learned_items[] = ['en' => $data['word'], 'es' => $data['translation'] ?? '', 'ph' => $ph];
        if (isset($data['sentence'])) $learned_items[] = ['en' => implode(" ", $data['sentence']), 'es' => $data['translation'] ?? '', 'ph' => $data['phonetic'] ?? implode(" ", $data['sentence'])];
        if (isset($data['target_word'])) $learned_items[] = ['en' => $data['target_word'], 'es' => $data['translation'] ?? '', 'ph' => $ph];
    }
}

// Filtra duplicados para que no salgan palabras repetidas
$learned_items = array_map("unserialize", array_unique(array_map("serialize", $learned_items)));
$page_title = "Mis Trofeos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .trophy-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 30px; }
        .trophy-card { background: var(--light); border-radius: 20px; padding: 20px; text-align: center; border: 2px solid var(--border-color); transition: 0.3s; position: relative; overflow: hidden; cursor: pointer; }
        .trophy-card:hover { transform: translateY(-5px); border-color: var(--accent); box-shadow: 0 10px 20px rgba(255, 159, 67, 0.2); }
        .trophy-en { font-size: 22px; font-weight: bold; color: var(--primary); margin-bottom: 5px; }
        .trophy-es { color: var(--text-muted); font-size: 14px; }
        .trophy-icon { font-size: 30px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        <h1 style="font-size: 2.5rem;">🏆 Salón de la Fama</h1>
        <p style="font-size: 18px; color: var(--text-muted);">Aquí están las palabras que <?php echo htmlspecialchars($user_info['child_name']); ?> ha conquistado.</p>

        <?php if (empty($learned_items)): ?>
            <div style="padding: 40px; background: #fffde7; border-radius: 20px; margin-top: 20px;">
                <h2>Aún no hay trofeos</h2>
                <p>¡Completa tu primera misión para desbloquear palabras aquí!</p>
            </div>
        <?php else: ?>
            <div class="trophy-grid">
                <?php foreach ($learned_items as $item): ?>
                    <div class="trophy-card" onclick="playTTS('<?php echo addslashes($item['ph']); ?>')" title="Toca para escuchar">
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