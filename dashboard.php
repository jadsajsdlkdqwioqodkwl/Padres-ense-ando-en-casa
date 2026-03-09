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
} catch (PDOException $e) {
    // Registro interno del error (evita exponer vulnerabilidades al front-end)
    error_log("Error al cargar los módulos en el dashboard: " . $e->getMessage());
    $modules = [];
}

$page_title = "Mis Módulos";
?>
<!DOCTYPE html>
<html lang="es">

<head>

<?php include 'includes/head.php'; ?>

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

/* Optimización Responsiva Extrema */
.module-grid {
    display: grid;
    /* Baja el minmax a 240px para soportar pantallas ultradelgadas (ej. iPhone SE) */
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 30px;
    margin-top: 40px;
    padding-bottom: 40px;
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
}

.module-card:hover {
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

.module-card:hover .module-icon {
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
}

.module-card:hover .btn-enter {
    background: #E2E8F0;
    transform: scale(1.05);
}

/* Modal padre: Escalable, Seguro y Moderno */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(8px); /* Efecto vidrio (Glassmorphism) */
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    z-index: 9999; /* Garantiza sobreponerse al Navbar */
    padding: 20px; /* Para que no choque con los bordes en celulares */
}

.modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
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
}

.modal-overlay.active .modal-content {
    transform: scale(1);
}

.modal-title {
    font-size: clamp(1.8rem, 5vw, 2.2rem); /* Tipografía fluida */
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

</style>

</head>

<body>

<div class="container text-center px-4">

    <?php include 'includes/navbar.php'; ?>

    <div style="margin-top: 2rem; padding: 0 1rem;">
        <h1 style="color:var(--brand-blue, #1E3A8A); font-size: clamp(2rem, 6vw, 3rem); font-weight: 900; margin-bottom: 10px; letter-spacing: -1px;">
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
            ?>

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
            setTimeout(() => {
                modal.classList.add('active');
            }, 600); // Pequeño delay para que la transición sea visible
        }

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            localStorage.setItem('parentWelcomeShown', 'true');
        });
    }

});

</script>

<script src="assets/js/engine.js"></script>

</body>
</html>