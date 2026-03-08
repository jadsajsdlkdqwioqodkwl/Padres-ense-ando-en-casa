<?php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($pdo, $user_id);

$stmt = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND is_completed = 1 AND selected_words IS NOT NULL");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll();

$learned_items = [];

foreach ($completed_lessons as $row) {
    $words = json_decode($row['selected_words'], true) ?: [];
    foreach ($words as $w) {
        $learned_items[] = ['en' => $w['en'], 'es' => $w['es']];
    }
}

$learned_items = array_map("unserialize", array_unique(array_map("serialize", $learned_items)));
$page_title = "Mis Trofeos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .trophy-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px; margin-top: 40px; }
        .trophy-card { background: var(--white); border-radius: 16px; padding: 25px 20px; text-align: center; border: 2px solid #E2E8F0; transition: 0.3s; position: relative; overflow: hidden; cursor: pointer; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05); }
        .trophy-card:hover { transform: translateY(-5px); border-color: var(--brand-orange); box-shadow: 0 15px 35px rgba(242, 156, 56, 0.15); }
        .trophy-en { font-size: 24px; font-weight: 800; color: var(--brand-blue); margin-bottom: 5px; }
        .trophy-es { color: #64748B; font-size: 15px; font-weight: 600; }
        .trophy-icon { font-size: 35px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">🏆 Salón de la Fama</h1>
        <p style="font-size: 1.1rem; color: #64748B;">Aquí están las palabras que <strong style="color: var(--brand-blue);"><?php echo htmlspecialchars($user_info['child_name']); ?></strong> ha conquistado.</p>

        <?php if (empty($learned_items)): ?>
            <div style="padding: 40px; background: #FFFBEB; border-radius: 16px; margin-top: 30px; border: 1px solid #FDE68A;">
                <h2 style="color: var(--brand-orange); margin-bottom: 10px;">Aún no hay trofeos</h2>
                <p style="color: #64748B; font-size: 16px;">¡Completa tu primer día para desbloquear palabras aquí!</p>
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