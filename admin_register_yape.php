<?php
require_once 'includes/config.php';

// Contraseña Maestra tuya para usar esta página (Cámbiala por seguridad)
$master_admin_password = "yape_myworld_2026"; 

$error = '';
$success = '';
$trigger_pixel = false;
$pixel_value = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_POST['admin_pass'] !== $master_admin_password) {
        $error = "Contraseña de Administrador incorrecta. Acceso Denegado.";
    } else {
        $child_name = trim($_POST['child_name']);
        $parent_phone = filter_var($_POST['parent_phone'], FILTER_SANITIZE_NUMBER_INT);
        $parent_phone = preg_replace('/[^0-9+]/', '', $parent_phone);
        $password = $_POST['password'];
        $has_bump = isset($_POST['has_bump']) ? 1 : 0;

        if (empty($child_name) || empty($parent_phone) || empty($password)) {
            $error = "Completa los datos del cliente.";
        } else {
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
            $stmtCheck->execute([$parent_phone]);
            
            if ($stmtCheck->fetch()) {
                $error = "El número $parent_phone ya está registrado en la base de datos.";
            } else {
                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, password, has_bump, total_stars) VALUES (?, ?, ?, ?, 0)");
                    
                    if ($stmtInsert->execute([$child_name, $parent_phone, $hashed_password, $has_bump])) {
                        $success = "¡Cuenta creada con éxito! El cliente ya puede loguearse.";
                        // Preparamos los datos para disparar el pixel de Meta en esta misma vista
                        $trigger_pixel = true;
                        $pixel_value = $has_bump ? 19.99 : 14.99;
                    } else {
                        $error = "Hubo un error al insertar en la base de datos.";
                    }
                } catch (PDOException $e) {
                    $error = "Error de base de datos.";
                }
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
    <title>Admin Yape - Creación de Cuentas</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    
    <?php if ($trigger_pixel): ?>
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'TU_PIXEL_ID'); // REEMPLAZAR CON TU ID
    fbq('track', 'PageView');
    fbq('track', 'Purchase', {value: <?php echo $pixel_value; ?>, currency: 'PEN'});
    console.log("Evento Purchase disparado por valor de: S/", <?php echo $pixel_value; ?>);
    </script>
    <?php endif; ?>

    <style>
        body { background: #F8FAFC; font-family: 'Fredoka', sans-serif; padding: 20px; display: flex; justify-content: center; }
        .admin-box { background: white; padding: 30px; border-radius: 16px; border-top: 5px solid #7B2CBF; max-width: 500px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h2 { color: #1C3D6A; margin-bottom: 20px; text-align: center; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #475569; }
        .input-group input[type="text"], .input-group input[type="tel"], .input-group input[type="password"] { width: 100%; padding: 12px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; box-sizing: border-box; }
        .btn-submit { background: #7B2CBF; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #5A189A; }
        .msg-error { color: #DC2626; background: #FEF2F2; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #FCA5A5; text-align: center; }
        .msg-success { color: #059669; background: #D1FAE5; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #6EE7B7; text-align: center; }
    </style>
</head>
<body>

<div class="admin-box">
    <h2>Activación Manual (Yape/Plin)</h2>
    
    <?php if (!empty($error)) echo "<div class='msg-error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='msg-success'>$success</div>"; ?>

    <form action="" method="POST">
        <div class="input-group">
            <label>Contraseña Maestra Admin:</label>
            <input type="password" name="admin_pass" required placeholder="Clave secreta tuya">
        </div>
        <hr style="border: 1px solid #E2E8F0; margin: 20px 0;">
        
        <div class="input-group">
            <label>Nombre del Niño:</label>
            <input type="text" name="child_name" required placeholder="Ej: Mateo">
        </div>
        
        <div class="input-group">
            <label>Número WhatsApp del Padre:</label>
            <input type="tel" name="parent_phone" required placeholder="Ej: 999888777">
        </div>

        <div class="input-group">
            <label>Contraseña Temporal a crearle:</label>
            <input type="text" name="password" required value="123456" placeholder="Se encriptará en la BD">
        </div>

        <div class="input-group" style="background: #FFFBEB; padding: 10px; border-radius: 8px; border: 1px solid #FDE68A;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0; color: #D97706;">
                <input type="checkbox" name="has_bump" value="1" style="width: 20px; height: 20px;">
                ¿Compró el BUMP SELL (S/19.99)?
            </label>
            <small style="display:block; margin-top:5px; color:#92400E;">Marcar disparará el Pixel por S/19.99. Desmarcar por S/14.99.</small>
        </div>

        <button type="submit" class="btn-submit">Registrar Cuenta y Detonar Pixel</button>
    </form>
</div>

</body>
</html>