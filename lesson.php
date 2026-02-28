<?php
require_once 'includes/config.php';

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("
    SELECT l.*, m.title as module_title 
    FROM lessons l 
    JOIN modules m ON l.module_id = m.id 
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header("Location: course.php?module=1");
    exit;
}

// CORRECCIÓN DE BUG: Leemos "content_data" como está en la base de datos
$lesson_data = json_decode($lesson['content_data'], true);
if ($lesson_data === null) $lesson_data = []; // Evita el error de Null

$page_title = $lesson['title'];
$module_title = $lesson['module_title'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>

    <?php include 'includes/audio_engine.php'; ?>

    <div class="container">
        
        <?php include 'includes/navbar.php'; ?>

        <h2 style="color: #666; font-size: 18px;"><?php echo htmlspecialchars($module_title); ?></h2>
        <h1>Lección <?php echo $lesson['order_num'] . ': ' . htmlspecialchars($lesson['title']); ?></h1>

        <?php include 'includes/teaching_guide.php'; ?>

        <div class="game-wrapper">
            <?php 
            // CORRECCIÓN DE BUG: Leemos "template_type" de la BD
            $template_type = $lesson['template_type'] ?? 'desconocido';
            $template_file = 'templates/type_' . $template_type . '.php';
            
            if (file_exists($template_file)) {
                include $template_file;
            } else {
                echo "<div style='color:red; text-align:center; padding: 20px; border: 2px dashed red;'>Error: Juego en construcción. (Falta archivo: {$template_file})</div>";
            }
            ?>
        </div>

    </div>

    <?php include 'includes/controls.php'; ?>
    <?php include 'includes/footer.php'; ?>

</body>
</html>