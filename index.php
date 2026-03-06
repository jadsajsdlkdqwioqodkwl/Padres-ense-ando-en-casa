<?php
require_once 'includes/config.php';

// Protección de ruta. Si no hay sesión, al login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Consultamos al usuario incluyendo su fecha de suscripción
$stmtUser = $pdo->prepare("SELECT child_name, total_stars, subscription_expires_at FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user_info = $stmtUser->fetch();

// Obtener los módulos (Semanas) disponibles
$stmt = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
$modules = $stmt->fetchAll();

$page_title = "Inicio - Mi Mundo";

// AÑADIDO: Lógica de Bloqueo por Suscripción Vencida (31 días)
$is_expired = false;
$days_left = 0;
if (empty($user_info['subscription_expires_at'])) {
    $is_expired = true; // Si por error no tiene fecha, lo bloqueamos
} else {
    $expire_timestamp = strtotime($user_info['subscription_expires_at']);
    $current_timestamp = time();
    if ($current_timestamp > $expire_timestamp) {
        $is_expired = true;
    } else {
        $days_left = ceil(($expire_timestamp - $current_timestamp) / 86400);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .dashboard-header {
            background: var(--primary); color: white; border-radius: 20px; padding: 30px;
            margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 10px 20px rgba(108, 92, 237, 0.2); flex-wrap: wrap; gap: 20px;
        }
        .user-stats { display: flex; flex-direction: column; gap: 10px; align-items: flex-end; }
        .stat-badge {
            background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 15px;
            font-size: 20px; font-weight: bold; backdrop-filter: blur(5px); display: flex; align-items: center; gap: 10px;
        }
        .subscription-badge { font-size: 14px; background: rgba(0,0,0,0.3); padding: 5px 15px; border-radius: 10px; }
        
        .module-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .module-card {
            background: white; border-radius: 25px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: 0.3s; text-decoration: none; color: inherit; display: block; border: 4px solid transparent;
        }
        .module-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .module-img { height: 180px; width: 100%; object-fit: cover; display: flex; align-items: center; justify-content: center; font-size: 80px; }
        .module-content { padding: 25px; }
        .btn-play {
            display: inline-block; background: var(--success); color: white; padding: 12px 25px;
            border-radius: 30px; font-weight: bold; margin-top: 15px; box-shadow: 0 4px 0 #218c74;
        }
        
        .btn-logout {
            background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 20px;
            border-radius: 15px; cursor: pointer; font-weight: bold; text-decoration: none; transition: 0.2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.4); }

        /* Estilos del Bloqueo por Pago */
        .lock-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.95); z-index: 9999; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            backdrop-filter: blur(10px); text-align: center; padding: 20px;
        }
        .lock-box {
            background: white; border-radius: 20px; padding: 40px; max-width: 500px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15); border: 3px solid #ff4757;
        }
        .btn-renew {
            display: inline-block; background: #ff4757; color: white; padding: 15px 30px;
            border-radius: 30px; font-size: 20px; font-weight: bold; text-decoration: none;
            margin-top: 20px; box-shadow: 0 6px 0 #c0392b; transition: 0.2s;
        }
        .btn-renew:active { transform: translateY(4px); box-shadow: 0 2px 0 #c0392b; }
    </style>
</head>
<body>
    
    <?php if($is_expired): ?>
    <div class="lock-overlay">
        <div class="lock-box">
            <h1 style="font-size: 60px; margin: 0;">⏳</h1>
            <h2 style="color: #ff4757; margin: 10px 0;">¡Tu mes de aprendizaje ha concluido!</h2>
            <p style="color: #555; font-size: 18px;">Han pasado los 31 días de tu suscripción. Para que <?php echo htmlspecialchars($user_info['child_name']); ?> siga aprendiendo nuevas palabras y desbloqueando trofeos, renueva el acceso.</p>
            <a href="landing.php" class="btn-renew">Renovar Suscripción (S/ 39.00)</a>
            <br><br>
            <a href="logout.php" style="color: #888; text-decoration: underline;">Cerrar Sesión</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="container" style="<?php echo $is_expired ? 'filter: blur(5px); pointer-events: none;' : ''; ?>">
        <?php include 'includes/navbar.php'; ?>

        <div class="dashboard-header">
            <div>
                <h1 style="margin: 0; font-size: 2.5rem;">¡Hola, <?php echo htmlspecialchars($user_info['child_name']); ?>! 🖐️</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 1.2rem;">¿Listo para aprender nuevas palabras hoy?</p>
            </div>
            <div class="user-stats">
                <div class="stat-badge">⭐ <?php echo $user_info['total_stars']; ?> Estrellas</div>
                <?php if(!$is_expired): ?>
                    <div class="subscription-badge">⏳ Te quedan <?php echo $days_left; ?> días</div>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout" title="Cerrar Sesión">🚪 Salir</a>
            </div>
        </div>

        <h2 style="color: var(--dark); margin-bottom: 20px; font-size: 2rem;">Tus Semanas de Aprendizaje</h2>
        
        <div class="module-grid">
            <?php foreach ($modules as $module): ?>
                <a href="course.php?module=<?php echo $module['id']; ?>" class="module-card" style="border-color: <?php echo htmlspecialchars($module['color_theme']); ?>;">
                    <div class="module-img" style="background: <?php echo htmlspecialchars($module['color_theme']); ?>20; color: <?php echo htmlspecialchars($module['color_theme']); ?>;">
                        <?php 
                        if(strpos(strtolower($module['title']), 'entorno') !== false) echo '🏡';
                        elseif(strpos(strtolower($module['title']), 'naturaleza') !== false) echo '🌲';
                        else echo '🚀'; 
                        ?>
                    </div>
                    <div class="module-content">
                        <h3 style="margin: 0 0 10px 0; color: var(--dark); font-size: 1.5rem;"><?php echo htmlspecialchars($module['title']); ?></h3>
                        <p style="color: var(--text-muted); margin-bottom: 15px;">Completa tus 5 palabras diarias y genera tu diploma.</p>
                        <div class="btn-play">Entrar a la Semana ▶</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>