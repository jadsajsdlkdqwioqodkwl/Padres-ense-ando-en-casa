<?php
// dashboard.php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $stmtModules = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
    $modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);

    $stmtStars = $pdo->prepare("SELECT SUM(stars_earned) as total_stars FROM progress WHERE user_id = ? AND is_completed = 1");
    $stmtStars->execute([$_SESSION['user_id']]);
    $user_total_stars = (int)$stmtStars->fetchColumn();

    // Obtener lecciones completadas para la zona de padres (CSV/Diplomas)
    $stmtCompleted = $pdo->prepare("SELECT p.lesson_id, l.title, m.title as module_title 
                                    FROM progress p 
                                    JOIN lessons l ON p.lesson_id = l.id 
                                    JOIN modules m ON l.module_id = m.id 
                                    WHERE p.user_id = ? AND p.is_completed = 1 
                                    ORDER BY m.order_num ASC, l.order_num ASC");
    $stmtCompleted->execute([$_SESSION['user_id']]);
    $completed_lessons = $stmtCompleted->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error al cargar los módulos en el dashboard: " . $e->getMessage());
    $modules = [];
    $completed_lessons = [];
    $user_total_stars = 0;
}

$page_title = "Mis Módulos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php include 'includes/head.php'; ?>
<script src="https://unpkg.com/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>
img.emoji {
    height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em;
    vertical-align: -0.1em; display: inline-block; pointer-events: none; 
}

.module-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 320px));
    gap: 30px; margin: 40px auto; padding-bottom: 40px;
    justify-content: center; width: 100%; box-sizing: border-box;
}

.module-card {
    background: white; border-radius: 24px; padding: 40px 20px;
    text-align: center; box-shadow: 0 15px 35px rgba(28,61,106,0.05);
    text-decoration: none; color: inherit; border: 2px solid #E2E8F0;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    display: block; position: relative; overflow: hidden;
    width: 100%; box-sizing: border-box; margin: 0 auto;
}

.module-card:hover:not(.locked-card) {
    transform: translateY(-8px); box-shadow: 0 20px 45px rgba(28,61,106,0.12);
    border-color: var(--brand-lblue, #bae6fd);
}

.module-icon {
    font-size: 75px; margin-bottom: 20px; transition: transform 0.3s ease;
    filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));
}

.module-card:hover:not(.locked-card) .module-icon { transform: scale(1.15) rotate(5deg); }

.btn-enter {
    margin-top: 25px; padding: 14px 24px; background: #F8FAFC;
    border-radius: 50px; font-weight: 800; font-size: 1.1rem;
    color: var(--brand-blue, #1E3A8A); border: 2px solid #E2E8F0;
    transition: 0.3s ease; display: inline-block;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 80%;
}

.module-card:hover:not(.locked-card) .btn-enter { background: #E2E8F0; transform: scale(1.05); }

.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); 
    display: none; justify-content: center; align-items: center;
    opacity: 0; transition: opacity 0.3s ease; z-index: 9999;
    padding: 20px; box-sizing: border-box;
}

.modal-overlay.active { display: flex; opacity: 1; }

.modal-content {
    background: white; padding: 40px; border-radius: 24px;
    max-width: 520px; width: 100%; text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.4); transform: scale(0.9);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 4px solid var(--brand-blue, #1E3A8A); box-sizing: border-box;
    margin: 0 auto; max-height: 90vh; overflow-y: auto;
}

.modal-overlay.active .modal-content { transform: scale(1); }

