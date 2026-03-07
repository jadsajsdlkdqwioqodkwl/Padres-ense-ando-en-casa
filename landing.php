<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inglés para Niños - Aprende 5 Palabras al Día</title>
    
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'TU_PIXEL_ID'); // REEMPLAZA CON TU PIXEL ID
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=TU_PIXEL_ID&ev=PageView&noscript=1"/></noscript>
    
    <style>
        :root { --primary: #6c5ced; --secondary: #ff9f43; --dark: #2d3436; --light: #f8f9fa; --success: #2ed573; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: var(--light); color: var(--dark); line-height: 1.6; }
        
        /* Navbar Landing */
        .landing-nav { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 24px; font-weight: bold; color: var(--primary); text-decoration: none; }
        .login-btn { background: transparent; border: 2px solid var(--primary); color: var(--primary); padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .login-btn:hover { background: var(--primary); color: white; }

        /* Hero Section */
        .hero { display: flex; flex-wrap: wrap; align-items: center; padding: 50px 5%; min-height: 80vh; background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); }
        .hero-text { flex: 1; min-width: 300px; padding-right: 20px; }
        .hero-text h1 { font-size: 3rem; color: var(--primary); margin-bottom: 20px; line-height: 1.2; }
        .hero-text p { font-size: 1.2rem; color: #555; margin-bottom: 30px; }
        
        /* Formulario de Checkout */
        .checkout-box { flex: 1; min-width: 300px; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .checkout-box h3 { text-align: center; color: var(--dark); margin-bottom: 20px; font-size: 1.5rem; }
        .form-group { margin-bottom: 20px; }
        .form-control { width: 100%; padding: 15px; border: 2px solid #eee; border-radius: 10px; font-size: 16px; transition: 0.3s; }
        .form-control:focus { border-color: var(--primary); outline: none; }
        .btn-pay { width: 100%; background: var(--success); color: white; border: none; padding: 18px; border-radius: 10px; font-size: 20px; font-weight: bold; cursor: pointer; box-shadow: 0 6px 0 #218c74; transition: 0.2s; margin-top: 10px; }
        .btn-pay:active:not(:disabled) { transform: translateY(4px); box-shadow: 0 2px 0 #218c74; }
        .btn-pay:disabled { background: #95a5a6; box-shadow: none; cursor: not-allowed; }
        
        .price-tag { text-align: center; font-size: 2rem; font-weight: bold; color: var(--secondary); margin-bottom: 15px; }
        .guarantee { text-align: center; font-size: 0.9rem; color: #888; margin-top: 15px; }

        /* Beneficios */
        .benefits { padding: 60px 5%; text-align: center; background: white; }
        .benefits h2 { font-size: 2.5rem; margin-bottom: 40px; color: var(--primary); }
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .benefit-card { padding: 30px; background: var(--light); border-radius: 15px; transition: 0.3s; }
        .benefit-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .benefit-icon { font-size: 50px; margin-bottom: 15px; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-text h1 { font-size: 2.2rem; }
            .hero { flex-direction: column; text-align: center; padding: 30px 5%; }
            .hero-text { padding-right: 0; margin-bottom: 30px; }
            .checkout-box { width: 100%; padding: 25px; }
        }
    </style>
</head>
<body>

    <nav class="landing-nav">
        <a href="#" class="logo">🚀 Mi Mundo en Inglés</a>
        <a href="login.php" class="login-btn">Ya soy alumno</a>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Que tu hijo/a aprenda 5 palabras de inglés al día sin aburrirse</h1>
            <p>Un sistema híbrido: Escucha la pronunciación nativa, anota las mnemotecnias en su cuaderno y juega minijuegos interactivos. ¡Al final del día, te entregará un diploma con lo aprendido!</p>
            <ul style="list-style: none; margin-bottom: 20px; font-size: 1.1rem; color: #444;">
                <li style="margin-bottom: 10px;">✅ <strong>Mnemotecnias divertidas</strong> para una memoria rápida.</li>
                <li style="margin-bottom: 10px;">✅ <strong>Voz Nativa (Screenreader)</strong> para acento perfecto.</li>
                <li style="margin-bottom: 10px;">✅ <strong>Diplomas diarios</strong> para que le preguntes en la cena.</li>
            </ul>
        </div>

        <div class="checkout-box">
            <h3>¡Empieza la aventura hoy!</h3>
            <div class="price-tag">S/ 39.00 <span style="font-size: 1rem; color: #666; font-weight: normal;">/ mes</span></div>
            
            <div class="form-group">
                <input type="text" id="child_name" class="form-control" placeholder="Nombre de tu hijo/a" required>
            </div>
            <div class="form-group">
                <input type="tel" id="parent_phone" class="form-control" placeholder="Tu número de WhatsApp (Ej: 999888777)" required>
            </div>
            
            <button id="btn-comprar" class="btn-pay">Pagar y Empezar ▶</button>
            <div class="guarantee">🔒 Simulador de Pago Activo (Modo Prueba)</div>
        </div>
    </section>

    <section class="benefits">
        <h2>¿Por qué este método funciona?</h2>
        <div class="grid-3">
            <div class="benefit-card">
                <div class="benefit-icon">🧠</div>
                <h3>Método Mnemotécnico</h3>
                <p>Asociamos palabras en inglés con sonidos divertidos en español para que nunca las olviden.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🎮</div>
                <h3>Juegos sin Estrés</h3>
                <p>Ellos eligen qué 5 palabras quieren aprender. Los juegos se adaptan automáticamente a su elección.</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">👨‍👩‍👧</div>
                <h3>Conexión Familiar</h3>
                <p>El diploma diario te indica exactamente qué palabras debes preguntarle durante la cena.</p>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('btn-comprar').addEventListener('click', function (e) {
            e.preventDefault();
            
            const childName = document.getElementById('child_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();

            if(childName === '' || parentPhone === '') {
                alert("Por favor, ingresa el nombre de tu hijo y tu WhatsApp antes de simular el pago.");
                return;
            }

            const btn = document.getElementById('btn-comprar');
            btn.innerText = "Simulando pago...";
            btn.disabled = true;

            fetch('app/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    token: "token_falso_de_prueba", 
                    child_name: childName, 
                    parent_phone: parentPhone 
                })
            })
            .then(response => {
                if (!response.ok) throw new Error("Error de red");
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    alert(data.message); 
                    window.location.href = 'dashboard.php'; 
                } else {
                    alert("Error: " + data.message);
                    btn.innerText = "Pagar y Empezar ▶";
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Hubo un problema procesando la respuesta. Revisa la consola.");
                btn.innerText = "Pagar y Empezar ▶";
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>