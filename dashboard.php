<?php
// dashboard.php
session_start();
require_once 'includes/config.php';

// Seguridad y Ciberseguridad: prevenir acceso sin login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Seguridad y Escalabilidad: Manejo de errores con PDO con try-catch
try {
    // Obtener módulos
    $stmtModules = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
    $modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el total de estrellas del usuario
    $stmtStars = $pdo->prepare("SELECT SUM(stars_earned) as total_stars FROM progress WHERE user_id = ? AND is_completed = 1");
    $stmtStars->execute([$_SESSION['user_id']]);
    $user_total_stars = (int)$stmtStars->fetchColumn();

} catch (PDOException $e) {
    // Registro interno del error (evita exponer vulnerabilidades al front-end)
    error_log("Error al cargar los módulos en el dashboard: " . $e->getMessage());
    $modules = [];
    $user_total_stars = 0;
}

$page_title = "Mis Módulos";
?>
<!DOCTYPE html>
<html lang="es">

<head>

<?php include 'includes/head.php'; ?>
<?php
// En dashboard.php (Asegúrate de tener session_start() al inicio)
$fire_purchase = isset($_SESSION['fire_purchase_pixel']) && $_SESSION['fire_purchase_pixel'] === true;
$event_id = isset($_SESSION['purchase_event_id']) ? $_SESSION['purchase_event_id'] : '';
$purchase_value = isset($_SESSION['purchase_value']) ? $_SESSION['purchase_value'] : 14.99;

// También disparamos el evento CompleteRegistration cada vez que llega aquí nuevo
$is_new_registration = $fire_purchase; // Si acaba de comprar, es un registro nuevo
?>

<script>
// Tu código base del Píxel (el init normal)
!function(f,b,e,v,n,t,s){if(f.fbq)return;/* ... */} // (Asegúrate de tener el código base del píxel aquí)
fbq('init', 'TU_PIXEL_ID');
fbq('track', 'PageView');

<?php if ($fire_purchase): ?>
    // Disparamos Purchase Client-Side con el MISMO event_id para deduplicar
    fbq('track', 'Purchase', {
        value: <?php echo $purchase_value; ?>,
        currency: 'PEN',
        content_name: 'My World - Acceso Vitalicio'
    }, {
        eventID: '<?php echo $event_id; ?>' // ¡CRÍTICO PARA LA DEDUPLICACIÓN!
    });

    // Disparamos CompleteRegistration
    fbq('track', 'CompleteRegistration', {
        value: <?php echo $purchase_value; ?>,
        currency: 'PEN'
    });
    
    <?php 
    // Limpiamos la sesión para que no se dispare de nuevo si el usuario recarga la página
    unset($_SESSION['fire_purchase_pixel']);
    unset($_SESSION['purchase_event_id']);
    unset($_SESSION['purchase_value']);
    ?>
<?php endif; ?>
</script>

<script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>
/* REGLA DE ORO: Estilos Twemoji Responsivos y Seguros */
img.emoji {
    height: 1.2em;
    width: 1.2em;
    margin: 0 .05em 0 .1em;
    vertical-align: -0.1em;
    display: inline-block;
    pointer-events: none; /* Evita bugs donde el click cae en el SVG y no en el botón */
}

/* Optimización Responsiva Extrema: Layout Maestro Centrado */
.module-grid {
    display: grid;
    /* Ajuste para que las tarjetas mantengan una proporción cuadrada y no se estiren al 100% de la pantalla */
    grid-template-columns: repeat(auto-fit, minmax(280px, 320px));
    gap: 30px;
    margin: 40px auto;
    padding-bottom: 40px;
    justify-content: center; /* Garantiza que los elementos se centren al hacer zoom out */
    width: 100%;
    box-sizing: border-box;
}

.module-card {
    background: white;
    border-radius: 24px; /* Suavizado más orgánico/infantil */
    padding: 40px 20px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(28,61,106,0.05);
    text-decoration: none;
    color: inherit;
    border: 2px solid #E2E8F0;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Animación más natural */
    display: block;
    position: relative;
    overflow: hidden;
    width: 100%; /* Asegura consistencia dimensional dentro del grid */
    box-sizing: border-box;
    margin: 0 auto;
}

.module-card:hover:not(.locked-card) {
    transform: translateY(-8px);
    box-shadow: 0 20px 45px rgba(28,61,106,0.12);
    border-color: var(--brand-lblue, #bae6fd);
}

.module-icon {
    font-size: 75px;
    margin-bottom: 20px;
    transition: transform 0.3s ease;
    filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));
}

.module-card:hover:not(.locked-card) .module-icon {
    transform: scale(1.15) rotate(5deg);
}

.btn-enter {
    margin-top: 25px;
    padding: 14px 24px;
    background: #F8FAFC;
    border-radius: 50px;
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--brand-blue, #1E3A8A);
    border: 2px solid #E2E8F0;
    transition: 0.3s ease;
    display: inline-block;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    width: 80%;
}

.module-card:hover:not(.locked-card) .btn-enter {
    background: #E2E8F0;
    transform: scale(1.05);
}

/* Modal padre: Escalable, Seguro y Moderno */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px); /* Efecto vidrio */
    display: none; /* FIX iOS: cambiado de flex a none inicial */
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 9999;
    padding: 20px;
    box-sizing: border-box;
}

