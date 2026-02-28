<?php
require_once 'includes/config.php';

// 1. Qué lección toca?
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// 2. Buscamos en la BD
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) { die("<h2>Lesson not found. Go back to <a href='course.php'>Course</a></h2>"); }

// 3. Decodificamos la información del juego
$lesson_data = json_decode($lesson['content_json'], true);
$page_title = $lesson['title'];
$current_stars = 0; // Se llenará con lo que pusiste en navbar.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/lessons.css">
</head>
<body>
    <?php include 'includes/audio_engine.php'; ?>
    
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/companion.php'; ?>
        
        <div class="lesson-header text-center" style="margin-bottom: 20px;">
            <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
            <div class="teaching-guide">
                <strong>👨‍🏫 Parents:</strong> Help your child click and interact!
            </div>
        </div>
        
        <div class="lesson-content">
            <?php 
            $template_file = 'templates/type_' . $lesson['type'] . '.php';
            if (file_exists($template_file)) {
                include $template_file;
            } else {
                echo "<p>Juego en construcción (Tipo: " . $lesson['type'] . ")</p>";
            }
            ?>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <?php include 'includes/controls.php'; ?>
    
    <script src="assets/js/engine.js"></script>
    <script src="assets/js/global.js"></script>
</body>
</html>