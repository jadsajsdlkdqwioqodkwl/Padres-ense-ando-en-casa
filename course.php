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
    
    <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

    <style>
        /* Regla global para Twemoji */
        img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

        .level-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 25px; margin-top: 40px; width: 100%; box-sizing: border-box; }
        .level-card { 
            background: var(--white); border-radius: 16px; padding: 30px 20px; text-align: center; 
            box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05); text-decoration: none; color: inherit;
            border: 2px solid #E2E8F0; transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; display: block; position: relative;
            box-sizing: border-box; width: 100%;
        }
        .level-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(28, 61, 106, 0.1); border-color: var(--brand-lblue); }
        .level-card.completed { border-color: var(--brand-green); background: #F0FDF4; }
        .level-icon { font-size: 50px; margin-bottom: 15px; }
        
        /* Estilos de la Barra de Progreso */
        .progress-container {
            background: #E2E8F0; border-radius: 50px; height: 32px; width: 100%; 
            max-width: 600px; margin: 25px auto; overflow: hidden; position: relative;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); box-sizing: border-box;
        }
        .progress-fill {
            background: linear-gradient(90deg, var(--brand-green), #8BC34A); height: 100%; 
            transition: width 0.8s ease-in-out; border-radius: 50px;
        }
        .progress-text {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: 700; color: var(--brand-blue); font-size: clamp(12px, 3vw, 14px); text-shadow: 0 1px 2px rgba(255,255,255,0.8);
            padding: 0 10px; box-sizing: border-box;
        }

        /* CLASES PARA LA RENOVACIÓN DE VOCABULARIO */
        .pool-header {
            background: #FFFBEB; border-left: 6px solid var(--brand-orange); padding: 20px;
            margin-bottom: 30px; border-radius: 12px; font-size: 15px; text-align: left;
            color: #475569; line-height: 1.7; box-shadow: 0 4px 10px rgba(242, 156, 56, 0.05);
            box-sizing: border-box; width: 100%;
        }
        .day-badge {
            background: var(--brand-blue); color: white; padding: 6px 14px; border-radius: 50px;
            font-size: 13px; font-weight: 700; position: absolute; top: -12px; right: -10px; 
            box-shadow: 0 4px 10px rgba(28, 61, 106, 0.2);
        }
        .level-title { font-size: 1.2rem; margin: 10px 0; color: var(--brand-blue); }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        
        <div class="text-center">
            <h1 style="font-size: clamp(1.8rem, 5vw, 2.2rem); margin-bottom: 20px;">Semana: <?php echo htmlspecialchars($module_title); ?></h1>
            
            <div class="pool-header">
                <strong style="color: var(--brand-orange); font-size: 16px;">🌟 Tu Pool de Vocabulario:</strong> Cada día te ofreceremos palabras nuevas. 
                Tú eliges las 5 que más te gusten para aprender con mnemotecnias. 
                ¡Al día siguiente, demostrarás lo aprendido para darle una foto de regalo a papá!
            </div>
            
            <p style="color: #64748B; font-size: clamp(1rem, 3vw, 1.1rem); font-weight: 600;">¡Selecciona tu día de entrenamiento!</p>
            
            <div class="progress-container">
                <div class="progress-fill" style="width: <?php echo $progress_percent; ?>%;"></div>
                <div class="progress-text">
                    Progreso: <?php echo $progress_percent; ?>% (<?php echo $completed_lessons; ?> de <?php echo $total_lessons; ?>)
                </div>
            </div>
        </div>

        <div class="level-grid">
            <?php 
            foreach ($lessons as $lesson): 
                $is_completed = $lesson['is_completed'] ? true : false;
                $stars_display = $is_completed ? "⭐ " . $lesson['stars_earned'] : "🎁 " . $lesson['reward_stars'] . " Estrellas";
                
                $completed_class = $is_completed ? 'completed' : '';
                $icon = $is_completed ? '✅' : '📅'; 
            ?>
                <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="level-card <?php echo $completed_class; ?>">
                    <div class="day-badge">Día <?php echo $lesson['order_num']; ?></div>
                    
                    <div class="level-icon"><?php echo $icon; ?></div>
                    <h3 style="color: #64748B; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Día <?php echo $lesson['order_num']; ?></h3>
                    <h2 class="level-title"><?php echo htmlspecialchars($lesson['title']); ?></h2>
                    <p style="color: <?php echo $is_completed ? 'var(--brand-green)' : 'var(--brand-orange)'; ?>; font-weight: 800; margin-top: 15px;"><?php echo $stars_display; ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div id="introWeekModal" class="modal-overlay">
        <div class="modal-content" style="border-top-color: var(--brand-orange);">
            <h2 class="modal-title">¡Bienvenido a tu Primera Semana! 🌟</h2>
            <p class="modal-text" style="margin-bottom: 20px;">
                Aquí funciona diferente: <strong>Tú y tu hijo tienen el control.</strong><br><br>
                Antes de jugar, se abrirá la <strong>Pool de Vocabulario</strong>. Cada día les daremos opciones y ustedes elegirán las <strong>5 palabras</strong> que quieran aprender hoy.<br><br>
                ¡Así aprenderán lo que realmente les interesa!
            </p>
            <button class="btn btn-large" style="width: 100%; font-size: 1.1rem;" onclick="closeIntroWeekModal()">¡Entendido, vamos! 🚀</button>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof twemoji !== 'undefined') {
                twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
            }

            // Lógica para mostrar el modal solo en la Semana 1 la primera vez
            const isWeek1 = <?php echo $module_id; ?> === 1;
            if (isWeek1 && !localStorage.getItem('week1IntroShown')) {
                // Pequeño retraso para que la animación sea fluida al cargar
                setTimeout(() => {
                    const introModal = document.getElementById('introWeekModal');
                    if(introModal) introModal.classList.add('active');
                }, 500);
            }
        });

        function closeIntroWeekModal() {
            document.getElementById('introWeekModal').classList.remove('active');
            localStorage.setItem('week1IntroShown', 'true');
        }
    </script>
</body>
</html>