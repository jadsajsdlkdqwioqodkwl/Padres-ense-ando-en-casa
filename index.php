<?php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'] ?? 1;

// Obtener información del usuario (usando la función que creamos en config.php)
$user_info = getUserInfo($pdo, $user_id);
$total_stars = $user_info ? $user_info['total_stars'] : 0;
$child_name = $user_info ? $user_info['child_name'] : 'Explorador';

// Obtener todos los módulos de la base de datos
$stmtMods = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
$modules = $stmtMods->fetchAll();

$page_title = "Módulos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .dashboard { padding: 40px 20px; }
        .welcome-section { text-align: center; margin-bottom: 40px; }
        .big-mascot { font-size: 80px; margin: 10px 0; animation: float 3s ease-in-out infinite; display: inline-block;}
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        
        .modules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .module-card { 
            background: white; border-radius: 20px; padding: 30px 20px; text-align: center; 
            text-decoration: none; color: inherit; display: block;
            border: 4px solid var(--border-color); transition: 0.3s;
            position: relative; overflow: hidden;
        }
        .module-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 20px rgba(43, 58, 103, 0.1); }
        .module-icon { font-size: 50px; margin-bottom: 15px; }
        .module-title { font-size: 24px; color: var(--primary); margin: 0 0 10px 0; }
        .btn-enter { background: var(--primary); color: white; padding: 10px 25px; border-radius: 20px; font-weight: bold; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container dashboard">
        <?php include 'includes/navbar.php'; ?>

        <div class="welcome-section">
            <div class="big-mascot"><style>
    .hero-character {
        width: 80px; height: 100px; background: var(--success);
        margin: 0 auto 20px; border-radius: 50% 50% 20% 20%;
        position: relative; animation: float 3s infinite ease-in-out;
    }
    .hero-character::before, .hero-character::after {
        content: ''; position: absolute; top: 25px; width: 12px; height: 12px;
        background: white; border-radius: 50%; border: 3px solid #333;
    }
    .hero-character::before { left: 15px; } .hero-character::after { right: 15px; }
    .mouth { position: absolute; bottom: 25px; left: 25px; width: 25px; height: 8px; border-bottom: 4px solid #333; border-radius: 50%; }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
</style>

<div class="welcome-section">
    <div class="hero-character"><div class="mouth"></div></div>
    <h1>¡Hola, <?php echo htmlspecialchars($child_name); ?>! 🚀</h1>
    <p style="font-size: 18px; color: var(--text-muted);">Elige un módulo para continuar tu aventura</p>
</div></div>
            <h1>¡Hola, <?php echo htmlspecialchars($child_name); ?>! 🚀</h1>
            <p style="font-size: 18px; color: var(--text-muted);">Elige un módulo para continuar tu aventura</p>
        </div>

        <div class="modules-grid">
            <?php foreach ($modules as $mod): ?>
                <a href="course.php?module=<?php echo $mod['id']; ?>" class="module-card">
                    <div class="module-icon">🌍</div> <h2 class="module-title"><?php echo htmlspecialchars($mod['title']); ?></h2>
                    <p style="color: #666;">Módulo <?php echo $mod['order_num']; ?></p>
                    <div class="btn-enter">Entrar ➡️</div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>