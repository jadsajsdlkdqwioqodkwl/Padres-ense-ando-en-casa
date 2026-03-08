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