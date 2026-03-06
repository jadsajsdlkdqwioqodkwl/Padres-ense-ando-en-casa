<?php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($pdo, $user_id);

// AÑADIDO: Consultamos únicamente las palabras seleccionadas de las lecciones completadas
$stmt = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND is_completed = 1 AND selected_words IS NOT NULL");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll();

$learned_items = [];

foreach ($completed_lessons as $row) {
    $words = json_decode($row['selected_words'], true) ?: [];
    foreach ($words as $w) {
        // Guardamos la palabra en inglés y su traducción
        $learned_items[] = ['en' => $w['en'], 'es' => $w['es']];
    }
}

// Filtra duplicados para que no salgan palabras repetidas si las escogió en diferentes días
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
                <p>¡Completa tu primer día para desbloquear palabras aquí!</p>
            </div>
        <?php else: ?>
            <div class="trophy-grid">
                <?php foreach ($learned_items as $item): ?>
                    <div class="trophy-card" onclick="playTTS('<?php echo addslashes($item['en']); ?>', false)" title="Toca para escuchar">
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