<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_phone = preg_replace('/[^0-9]/', '', $_POST['parent_phone']);
    
    if (empty($parent_phone)) {
        $error = "Por favor, ingresa tu número de WhatsApp.";
    } else {
        // Buscamos al usuario basado únicamente en su número de teléfono
        $stmt = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
        $stmt->execute([$parent_phone]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Número no encontrado. Verifica tu número o crea una cuenta nueva.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Mi Mundo en Inglés</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .login-wrapper {
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 20px;
        }
        .login-card {
            background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center;
        }
        .login-input {
            width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #ddd;
            font-size: 18px; margin-bottom: 20px; text-align: center; box-sizing: border-box;
            transition: 0.3s;
        }
        .login-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 10px rgba(108, 92, 237, 0.2); }
        .btn-login {
            background: var(--primary); color: white; border: none; padding: 15px; width: 100%;
            border-radius: 10px; font-size: 20px; font-weight: bold; cursor: pointer;
            box-shadow: 0 6px 0 #3b2a9e; transition: 0.2s;
        }
        .btn-login:active { transform: translateY(4px); box-shadow: 0 2px 0 #3b2a9e; }
        .error-msg { background: #ffeaa7; color: #d63031; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1 style="color: var(--primary); margin-bottom: 5px;">🚀 ¡Hola de Nuevo!</h1>
            <p style="color: #666; margin-bottom: 30px;">Ingresa tu número de WhatsApp para continuar la aventura.</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="tel" name="parent_phone" class="login-input" placeholder="Ej: +51999888777" required>
                <button type="submit" class="btn-login">Entrar a Jugar</button>
            </form>
        
        </div>
    </div>
</body>
</html>