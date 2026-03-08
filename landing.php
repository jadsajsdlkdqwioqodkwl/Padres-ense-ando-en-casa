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
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --brand-lblue: #5CB2E4;  
            
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --text-muted: #64748B;
            --white: #FFFFFF;
        }

        /* Reset & Tipografía de Sistema */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: var(--text-main); line-height: 1.6; background-color: var(--white); overflow-x: hidden; }
        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; }

        /* Componentes Globales */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; }
        
        /* Botón Verde Marca */
        .btn { display: inline-block; background: var(--brand-green); color: var(--white); font-weight: 700; padding: 14px 32px; border-radius: 50px; text-align: center; transition: background 0.3s, transform 0.2s; border: none; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); }
        .btn:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .text-center { text-align: center; }
        
        /* El Subrayado Zola */
        .underline { position: relative; white-space: nowrap; z-index: 1; }
        .underline::after { content: ''; position: absolute; left: 0; bottom: 8%; width: 100%; height: 35%; background-color: var(--brand-orange); z-index: -1; opacity: 0.6; }

        /* Navbar */
        header { padding: 15px 0; background: var(--white); position: relative; z-index: 10; box-shadow: 0 1px 10px rgba(0,0,0,0.03); }
        .nav-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo img { height: 45px; width: auto; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .login-link { color: var(--brand-blue); font-weight: 700; font-size: 0.95rem; }

        /* Hero Section */
        .hero { background: var(--bg-light); padding: 70px 0 90px; overflow: hidden; }
        .hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .hero-text h1 { font-size: clamp(2.5rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.1; color: var(--brand-blue); margin-bottom: 20px; letter-spacing: -1px; }
        .hero-text p { font-size: 1.25rem; color: var(--text-muted); margin-bottom: 30px; }
        .hero-price { font-size: 1.1rem; font-weight: 600; color: var(--brand-blue); margin-top: 15px; display: block; }
        .hero-image-wrapper { position: relative; }
        .hero-image-wrapper img { border-radius: 20px; box-shadow: 0 20px 40px rgba(28, 61, 106, 0.1); transform: rotate(2deg); transition: transform 0.5s; }
        .hero-image-wrapper img:hover { transform: rotate(0deg); }

        /* Agitation Section */
        .agitation { padding: 80px 0; background: var(--white); }
        .section-title { font-size: 2.2rem; color: var(--brand-blue); margin-bottom: 50px; font-weight: 800; letter-spacing: -0.5px; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card-simple { padding: 30px; text-align: center; border: 1px solid #E2E8F0; border-radius: 12px; }
        .card-simple h4 { color: #E53E3E; margin-bottom: 15px; font-size: 1.2rem; }

        /* Benefits */
        .benefits { background: var(--brand-blue); color: var(--white); padding: 80px 0; }
        .benefits .section-title { color: var(--white); }
        .benefits .underline::after { opacity: 0.8;} 
        .benefits-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-top: 40px; }
        .benefit-item { text-align: center; }
        .benefit-icon { width: 64px; height: 64px; margin: 0 auto 20px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .benefit-item h3 { font-size: 1.3rem; margin-bottom: 10px; color: var(--brand-lblue); }
        .benefit-item p { font-size: 0.95rem; color: rgba(255,255,255,0.9); }

        /* Zig-Zag Features */
        .features { padding: 80px 0; background: var(--white); }
        .feature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 80px; }
        .feature-row:nth-child(even) .feature-text { order: -1; }
        .feature-text h2 { font-size: 2rem; color: var(--brand-blue); margin-bottom: 20px; }
        .feature-image img { border-radius: 12px; box-shadow: 0 10px 30px rgba(28, 61, 106, 0.08); }

        /* Checkout Bottom Section */
        .checkout-section { background: var(--bg-light); padding: 80px 0; text-align: center; }
        .checkout-script-font { font-family: "Georgia", serif; font-style: italic; font-size: 2.5rem; color: var(--brand-blue); margin-bottom: 30px; }
        .checkout-box { max-width: 500px; margin: 0 auto; background: var(--white); padding: 40px; border-radius: 16px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center; border-top: 5px solid var(--brand-green); }
        .price-tag { text-align: center; font-size: 2.5rem; font-weight: 800; color: var(--brand-blue); margin-bottom: 25px; }
        .price-tag span { font-size: 1rem; color: var(--text-muted); font-weight: normal; display: block; }
        .btn-pay { width: 100%; font-size: 1.1rem; padding: 16px; margin-top: 10px; }
        .guarantee { text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 15px; display: flex; justify-content: center; align-items: center; gap: 5px; }

        /* Modal Styles */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(28, 61, 106, 0.7); z-index: 1000; justify-content: center; align-items: center; padding: 20px; opacity: 0; transition: opacity 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; }
        /* AÑADIDO: max-height y overflow-y para que sea scrolleable en móviles pequeños/horizontales */
        .modal-content { background: var(--white); width: 100%; max-width: 450px; padding: 40px; border-radius: 16px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border-top: 5px solid var(--brand-green); transform: translateY(-20px); transition: transform 0.3s ease; max-height: 90vh; overflow-y: auto; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        .modal-close { position: absolute; top: 15px; right: 20px; font-size: 1.8rem; color: var(--text-muted); cursor: pointer; border: none; background: none; transition: color 0.2s; }
        .modal-close:hover { color: #E53E3E; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: var(--brand-blue); }
        .form-control { width: 100%; padding: 14px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-control:focus { border-color: var(--brand-lblue); outline: none; box-shadow: 0 0 0 3px rgba(92, 178, 228, 0.2); }
        .btn-pay:disabled { background: #CBD5E0; cursor: not-allowed; transform: none; box-shadow: none; }

        /* Responsive Global */
        @media (max-width: 768px) {
            .hero-grid, .feature-row { grid-template-columns: 1fr; text-align: center; gap: 30px; }
            .hero-image-wrapper img { transform: none; }
            .feature-row:nth-child(even) .feature-text { order: 0; }
            .checkout-script-font { font-size: 2rem; }
            .checkout-box, .modal-content { padding: 25px; }
        }

        /* AÑADIDO: Ajustes específicos para móviles pequeños (Navbar) */
        @media (max-width: 480px) {
            .nav-inner { flex-direction: column; gap: 15px; }
            .btn { padding: 12px 24px; font-size: 0.95rem; }
            .nav-links { width: 100%; justify-content: space-evenly; }
        }
    </style>
</head>
<body>

    <header>
        <div class="container nav-inner">
            <a href="#" class="logo" aria-label="Inicio">
                <img src="assets/logo-myworld.svg" alt="My World Logo" width="240" height="60">
            </a>
            <div class="nav-links">
                <a href="login.php" class="login-link">LOG IN</a>
                <a href="#" class="btn trigger-modal">Empezar Ahora</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Aprende inglés jugando, pero aprendiendo de <span class="underline">verdad</span>.</h1>
                <p>La app donde los niños deben escribir antes de jugar. Olvídate de los pagos mensuales y las suscripciones escondidas.</p>
                <button class="btn trigger-modal">Crear cuenta para tu hijo</button>
                <span class="hero-price">Un solo pago de S/14.99. Acceso de por vida.</span>
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
                <p>Mensualidades caras, libros nuevos cada ciclo y clases por Zoom con otros alumnos donde apenas participan.</p>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="container">
            <h2 class="section-title text-center">Por qué nuestro método <span class="underline">funciona</span></h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--brand-orange)" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <h3>Escribe Antes de Jugar</h3>
                    <p>El "peaje cognitivo". Tienen que escribir la traducción correcta para desbloquear el minijuego.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--brand-orange)" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h3>Voz Nativa y Mnemotecnias</h3>
                    <p>Asociamos palabras en inglés con sonidos divertidos en español con pronunciación 100% nativa.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--brand-orange)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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

    <section class="checkout-section">
        <div class="container">
            <h2 class="checkout-script-font">El inglés que tu hijo sí quiere aprender.</h2>
            
            <div class="checkout-box">
                <div class="price-tag">
                    S/14.99 <span>Pago único. Acceso de por vida.</span>
                </div>
                
                <button type="button" class="btn btn-pay trigger-modal">Comprar Acceso Seguro</button>
                
                <div class="guarantee">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Pago seguro procesado por Culqi
                </div>
            </div>
        </div>
    </section>

    <div class="modal-overlay" id="checkoutModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">&times;</button>
            <div class="price-tag" style="margin-bottom: 20px; font-size: 2rem;">
                S/14.99 <span>Pago único. Acceso de por vida.</span>
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
                
                <button type="submit" id="btn-comprar" class="btn btn-pay">Procesar Pago Seguro</button>
                
                <div class="guarantee">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Encriptado y procesado por Culqi
                </div>
            </form>
        </div>
    </div>

    <script>
        // Lógica del Modal
        const modal = document.getElementById('checkoutModal');
        const triggers = document.querySelectorAll('.trigger-modal');
        const closeBtn = document.getElementById('closeModal');

        triggers.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        // Lógica del Formulario
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
                    btn.innerText = "Procesar Pago Seguro";
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Hubo un problema procesando el pago. Revisa tu conexión.");
                btn.innerText = "Procesar Pago Seguro";
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>