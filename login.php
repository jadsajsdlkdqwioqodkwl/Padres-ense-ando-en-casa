<?php
// login.php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['parent_phone']);
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ? LIMIT 1");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Número no encontrado. ¿Estás registrado?";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .auth-box { max-width: 400px; margin: 50px auto; text-align: center; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 8px; color: var(--primary); }
        .input-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 10px; font-size: 16px; }
        .btn-auth { width: 100%; padding: 15px; background: var(--primary); color: white; font-size: 20px; border: none; border-radius: 30px; cursor: pointer; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container auth-box">
        <h1>Entrar a jugar 🎮</h1>
        
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <div class="input-group">
                <label>Tu número de WhatsApp:</label>
                <input type="text" name="parent_phone" placeholder="Ej: +51999888777" required>
            </div>
            <button type="submit" class="btn-auth">Entrar ➡️</button>
        </form>
        <p style="margin-top:20px;">¿Primera vez? <a href="register.php">Crear cuenta gratis</a></p>
    </div>
</body>
</html>