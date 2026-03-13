<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// 1. RECEPCIÓN DE VARIABLES DE MERCADO PAGO
$payment_id = isset($_GET['payment_id']) ? filter_var($_GET['payment_id'], FILTER_SANITIZE_NUMBER_INT) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$is_bump = (isset($_GET['external_reference']) && $_GET['external_reference'] === 'bump') ? 1 : 0;

$error = '';
$payment_verified = false;

// TOKEN DE ACCESO DE MERCADO PAGO (PRODUCCIÓN)
// Integrado con tu credencial real para validar pagos
$mp_access_token = "APP_USR-6122068330896334-031116-fb54d0e609056882561210e9e7c074a0-3259142883";

// 2. VERIFICACIÓN ANTI-FRAUDE CON LA API DE MERCADO PAGO
if (empty($payment_id) || $status !== 'approved') {
    $error = "Acceso denegado. Pago no detectado o no aprobado.";
} else {
    // Comprobar si el payment_id ya fue usado en nuestra Base de Datos
    $stmtCheckPayment = $pdo->prepare("SELECT id FROM users WHERE payment_id = ?");
    $stmtCheckPayment->execute([$payment_id]);
    if ($stmtCheckPayment->fetch()) {
        $error = "Este pago ya ha sido registrado previamente. El enlace ha expirado por seguridad.";
    } else {
        // Llamada cURL a Mercado Pago para verificar que el pago existe y no es falsificado
        $ch = curl_init("https://api.mercadopago.com/v1/payments/" . $payment_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $mp_access_token
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $mp_data = json_decode($response, true);
            if (isset($mp_data['status']) && $mp_data['status'] === 'approved') {
                $payment_verified = true;
            } else {
                $error = "El pago existe pero su estado actual no es 'Aprobado'.";
            }
        } else {
            // Si el Access Token falla o hay error de red
            $error = "No se pudo validar el pago con el banco. Intenta recargar la página.";
        }
    }
}