.modal-title { font-size: clamp(1.8rem, 5vw, 2.2rem); color: var(--brand-blue, #1E3A8A); margin-bottom: 20px; font-weight: 900; }
.modal-text { color: #475569; font-size: clamp(1rem, 3vw, 1.15rem); line-height: 1.7; margin-bottom: 10px; }
.modal-text strong { color: var(--brand-orange, #F59E0B); }

.btn-understood, .btn-parents {
    padding: 16px 30px; background: var(--brand-green, #10B981);
    color: white; border-radius: 50px; font-weight: 800;
    font-size: 1.2rem; border: none; cursor: pointer; transition: 0.3s;
    width: 100%; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); display: block; text-decoration: none; margin-top: 15px;
}

.btn-understood:hover, .btn-parents:hover { background: #059669; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(16, 185, 129, 0.4); }

.btn-parents {
    background: var(--brand-blue); box-shadow: 0 10px 20px rgba(28, 61, 106, 0.3); margin: 20px auto 0; max-width: 300px;
}
.btn-parents:hover { background: #152d66; box-shadow: 0 15px 25px rgba(28, 61, 106, 0.4); }

.stars-hud {
    display: inline-flex; justify-content: center; align-items: center;
    gap: 10px; margin-top: 20px; background: #FFFBEB; padding: 10px 25px;
    border-radius: 50px; border: 2px solid #FDE68A; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.1);
}

.printables-list { text-align: left; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 15px; max-height: 250px; overflow-y: auto; margin-bottom: 20px; }
.printable-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #CBD5E1; }
.printable-item:last-child { border-bottom: none; }
.btn-sm-download { background: var(--brand-orange); color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.2s; }
.btn-sm-download:hover { background: #D97706; transform: scale(1.05); }
</style>

</head>
<body>

<div class="container text-center px-4" style="max-width: 1000px; margin: 0 auto;">

    <?php include 'includes/navbar.php'; ?>

    <div style="margin-top: 1rem; padding: 0 1rem; box-sizing: border-box;">
        <div class="stars-hud">
            <span style="font-size: 24px;">⭐</span>
            <span style="font-size: 20px; font-weight: 900; color: #D97706;"><?php echo $user_total_stars; ?> Estrellas Totales</span>
        </div>

        <button class="btn-parents" id="openParentsZoneBtn">👨‍👩‍👧‍👦 Zona para Padres</button>

        <h1 style="color:var(--brand-blue, #1E3A8A); font-size: clamp(2rem, 6vw, 3rem); font-weight: 900; margin-bottom: 10px; margin-top: 25px; letter-spacing: -1px;">
            Explora tu Mundo 🌍
        </h1>
        <p style="color:#64748B; font-size: clamp(1rem, 3vw, 1.2rem); font-weight: 500;">
            Selecciona una semana para empezar a jugar.
        </p>
    </div>

    <div class="module-grid">
        <?php if (empty($modules)): ?>
            <div style="grid-column: 1 / -1; padding: 40px; background: #FEE2E2; border-radius: 18px; color: #991B1B; font-weight: bold; border: 2px dashed #F87171;">
                No se encontraron módulos activos. Por favor, revisa la base de datos.
            </div>
        <?php else: ?>
            <?php foreach ($modules as $mod): 
                $icon = '📦'; 
                if ($mod['order_num'] == 1) $icon = '🏡';
                elseif ($mod['order_num'] == 2) $icon = '🌳';
                elseif ($mod['order_num'] == 3) $icon = '🚀';
                elseif ($mod['order_num'] == 4) $icon = '🏰';

                $safe_id = (int)($mod['id'] ?? 0);
                $safe_title = htmlspecialchars($mod['title'] ?? 'Módulo');
                $safe_color = htmlspecialchars($mod['color_theme'] ?? '#38BDF8');

                $required_stars = ($mod['order_num'] - 1) * 15; 
                $is_locked = $user_total_stars < $required_stars;
            ?>

            <?php if ($is_locked): ?>
                <div class="module-card locked-card" style="border-bottom:8px solid #CBD5E1; background: #F8FAFC; cursor: not-allowed; opacity: 0.8;">
                    <div class="module-icon" style="filter: grayscale(100%);">🔒</div>
                    <h2 style="color:#94A3B8; margin-bottom:10px; font-weight: 800; font-size: 1.8rem;">
                        <?php echo $safe_title; ?>
                    </h2>
                    <p style="color: #64748B; font-size: 14px; font-weight: bold; margin-bottom: 15px;">
                        Necesitas <?php echo $required_stars; ?> ⭐ para desbloquear
                    </p>
                    <div class="btn-enter" style="background: #E2E8F0; color: #94A3B8; border-color: #CBD5E1;">
                        Bloqueado
                    </div>
                </div>
            <?php else: ?>
                <a href="course.php?module=<?php echo $safe_id; ?>" class="module-card" style="border-bottom:8px solid <?php echo $safe_color; ?>;">
                    <div class="module-icon"><?php echo $icon; ?></div>
                    <h2 style="color:<?php echo $safe_color; ?>; margin-bottom:10px; font-weight: 800; font-size: 1.8rem;">
                        <?php echo $safe_title; ?>
                    </h2>
                    <div class="btn-enter">Entrar a la Semana ➡️</div>
                </a>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="welcomeParentModal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title">¡Bienvenido, Papá / Mamá! 👨‍👩‍👧‍👦</h2>
        <p class="modal-text" style="margin-bottom: 20px;">Esta plataforma está diseñada para que <strong>tú</strong> enseñes inglés a tu hijo, aunque no hables el idioma.</p>
        <div style="background: #F8FAFC; padding: 20px; border-radius: 16px; text-align: left; border: 2px dashed #CBD5E1; margin-bottom: 20px;">
            <p class="modal-text" style="margin: 5px 0;">✔ Lee la pronunciación en español 🗣️</p>
            <p class="modal-text" style="margin: 5px 0;">✔ Jueguen juntos 🎮</p>
            <p class="modal-text" style="margin: 5px 0;">✔ Tu voz es la guía del aprendizaje 🌟</p>
        </div>
        <p class="modal-text" style="font-weight: 800; color: var(--brand-blue, #1E3A8A);">¡Disfruten esta aventura! 🚀✨</p>
        <button id="closeParentModalBtn" class="btn-understood">¡Entendido! 👍</button>
    </div>
</div>

<div id="parentsZoneModal" class="modal-overlay">
    <div class="modal-content" style="border-color: var(--brand-orange);">
        <h2 class="modal-title" style="color: var(--brand-orange);">📚 Zona para Padres</h2>
        
        <h3 style="color: var(--brand-blue); font-size: 1.2rem; text-align: left; margin-bottom: 10px; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px;">📖 Manual de Uso</h3>
        <p style="text-align: left; color: #475569; font-size: 0.95rem; margin-bottom: 20px;">
            1. <strong>Contraseña de Exámenes:</strong> Al finalizar el día, la plataforma pedirá tu contraseña para autorizar el examen del día siguiente. Esto evita que el niño avance todos los juegos de golpe.<br>
            2. <strong>Descargas Diarias:</strong> Debajo podrás descargar el resumen imprimible (CSV/Diplomas) de las palabras aprendidas cada día para repasar.
        </p>

<h3 style="color: var(--brand-blue); font-size: 1.2rem; text-align: left; margin-bottom: 10px; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px;">🖨️ Archivos Imprimibles</h3>
        <?php if(empty($completed_lessons)): ?>
            <p style="color: #64748B; font-style: italic; text-align: left;">Completa tu primer día para desbloquear archivos descargables aquí.</p>
        <?php else: ?>
            <div class="printables-list">
                <?php foreach($completed_lessons as $cl): ?>
                    <div class="printable-item">
                        <span style="color: var(--brand-blue); font-weight: 600; font-size: 0.95rem;">📄 <?php echo htmlspecialchars($cl['module_title'] . " - " . $cl['title']); ?></span>
                        <a href="printable_material.php?lesson_id=<?php echo $cl['lesson_id']; ?>" target="_blank" class="btn-sm-download" style="background: var(--brand-green); box-shadow: 0 4px 10px rgba(104, 169, 62, 0.3);">🖨️ Imprimir</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <button id="closeParentsZoneBtn" class="btn-understood" style="background: #94A3B8; box-shadow: none;">Cerrar Ventana</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loadEmojis = () => {
        if (typeof twemoji !== 'undefined') {
            twemoji.parse(document.body, { 
                base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
                folder: 'svg', ext: '.svg' 
            });
        } else {
            setTimeout(loadEmojis, 500);
        }
    };
    loadEmojis();

    // Lógica Modal Bienvenida
    const modalWel = document.getElementById('welcomeParentModal');
    const closeBtnWel = document.getElementById('closeParentModalBtn');
    if (modalWel && closeBtnWel) {
        if (!localStorage.getItem('parentWelcomeShown')) {
            modalWel.style.display = 'flex';
            setTimeout(() => modalWel.classList.add('active'), 50);
        }
        closeBtnWel.addEventListener('click', () => {
            modalWel.classList.remove('active');
            setTimeout(() => modalWel.style.display = 'none', 300);
            localStorage.setItem('parentWelcomeShown', 'true');
        });
    }

    // Lógica Modal Zona Padres
    const modalParents = document.getElementById('parentsZoneModal');
    const openBtnParents = document.getElementById('openParentsZoneBtn');
    const closeBtnParents = document.getElementById('closeParentsZoneBtn');

    if(modalParents && openBtnParents && closeBtnParents) {
        openBtnParents.addEventListener('click', () => {
            modalParents.style.display = 'flex';
            setTimeout(() => modalParents.classList.add('active'), 50);
        });
        closeBtnParents.addEventListener('click', () => {
            modalParents.classList.remove('active');
            setTimeout(() => modalParents.style.display = 'none', 300);
        });
    }
});
</script>

<script src="assets/js/engine.js"></script>

</body>
</html>