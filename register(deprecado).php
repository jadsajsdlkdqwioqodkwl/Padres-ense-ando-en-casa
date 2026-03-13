<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_name = trim($_POST['child_name']);
    $parent_phone = trim($_POST['parent_phone']);
    
    if (empty($child_name) || empty($parent_phone)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
        $stmtCheck->execute([$parent_phone]);
        
        if ($stmtCheck->fetch()) {
            $error = "Este número de WhatsApp ya está registrado. Por favor, inicia sesión.";
        } else {
            try {
                $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars) VALUES (?, ?, 0)");
                if ($stmtInsert->execute([$child_name, $parent_phone])) {
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Hubo un problema creando la cuenta. Inténtalo de nuevo.";
                }
            } catch (PDOException $e) {
                $error = "Error de base de datos: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - My World</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .login-wrapper {
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); padding: 20px;
        }
        .login-card {
            background: white; border-radius: 16px; padding: 40px; width: 100%; max-width: 450px;
            box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center;
            border-top: 5px solid var(--brand-green);
        }
        .login-input {
            width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #E2E8F0;
            font-size: 16px; margin-bottom: 20px; box-sizing: border-box;
            transition: 0.3s; color: var(--text-main);
        }
        .login-input:focus { border-color: var(--brand-blue); outline: none; box-shadow: 0 0 0 3px rgba(28, 61, 106, 0.1); }
        .btn-login {
            background: var(--brand-green); color: white; border: none; padding: 16px; width: 100%;
            border-radius: 50px; font-size: 18px; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-login:active { transform: scale(0.98); }
        .error-msg { background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #FCA5A5; font-size: 14px;}
        .form-group { text-align: left; margin-bottom: 10px; }
        .form-label { display: block; font-weight: 600; color: var(--brand-blue); margin-bottom: 8px; font-size: 14px; }
        .brand-logo { height: 40px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <img src="assets/logo-myworld.svg" alt="My World" class="brand-logo" onerror="this.style.display='none';">
            <h1 style="color: var(--brand-blue); margin-bottom: 5px;">Nueva Aventura</h1>
            <p style="color: #64748B; margin-bottom: 25px; font-size: 15px;">Crea una cuenta para que tu hijo/a empiece a aprender.</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre del Niño/a:</label>
                    <input type="text" name="child_name" class="login-input" placeholder="Ej: Mateo" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">WhatsApp del Padre/Madre:</label>
                    <input type="tel" name="parent_phone" class="login-input" placeholder="Ej: +51999888777" required>
                </div>

                <button type="submit" class="btn-login">¡Crear Cuenta y Jugar!</button>
            </form>
            
            <p style="margin-top: 25px; color: #64748B; font-size: 14px;">
                ¿Ya tienes una cuenta? <a href="login.php" style="color: var(--brand-blue); font-weight: 700; text-decoration: none;">Inicia Sesión</a>
            </p>
        </div>
    </div>
</body>
</html>