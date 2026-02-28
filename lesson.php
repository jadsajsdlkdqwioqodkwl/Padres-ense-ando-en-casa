<?php
require_once 'includes/config.php';

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT l.*, m.title as module_title FROM lessons l JOIN modules m ON l.module_id = m.id WHERE l.id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) { header("Location: course.php?module=1"); exit; }

$lesson_data = json_decode($lesson['content_data'], true) ?: [];
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

        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #666; font-size: 18px; margin-bottom: 5px;"><?php echo htmlspecialchars($module_title); ?></h2>
            
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                <h1 style="margin: 0;">Lección <?php echo $lesson['order_num'] . ': ' . htmlspecialchars($lesson['title']); ?></h1>
                
                <button id="music-toggle" onclick="toggleMusic()" style="font-size: 26px; background: none; border: none; cursor: pointer; padding: 0; transition: 0.2s;" title="Música de fondo">🔇</button>
                
                <button onclick="document.getElementById('parent-modal').style.display='flex'" style="font-size: 20px; background: var(--primary); color: white; border: none; cursor: pointer; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" title="Guía para Papá/Mamá">i</button>
            </div>
        </div>

        <?php include 'includes/teaching_guide.php'; ?> <div class="game-wrapper">
            <?php 
            $template_type = $lesson['template_type'] ?? 'desconocido';
            $template_file = 'templates/type_' . $template_type . '.php';
            if (file_exists($template_file)) { include $template_file; } 
            else { echo "<div style='color:red; text-align:center;'>Error: Falta archivo {$template_file}</div>"; }
            ?>
        </div>
    </div>
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
    <a href="course.php?module=<?php echo $lesson['module_id']; ?>" style="background: var(--light); color: var(--dark); padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; border: 1px solid #ddd; transition: 0.2s;">⬅️ Volver</a>
    
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 style="margin: 0; font-size: 24px;">Lección <?php echo $lesson['order_num']; ?></h1>
        <button onclick="document.getElementById('parent-modal').style.display='flex'" style="font-size: 18px; background: var(--primary); color: white; border: none; cursor: pointer; border-radius: 50%; width: 35px; height: 35px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">i</button>
    </div>
</div>
    <?php include 'includes/controls.php'; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html>