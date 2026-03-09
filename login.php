<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización extrema: Filtra todo lo que no sea dígito
    $parent_phone = filter_var($_POST['parent_phone'], FILTER_SANITIZE_NUMBER_INT);
    $parent_phone = preg_replace('/[^0-9]/', '', $parent_phone);
    
    if (empty($parent_phone)) {
        $error = "Por favor, ingresa tu número de WhatsApp.";
    } else {
        try {
            // Prepared Statement para evitar SQL Injection
            $stmt = $pdo->prepare("SELECT id FROM users WHERE parent_phone = :phone LIMIT 1");
            $stmt->bindValue(':phone', $parent_phone, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Regenerar el ID de sesión para prevenir Session Hijacking
                session_regenerate_id(true); 
                $_SESSION['user_id'] = $user['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Número no encontrado. Verifica tu número o crea una cuenta nueva.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión temporal. Inténtalo más tarde.";
            // Opcional: error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar Sesión - My World</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="manifest" href="manifest.json">
    <style>
        .login-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); padding: 20px; }
        .login-card { background: white; border-radius: 16px; padding: 50px 40px; width: 100%; max-width: 420px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center; border-top: 5px solid var(--brand-green); }
        .login-input { width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #E2E8F0; font-size: 18px; margin-bottom: 25px; text-align: center; box-sizing: border-box; transition: 0.3s; color: var(--text-main); }
        .login-input:focus { border-color: var(--brand-blue); outline: none; box-shadow: 0 0 0 3px rgba(28, 61, 106, 0.1); }
        .btn-login { background: var(--brand-green); color: white; border: none; padding: 16px; width: 100%; border-radius: 50px; font-size: 18px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); transition: 0.3s; }
        .btn-login:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-login:active { transform: scale(0.98); }
        .error-msg { background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; border: 1px solid #FCA5A5; font-size: 14px; }
        .brand-logo { height: 50px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <img src="assets/logo-myworld.svg" alt="My World" class="brand-logo" onerror="this.style.display='none';">
            <h1 style="color: var(--brand-blue); margin-bottom: 10px;">¡Hola de Nuevo!</h1>
            <p style="color: #64748B; margin-bottom: 30px; font-size: 15px;">Ingresa tu número de WhatsApp para continuar la aventura.</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="tel" name="parent_phone" class="login-input" placeholder="Ej: +51999888777" pattern="[0-9+]*" inputmode="numeric" required>
                <button type="submit" class="btn-login">Entrar a Jugar</button>
            </form>
            
            <p style="margin-top: 25px; color: #64748B; font-size: 14px;">
                <a href="index.php" style="color: var(--brand-blue); font-weight: 600; text-decoration: none;">← Volver al inicio</a>
            </p>
        </div>
    </div>
</body>
</html>