// 3. GENERACIÓN DE CAPTCHA ANTI-BOTS
if (!isset($_SESSION['captcha_num1']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['captcha_num1'] = rand(1, 9);
    $_SESSION['captcha_num2'] = rand(1, 9);
}

// 4. PROCESAMIENTO DEL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST" && $payment_verified) {
    $child_name = trim($_POST['child_name']);
    $parent_phone = filter_var($_POST['parent_phone'], FILTER_SANITIZE_NUMBER_INT);
    $parent_phone = preg_replace('/[^0-9+]/', '', $parent_phone);
    $password = $_POST['password'];
    $captcha_answer = (int)$_POST['captcha'];
    $correct_captcha = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];

    if (empty($child_name) || empty($parent_phone) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } elseif ($captcha_answer !== $correct_captcha) {
        $error = "El resultado de la suma de seguridad es incorrecto. Eres un humano, ¿verdad?";
        // Regenerar Captcha
        $_SESSION['captcha_num1'] = rand(1, 9);
        $_SESSION['captcha_num2'] = rand(1, 9);
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE parent_phone = ?");
        $stmtCheck->execute([$parent_phone]);
        
        if ($stmtCheck->fetch()) {
            $error = "Este número de WhatsApp ya está registrado.";
        } else {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // INSERTAMOS EL payment_id PARA QUEMAR ESTE LINK
                $stmtInsert = $pdo->prepare("INSERT INTO users (child_name, parent_phone, password, has_bump, payment_id, total_stars) VALUES (?, ?, ?, ?, ?, 0)");
                if ($stmtInsert->execute([$child_name, $parent_phone, $hashed_password, $is_bump, $payment_id])) {
                    
                    // =========================================================================
                    // INICIO INYECCIÓN: META CONVERSIONS API (CAPI) - EVENTO PURCHASE
                    // =========================================================================
                    $pixel_id = '1602561284224693'; // PIXEL REAL
                    $access_token = 'EAAMOcyoXvxQBQZBjuE72IyuQolQ0ZBPOvqfj4FpaMku5aNJgxuUrbKkhS1o7O06iGf5u5E2xlBMffHVx2EmGBOT4IJCI8hVgBPyqZAnW2hLGa22nshDPeSBowDVXd38FQ3UDq99h93aCBBW0YnvXPrivxu9mXGr2lmTbFBPHjvWjCLWglwZA2FulqTs79wZDZD'; // TOKEN REAL
                    $test_event_code = 'TEST38270'; // CÓDIGO DE PRUEBAS ACTIVO

                    // 1. Recolección de IP y User Agent (Crucial para el Event Match Quality)
                    $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip_array = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                        $client_ip = trim($ip_array[0]);
                    }
                    $client_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

                    // 2. Limpieza y Hashing del Teléfono a SHA-256 (Obligatorio por Meta)
                    $clean_phone = preg_replace('/[^0-9]/', '', $parent_phone);
                    // Validamos si es un número peruano sin código de país (9 dígitos)
                    if (strlen($clean_phone) == 9) {
                        $clean_phone = '51' . $clean_phone; // Se concatena el 51
                    }
                    $hashed_phone = hash('sha256', $clean_phone);

                    // 3. Determinamos el valor del Purchase
                    $purchase_value = ($is_bump == 1) ? 19.99 : 14.99;

                    // 4. Armamos el Payload (JSON)
                    $event_data = [
                        'data' => [
                            [
                                'event_name' => 'Purchase',
                                'event_time' => time(),
                                'action_source' => 'website',
                                'event_id' => (string)$payment_id, // CLAVE DE DEDUPLICACIÓN
                                'user_data' => [
                                    'client_ip_address' => $client_ip,
                                    'client_user_agent' => $client_user_agent,
                                    'ph' => [$hashed_phone] // Teléfono encriptado en array
                                ],
                                'custom_data' => [
                                    'currency' => 'PEN',
                                    'value' => $purchase_value,
                                    'content_name' => 'My World - Acceso Vitalicio'
                                ]
                            ]
                        ],
                        'test_event_code' => $test_event_code // BORRAR AL PASAR A PRODUCCIÓN
                    ];

                    // 5. Envío de datos vía cURL hacia Graph API (Sin trabar el servidor)
                    $ch_capi = curl_init("https://graph.facebook.com/v19.0/{$pixel_id}/events?access_token={$access_token}");
                    curl_setopt($ch_capi, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_capi, CURLOPT_POST, true);
                    curl_setopt($ch_capi, CURLOPT_POSTFIELDS, json_encode($event_data));
                    curl_setopt($ch_capi, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);
                    curl_setopt($ch_capi, CURLOPT_TIMEOUT, 5); // Seguridad: Límite de 5s para no congelar tu app
                    curl_exec($ch_capi);
                    curl_close($ch_capi);

                    // 6. Configuración de Variables de Sesión para la Deduplicación del Píxel
                    $_SESSION['fire_purchase_pixel'] = true;
                    $_SESSION['purchase_event_id'] = $payment_id;
                    $_SESSION['purchase_value'] = $purchase_value;
                    // =========================================================================
                    // FIN INYECCIÓN: META CONVERSIONS API
                    // =========================================================================

                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Hubo un problema creando la cuenta. Inténtalo de nuevo.";
                }
            } catch (PDOException $e) {
                $error = "Error de base de datos. Es posible que este pago ya se haya registrado.";
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
    <title>¡Pago Exitoso! Crear Cuenta - My World</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-blue: #1C3D6A; --brand-green: #68A93E; --bg-light: #F8FAFC; --text-main: #333333; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .login-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); padding: 20px; }
        .login-card { background: white; border-radius: 16px; padding: 40px; width: 100%; max-width: 450px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center; border-top: 5px solid var(--brand-green); }
        .success-icon { font-size: 50px; margin-bottom: 10px; display: block; }
        .login-input { width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #E2E8F0; font-size: 16px; margin-bottom: 20px; box-sizing: border-box; transition: 0.3s; color: var(--text-main); }
        .login-input:focus { border-color: var(--brand-blue); outline: none; box-shadow: 0 0 0 3px rgba(28, 61, 106, 0.1); }
        .btn-login { background: var(--brand-green); color: white; border: none; padding: 16px; width: 100%; border-radius: 50px; font-size: 18px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); transition: 0.3s; margin-top: 10px; }
        .btn-login:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-login:disabled { background: #94A3B8; cursor: not-allowed; box-shadow: none; transform: none; }
        .error-msg { background: #FEF2F2; color: #DC2626; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #FCA5A5; font-size: 14px; line-height: 1.4; }
        .form-group { text-align: left; margin-bottom: 10px; }
        .form-label { display: block; font-weight: 600; color: var(--brand-blue); margin-bottom: 8px; font-size: 14px; }
        .captcha-box { display: flex; align-items: center; gap: 10px; background: #F8FAFC; padding: 10px 15px; border-radius: 10px; border: 1px solid #E2E8F0; margin-bottom: 20px; }
        .captcha-question { font-size: 18px; font-weight: 800; color: var(--brand-blue); white-space: nowrap; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <?php if (!$payment_verified): ?>
                <span class="success-icon">🛑</span>
                <h1 style="color: #DC2626; margin-bottom: 5px;">Acceso Bloqueado</h1>
                <p style="color: #64748B; margin-bottom: 25px; font-size: 15px;">No se pudo verificar un pago válido con Mercado Pago.</p>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <a href="landing.php" class="btn-login" style="display:inline-block; text-decoration:none; background: var(--brand-blue);">Volver al Inicio</a>
            <?php else: ?>
                <span class="success-icon">🎉</span>
                <h1 style="color: var(--brand-blue); margin-bottom: 5px;">¡Pago Confirmado!</h1>
                <p style="color: #64748B; margin-bottom: 25px; font-size: 15px;">Pago <strong>#<?php echo htmlspecialchars($payment_id); ?></strong> verificado. Establece los datos de acceso para tu hijo.</p>
                
                <?php if (!empty($error)): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">Nombre del Niño/a:</label>
                        <input type="text" name="child_name" class="login-input" placeholder="Ej: Mateo" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tu Número de WhatsApp:</label>
                        <input type="tel" name="parent_phone" class="login-input" placeholder="Ej: 999888777" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Crea una Contraseña:</label>
                        <input type="password" name="password" class="login-input" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Seguridad Anti-Bot:</label>
                        <div class="captcha-box">
                            <span class="captcha-question">¿Cuánto es <?php echo $_SESSION['captcha_num1']; ?> + <?php echo $_SESSION['captcha_num2']; ?>? =</span>
                            <input type="number" name="captcha" class="login-input" style="margin-bottom: 0;" placeholder="Resultado" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">¡Guardar y Entrar a Jugar!</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>