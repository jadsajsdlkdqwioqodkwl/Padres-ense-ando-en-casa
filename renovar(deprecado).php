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
    <title>Renovar Acceso - My World</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --white: #FFFFFF;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); color: var(--text-main); line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
        
        .landing-nav { background: var(--white); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(28, 61, 106, 0.05); border-bottom: 1px solid #E2E8F0;}
        .logo { font-size: 24px; font-weight: 800; color: var(--brand-blue); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .logout-btn { background: var(--bg-light); border: 2px solid #E2E8F0; color: #64748B; padding: 10px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .logout-btn:hover { background: #E2E8F0; color: var(--brand-blue); transform: translateY(-2px); }

        .renew-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
        .checkout-box { background: var(--white); padding: 50px 40px; border-radius: 16px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); max-width: 500px; width: 100%; text-align: center; border-top: 5px solid var(--brand-orange); }
        .checkout-box h3 { color: var(--brand-blue); margin-bottom: 15px; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; }
        .checkout-box p { color: #64748B; font-size: 1.1rem; margin-bottom: 30px; line-height: 1.7; }
        
        .price-tag { font-size: 3rem; font-weight: 800; color: var(--brand-orange); margin-bottom: 5px; }
        .price-sub { font-size: 1.1rem; color: #94A3B8; margin-bottom: 35px; display: block; font-weight: 600; }
        
        .btn-pay { width: 100%; background: var(--brand-green); color: white; border: none; padding: 18px; border-radius: 50px; font-size: 20px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-pay:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-pay:active { transform: scale(0.98); }
        
        .guarantee { font-size: 0.95rem; color: #94A3B8; margin-top: 20px; font-weight: 600; }
        .payment-methods { display: flex; justify-content: center; gap: 20px; margin-top: 25px; opacity: 0.8; font-size: 28px; filter: grayscale(100%); transition: 0.3s; }
        .payment-methods:hover { filter: grayscale(0%); opacity: 1; }
    </style>
</head>
<body>

    <nav class="landing-nav">
        <a href="index.php" class="logo">
            <img src="assets/logo-myworld.svg" alt="My World" style="height: 35px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span style="display:none;">🚀 My World</span>
        </a>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </nav>

    <div class="renew-container">
        <div class="checkout-box">
            <h3>¡Renueva la magia! ✨</h3>
            <p>El mes de aprendizaje de <strong style="color: var(--brand-blue);"><?php echo htmlspecialchars($user_info['child_name']); ?></strong> ha concluido. Renueva ahora para desbloquear 31 días más de nuevos minijuegos, palabras y diplomas.</p>
            
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
            btn.style.background = "#94A3B8";
            btn.style.boxShadow = "none";

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
                    resetBtn(btn);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Verifica tu conexión a internet.");
                resetBtn(btn);
            });
        });

        function resetBtn(btn) {
            btn.innerHTML = "<span>💳 Renovar con Tarjeta o Yape</span>";
            btn.disabled = false;
            btn.style.background = "var(--brand-green)";
            btn.style.boxShadow = "0 4px 14px rgba(104, 169, 62, 0.3)";
        }
    </script>
</body>
</html>