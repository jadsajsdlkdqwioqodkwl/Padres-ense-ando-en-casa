<?php
require_once 'includes/config.php';

// Simulamos que estamos en el Módulo 1 por ahora
$module_id = isset($_GET['module']) ? (int)$_GET['module'] : 1;
$user_id = $_SESSION['user_id'] ?? 1;

// Obtener título del módulo
$stmtMod = $pdo->prepare("SELECT title FROM modules WHERE id = ?");
$stmtMod->execute([$module_id]);
$module_title = $stmtMod->fetchColumn() ?: "My Course";

// Obtener lecciones y verificar si el niño ya las completó
$stmtLessons = $pdo->prepare("
    SELECT l.id, l.title, l.reward_stars, l.order_num, p.is_completed, p.stars_earned
    FROM lessons l
    LEFT JOIN progress p ON l.id = p.lesson_id AND p.user_id = ?
    WHERE l.module_id = ?
    ORDER BY l.order_num ASC
");
$stmtLessons->execute([$user_id, $module_id]);
$lessons = $stmtLessons->fetchAll();

$page_title = $module_title;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .level-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .level-card { 
            background: white; border-radius: 15px; padding: 20px; text-align: center; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-decoration: none; color: inherit;
            border: 3px solid transparent; transition: 0.3s; display: block;
        }
        .level-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .level-card.completed { border-color: var(--success); background: #f0fdf4; }
        .level-card.locked { opacity: 0.6; pointer-events: none; filter: grayscale(100%); }
        .level-icon { font-size: 40px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        
        <div class="text-center">
            <h1><?php echo htmlspecialchars($module_title); ?></h1>
            <p>Select a lesson to start playing!</p>
        </div>

        <div class="level-grid">
            <?php 
            $is_previous_completed = true; // La primera lección siempre está desbloqueada
            foreach ($lessons as $index => $lesson): 
                $is_completed = $lesson['is_completed'] ? true : false;
                $stars_display = $is_completed ? "⭐ " . $lesson['stars_earned'] : "🎁 " . $lesson['reward_stars'] . " Stars";
                
                // Lógica para bloquear niveles si el anterior no está completo
                $locked_class = !$is_previous_completed && !$is_completed ? 'locked' : '';
                $completed_class = $is_completed ? 'completed' : '';
                $icon = $is_completed ? '✅' : ($locked_class ? '🔒' : '▶️');
            ?>
                <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="level-card <?php echo $completed_class . ' ' . $locked_class; ?>">
                    <div class="level-icon"><?php echo $icon; ?></div>
                    <h3>Lesson <?php echo $lesson['order_num']; ?></h3>
                    <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
                    <p style="color: #666; font-weight: bold;"><?php echo $stars_display; ?></p>
                </a>
            <?php 
                // Actualizamos el estado para la siguiente iteración
                $is_previous_completed = $is_completed;
            endforeach; 
            ?>
        </div>
    </div>
</body>
</html>