.modal-overlay.active {
    display: flex; /* FIX iOS: activado al mostrar */
    opacity: 1;
}

.modal-content {
    background: white;
    padding: 40px;
    border-radius: 24px;
    max-width: 520px;
    width: 100%;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.4);
    transform: scale(0.9);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 4px solid var(--brand-blue, #1E3A8A);
    box-sizing: border-box;
    margin: 0 auto;
}

.modal-overlay.active .modal-content {
    transform: scale(1);
}

.modal-title {
    font-size: clamp(1.8rem, 5vw, 2.2rem);
    color: var(--brand-blue, #1E3A8A);
    margin-bottom: 20px;
    font-weight: 900;
}

.modal-text {
    color: #475569;
    font-size: clamp(1rem, 3vw, 1.15rem);
    line-height: 1.7;
    margin-bottom: 10px;
}

.modal-text strong {
    color: var(--brand-orange, #F59E0B);
}

.btn-understood {
    margin-top: 30px;
    padding: 16px 30px;
    background: var(--brand-green, #10B981);
    color: white;
    border-radius: 50px;
    font-weight: 800;
    font-size: 1.2rem;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    width: 100%;
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

.btn-understood:hover {
    background: #059669;
    transform: translateY(-3px);
    box-shadow: 0 15px 25px rgba(16, 185, 129, 0.4);
}

/* Indicador de estrellas superior */
.stars-hud {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    background: #FFFBEB;
    padding: 10px 25px;
    border-radius: 50px;
    border: 2px solid #FDE68A;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.1);
}

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
                
                // Sistema escalable de íconos por si añades más semanas a futuro
                $icon = '📦'; 
                if ($mod['order_num'] == 1) $icon = '🏡';
                elseif ($mod['order_num'] == 2) $icon = '🌳';
                elseif ($mod['order_num'] == 3) $icon = '🚀';
                elseif ($mod['order_num'] == 4) $icon = '🏰';

                // Ciberseguridad: Escapar salidas para prevenir XSS
                $safe_id = (int)($mod['id'] ?? 0);
                $safe_title = htmlspecialchars($mod['title'] ?? 'Módulo');
                $safe_color = htmlspecialchars($mod['color_theme'] ?? '#38BDF8');

                // Lógica Progresiva de Bloqueo
                $required_stars = ($mod['order_num'] - 1) * 15; // Requiere 15 estrellas de la semana anterior
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
                <a href="course.php?module=<?php echo $safe_id; ?>" 
                   class="module-card"
                   style="border-bottom:8px solid <?php echo $safe_color; ?>;">

                    <div class="module-icon">
                        <?php echo $icon; ?>
                    </div>

                    <h2 style="color:<?php echo $safe_color; ?>; margin-bottom:10px; font-weight: 800; font-size: 1.8rem;">
                        <?php echo $safe_title; ?>
                    </h2>

                    <div class="btn-enter">
                        Entrar a la Semana ➡️
                    </div>

                </a>
            <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</div>

<div id="welcomeParentModal" class="modal-overlay">
    <div class="modal-content">

        <h2 class="modal-title">
            ¡Bienvenido, Papá / Mamá! 👨‍👩‍👧‍👦
        </h2>

        <p class="modal-text" style="margin-bottom: 20px;">
            Esta plataforma está diseñada para que <strong>tú</strong> enseñes inglés a tu hijo, aunque no hables el idioma.
        </p>

        <div style="background: #F8FAFC; padding: 20px; border-radius: 16px; text-align: left; border: 2px dashed #CBD5E1; margin-bottom: 20px;">
            <p class="modal-text" style="margin: 5px 0;">✔ Lee la pronunciación en español 🗣️</p>
            <p class="modal-text" style="margin: 5px 0;">✔ Jueguen juntos 🎮</p>
            <p class="modal-text" style="margin: 5px 0;">✔ Tu voz es la guía del aprendizaje 🌟</p>
        </div>

        <p class="modal-text" style="font-weight: 800; color: var(--brand-blue, #1E3A8A);">
            ¡Disfruten esta aventura! 🚀✨
        </p>

        <button id="closeParentModalBtn" class="btn-understood">
            ¡Entendido! 👍
        </button>

    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    /* Ciberseguridad/Robustez: Comprobar que Twemoji haya cargado de la CDN */
    if (typeof twemoji !== 'undefined') {
        twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
    } else {
        console.warn('Twemoji no cargó a tiempo. Reintentando...');
        setTimeout(() => {
            if (typeof twemoji !== 'undefined') twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
        }, 1000);
    }

    /* Lógica del Modal de bienvenida */
    const modal = document.getElementById('welcomeParentModal');
    const closeBtn = document.getElementById('closeParentModalBtn');

    if (modal && closeBtn) {
        if (!localStorage.getItem('parentWelcomeShown')) {
            modal.style.display = 'flex'; // FIX iOS: Primero mostramos
            setTimeout(() => {
                modal.classList.add('active'); // Luego animamos
            }, 50); // Delay mínimo para que el navegador procese el display flex
        }

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none'; // FIX iOS: Ocultamos tras la animación
            }, 300);
            localStorage.setItem('parentWelcomeShown', 'true');
        });
    }

});

</script>

<script src="assets/js/engine.js"></script>

</body>
</html>