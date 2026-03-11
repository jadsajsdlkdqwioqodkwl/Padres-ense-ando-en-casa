<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$has_bump = false; // Se evaluará de la BD si compró el bump

if ($is_logged_in) {
    require_once 'includes/config.php';
    try {
        // Obtenemos los datos del usuario para ver si tiene el beneficio bump
        // Nota: Asegúrate de tener una columna "has_bump" (TINYINT 1) en tu BD 'users'
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user && isset($user['has_bump']) && $user['has_bump'] == 1) {
            $has_bump = true;
        }
    } catch (Exception $e) {
        // Failsafe por si la columna aún no se crea en la BD
        $has_bump = false;
    }
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
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --brand-lblue: #5CB2E4;  
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --white: #FFFFFF;
            
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
        .nav-actions { display: flex; gap: 10px; align-items: center; }
        .login-btn { background: var(--bg-light); border: 2px solid #E2E8F0; color: var(--brand-blue); padding: 10px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .login-btn:hover { background: #E2E8F0; transform: translateY(-2px); }
        .logout-btn { background: #FFF5F5; border: 2px solid #FECACA; color: #DC2626; padding: 10px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .logout-btn:hover { background: #FECACA; transform: translateY(-2px); }

        /* Hero */
        .hero { text-align: center; padding: 80px 5%; background: linear-gradient(135deg, var(--bg-light) 0%, #E2E8F0 100%); }
        .hero h1 { font-size: 3rem; color: var(--brand-blue); margin-bottom: 15px; }
        .hero p { color: #64748B; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }
        
        /* Grid de Productos */
        .products-section { padding: 60px 5%; text-align: center; }
        .grid-products { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 40px; }
        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05); text-align: left; transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #E2E8F0; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(28, 61, 106, 0.1); }
        .product-img { width: 100%; height: 180px; display: flex; justify-content: center; align-items: center; font-size: 70px; }
        .product-info { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-info h3 { color: var(--brand-blue); margin-bottom: 10px; }
        .product-price { font-size: 1.8rem; font-weight: 800; color: var(--brand-orange); margin: 15px 0; margin-top: auto; }
        
        /* Botones estilo Landing */
        .btn-cart { display: block; width: 100%; text-align: center; background: var(--brand-green); color: white; padding: 14px; border-radius: 50px; text-decoration: none; font-weight: 700; cursor: pointer; border: none; font-size: 16px; transition: 0.3s; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); }
        .btn-cart:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        .btn-disabled { display: block; width: 100%; text-align: center; background: var(--disabled); color: white; padding: 14px; border-radius: 50px; font-weight: 700; border: none; cursor: not-allowed; }
        .badge-soon { background: var(--brand-orange); color: white; padding: 4px 10px; border-radius: 50px; font-size: 12px; vertical-align: middle; margin-left: 10px; font-weight: 700; }

        /* Panel Extra de Descargas para Logueados (Bump Sell) */
        .premium-downloads { background: #FFFBEB; border: 2px dashed var(--brand-orange); border-radius: 16px; padding: 30px; margin: 40px auto; max-width: 800px; text-align: left; box-shadow: 0 10px 25px rgba(242, 156, 56, 0.15); }
        .premium-downloads h3 { color: #D97706; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .btn-drive { display: inline-block; background: #3B82F6; color: white; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 700; margin-top: 15px; transition: 0.3s; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3); }
        .btn-drive:hover { background: #2563EB; transform: translateY(-2px); }

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
        <a href="index.php" class="logo">
            <img src="assets/logo-myworld.svg" alt="My World" style="height: 40px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span style="display:none;">🚀 My World</span>
        </a>
        <div class="nav-actions">
            <?php if ($is_logged_in): ?>
                <a href="dashboard.php" class="login-btn" style="background: var(--brand-blue); color: white;">Ir a mis Clases</a>
                <a href="logout.php" class="logout-btn">Salir</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Acceso Alumnos</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <h1>Educación Interactiva para tus Hijos</h1>
        <p>Aprende jugando con nuestros cursos especializados. ¡Selecciona un curso, complementa tu aprendizaje y comienza la aventura!</p>
    </div>

    <?php if ($is_logged_in && $has_bump): ?>
    <section style="padding: 0 5%;">
        <div class="premium-downloads">
            <h3>⭐️ Tus Descargas Premium (Pack de Mnemotecnias)</h3>
            <p style="color: #475569;">Como adquiriste el potenciador de escritura y el Pasaporte de Aventurero, aquí tienes tu enlace exclusivo de acceso de por vida a tu material.</p>
            <a href="https://drive.google.com/drive/folders/TU_LINK_DE_DRIVE_AQUI" target="_blank" class="btn-drive">📂 Abrir Google Drive</a>
        </div>
    </section>
    <?php endif; ?>

    <section class="products-section">
        <h2>Catálogo de Cursos y Extras</h2>
        <div class="grid-products">
            
            <div class="product-card" style="border: 2px solid var(--success);">
                <div class="product-img" style="background: #E0E7FF;">🇬🇧</div>
                <div class="product-info">
                    <h3>Inglés: My World</h3>
                    <p style="color: #64748B;">Aprende 5 palabras al día con juegos interactivos, audios nativos y mnemotecnias.</p>
                    <div class="product-price">S/ 14.99 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">/mes</span></div>
                    <a href="landing.php" class="btn-cart" style="text-decoration: none;">Ver Detalles y Comprar</a>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #ECFCCB;">🌿</div>
                <div class="product-info">
                    <h3>150 Actividades sin Pantallas</h3>
                    <p style="color: #64748B;">Ideas simples para entretener, educar y estimular la creatividad de tu hijo sin usar el celular.</p>
                    <div class="product-price">S/ 6.90 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">(PDF)</span></div>
                    <a href="https://link.mercadopago.com.pe/PON_TU_LINK_690_AQUI" target="_blank" class="btn-cart" style="background: var(--brand-blue);">🛒 Comprar E-book</a>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #FEF3C7;">⭐</div>
                <div class="product-info">
                    <h3>Sistema de Hábitos para Niños</h3>
                    <p style="color: #64748B;">Tablas de hábitos, recompensas y rutinas para ayudar a tu hijo a desarrollar disciplina de forma positiva.</p>
                    <div class="product-price">S/ 5.90 <span style="font-size: 14px; color: #94A3B8; font-weight: 400;">(PDF)</span></div>
                    <a href="https://link.mercadopago.com.pe/PON_TU_LINK_590_AQUI" target="_blank" class="btn-cart" style="background: var(--brand-blue);">🛒 Comprar Tablas</a>
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
            <p style="margin-bottom: 25px; font-size: 14px; color: #64748B;">Serás redirigido a nuestra página segura de oferta.</p>
            <a href="landing.php" class="btn-cart" style="text-decoration: none;">Ir a Ver Oferta</a>
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
                <p>3. <strong>Suscripción:</strong> El servicio se brinda bajo una modalidad de suscripción recurrente o pago único según el plan seleccionado.</p><br>
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
            window.location.href = "landing.php"; // Ahora centralizamos la venta del curso en landing
        }
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>