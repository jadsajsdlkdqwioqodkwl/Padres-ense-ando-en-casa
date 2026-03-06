<?php
require_once 'includes/config.php';

// Si no está logueado, lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmtUser = $pdo->prepare("SELECT child_name FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user_info = $stmtUser->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar Acceso - Mi Mundo en Inglés</title>
    <style>
        :root { --primary: #6c5ced; --secondary: #ff9f43; --dark: #2d3436; --light: #f8f9fa; --success: #2ed573; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); color: var(--dark); line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
        
        .landing-nav { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: bold; color: var(--primary); text-decoration: none; }
        .logout-btn { background: transparent; border: 2px solid #ccc; color: #888; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .logout-btn:hover { background: #eee; color: #333; }

        .renew-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
        .checkout-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); max-width: 500px; width: 100%; text-align: center; border-top: 5px solid var(--secondary); }
        .checkout-box h3 { color: var(--dark); margin-bottom: 10px; font-size: 1.8rem; }
        .checkout-box p { color: #666; font-size: 1.1rem; margin-bottom: 25px; }
        
        .price-tag { font-size: 2.5rem; font-weight: bold; color: var(--secondary); margin-bottom: 5px; }
        .price-sub { font-size: 1rem; color: #888; margin-bottom: 25px; display: block; }
        
        .btn-pay { width: 100%; background: var(--success); color: white; border: none; padding: 18px; border-radius: 10px; font-size: 20px; font-weight: bold; cursor: pointer; box-shadow: 0 6px 0 #218c74; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-pay:active { transform: translateY(4px); box-shadow: 0 2px 0 #218c74; }
        .guarantee { font-size: 0.9rem; color: #888; margin-top: 15px; }
        
        .payment-methods { display: flex; justify-content: center; gap: 15px; margin-top: 20px; opacity: 0.6; font-size: 24px; }
    </style>
</head>
<body>

    <nav class="landing-nav">
        <a href="index.php" class="logo">🚀 Mi Mundo en Inglés</a>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </nav>

    <div class="renew-container">
        <div class="checkout-box">
            <h3>¡Renueva la magia! ✨</h3>
            <p>El mes de aprendizaje de <strong><?php echo htmlspecialchars($user_info['child_name']); ?></strong> ha concluido. Renueva ahora para desbloquear 31 días más de nuevos minijuegos, palabras y diplomas.</p>
            
            <div class="price-tag">S/ 39.00</div>
            <span class="price-sub">Por 31 días de acceso completo</span>
            
            <button id="btn-comprar" class="btn-pay"><span>💳 Renovar con Tarjeta o Yape</span></button>
            
            <div class="payment-methods">
                📱 💳 🏦
            </div>
            <div class="guarantee">🔒 Pago 100% seguro procesado por Culqi</div>
        </div>
    </div>

    <script>
        document.getElementById('btn-comprar').addEventListener('click', function (e) {
            e.preventDefault();
            
            const btn = document.getElementById('btn-comprar');
            btn.innerHTML = "<span>⏳ Procesando pago...</span>";
            btn.disabled = true;

            fetch('app/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    token: "token_falso_renovacion",
                    is_renewal: true // Indicamos al backend que es una renovación
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message); 
                    window.location.href = 'index.php'; 
                } else {
                    alert("Error: " + data.message);
                    btn.innerHTML = "<span>💳 Renovar con Tarjeta o Yape</span>";
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Verifica tu conexión a internet.");
                btn.innerHTML = "<span>💳 Renovar con Tarjeta o Yape</span>";
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>