<?php
// Seguro para evitar choques de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya pagó y está logueado, lo mandamos directo a su zona de estudio.
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php?module=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My World - Plataforma Educativa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            /* Nueva Paleta Oficial */
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --brand-lblue: #5CB2E4;  
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --white: #FFFFFF;
            
            /* Mapeo de seguridad para retrocompatibilidad */
            --primary: var(--brand-blue); 
            --secondary: var(--brand-orange); 
            --dark: var(--brand-blue); 
            --light: var(--bg-light); 
            --success: var(--brand-green); 
            --disabled: #cbd5e1; 
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background: var(--bg-light); color: var(--text-main); line-height: 1.6; }
        h1, h2, h3 { font-weight: 800; letter-spacing: -0.5px; }
        
        /* Navegación */
        .landing-nav { background: var(--white); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(28, 61, 106, 0.05); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid #E2E8F0; }
        .logo { font-size: 24px; font-weight: 800; color: var(--brand-blue); text-decoration: none; display: flex; align-items: center; gap: 10px;}
        .login-btn { background: var(--bg-light); border: 2px solid #E2E8F0; color: var(--brand-blue); padding: 10px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .login-btn:hover { background: #E2E8F0; transform: translateY(-2px); }

        /* Hero */
        .hero { text-align: center; padding: 80px 5%; background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); }
        .hero h1 { font-size: 3rem; color: var(--brand-blue); margin-bottom: 15px; }
        .hero p { color: #64748B; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
        
        /* Grid de Productos */
        .products-section { padding: 60px 5%; text-align: center; }
        .grid-products { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 40px; }
        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: left; transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #E2E8F0; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(28, 61, 106, 0.1); }
        .product-img { width: 100%; height: 180px; display: flex; justify-content: center; align-items: center; font-size: 70px; }
        .product-info { padding: 25px; }
        .product-info h3 { color: var(--brand-blue); margin-bottom: 10px; }
        .product-price { font-size: 1.8rem; font-weight: 800; color: var(--brand-orange); margin: 15px 0; }
        
        /* Botones estilo Landing */
        .btn-cart { display: block; width: 100%; text-align: center; background: var(--brand-green); color: white; padding: 14px; border-radius: 50px; text-decoration: none; font-weight: 700; cursor: pointer; border: none; font-size: 16px; transition: 0.3s; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); }
        .btn-cart:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-disabled { display: block; width: 100%; text-align: center; background: var(--disabled); color: white; padding: 14px; border-radius: 50px; font-weight: 700; border: none; cursor: not-allowed; }
        .badge-soon { background: var(--brand-orange); color: white; padding: 4px 10px; border-radius: 50px; font-size: 12px; vertical-align: middle; margin-left: 10px; font-weight: 700; }

        /* Footer y Legal */
        .footer { background: var(--brand-blue); color: white; padding: 60px 5%; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; }
        .footer h3 { color: var(--brand-lblue); margin-bottom: 20px; }
        .footer p, .footer a { color: #CBD5E1; text-decoration: none; margin-bottom: 12px; display: block; font-size: 0.95rem; }
        .footer a:hover { color: white; }
        
        /* Modales rediseñados */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(28, 61, 106, 0.8); z-index: 1000; display: none; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(4px); }
        .modal-box { background: white; padding: 40px; border-radius: 16px; max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.2); border-top: 5px solid var(--brand-green); }
        .close-modal { position: absolute; top: 20px; right: 20px; font-size: 28px; cursor: pointer; color: #94A3B8; background: none; border: none; transition: 0.2s; }
        .close-modal:hover { color: var(--brand-blue); }
        
        .form-control { width: 100%; padding: 14px; margin-bottom: 20px; border: 2px solid #E2E8F0; border-radius: 10px; font-size: 16px; transition: 0.3s; }
        .form-control:focus { border-color: var(--brand-blue); outline: none; }
    </style>
</head>
<body>

    <nav class="landing-nav">
        <a href="#" class="logo">
            <img src="assets/logo-myworld.svg" alt="My World" style="height: 40px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span style="display:none;">🚀 My World</span>
        </a>
        <a href="login.php" class="login-btn">Acceso Alumnos</a>
    </nav>

    <div class="hero">
        <h1>Educación Interactiva para tus Hijos</h1>
        <p>Aprende jugando con nuestros cursos especializados. ¡Selecciona un curso y comienza la aventura!</p>
    </div>

    <section class="products-section">
        <h2>Catálogo de Cursos</h2>
        <div class="grid-products">
            
            <div class="product-card" style="border: 2px solid var(--success);">
                <div class="product-img" style="background: #E0E7FF;">🇬🇧</div>
                <div class="product-info">
                    <h3>Inglés: My World</h3>
                    <p style="color: #64748B;">Aprende 5 palabras al día con juegos interactivos, audios nativos y mnemotecnias.</p>
                    <div class="product-price">S/ 39.00 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <button class="btn-cart" onclick="openCheckout('Inglés: My World')">🛒 Comprar Curso</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #FEF3C7;">🔢</div>
                <div class="product-info">
                    <h3>Matemáticas Lúdicas <span class="badge-soon">Próximamente</span></h3>
                    <p style="color: #64748B;">Sumas y restas divertidas salvando a los alienígenas en el espacio.</p>
                    <div class="product-price">S/ 29.00 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #E0F2FE;">🔬</div>
                <div class="product-info">
                    <h3>Ciencias: Pequeño Genio <span class="badge-soon">Próximamente</span></h3>
                    <p style="color: #64748B;">Experimentos virtuales y el descubrimiento del cuerpo humano.</p>
                    <div class="product-price">S/ 45.00 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #FEF08A;">🎨</div>
                <div class="product-info">
                    <h3>Arte y Creatividad <span class="badge-soon">Próximamente</span></h3>
                    <p style="color: #64748B;">Desarrolla habilidades motoras finas coloreando en nuestra pizarra digital.</p>
                    <div class="product-price">S/ 25.00 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #FCE7F3;">🧠</div>
                <div class="product-info">
                    <h3>Lógica y Memoria <span class="badge-soon">Próximamente</span></h3>
                    <p style="color: #64748B;">Juegos de cartas y secuencias lógicas para ejercitar el cerebro a temprana edad.</p>
                    <div class="product-price">S/ 35.00 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

        </div>
    </section>

    <footer class="footer">
        <div>
            <h3>Datos de Contacto</h3>
            <p>📍 Dirección: Av. Santa Anita Mz D1 Lote 5 Chorrillos, Lima, Perú</p>
            <p>📞 Teléfono / WhatsApp: +51 928 529 656</p>
            <p>✉️ Correo: moises.olortegui90@gmail.com</p>
        </div>
        <div>
            <h3>Información Legal</h3>
            <a href="#" onclick="openModal('modal-terminos')">Términos y Condiciones</a>
            <a href="#" onclick="openModal('modal-devoluciones')">Políticas de Devoluciones</a>
        </div>
        <div>
            <h3>Atención al Cliente</h3>
            <a href="#" onclick="openModal('modal-reclamaciones')" style="display: inline-block; padding: 12px 20px; border: 2px solid #CBD5E1; border-radius: 50px; margin-top: 10px; font-weight: 700;">📖 Libro de Reclamaciones</a>
        </div>
    </footer>

    <div id="modal-checkout" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-checkout')">&times;</button>
            <h3 style="margin-bottom: 10px; color: var(--brand-blue);" id="checkout-title">Comprar Curso</h3>
            <p style="margin-bottom: 25px; font-size: 14px; color: #64748B;">Crea la cuenta de tu hijo/a para acceder a la plataforma.</p>
            
            <form id="checkout-form">
                <input type="text" id="child_name" class="form-control" placeholder="Nombre de tu hijo/a" required>
                <input type="tel" id="parent_phone" class="form-control" placeholder="Tu número de WhatsApp (Ej: 999888777)" required>
                
                <button type="submit" class="btn-cart" id="btn-comprar">Pagar S/ 39.00 (Simulador)</button>
            </form>
        </div>
    </div>

    <div id="modal-reclamaciones" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-reclamaciones')">&times;</button>
            <h3 style="margin-bottom: 10px; color: var(--brand-blue);">Libro de Reclamaciones</h3>
            <p style="font-size: 12px; color: #64748B; margin-bottom: 20px;">Conforme al Código de Protección y Defensa del Consumidor de INDECOPI.</p>
            <input type="text" class="form-control" placeholder="Nombre y Apellidos completos">
            <input type="email" class="form-control" placeholder="Correo electrónico">
            <input type="text" class="form-control" placeholder="DNI / CE">
            <textarea class="form-control" rows="4" placeholder="Detalle su reclamo o queja aquí..."></textarea>
            <button class="btn-cart" onclick="alert('Reclamo registrado correctamente en nuestro sistema interno. Recibirá una copia en su correo en las próximas 24 horas.'); closeModal('modal-reclamaciones');">Enviar Reclamo</button>
        </div>
    </div>

    <div id="modal-terminos" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-terminos')">&times;</button>
            <h3 style="margin-bottom: 20px; color: var(--brand-blue);">Términos y Condiciones</h3>
            <div style="font-size: 14px; color: #475569; max-height: 300px; overflow-y: auto; padding-right: 15px; line-height: 1.8;">
                <p>1. <strong>Aceptación:</strong> Al adquirir nuestros servicios, el usuario acepta estos términos.</p><br>
                <p>2. <strong>Acceso:</strong> El acceso a la plataforma es personal e intransferible. Está prohibido compartir credenciales.</p><br>
                <p>3. <strong>Suscripción:</strong> El servicio se brinda bajo una modalidad de suscripción recurrente mensual.</p><br>
                <p>4. <strong>Responsabilidad:</strong> No garantizamos el aprendizaje fluido del idioma sin la supervisión constante y el involucramiento activo del padre o apoderado en el uso de la herramienta.</p>
            </div>
        </div>
    </div>

    <div id="modal-devoluciones" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-devoluciones')">&times;</button>
            <h3 style="margin-bottom: 20px; color: var(--brand-blue);">Política de Cambios y Devoluciones</h3>
            <div style="font-size: 14px; color: #475569; line-height: 1.8;">
                <p>Ofrecemos una <strong>Garantía de Satisfacción de 7 días</strong>.</p>
                <br>
                <p>Si durante los primeros 7 días calendario desde la fecha de compra inicial el usuario no está satisfecho con la plataforma, puede solicitar el reembolso íntegro de su dinero enviando un correo electrónico detallando el motivo a soporte@myworldingles.simpledomai123n.online.</p>
                <br>
                <p>Pasado este plazo de 7 días, no se emitirán reembolsos parciales ni totales bajo ninguna circunstancia. No se realizan cambios de un curso a otro una vez activada la licencia.</p>
            </div>
        </div>
    </div>

    <script>
        function openCheckout(curso) {
            document.getElementById('checkout-title').innerText = "Comprar " + curso;
            document.getElementById('modal-checkout').style.display = 'flex';
        }
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
            }
        }

        document.getElementById('checkout-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const childName = document.getElementById('child_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();

            const btn = document.getElementById('btn-comprar');
            btn.innerText = "Procesando pago..."; 
            btn.disabled = true;
            btn.style.background = "#94A3B8";
            btn.style.boxShadow = "none";

            fetch('app/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: "simulacion_aprobada", child_name: childName, parent_phone: parentPhone })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("¡Pago exitoso! Cuenta creada. Redirigiendo a tus cursos..."); 
                    window.location.href = 'dashboard.php'; 
                } else {
                    alert("Error en el pago: " + data.message);
                    resetBtn(btn);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Hubo un error de conexión con el servidor.");
                resetBtn(btn);
            });
        });

        function resetBtn(btn) {
            btn.innerText = "Pagar S/ 39.00 (Simulador)"; 
            btn.disabled = false;
            btn.style.background = "var(--brand-green)";
            btn.style.boxShadow = "0 4px 14px rgba(104, 169, 62, 0.3)";
        }
    </script>
</body>
</html>