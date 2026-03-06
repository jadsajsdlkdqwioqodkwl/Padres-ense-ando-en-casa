<?php
require_once 'includes/config.php';

$module_id = isset($_GET['module']) ? (int)$_GET['module'] : 1;
$user_id = $_SESSION['user_id'];

// Obtener título del módulo (Ahora tratado como Semana/Pool temático)
$stmtMod = $pdo->prepare("SELECT title FROM modules WHERE id = ?");
$stmtMod->execute([$module_id]);
// EDICIÓN: Cambiamos el fallback para que refleje la nueva estructura de semanas
$module_title = $stmtMod->fetchColumn() ?: "Semana de Vocabulario";

// Obtener lecciones y progreso
$stmtLessons = $pdo->prepare("
    SELECT l.id, l.title, l.reward_stars, l.order_num, p.is_completed, p.stars_earned
    FROM lessons l
    LEFT JOIN progress p ON l.id = p.lesson_id AND p.user_id = ?
    WHERE l.module_id = ?
    ORDER BY l.order_num ASC
");
$stmtLessons->execute([$user_id, $module_id]);
$lessons = $stmtLessons->fetchAll();

// --- LÓGICA DE LA BARRA DE PROGRESO ---
$total_lessons = count($lessons);
$completed_lessons = 0;
foreach ($lessons as $l) {
    if ($l['is_completed']) $completed_lessons++;
}
$progress_percent = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;
// --------------------------------------

$page_title = $module_title;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .level-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px; margin-top: 30px; }
        .level-card { 
            background: white; border-radius: 15px; padding: 20px; text-align: center; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-decoration: none; color: inherit;
            border: 3px solid transparent; transition: 0.3s; display: block; position: relative;
        }
        .level-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .level-card.completed { border-color: var(--success); background: #f0fdf4; }
        .level-icon { font-size: 40px; margin-bottom: 10px; }
        
        /* Estilos de la Barra de Progreso */
        .progress-container {
            background: #e0e0e0; border-radius: 20px; height: 28px; width: 100%; 
            max-width: 500px; margin: 20px auto; overflow: hidden; position: relative;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress-fill {
            background: linear-gradient(90deg, #4CAF50, #8BC34A); height: 100%; 
            transition: width 0.8s ease-in-out; border-radius: 20px;
        }
        .progress-text {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: bold; color: #333; font-size: 13px; text-shadow: 1px 1px 2px white;
        }

        /* NUEVAS CLASES AÑADIDAS PARA LA RENOVACIÓN DE VOCABULARIO */
        .pool-header {
            background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px;
            margin-bottom: 20px; border-radius: 5px; font-size: 14px; text-align: left;
        }
        .day-badge {
            background: #007bff; color: white; padding: 5px 10px; border-radius: 10px;
            font-size: 12px; position: absolute; top: -10px; right: -10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        
        <div class="text-center">
            <h1>Semana: <?php echo htmlspecialchars($module_title); ?></h1>
            
            <div class="pool-header">
                <strong>🌟 Tu Pool de Vocabulario:</strong> Cada día te ofreceremos 10 palabras nuevas. 
                Tú eliges las 5 que más te gusten para aprender con mnemotecnias. 
                ¡Al día siguiente, demostrarás lo aprendido para darle una foto de regalo a papá!
            </div>
            
            <p>¡Selecciona tu día de entrenamiento!</p>
            
            <div class="progress-container">
                <div class="progress-fill" style="width: <?php echo $progress_percent; ?>%;"></div>
                <div class="progress-text">
                    Progreso: <?php echo $progress_percent; ?>% (<?php echo $completed_lessons; ?> de <?php echo $total_lessons; ?> días completados)
                </div>
            </div>
        </div>

        <div class="level-grid">
            <?php 
            foreach ($lessons as $lesson): 
                $is_completed = $lesson['is_completed'] ? true : false;
                $stars_display = $is_completed ? "⭐ " . $lesson['stars_earned'] : "🎁 " . $lesson['reward_stars'] . " Estrellas";
                
                $completed_class = $is_completed ? 'completed' : '';
                $icon = $is_completed ? '✅' : '📅'; // AÑADIDO: Cambio de icono a calendario para representar días
            ?>
                <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="level-card <?php echo $completed_class; ?>">
                    <div class="day-badge">Día <?php echo $lesson['order_num']; ?></div>
                    
                    <div class="level-icon"><?php echo $icon; ?></div>
                    <h3>Día <?php echo $lesson['order_num']; ?></h3>
                    <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
                    <p style="color: #666; font-weight: bold;"><?php echo $stars_display; ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>