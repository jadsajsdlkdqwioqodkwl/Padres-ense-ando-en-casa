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
        .module-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 30px; }
        .module-card { 
            background: white; border-radius: 20px; padding: 30px; text-align: center; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); text-decoration: none; color: inherit;
            border: 3px solid transparent; transition: 0.3s; display: block;
        }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); border-color: var(--primary); }
        .module-icon { font-size: 60px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        
        <h1>Explora tu Mundo</h1>
        <p style="color: var(--text-muted); font-size: 18px;">Selecciona una semana para empezar a jugar.</p>

        <div class="module-grid">
            <?php foreach ($modules as $mod): 
                $icon = ($mod['order_num'] == 1) ? '🏡' : '🌳'; 
            ?>
                <a href="course.php?module=<?php echo $mod['id']; ?>" class="module-card" style="border-bottom: 5px solid <?php echo $mod['color_theme']; ?>;">
                    <div class="module-icon"><?php echo $icon; ?></div>
                    <h2 style="color: <?php echo $mod['color_theme']; ?>;"><?php echo htmlspecialchars($mod['title']); ?></h2>
                    <div style="margin-top: 15px; padding: 10px; background: var(--bg); border-radius: 10px; font-weight: bold; color: var(--primary);">
                        Entrar a la Semana ➡️
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="assets/js/engine.js"></script>
</body>
</html>