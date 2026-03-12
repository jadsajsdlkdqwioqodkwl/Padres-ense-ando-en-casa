<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>My World - El Inglés que Asegura el Futuro de tu Hijo</title>
    <meta name="description" content="Asegura sus oportunidades futuras con inglés fluido sin pagar mensualidades.">
    
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: 'unsafe-inline' 'unsafe-eval' data: blob:; img-src 'self' https: data:;">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://connect.facebook.net">
    <link rel="preload" as="image" href="assets/hero-app.webp" fetchpriority="high">

    <script src="https://unpkg.com/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous" defer></script>

    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    
    fbq('init', '1602561284224693'); 
    fbq('track', 'PageView');
    
    // EVENTO: ViewContent al cargar la landing
    fbq('track', 'ViewContent', {
        content_name: 'My World - Acceso Vitalicio',
        content_category: 'Educación',
        value: 14.99,
        currency: 'PEN'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1602561284224693&ev=PageView&noscript=1"/></noscript>
    <style>
        :root {
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --brand-lblue: #5CB2E4;  
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --text-muted: #475569;
            --white: #FFFFFF;
            --red-alert: #E53E3E;
            --wave-color: #E8EEF2; 
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { overflow-x: hidden; width: 100%; scroll-behavior: smooth; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: var(--text-main); line-height: 1.6; background-color: var(--white); position: relative; }
        img { max-width: 100%; height: auto; display: block; }
        a { text-decoration: none; }
        
        /* Ciberseguridad UI: Evitar clicks fantasma en los SVGs de Twemoji */
        img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; width: 100%; box-sizing: border-box;}
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .flex-container { display: flex; flex-wrap: wrap; gap: 20px; align-items: center; }
        .text-center { text-align: center; }
        
        .main-header { display: flex; justify-content: center; align-items: center; padding: 20px 5%; background-color: var(--white); box-shadow: 0 4px 15px rgba(28, 61, 106, 0.05); position: relative; z-index: 10; width: 100%; }
        .main-header img { height: clamp(45px, 6vw, 65px); width: auto; max-width: 100%; }

        .btn { display: inline-flex; flex-direction: column; align-items: center; justify-content: center; background: var(--brand-green); color: var(--white); font-weight: 800; padding: clamp(14px, 2vw, 18px) clamp(24px, 4vw, 36px); border-radius: 50px; text-align: center; transition: all 0.3s ease; border: none; cursor: pointer; font-size: clamp(1rem, 2vw, 1.15rem); box-shadow: 0 4px 14px rgba(104, 169, 62, 0.4); word-wrap: break-word; max-width: 100%; box-sizing: border-box; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn:hover { background: #579232; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(104, 169, 62, 0.5); }
        .btn-subtext { display: block; font-size: 0.75rem; font-weight: 500; text-transform: none; margin-top: 5px; opacity: 0.9; letter-spacing: 0; }
        
        .btn-back-modal { background: transparent; border: none; color: var(--text-muted); width: 100%; padding: 12px; margin-top: 5px; cursor: pointer; text-decoration: underline; font-size: 0.95rem; transition: color 0.3s; font-weight: 500; }
        .btn-back-modal:hover { color: var(--text-main); }

        .underline { position: relative; white-space: nowrap; z-index: 1; }
        .underline::after { content: ''; position: absolute; left: 0; bottom: 8%; width: 100%; height: 35%; background-color: var(--brand-orange); z-index: -1; opacity: 0.6; }
        .highlight { color: var(--brand-orange); }

        .wave-container-top { position: absolute; top: 0; left: 0; width: 100%; height: clamp(150px, 25vw, 350px); overflow: hidden; z-index: 0; pointer-events: none; }
        .wave-container-top svg { width: 100%; height: 100%; display: block; }
        .wave-container-bottom { width: 100%; overflow: hidden; line-height: 0; margin-top: -5px; background: var(--white); pointer-events: none; }
        .wave-container-bottom svg { width: 100%; height: auto; display: block; }

        .hero { position: relative; background: var(--white); padding: clamp(60px, 12vw, 100px) 0 clamp(60px, 10vw, 100px); overflow: hidden; }
        .hero-text h1 { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; line-height: 1.1; color: var(--brand-blue); margin-bottom: 25px; letter-spacing: -1px; }
        .hero-text p { font-size: clamp(1.1rem, 2.5vw, 1.3rem); color: var(--text-muted); margin-bottom: 20px; font-weight: 500; }
        
        .video-wrapper { position: relative; width: 100%; max-width: 600px; margin: 0 auto; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(28, 61, 106, 0.2); background: #000; border: 4px solid var(--white); transform: rotate(1deg); transition: transform 0.4s; aspect-ratio: 16/9; z-index: 2; }
        .video-wrapper:hover { transform: rotate(0deg); }
        .video-thumbnail { width: 100%; height: 100%; object-fit: cover; opacity: 1; display: block; }

        .section-padding { padding: clamp(60px, 8vw, 90px) 5%; }
        .section-title { font-size: clamp(2rem, 4vw, 2.5rem); color: var(--brand-blue); margin-bottom: 20px; font-weight: 800; letter-spacing: -0.5px; text-align: center; }
        .section-subtitle { text-align: center; font-size: 1.2rem; color: var(--text-muted); max-width: 800px; margin: 0 auto 50px; }

        .what-you-get { background: var(--brand-blue); color: var(--white); }
        .what-you-get .section-title { color: var(--white); }
        .what-you-get .section-subtitle { color: rgba(255,255,255,0.8); }
        .get-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .get-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 45px 30px 30px; position: relative; overflow: hidden; transition: transform 0.3s; }
        .get-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.08); }
        .get-card h4 { color: var(--brand-orange); font-size: 1.2rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; line-height: 1.4; }
        .get-card p { font-size: 0.95rem; color: rgba(255,255,255,0.9); }
        .get-badge { position: absolute; top: 12px; right: 15px; background: var(--brand-green); color: white; font-size: 0.7rem; font-weight: bold; padding: 4px 10px; border-radius: 50px; text-transform: uppercase; z-index: 2; }

        .problem-section { background: var(--white); padding: clamp(60px, 8vw, 80px) 5% clamp(30px, 4vw, 40px); }
        .problem-card { background: var(--bg-light); border: 1px solid #E2E8F0; border-radius: 16px; padding: clamp(20px, 5vw, 40px); margin: 0 auto; max-width: 800px; text-align: left; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); }
        .problem-list { list-style: none; padding: 0; margin: 0; }
        .problem-list li { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 18px; font-size: 1.05rem; color: var(--text-muted); }
        .problem-list li:last-child { margin-bottom: 0; }
        .problem-icon { color: #E53E3E; font-weight: 900; font-size: 1.2rem; line-height: 1.2; flex-shrink: 0; }
        .solution-card { background: #ECFCCB; border: 2px solid var(--brand-green); border-radius: 16px; padding: clamp(20px, 5vw, 30px); margin: 30px auto 0; max-width: 800px; box-shadow: 0 10px 25px rgba(104, 169, 62, 0.1); }
        .solution-card h3 { color: var(--brand-green); font-size: 1.3rem; margin-bottom: 15px; font-weight: 800; }
        .solution-flow { font-weight: 700; color: var(--brand-blue); font-size: 1.15rem; margin-bottom: 12px; line-height: 1.4; }

        .how-you-look { background: var(--bg-light); padding: clamp(40px, 6vw, 60px) 5% clamp(50px, 8vw, 70px); }
        .feature-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: clamp(30px, 5vw, 60px); align-items: center; margin-bottom: clamp(50px, 8vw, 80px); }
        .feature-row:nth-child(even) .feature-text { order: -1; }
        .feature-text h2 { font-size: clamp(1.8rem, 4vw, 2.2rem); color: var(--brand-blue); margin-bottom: 20px; line-height: 1.2; }
        .feature-text p { font-size: 1.1rem; margin-bottom: 20px; color: var(--text-muted); }
        .feature-image img { border-radius: 16px; box-shadow: 0 20px 40px rgba(28, 61, 106, 0.12); width: 100%; border: 1px solid #E2E8F0; }
        .diploma-badge { display: inline-flex; align-items: center; gap: 10px; background: #FEF3C7; color: #D97706; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 0.95rem; margin-top: 10px; }

        .checkout-section { background: var(--white); padding: clamp(60px, 8vw, 80px) 5% 20px; text-align: center; position: relative; }
        .checkout-box { max-width: 550px; margin: 0 auto; background: var(--white); padding: clamp(30px, 5vw, 50px); border-radius: 24px; box-shadow: 0 25px 60px rgba(28, 61, 106, 0.1); text-align: center; border-top: 6px solid var(--brand-green); width: 100%; box-sizing: border-box; position: relative; z-index: 1; border: 1px solid #E2E8F0; border-top: 6px solid var(--brand-green); }
        
        .price-tag { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 25px; }
        .price-tag .amount { font-size: clamp(3rem, 7vw, 4rem); font-weight: 900; color: var(--brand-blue); line-height: 1; margin-bottom: 12px; }
        .price-tag .badge-container { margin: 0; }
        .price-tag span.badge { display: inline-block; font-size: 0.85rem; background: var(--brand-orange); color: white; padding: 6px 14px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(242, 156, 56, 0.3); font-weight: 700; }
        .price-tag p.desc { font-size: clamp(0.95rem, 2vw, 1.05rem); color: var(--text-muted); font-weight: 500; margin-top: 10px; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-size: 0.95rem; font-weight: 700; margin-bottom: 8px; color: var(--brand-blue); }
        .form-control { width: 100%; padding: 16px; border: 2px solid #CBD5E1; border-radius: 12px; font-size: 16px; transition: all 0.3s; box-sizing: border-box; max-width: 100%; background: #F8FAFC; }
        .form-control:focus { border-color: var(--brand-blue); outline: none; box-shadow: 0 0 0 4px rgba(28, 61, 106, 0.1); background: var(--white); }
        .btn-pay { width: 100%; font-size: 1.2rem; padding: 18px; margin-top: 15px; box-shadow: 0 10px 25px rgba(104, 169, 62, 0.3); }
        
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); z-index: 1000; justify-content: center; align-items: center; padding: 15px; opacity: 0; transition: opacity 0.3s ease; overflow-y: auto; backdrop-filter: blur(5px); box-sizing: border-box; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-content { background: var(--white); width: 100%; max-width: 500px; padding: clamp(25px, 5vw, 40px); border-radius: 20px; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.3); border-top: 6px solid var(--brand-green); margin: auto; box-sizing: border-box; }
        .modal-close { position: absolute; top: 15px; right: 20px; font-size: 2.5rem; color: #94A3B8; cursor: pointer; border: none; background: none; line-height: 1; transition: color 0.2s; }
        .modal-close:hover { color: var(--text-main); }

        .trust-badges { display: flex; justify-content: center; gap: 20px; margin-top: 25px; opacity: 0.7; }
        .trust-badges img { height: 25px; filter: grayscale(100%); transition: filter 0.3s; }
        .trust-badges img:hover { filter: grayscale(0%); }

        .pay-method-label { flex: 1; text-align: center; border: 2px solid #CBD5E1; border-radius: 10px; padding: 12px 5px; cursor: pointer; font-weight: 700; color: var(--text-muted); transition: all 0.2s; background: #F8FAFC; font-size: 0.95rem; }
        .pay-method-label:hover { border-color: var(--brand-blue); }
        .pay-method-input:checked + .pay-method-label { border-color: var(--brand-green) !important; background: rgba(104, 169, 62, 0.05) !important; color: var(--brand-green) !important; box-shadow: 0 0 0 2px rgba(104, 169, 62, 0.2); }

        @media (max-width: 480px) {
            .nav-inner { flex-direction: column; text-align: center; }
            .nav-links { width: 100%; flex-direction: column; gap: 15px; }
            .btn { width: 100%; }
            .payment-methods { flex-direction: column; }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <a href="#" aria-label="Inicio">
            <img src="assets/logo-myworld.svg" alt="My World Logo" width="240" height="60" onerror="this.style.display='none'">
        </a>
    </header>

    <section class="hero">
        <div class="wave-container-top">
            <svg viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="var(--wave-color)" d="M0,0 L1440,0 L1440,160 C1000,320 400,0 0,160 Z"></path>
            </svg>
        </div>

        <div class="container grid-container" style="align-items: center; position: relative; z-index: 1;">
            <div class="hero-text text-center" style="text-align: left;">
                <h1><span class="highlight">El orgullo de ser el</span> Padre Héroe 🦸‍♂️</h1>
                <p>En 15 minutos al día, My World convierte el tiempo de tablet en aprendizaje que puedes auditar: <strong>te muestra un diploma</strong> con las palabras que dominó cada día para que las evalúes. Sin estrés.</p>
                
                <div style="margin-top: 35px;">
                    <button class="btn trigger-modal">
                        ACCEDE AHORA
                        <span class="btn-subtext">S/14.99 Pago Único.</span>
                    </button>
                </div>
            </div>
            
            <div class="hero-video">
                <div class="video-wrapper" style="cursor: default;">
                    <img src="assets/hero-app.webp" alt="Niño y padre aprendiendo inglés juntos" class="video-thumbnail" loading="eager" width="600" height="337">
                </div>
            </div>
        </div>
    </section>

    <section class="what-you-get section-padding">
        <div class="container">
            <h2 class="section-title">¿Qué incluye tu <span class="underline">inversión hoy</span>? 📦</h2>
            <p class="section-subtitle">Te entregamos las herramientas para que tu hijo <strong>aprenda solo</strong> diariamente!</p>
            
            <div class="get-grid">
                <div class="get-card">
                    <span class="get-badge">Para Siempre</span>
                    <h4>🧠 Las 500 palabras más usadas</h4>
                    <p>Tu hijo practica y domina vocabulario real que sí se usa en conversaciones.</p>
                </div>
                <div class="get-card">
                    <span class="get-badge">Inteligencia</span>
                    <h4>🗺️ Reto físico de 30 días</h4>
                    <p>Un algoritmo que adapta los desafíos a las palabras que tu hijo eligió aprender ese día. Autonomía guiada que aniquila el aburrimiento.</p>
                </div>
                <div class="get-card">
                    <span class="get-badge">Mnemotecnia</span>
                    <h4>🗣️ Trucos de Pronunciación</h4>
                    <p>Audios nativos y trucos visuales en español (ej: "ápol", "jáus") para que aprenda sin miedo a equivocarse.</p>
                </div>
                <div class="get-card">
                    <span class="get-badge">Imprimible</span>
                    <h4>🗺️ Pasaporte de Aventurero</h4>
                     <p>Herramientas descargables para que marque sus logros físicos e interactúe contigo al final del día.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="problem-section">
        <div class="container text-center">
            <h2 class="section-title" style="font-size: clamp(1.8rem, 4vw, 2.2rem);">El problema no es tu hijo. Es que todos esos métodos <span class="highlight">saltaron el primer paso.</span></h2>
            <p class="section-subtitle" style="margin-bottom: 35px; color: var(--text-main); font-weight: 600;">Sin vocabulario base, la academia frustra. La app entretiene. El colegio abandona.</p>
            
            <div class="problem-card">
                <ul class="problem-list">
                    <li>
                        <span class="problem-icon">✕</span>
                        <div><strong>Academias:</strong> 2 horas a la semana sin refuerzo diario &rarr; olvido garantizado.</div>
                    </li>
                    <li>
                        <span class="problem-icon">✕</span>
                        <div><strong>Apps:</strong> recompensan el clic, no el conocimiento &rarr; cero retención escrita.</div>
                    </li>
                    <li>
                        <span class="problem-icon">✕</span>
                        <div><strong>YouTube:</strong> consumo pasivo sin estructura &rarr; aprende a pedir juegos, no a hablar inglés.</div>
                    </li>
                </ul>
            </div>
            
            <div class="solution-card text-center">
                <h3>My World refuerza lo importante primero.</h3>
                <p class="solution-flow">Escribe &rarr; Escucha &rarr; Interactúa &rarr; Evalúa &rarr; tú corroboras.</p>
                <p style="color: var(--text-muted); font-size: 1.05rem;"><strong>500 palabras dominadas.</strong> La base que hace que todo lo demás funcione.</p>
            </div>
        </div>
    </section>

    <section class="how-you-look">
        <div class="container">
            <h2 class="section-title">Sé el que tome la decisión <span class="highlight">inteligente 🧠</span></h2>
            <p class="section-subtitle">Ahorraste miles de soles en academias y construiste un vínculo irrompible compartiendo una aventura bilingüe.</p>

            <div class="feature-row">
                <div class="feature-image">
                    <img src="assets/dashboard-mockup.webp" loading="lazy" width="500" height="400" alt="Diploma digital con reporte de progreso diario">
                </div>
                <div class="feature-text">
                    <h2>Audita su progreso en <span class="highlight">la mesa a la hora de cenar</span>. 🍽️</h2>
                    <p>Ya no tendrás la duda de <em>"¿estará aprendiendo algo con esa tablet?"</em>. Al final de cada sesión de 15 minutos, el sistema exige un examen.</p>
                    <p>Si lo pasa, obtienes un <strong>Diploma de Logros Descargable</strong> con las palabras exactas que dominó. Verás a tu hijo emocionado por mostrarte su éxito y pronunciando con confianza frente a la familia.</p>
                    <div class="diploma-badge">🏆 Cero excusas: Progreso auditable 100% real</div>
                </div>
            </div>
            
            <div class="feature-row" style="margin-bottom: 0;">
                <div class="feature-text">
                    <h2>Transforma el tiempo de pantalla en <span class="highlight">tiempo de calidad</span> ❤️</h2>
                    <p>Otras apps aíslan al niño. My World te da el <strong>superpoder</strong> de ser su cómplice. Se reirán juntos con las mnemotecnias y tú le darás el "visto bueno" a su aprendizaje.</p>
                    <div class="diploma-badge">🚀 Padre e Hijo vs. El Mundo</div>
                </div>
                <div class="feature-image">
                    <img src="assets/game-mockup.webp" loading="lazy" width="500" height="400" alt="Padre e hijo riendo mientras aprenden inglés en My World">
                </div>
            </div>
        </div>
    </section>

    <section id="checkout" class="checkout-section">
        <div class="container">
            <h2 class="section-title">El inglés que tu hijo sí quiere aprender.</h2>
            <p class="section-subtitle">Asegura su futuro hoy mismo, no postergues su educación.</p>
            
            <div class="checkout-box">
                <div class="price-tag">
                    <div class="amount">S/14.99</div>
                    <div class="badge-container">
                        <span class="badge">Acceso de por vida</span>
                    </div>
                    <p class="desc">Aprovecha esta promoción hoy. Cero Riesgos.</p>
                </div>
                
                <form id="bottom-payment-form">
                    <div class="form-group">
                        <label for="bottom_parent_name">Escribe tu nombre (Padre/Madre)</label>
                        <input type="text" id="bottom_parent_name" class="form-control" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="form-group">
                        <label for="bottom_parent_phone">Escribe tu número de WhatsApp</label>
                        <input type="tel" id="bottom_parent_phone" class="form-control" placeholder="Ej: 999888777" pattern="^9\d{8}$" maxlength="9" required title="Debe empezar con 9 y tener 9 dígitos">
                    </div>
                    
                    <button type="submit" id="btn-comprar-bottom" class="btn btn-pay">
                        COMPRAR AHORA Y SER SU GUÍA
                        <span class="btn-subtext">Proceso encriptado vía Mercado Pago o Yape</span>
                    </button>
                    
                    <div class="trust-badges">
                        <span>💳 Visa / Mastercard / Yape / Plin 100% Seguro</span>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="wave-container-bottom">
        <svg viewBox="0 0 1440 200" preserveAspectRatio="none">
            <path fill="var(--wave-color)" d="M0,200 L1440,200 L1440,50 C1000,-50 400,150 0,0 Z"></path>
        </svg>
    </div>

    <div class="modal-overlay" id="checkoutModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal" aria-label="Cerrar">&times;</button>
            <div class="price-tag">
                <div class="amount" style="font-size: clamp(2rem, 5vw, 2.5rem);">S/14.99</div>
                <div class="badge-container">
                    <span class="badge">Oferta Única Vitalicia</span>
                </div>
                <p class="desc">Aprovecha esta promoción hoy.</p>
            </div>
            <form id="payment-form">
                <div class="form-group">
                    <label for="parent_name">Escribe tu nombre (Padre/Madre)</label>
                    <input type="text" id="parent_name" class="form-control" placeholder="Ej: Juan Pérez" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras y espacios" required>
                </div>
                <div class="form-group">
                    <label for="parent_phone">Escribe tu número de WhatsApp</label>
                    <input type="tel" id="parent_phone" class="form-control" placeholder="Ej: 999888777" pattern="^9\d{8}$" maxlength="9" required title="Debe empezar con 9 y tener 9 dígitos">
                </div>
                
                <button type="submit" id="btn-comprar-modal" class="btn btn-pay">
                    ¡Comprar y acceder ahora!
                </button>
                
                <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 15px;">
                    Paga 100% seguro con Mercado Pago o Yape.
                </p>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="paymentModal">
        <div class="modal-content" style="background: var(--white); border-top: 6px solid var(--brand-green); text-align: left;">
            <button class="modal-close" id="closePaymentModal" aria-label="Cerrar">&times;</button>
            <h3 style="color: var(--brand-blue); font-size: 1.5rem; font-weight: 800; margin-bottom: 10px; text-align: center;">Último paso 🚀</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 25px; text-align: center;">Elige el método que te sea más cómodo para activar el acceso.</p>
            
            <label style="font-size: 1.05rem; font-weight: 700; color: var(--brand-blue); display:block; margin-bottom: 15px; text-align: center;">Método de Pago (S/ 14.99)</label>
            <div class="payment-methods" style="display: flex; gap: 15px; margin-bottom: 25px;">
                
                <input type="radio" name="pay_method" id="pay_card" value="card" class="pay-method-input" style="display:none;">
                <label for="pay_card" class="pay-method-label" style="padding: 15px 10px;">
                    💳 Tarjeta / Yape <br>
                    <small style="font-weight: 400;">(Con código de aprobación)</small>
                </label>
                
                <input type="radio" name="pay_method" id="pay_yape" value="yape" class="pay-method-input" style="display:none;">
                <label for="pay_yape" class="pay-method-label" style="padding: 15px 10px;">
                    📱 Yape / Plin <br>
                    <small style="font-weight: 400;">(Directo al número)</small>
                </label>
            </div>
            
            <button id="btn-final-pay" class="btn btn-pay" style="width: 100%; font-size: 1.2rem; padding: 18px; margin-top: 5px;">
                IR A PAGAR AHORA
            </button>
            <button id="btn-back-modal" class="btn-back-modal" type="button">
                ← Regresar a cambiar mis datos
            </button>
        </div>
    </div>

    <script>
        // ==========================================
        // REGLA DE ORO: Twemoji Carga Segura con Fallback y CDN JSDelivr
        // ==========================================
        document.addEventListener("DOMContentLoaded", function() {
            const loadEmojis = () => {
                if (typeof twemoji !== 'undefined') {
                    twemoji.parse(document.body, { 
                        base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
                        folder: 'svg', 
                        ext: '.svg' 
                    });
                } else {
                    console.warn("Twemoji cargando. Reintentando en 500ms...");
                    setTimeout(loadEmojis, 500);
                }
            };
            loadEmojis();
        });

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
        
        // MODAL FINAL DE PAGO
        const paymentModal = document.getElementById('paymentModal');
        const closePaymentModal = document.getElementById('closePaymentModal');
        const btnBackModal = document.getElementById('btn-back-modal');
        
        let currentParentName = "";
        let currentParentPhone = "";

        closePaymentModal.addEventListener('click', () => { paymentModal.classList.remove('active'); });
        
        btnBackModal.addEventListener('click', () => { 
            paymentModal.classList.remove('active'); 
            modal.classList.add('active'); 
        });

        function openPaymentModal(parentName, parentPhone) {
            currentParentName = parentName.replace(/<[^>]*>?/gm, ''); 
            currentParentPhone = parentPhone;
            modal.classList.remove('active'); 
            paymentModal.classList.add('active'); 
            
            // ==========================================
            // EVENTO ADD TO CART (Corregido)
            // Se dispara con fiabilidad cuando el usuario avanza al modal de pago.
            // ==========================================
            if (typeof fbq !== 'undefined') {
                fbq('track', 'AddToCart', {
                    value: 14.99,
                    currency: 'PEN',
                    content_name: 'Acceso Vitalicio - Datos Ingresados'
                });
            }
        }

        const bottomBtn = document.getElementById('btn-comprar-bottom');
        const bottomName = document.getElementById('bottom_parent_name');
        const bottomPhone = document.getElementById('bottom_parent_phone');

        bottomBtn.addEventListener('click', function (e) {
            if (bottomName.value.trim() === '' && bottomPhone.value.trim() === '') {
                e.preventDefault(); 
                modal.classList.add('active');
            }
        });

        document.getElementById('bottom-payment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const parentName = bottomName.value.trim();
            const parentPhone = bottomPhone.value.trim();
            openPaymentModal(parentName, parentPhone);
        });

        document.getElementById('payment-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const parentName = document.getElementById('parent_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();
            openPaymentModal(parentName, parentPhone);
        });

        // ==========================================
        // VALIDACIÓN DEL BOTÓN DE PAGO FINAL
        // ==========================================
        document.getElementById('btn-final-pay').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificamos si el usuario ha seleccionado alguna opción
            const selectedOption = document.querySelector('input[name="pay_method"]:checked');
            
            if (!selectedOption) {
                alert("Por favor, selecciona Tarjeta o Yape para continuar con el pago.");
                return; // Detiene la ejecución si no hay nada seleccionado
            }
            
            const btnElement = document.getElementById('btn-final-pay');
            const payMethod = selectedOption.value;
            
            procesarPagoSeguro(currentParentName, currentParentPhone, payMethod, btnElement);
        });

        function procesarPagoSeguro(parentName, parentPhone, payMethod, btnElement) {
            const phoneRegex = /^9\d{8}$/;
            if (!phoneRegex.test(parentPhone)) {
                return alert("Por favor, ingresa un número de WhatsApp válido que empiece con 9 y tenga 9 dígitos.");
            }

            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = "Redirigiendo a pago seguro... ⏳";
            btnElement.disabled = true;

            // ==========================================
            // EVENTO INITIATE CHECKOUT (Corregido)
            // Se dispara justo al hacer clic para ir a pagar.
            // ==========================================
            if (typeof fbq !== 'undefined') {
                fbq('track', 'InitiateCheckout', {
                    value: 14.99,
                    currency: 'PEN',
                    content_name: payMethod === 'yape' ? 'Checkout Yape Directo' : 'Checkout Mercado Pago'
                });
            }

            if (payMethod === 'yape') {
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Contact', {
                        content_name: 'Contacto por Yape Directo',
                        value: 14.99,
                        currency: 'PEN'
                    });
                }
                
                setTimeout(() => {
                    window.location.href = `checkout_yape.php?bump=false&name=${encodeURIComponent(parentName)}&phone=${encodeURIComponent(parentPhone)}`;
                }, 500);
                
            } else {
                setTimeout(() => {
                    const linkMercadoPago = "https://mpago.la/1eBkEeq"; 
                    window.location.href = linkMercadoPago;
                }, 500);
            }
            
            setTimeout(() => {
                btnElement.innerHTML = originalText;
                btnElement.disabled = false;
            }, 5000);
        }
    </script>
</body>
</html>