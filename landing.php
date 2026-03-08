<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My World - Inglés para Niños</title>
    <meta name="description" content="La app donde tu hijo aprende inglés escribiendo y jugando. Un solo pago, acceso de por vida.">
    
    <link rel="preload" as="image" href="assets/hero-app.webp">

    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'TU_PIXEL_ID');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=TU_PIXEL_ID&ev=PageView&noscript=1"/></noscript>

    <style>
        :root {
            --primary-dark: #2A4858; /* Inspirado en el azul oscuro de Zola */
            --accent: #00B2B2; /* Botones vibrantes turquesa */
            --accent-hover: #009595;
            --bg-light: #F2F7F9; /* Fondo pastel suave */
            --text-main: #333333;
            --text-muted: #666666;
            --white: #FFFFFF;
        }

        /* Reset & Tipografía de Sistema (Rendimiento 100%) */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: var(--text-main); line-height: 1.6; background-color: var(--white); overflow-x: hidden; }
        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; }

        /* Componentes Globales */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; }
        .btn { display: inline-block; background: var(--accent); color: var(--white); font-weight: 700; padding: 14px 32px; border-radius: 50px; text-align: center; transition: background 0.3s, transform 0.2s; border: none; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: var(--accent-hover); transform: translateY(-2px); }
        .text-center { text-align: center; }
        
        /* El famoso Subrayado Estilo Zola */
        .underline { position: relative; white-space: nowrap; z-index: 1; }
        .underline::after { content: ''; position: absolute; left: 0; bottom: 10%; width: 100%; height: 35%; background-color: #FFD166; z-index: -1; opacity: 0.8; }

        /* Navbar */
        header { padding: 20px 0; background: var(--white); position: relative; z-index: 10; }
        .nav-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo-svg { height: 40px; width: auto; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .login-link { color: var(--text-muted); font-weight: 600; font-size: 0.9rem; }

        /* Hero Section */
        .hero { background: var(--bg-light); padding: 60px 0 80px; overflow: hidden; }
        .hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .hero-text h1 { font-size: clamp(2.5rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.1; color: var(--primary-dark); margin-bottom: 20px; letter-spacing: -1px; }
        .hero-text p { font-size: 1.25rem; color: var(--text-muted); margin-bottom: 30px; }
        .hero-price { font-size: 1.1rem; font-weight: 600; color: var(--primary-dark); margin-top: 15px; display: block; }
        .hero-image-wrapper { position: relative; }
        .hero-image-wrapper img { border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); transform: rotate(2deg); transition: transform 0.5s; }
        .hero-image-wrapper img:hover { transform: rotate(0deg); }

        /* Agitation Section (Pattern Interrupt) */
        .agitation { padding: 80px 0; background: var(--white); }
        .section-title { font-size: 2.2rem; color: var(--primary-dark); margin-bottom: 50px; font-weight: 800; letter-spacing: -0.5px; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card-simple { padding: 30px; text-align: center; border: 1px solid #E2E8F0; border-radius: 12px; }
        .card-simple h4 { color: #E53E3E; margin-bottom: 15px; font-size: 1.2rem; }

        /* Benefits (Dark Section Zola Style) */
        .benefits { background: var(--primary-dark); color: var(--white); padding: 80px 0; }
        .benefits .section-title { color: var(--white); }
        .benefits .underline::after { background-color: #FF9F43; } /* Naranja suave para oscuros */
        .benefits-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-top: 40px; }
        .benefit-item { text-align: center; }
        .benefit-icon { width: 64px; height: 64px; margin: 0 auto 20px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .benefit-item h3 { font-size: 1.3rem; margin-bottom: 10px; }
        .benefit-item p { font-size: 0.95rem; color: rgba(255,255,255,0.8); }

        /* Zig-Zag Features */
        .features { padding: 80px 0; background: var(--white); }
        .feature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 80px; }
        .feature-row:nth-child(even) .feature-text { order: -1; }
        .feature-text h2 { font-size: 2rem; color: var(--primary-dark); margin-bottom: 20px; }
        .feature-image img { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

        /* Checkout / Bottom CTA */
        .checkout-section { background: var(--bg-light); padding: 80px 0; text-align: center; }
        .checkout-script-font { font-family: "Georgia", serif; font-style: italic; font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 30px; }
        .checkout-box { max-width: 500px; margin: 0 auto; background: var(--white); padding: 40px; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); text-align: left; }
        .price-tag { text-align: center; font-size: 2.5rem; font-weight: 800; color: var(--primary-dark); margin-bottom: 25px; }
        .price-tag span { font-size: 1rem; color: var(--text-muted); font-weight: normal; display: block; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: var(--primary-dark); }
        .form-control { width: 100%; padding: 14px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { border-color: var(--accent); outline: none; }
        .btn-pay { width: 100%; font-size: 1.1rem; padding: 16px; margin-top: 10px; }
        .btn-pay:disabled { background: #CBD5E0; cursor: not-allowed; transform: none; }
        .guarantee { text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 15px; display: flex; justify-content: center; align-items: center; gap: 5px; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-grid, .feature-row { grid-template-columns: 1fr; text-align: center; gap: 30px; }
            .hero-image-wrapper img { transform: none; }
            .feature-row:nth-child(even) .feature-text { order: 0; }
            .checkout-script-font { font-size: 2rem; }
            .checkout-box { padding: 25px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container nav-inner">
            <a href="#" class="logo" aria-label="Inicio">
                <svg class="logo-svg" viewBox="0 0 200 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="200" height="50" fill="#E2E8F0" rx="8"/>
                    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#64748B" font-family="sans-serif" font-weight="bold">LOGO.SVG AQUÍ</text>
                </svg>
            </a>
            <div class="nav-links">
                <a href="login.php" class="login-link">LOG IN</a>
                <a href="#checkout" class="btn">Empezar Ahora</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Aprende inglés jugando, pero aprendiendo de <span class="underline">verdad</span>.</h1>
                <p>La app donde los niños deben escribir antes de jugar. Olvídate de los pagos mensuales y las suscripciones escondidas.</p>
                <a href="#checkout" class="btn">Crear cuenta para tu hijo</a>
                <span class="hero-price">Un solo pago de $14.99 USD. Acceso de por vida.</span>
            </div>
            <div class="hero-image-wrapper">
                <img src="assets/hero-app.webp" width="600" height="450" alt="Niño usando la app de My World" loading="eager">
            </div>
        </div>
    </section>

    <section class="agitation container">
        <h2 class="section-title text-center">¿Ya probaste de todo, <span class="underline">verdad</span>?</h2>
        <div class="cards-grid">
            <div class="card-simple">
                <h4>Apps Populares</h4>
                <p>Solo presionan colores o tocan dibujitos hasta que aciertan por descarte, sin aprender a formar oraciones.</p>
            </div>
            <div class="card-simple">
                <h4>Videos de YouTube</h4>
                <p>Los mantienen hipnotizados por horas, pero el aprendizaje es 100% pasivo. Entienden, pero no hablan.</p>
            </div>
            <div class="card-simple">
                <h4>Academias Tradicionales</h4>
                <p>S/200 al mes, libros nuevos cada ciclo y clases por Zoom con otros 30 alumnos donde apenas participan.</p>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="container">
            <h2 class="section-title text-center">Por qué nuestro método <span class="underline">funciona</span></h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <h3>Escribe Antes de Jugar</h3>
                    <p>El "peaje cognitivo". Tienen que escribir la traducción correcta para desbloquear el minijuego.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h3>Voz Nativa y Mnemotecnias</h3>
                    <p>Asociamos palabras en inglés con sonidos divertidos en español con pronunciación 100% nativa.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h3>Cero Mensualidades</h3>
                    <p>Las suscripciones recurrentes son estresantes. Pagas una sola vez y lo usan para siempre.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="features container">
        <div class="feature-row">
            <div class="feature-image">
                <img src="assets/dashboard-mockup.webp" loading="lazy" width="500" height="400" alt="Dashboard de progreso para padres">
            </div>
            <div class="feature-text">
                <h2>Progreso que puedes <span class="underline">ver y medir</span></h2>
                <p>Nuestra plataforma te entrega un diploma digital diario indicándote exactamente qué 5 palabras aprendió tu hijo hoy. Perfecto para preguntárselas en la cena y validar su avance.</p>
            </div>
        </div>
        <div class="feature-row">
            <div class="feature-text">
                <h2>Juegos adaptados <span class="underline">sin estrés</span></h2>
                <p>Ellos mismos eligen qué categoría de palabras quieren aprender hoy. Los minijuegos de recompensa se adaptan automáticamente a su elección para mantener la motivación al máximo.</p>
            </div>
            <div class="feature-image">
                <img src="assets/game-mockup.webp" loading="lazy" width="500" height="400" alt="Minijuegos educativos de recompensa">
            </div>
        </div>
    </section>

    <section id="checkout" class="checkout-section">
        <div class="container">
            <h2 class="checkout-script-font">Hagamos esto fácil y divertido.</h2>
            
            <div class="checkout-box">
                <div class="price-tag">
                    $14.99 <span>Pago único. Acceso de por vida.</span>
                </div>
                
                <form id="payment-form">
                    <div class="form-group">
                        <label for="child_name">Nombre de tu hijo/a</label>
                        <input type="text" id="child_name" class="form-control" placeholder="Ej: Mateo" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_phone">Tu número de WhatsApp</label>
                        <input type="tel" id="parent_phone" class="form-control" placeholder="Ej: 999888777" required>
                    </div>
                    
                    <button type="submit" id="btn-comprar" class="btn btn-pay">Comprar Acceso Seguro</button>
                    
                    <div class="guarantee">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Pago seguro procesado por Culqi
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('payment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            
            const childName = document.getElementById('child_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();

            if(childName === '' || parentPhone === '') {
                alert("Por favor, ingresa el nombre de tu hijo y tu WhatsApp.");
                return;
            }

            const btn = document.getElementById('btn-comprar');
            btn.innerText = "Procesando...";
            btn.disabled = true;

            // Conservando la conexión exacta con tu backend actual
            fetch('app/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    token: "token_falso_de_prueba", // Aquí irá tu token real de Culqi
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
                    btn.innerText = "Comprar Acceso Seguro";
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Hubo un problema procesando el pago. Revisa tu conexión.");
                btn.innerText = "Comprar Acceso Seguro";
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>