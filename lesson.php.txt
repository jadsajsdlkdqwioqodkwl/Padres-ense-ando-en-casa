<?php
// 1. Conexión a la base de datos (Asumimos que crearás includes/config.php)
require_once 'includes/config.php';

// 2. Obtener la lección desde la URL (ej: tusitio.com/lesson.php?id=1)
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Por defecto carga la 1 para pruebas

// 3. Consultar la base de datos
$stmt = $pdo->prepare("
    SELECT l.*, m.title as module_title 
    FROM lessons l 
    JOIN modules m ON l.module_id = m.id 
    WHERE l.id = ?
");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

// Si la lección no existe, lo mandamos al index (mapa)
if (!$lesson) {
    header("Location: index.php");
    exit;
}

// 4. Decodificar la magia (Convierte el JSON de la BD en un Array de PHP)
$lesson_data = json_decode($lesson['content_data'], true);

// 5. Variables para la vista
$page_title = $lesson['title'];
$module_title = $lesson['module_title'];

// ==========================================
// 🎨 INICIO DEL RENDERIZADO VISUAL
// ==========================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; // Aquí van tus variables CSS y <style> globales ?>
</head>
<body>

    <?php include 'includes/audio_engine.php'; ?>

    <div class="container">
        
        <?php include 'includes/navbar.php'; ?>

        <h2><?php echo htmlspecialchars($module_title); ?></h2>
        <h1>Lesson <?php echo $lesson['order_num'] . ': ' . htmlspecialchars($lesson['title']); ?></h1>

        <?php include 'includes/teaching_guide.php'; ?>

        <div class="game-wrapper">
            <?php 
            // Esto busca en la carpeta /templates/ el archivo correspondiente
            // Ej: /templates/type_drag_drop.php
            $template_file = 'templates/type_' . $lesson['template_type'] . '.php';
            
            if (file_exists($template_file)) {
                include $template_file;
            } else {
                echo "<div style='color:red; text-align:center;'>Error: Plantilla no encontrada ({$template_file}).</div>";
            }
            ?>
        </div>

    </div>

    <?php include 'includes/controls.php'; ?>

</body>
</html>