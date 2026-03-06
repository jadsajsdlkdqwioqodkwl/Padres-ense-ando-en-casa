<?php
require_once 'includes/config.php';

// Si ya inició sesión, lo mandamos al juego
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
        // Verificamos si el número de WhatsApp ya está registrado
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
        $stmtCheck->execute([$parent_phone]);
        
        if ($stmtCheck->fetch()) {
            $error = "Este número de WhatsApp ya está registrado. Por favor, inicia sesión.";
        } else {
            // Registramos al nuevo usuario
            try {
                $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars) VALUES (?, ?, 0)");
                if ($stmtInsert->execute([$child_name, $parent_phone])) {
                    // Iniciamos sesión automáticamente con el ID recién creado
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
    <title>Crear Cuenta - Mi Mundo en Inglés</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .login-wrapper {
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 20px;
        }
        .login-card {
            background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center;
        }
        .login-input {
            width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #ddd;
            font-size: 18px; margin-bottom: 20px; text-align: center; box-sizing: border-box;
            transition: 0.3s;
        }
        .login-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 10px rgba(108, 92, 237, 0.2); }
        .btn-login {
            background: var(--success); color: white; border: none; padding: 15px; width: 100%;
            border-radius: 10px; font-size: 20px; font-weight: bold; cursor: pointer;
            box-shadow: 0 6px 0 #218c74; transition: 0.2s; margin-top: 10px;
        }
        .btn-login:active { transform: translateY(4px); box-shadow: 0 2px 0 #218c74; }
        .error-msg { background: #ffeaa7; color: #d63031; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .form-group { text-align: left; margin-bottom: 5px; }
        .form-label { display: block; font-weight: bold; color: var(--dark); margin-bottom: 5px; margin-left: 5px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1 style="color: var(--primary); margin-bottom: 5px;">🌟 Nueva Aventura</h1>
            <p style="color: #666; margin-bottom: 30px;">Crea una cuenta para que tu hijo/a empiece a aprender.</p>
            
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
            
            <p style="margin-top: 25px; color: #666;">
                ¿Ya tienes una cuenta? <a href="login.php" style="color: var(--primary); font-weight: bold;">Inicia Sesión</a>
            </p>
        </div>
    </div>
</body>
</html>