<?php
// register.php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_name = trim($_POST['child_name']);
    $parent_phone = trim($_POST['parent_phone']);
    
    // Formatear el número (quitar espacios)
    $parent_phone = preg_replace('/[^0-9+]/', '', $parent_phone);

    if (!empty($child_name) && !empty($parent_phone)) {
        $stmt = $pdo->prepare("INSERT INTO users (child_name, parent_phone, total_stars) VALUES (?, ?, 0)");
        if ($stmt->execute([$child_name, $parent_phone])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: index.php");
            exit;
        }
    } else {
        $error = "Por favor, completa ambos campos.";
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
        .btn-auth { width: 100%; padding: 15px; background: var(--success); color: white; font-size: 20px; border: none; border-radius: 30px; cursor: pointer; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container auth-box">
        <h1>👋 ¡Bienvenidos!</h1>
        <p>Crea una cuenta para guardar el progreso y recibir alertas por WhatsApp.</p>
        
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <div class="input-group">
                <label>Nombre del Niño(a):</label>
                <input type="text" name="child_name" placeholder="Ej: Mateo" required>
            </div>
            <div class="input-group">
                <label>Tu número de WhatsApp (Papá/Mamá):</label>
                <input type="text" name="parent_phone" placeholder="Ej: +51999888777" required>
            </div>
            <button type="submit" class="btn-auth">Crear Cuenta 🚀</button>
        </form>
        <p style="margin-top:20px;">¿Ya tienes cuenta? <a href="login.php">Entrar aquí</a></p>
    </div>
</body>
</html>