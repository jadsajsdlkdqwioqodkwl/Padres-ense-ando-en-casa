<?php
// dashboard.php
require_once 'includes/config.php';

// Si no está logueado, lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmtModules = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
$modules = $stmtModules->fetchAll();

$page_title = "Mis Módulos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .module-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 40px; }
        .module-card { 
            background: var(--white); border-radius: 16px; padding: 40px 30px; text-align: center; 
            box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-decoration: none; color: inherit;
            border: 1px solid #E2E8F0; transition: transform 0.3s, box-shadow 0.3s; display: block;
            position: relative; overflow: hidden;
        }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(28, 61, 106, 0.1); }
        .module-icon { font-size: 70px; margin-bottom: 20px; transition: transform 0.3s; }
        .module-card:hover .module-icon { transform: scale(1.1); }
        .btn-enter {
            margin-top: 25px; padding: 12px 20px; background: var(--bg-light); 
            border-radius: 50px; font-weight: 700; color: var(--brand-blue);
            border: 2px solid #E2E8F0; transition: 0.3s; display: inline-block;
        }
        .module-card:hover .btn-enter { background: #E2E8F0; }
    </style>
</head>
<div id="onboarding-modal" class="mission-modal" style="display: none; background: rgba(255,255,255,0.98) !important;">
    <h2 style="color: var(--brand-blue); font-size: 2.2rem; margin-top: 0; text-align: center;">¡Bienvenidos a My World! 🌍</h2>
    
    <div style="background: #F0F9FF; border-left: 6px solid var(--brand-lblue); padding: 20px; border-radius: 12px; max-width: 500px; text-align: left; margin-bottom: 25px;">
        <p style="color: #334155; font-size: 1.1rem; margin-top: 0;">
            Esta plataforma está diseñada para que <strong>tú</strong> seas el mejor maestro de inglés de tu hijo, aunque no sepas el idioma.
        </p>
        <ul style="color: #475569; font-size: 1.05rem; padding-left: 20px;">
            <li style="margin-bottom: 10px;"><strong>Lee la pronunciación:</strong> Todo el inglés tiene debajo cómo se lee en español (ej. Apple = "épol"). ¡Léelo en voz alta!</li>
            <li style="margin-bottom: 10px;"><strong>Jueguen juntos:</strong> Ayuda a tu hijo a relacionar lo que pronuncias con los elementos en pantalla.</li>
            <li><strong>¡Cero audios automáticos!</strong> Queremos que tu voz sea la guía de esta aventura.</li>
        </ul>
    </div>

    <div class="modal-actions">
        <button class="btn btn-action" onclick="closeOnboarding()" style="background: var(--brand-green);">¡Entendido, a jugar! ▶️</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Muestra el modal si nunca lo ha visto
        if(!localStorage.getItem('onboarding_seen')) {
            document.getElementById('onboarding-modal').style.display = 'flex';
        }
    });

    function closeOnboarding() {
        document.getElementById('onboarding-modal').style.display = 'none';
        localStorage.setItem('onboarding_seen', 'true');
        // Activa la música global al iniciar la primera vez
        if(typeof toggleMusic === 'function') toggleMusic(); 
    }
</script>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        
        <h1 style="color: var(--brand-blue); font-size: 2.5rem; margin-bottom: 10px;">Explora tu Mundo</h1>
        <p style="color: #64748B; font-size: 1.1rem;">Selecciona una semana para empezar a jugar.</p>

        <div class="module-grid">
            <?php foreach ($modules as $mod): 
                $icon = ($mod['order_num'] == 1) ? '🏡' : '🌳'; 
            ?>
                <a href="course.php?module=<?php echo $mod['id']; ?>" class="module-card" style="border-bottom: 6px solid <?php echo $mod['color_theme']; ?>;">
                    <div class="module-icon"><?php echo $icon; ?></div>
                    <h2 style="color: <?php echo $mod['color_theme']; ?>; margin-bottom: 5px;"><?php echo htmlspecialchars($mod['title']); ?></h2>
                    <div class="btn-enter">
                        Entrar a la Semana ➡️
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="assets/js/engine.js"></script>
</body>
</html>