<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My World - Inglés para Niños</title>
    <meta name="description" content="La app donde tu hijo aprende inglés escribiendo y jugando. Acceso de por vida.">
    
    <link rel="preload" as="image" href="assets/hero-app.webp">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/twemoji.min.js"></script>

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

        /* FASE 5: Reset & Tipografía Fluida */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { overflow-x: hidden; width: 100%; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: var(--text-main); line-height: 1.6; background-color: var(--white); }
        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; }
        img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

        /* Clases Globales Responsivas */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; width: 100%; box-sizing: border-box;}
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .flex-container { display: flex; flex-wrap: wrap; gap: 20px; align-items: center; }
        .text-center { text-align: center; }
        
        /* Botón Fluido */
        .btn { display: inline-block; background: var(--brand-green); color: var(--white); font-weight: 700; padding: clamp(12px, 2vw, 16px) clamp(20px, 4vw, 32px); border-radius: 50px; text-align: center; transition: background 0.3s, transform 0.2s; border: none; cursor: pointer; font-size: clamp(0.95rem, 2vw, 1.1rem); box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); word-wrap: break-word; max-width: 100%; box-sizing: border-box; }
        .btn:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        
        .underline { position: relative; white-space: nowrap; z-index: 1; }
        .underline::after { content: ''; position: absolute; left: 0; bottom: 8%; width: 100%; height: 35%; background-color: var(--brand-orange); z-index: -1; opacity: 0.6; }

        /* Navbar */
        header { padding: 15px 0; background: var(--white); position: relative; z-index: 10; box-shadow: 0 1px 10px rgba(0,0,0,0.03); }
        .nav-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .logo img { height: clamp(35px, 5vw, 45px); width: auto; max-width: 100%; }
        .nav-links { display: flex; align-items: center; gap: clamp(10px, 3vw, 20px); flex-wrap: wrap; justify-content: center; }
        .login-link { color: var(--brand-blue); font-weight: 700; font-size: 0.95rem; }

        /* Hero Section */
        .hero { background: var(--bg-light); padding: clamp(40px, 8vw, 70px) 0 clamp(50px, 10vw, 90px); overflow: hidden; }
        .hero-text h1 { font-size: clamp(2rem, 6vw, 3.5rem); font-weight: 800; line-height: 1.1; color: var(--brand-blue); margin-bottom: 20px; letter-spacing: -1px; }
        .hero-text p { font-size: clamp(1rem, 2.5vw, 1.25rem); color: var(--text-muted); margin-bottom: 30px; }
        .hero-price { font-size: clamp(0.9rem, 2vw, 1.1rem); font-weight: 600; color: var(--brand-blue); margin-top: 15px; display: block; }
        .hero-image-wrapper img { border-radius: 20px; box-shadow: 0 20px 40px rgba(28, 61, 106, 0.1); transform: rotate(2deg); transition: transform 0.5s; width: 100%; max-width: 100%; }
        .hero-image-wrapper img:hover { transform: rotate(0deg); }

        /* Agitation & Benefits */
        .agitation, .features { padding: clamp(50px, 8vw, 80px) 0; background: var(--white); }
        .section-title { font-size: clamp(1.8rem, 4vw, 2.2rem); color: var(--brand-blue); margin-bottom: 40px; font-weight: 800; letter-spacing: -0.5px; }
        .card-simple { padding: clamp(20px, 4vw, 30px); text-align: center; border: 1px solid #E2E8F0; border-radius: 12px; height: 100%; }
        .card-simple h4 { color: #E53E3E; margin-bottom: 15px; font-size: clamp(1.1rem, 2vw, 1.2rem); }
        .card-simple p { font-size: clamp(0.95rem, 2vw, 1rem); }

        .benefits { background: var(--brand-blue); color: var(--white); padding: clamp(50px, 8vw, 80px) 0; }
        .benefits .section-title { color: var(--white); }
        .benefit-item { text-align: center; }
        .benefit-icon { width: 64px; height: 64px; margin: 0 auto 20px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .benefit-item h3 { font-size: clamp(1.1rem, 2.5vw, 1.3rem); margin-bottom: 10px; color: var(--brand-lblue); }
        .benefit-item p { font-size: clamp(0.9rem, 2vw, 0.95rem); color: rgba(255,255,255,0.9); }

        /* Features Zig-Zag */
        .feature-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: clamp(30px, 5vw, 50px); align-items: center; margin-bottom: clamp(40px, 8vw, 80px); }
        .feature-row:nth-child(even) .feature-text { order: -1; }
        .feature-text h2 { font-size: clamp(1.6rem, 4vw, 2rem); color: var(--brand-blue); margin-bottom: 20px; }
        .feature-image img { border-radius: 12px; box-shadow: 0 10px 30px rgba(28, 61, 106, 0.08); width: 100%; }

        /* Checkout */
        .checkout-section { background: var(--bg-light); padding: clamp(50px, 8vw, 80px) 0; text-align: center; }
        .checkout-script-font { font-family: "Georgia", serif; font-style: italic; font-size: clamp(1.8rem, 5vw, 2.5rem); color: var(--brand-blue); margin-bottom: 30px; }
        .checkout-box { max-width: 500px; margin: 0 auto; background: var(--white); padding: clamp(20px, 5vw, 40px); border-radius: 16px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: center; border-top: 5px solid var(--brand-green); width: 100%; box-sizing: border-box; }
        .price-tag { text-align: center; font-size: clamp(2rem, 6vw, 2.5rem); font-weight: 800; color: var(--brand-blue); margin-bottom: 25px; }
        .price-tag span { font-size: clamp(0.9rem, 2vw, 1rem); color: var(--text-muted); font-weight: normal; display: block; }
        .btn-pay { width: 100%; font-size: clamp(1rem, 2.5vw, 1.1rem); padding: 16px; margin-top: 10px; }

        /* Forms */
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: var(--brand-blue); }
        .form-control { width: 100%; padding: 14px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; box-sizing: border-box; max-width: 100%; }
        .form-control:focus { border-color: var(--brand-lblue); outline: none; box-shadow: 0 0 0 3px rgba(92, 178, 228, 0.2); }
        
        /* Modal Fluido */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(28, 61, 106, 0.7); z-index: 1000; justify-content: center; align-items: center; padding: 15px; opacity: 0; transition: opacity 0.3s ease; overflow-y: auto; box-sizing: border-box; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-content { background: var(--white); width: 100%; max-width: 450px; padding: clamp(20px, 5vw, 40px); border-radius: 16px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border-top: 5px solid var(--brand-green); margin: auto; box-sizing: border-box; }
        .modal-close { position: absolute; top: 10px; right: 15px; font-size: 2rem; color: var(--text-muted); cursor: pointer; border: none; background: none; }

        /* Parches Responsivos Extra para Tostadoras */
        @media (max-width: 768px) {
            .feature-row:nth-child(even) .feature-text { order: 0; }
            .hero-image-wrapper img { transform: none; }
        }
        @media (max-width: 480px) {
            .nav-inner { flex-direction: column; justify-content: center; text-align: center; }
            .nav-links { width: 100%; justify-content: center; flex-direction: column; gap: 15px; }
            .btn { width: 100%; }
            .logo img { max-width: 200px; }
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
        <div class="container grid-container" style="align-items: center;">
            <div class="hero-text text-center">
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
        <h2 class="section-title text-center">¿Ya probaste de todo, <span class="underline">verdad</span>? 😫</h2>
        <div class="grid-container">
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
            <h2 class="section-title text-center">Por qué nuestro método <span class="underline">funciona</span> 🚀</h2>
            <div class="grid-container" style="margin-top: 40px;">
                <div class="benefit-item">
                    <div class="benefit-icon">✍️</div>
                    <h3>Escribe Antes de Jugar</h3>
                    <p>El "peaje cognitivo". Tienen que escribir la traducción correcta para desbloquear el minijuego.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🗣️</div>
                    <h3>Voz Nativa y Mnemotecnias</h3>
                    <p>Asociamos palabras en inglés con sonidos divertidos en español con pronunciación 100% nativa.</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">💳</div>
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
                <h2>Progreso que puedes <span class="underline">ver y medir</span> 📈</h2>
                <p>Nuestra plataforma te entrega un diploma digital diario indicándote exactamente qué 5 palabras aprendió tu hijo hoy. Perfecto para preguntárselas en la cena y validar su avance.</p>
            </div>
        </div>
        <div class="feature-row">
            <div class="feature-text">
                <h2>Juegos adaptados <span class="underline">sin estrés</span> 🎮</h2>
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
                
                <form id="bottom-payment-form">
                    <div class="form-group">
                        <label for="bottom_child_name">Nombre de tu hijo/a</label>
                        <input type="text" id="bottom_child_name" class="form-control" placeholder="Ej: Mateo">
                    </div>
                    <div class="form-group">
                        <label for="bottom_parent_phone">Tu número de WhatsApp</label>
                        <input type="tel" id="bottom_parent_phone" class="form-control" placeholder="Ej: 999888777">
                    </div>
                    
                    <button type="submit" id="btn-comprar-bottom" class="btn btn-pay">Comprar Acceso Seguro 🔒</button>
                    
                    <div class="guarantee" style="margin-top: 15px; color: var(--text-muted); font-size: 0.85rem;">
                        Pago seguro procesado por Culqi
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="modal-overlay" id="checkoutModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">&times;</button>
            <div class="price-tag" style="margin-bottom: 20px; font-size: clamp(1.8rem, 5vw, 2rem);">
                S/14.99 <span style="font-size: 1rem;">Pago único. Acceso de por vida.</span>
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
                
                <button type="submit" id="btn-comprar-modal" class="btn btn-pay">Procesar Pago Seguro 🔒</button>
                
                <div class="guarantee" style="margin-top: 15px; color: var(--text-muted); font-size: 0.85rem; text-align: center;">
                    Encriptado y procesado por Culqi
                </div>
            </form>
        </div>
    </div>

    <script>
        // FASE 5: Parseo Twemoji de la Landing
        document.addEventListener("DOMContentLoaded", function() {
            twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
        });

        function procesarPago(childName, parentPhone, btnElement) {
            const originalText = btnElement.innerText;
            btnElement.innerText = "Procesando...";
            btnElement.disabled = true;

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
                    btnElement.innerText = originalText;
                    btnElement.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Hubo un problema procesando el pago. Revisa tu conexión.");
                btnElement.innerText = originalText;
                btnElement.disabled = false;
            });
        }

        const modal = document.getElementById('checkoutModal');
        const triggers = document.querySelectorAll('.trigger-modal');
        const closeBtn = document.getElementById('closeModal');

        triggers.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => { modal.classList.remove('active'); });
        window.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });

        document.getElementById('payment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const childName = document.getElementById('child_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();
            if(childName === '' || parentPhone === '') return alert("Por favor, ingresa los datos.");
            procesarPago(childName, parentPhone, document.getElementById('btn-comprar-modal'));
        });

        document.getElementById('bottom-payment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const childName = document.getElementById('bottom_child_name').value.trim();
            const parentPhone = document.getElementById('bottom_parent_phone').value.trim();
            if (childName === '' || parentPhone === '') {
                modal.classList.add('active');
            } else {
                procesarPago(childName, parentPhone, document.getElementById('btn-comprar-bottom'));
            }
        });
    </script>
</body>
</html>