<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// RATE LIMITING BASICO
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = 0;
}

// Bloqueo temporal de 3 minutos si falla 5 veces
if ($_SESSION['login_attempts'] >= 5 && time() - $_SESSION['lockout_time'] < 180) {
    $remaining = 180 - (time() - $_SESSION['lockout_time']);
    $error = "Demasiados intentos fallidos. Inténtalo de nuevo en $remaining segundos.";
} else {
    // Si ya pasó el tiempo, reseteamos intentos
    if ($_SESSION['login_attempts'] >= 5 && time() - $_SESSION['lockout_time'] >= 180) {
        $_SESSION['login_attempts'] = 0;
    }

    // GENERAR CAPTCHA
    if (!isset($_SESSION['login_captcha_1']) || $_SERVER["REQUEST_METHOD"] != "POST") {
        $_SESSION['login_captcha_1'] = rand(1, 10);
        $_SESSION['login_captcha_2'] = rand(1, 10);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $parent_phone = filter_var($_POST['parent_phone'], FILTER_SANITIZE_NUMBER_INT);
        $parent_phone = preg_replace('/[^0-9+]/', '', $parent_phone);
        $password = $_POST['password'] ?? '';
        $captcha_answer = (int)$_POST['captcha'];
        $correct_captcha = $_SESSION['login_captcha_1'] + $_SESSION['login_captcha_2'];
        
        if (empty($parent_phone) || empty($password)) {
            $error = "Por favor, ingresa tu número y tu contraseña.";
        } elseif ($captcha_answer !== $correct_captcha) {
            $error = "El resultado de seguridad es incorrecto.";
            $_SESSION['login_attempts']++;
            $_SESSION['login_captcha_1'] = rand(1, 10);
            $_SESSION['login_captcha_2'] = rand(1, 10);
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, password FROM users WHERE parent_phone = :phone LIMIT 1");
                $stmt->bindValue(':phone', $parent_phone, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true); 
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['login_attempts'] = 0; // Reset
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $_SESSION['login_attempts']++;
                    if ($_SESSION['login_attempts'] >= 5) {
                        $_SESSION['lockout_time'] = time();
                    }
                    $error = "Número o contraseña incorrectos.";
                    $_SESSION['login_captcha_1'] = rand(1, 10);
                    $_SESSION['login_captcha_2'] = rand(1, 10);
                }
            } catch (PDOException $e) {
                $error = "Error de conexión temporal. Inténtalo más tarde.";
            }
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
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <style>
        :root { --brand-blue: #1C3D6A; --brand-green: #68A93E; --bg-light: #F8FAFC; --text-main: #333333; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .login-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); padding: 20px; }
        .login-card { background: white; border-radius: 16px; padding: 50px 40px; width: 100%; max-width: 420px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center; border-top: 5px solid var(--brand-green); }
        .login-input { width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #E2E8F0; font-size: 16px; margin-bottom: 20px; text-align: center; box-sizing: border-box; transition: 0.3s; color: var(--text-main); }
        .login-input:focus { border-color: var(--brand-blue); outline: none; box-shadow: 0 0 0 3px rgba(28, 61, 106, 0.1); }
        .btn-login { background: var(--brand-green); color: white; border: none; padding: 16px; width: 100%; border-radius: 50px; font-size: 18px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); transition: 0.3s; }
        .btn-login:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-login:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none;}
        .error-msg { background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; border: 1px solid #FCA5A5; font-size: 14px; }
        .brand-logo { height: 50px; margin-bottom: 20px; }
        .form-label { display: block; text-align: left; font-weight: 600; color: var(--brand-blue); margin-bottom: 8px; font-size: 14px; }
        .captcha-box { display: flex; align-items: center; justify-content: space-between; gap: 10px; background: #F8FAFC; padding: 10px; border-radius: 10px; border: 1px solid #E2E8F0; margin-bottom: 20px; }
        .captcha-question { font-size: 16px; font-weight: 800; color: var(--brand-blue); }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <img src="assets/logo-myworld.svg" alt="My World" class="brand-logo" onerror="this.style.display='none';">
            <h1 style="color: var(--brand-blue); margin-bottom: 10px;">¡Hola de Nuevo!</h1>
            <p style="color: #64748B; margin-bottom: 30px; font-size: 15px;">Ingresa tus datos para continuar la aventura.</p>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div style="text-align: left;">
                    <label class="form-label">Tu Número de WhatsApp</label>
                    <input type="tel" name="parent_phone" class="login-input" placeholder="Ej: 999888777" pattern="[0-9+]*" inputmode="numeric" required <?php if(isset($remaining)) echo 'disabled'; ?>>
                </div>
                
                <div style="text-align: left;">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="login-input" placeholder="********" required <?php if(isset($remaining)) echo 'disabled'; ?>>
                </div>

                <?php if(!isset($remaining)): ?>
                <div style="text-align: left;">
                    <label class="form-label">Seguridad:</label>
                    <div class="captcha-box">
                        <span class="captcha-question">¿<?php echo $_SESSION['login_captcha_1']; ?> + <?php echo $_SESSION['login_captcha_2']; ?>? =</span>
                        <input type="number" name="captcha" class="login-input" style="margin-bottom: 0; width: 60%; text-align: center;" placeholder="Resultado" required>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-login" style="margin-top: 10px;" <?php if(isset($remaining)) echo 'disabled'; ?>>Entrar a Jugar</button>
            </form>
            
            <p style="margin-top: 25px; color: #64748B; font-size: 14px;">
                <a href="index.php" style="color: var(--brand-blue); font-weight: 600; text-decoration: none;">← Volver al inicio</a>
            </p>
        </div>
    </div>
</body>
</html>