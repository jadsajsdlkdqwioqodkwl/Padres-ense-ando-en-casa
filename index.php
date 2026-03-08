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
    <style>
        :root { --primary: #6c5ced; --secondary: #ff9f43; --dark: #2d3436; --light: #f8f9fa; --success: #2ed573; --disabled: #b2bec3; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: var(--light); color: var(--dark); line-height: 1.6; }
        
        /* Navegación */
        .landing-nav { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 24px; font-weight: bold; color: var(--primary); text-decoration: none; }
        .login-btn { background: transparent; border: 2px solid var(--primary); color: var(--primary); padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .login-btn:hover { background: var(--primary); color: white; }

        /* Hero */
        .hero { text-align: center; padding: 60px 5%; background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); }
        .hero h1 { font-size: 2.8rem; color: var(--primary); margin-bottom: 15px; }
        
        /* Grid de 5 Productos */
        .products-section { padding: 50px 5%; text-align: center; }
        .grid-products { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 30px; }
        .product-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); text-align: left; transition: transform 0.3s; border: 1px solid #eee; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .product-img { width: 100%; height: 200px; display: flex; justify-content: center; align-items: center; font-size: 80px; }
        .product-info { padding: 20px; }
        .product-price { font-size: 1.5rem; font-weight: bold; color: var(--secondary); margin: 10px 0; }
        
        /* Botones de producto */
        .btn-cart { display: block; width: 100%; text-align: center; background: var(--success); color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; font-size: 16px; }
        .btn-cart:hover { background: #26b964; }
        .btn-disabled { display: block; width: 100%; text-align: center; background: var(--disabled); color: white; padding: 12px; border-radius: 8px; font-weight: bold; border: none; cursor: not-allowed; }
        .badge-soon { background: #ff4757; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; vertical-align: middle; margin-left: 10px; }

        /* Footer y Legal */
        .footer { background: var(--dark); color: white; padding: 50px 5%; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .footer h3 { color: var(--secondary); margin-bottom: 15px; }
        .footer p, .footer a { color: #ccc; text-decoration: none; margin-bottom: 10px; display: block; }
        .footer a:hover { color: white; }
        
        /* Modales */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; display: none; justify-content: center; align-items: center; padding: 20px; }
        .modal-box { background: white; padding: 30px; border-radius: 15px; max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto; position: relative; }
        .close-modal { position: absolute; top: 15px; right: 15px; font-size: 24px; cursor: pointer; color: #555; background: none; border: none; }
        
        .form-control { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; }
    </style>
</head>
<body>

    <nav class="landing-nav">
        <a href="#" class="logo">🚀 My World</a>
        <a href="login.php" class="login-btn">Acceso Alumnos</a>
    </nav>

    <div class="hero">
        <h1>Educación Interactiva para tus Hijos</h1>
        <p>Aprende jugando con nuestros cursos especializados. ¡Selecciona un curso y comienza la aventura!</p>
        

    </div>

    <section class="products-section">
        <h2 style="font-size: 2rem; color: var(--dark);">Catálogo de Cursos</h2>
        <div class="grid-products">
            
            <div class="product-card" style="border: 2px solid var(--success);">
                <div class="product-img" style="background: #e0e7ff;">🇬🇧</div>
                <div class="product-info">
                    <h3>Inglés: My World</h3>
                    <p>Aprende 5 palabras al día con juegos interactivos, audios nativos y mnemotecnias.</p>
                    <div class="product-price">S/ 39.00 <span style="font-size: 14px; color: #888;">/mes</span></div>
                    <button class="btn-cart" onclick="openCheckout('Inglés: My World')">🛒 Comprar Curso</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #ffeaa7;">🔢</div>
                <div class="product-info">
                    <h3>Matemáticas Lúdicas <span class="badge-soon">Próximamente</span></h3>
                    <p>Sumas y restas divertidas salvando a los alienígenas en el espacio.</p>
                    <div class="product-price">S/ 29.00 <span style="font-size: 14px; color: #888;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #dff9fb;">🔬</div>
                <div class="product-info">
                    <h3>Ciencias: Pequeño Genio <span class="badge-soon">Próximamente</span></h3>
                    <p>Experimentos virtuales y el descubrimiento del cuerpo humano.</p>
                    <div class="product-price">S/ 45.00 <span style="font-size: 14px; color: #888;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #ffda79;">🎨</div>
                <div class="product-info">
                    <h3>Arte y Creatividad <span class="badge-soon">Próximamente</span></h3>
                    <p>Desarrolla habilidades motoras finas coloreando en nuestra pizarra digital.</p>
                    <div class="product-price">S/ 25.00 <span style="font-size: 14px; color: #888;">/mes</span></div>
                    <button class="btn-disabled" disabled>No Disponible Aún</button>
                </div>
            </div>

            <div class="product-card">
                <div class="product-img" style="background: #f8a5c2;">🧠</div>
                <div class="product-info">
                    <h3>Lógica y Memoria <span class="badge-soon">Próximamente</span></h3>
                    <p>Juegos de cartas y secuencias lógicas para ejercitar el cerebro a temprana edad.</p>
                    <div class="product-price">S/ 35.00 <span style="font-size: 14px; color: #888;">/mes</span></div>
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
            <a href="#" onclick="openModal('modal-reclamaciones')" style="display: inline-block; padding: 10px; border: 2px solid white; border-radius: 8px; margin-top: 10px;">📖 Libro de Reclamaciones</a>
        </div>
    </footer>

    <div id="modal-checkout" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-checkout')">&times;</button>
            <h3 style="margin-bottom: 20px; color: var(--primary);" id="checkout-title">Comprar Curso</h3>
            <p style="margin-bottom: 15px; font-size: 14px; color: #666;">Crea la cuenta de tu hijo/a para acceder a la plataforma.</p>
            
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
            <h3 style="margin-bottom: 15px;">Libro de Reclamaciones</h3>
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Conforme al Código de Protección y Defensa del Consumidor de INDECOPI.</p>
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
            <h3 style="margin-bottom: 15px;">Términos y Condiciones</h3>
            <div style="font-size: 14px; color: #555; max-height: 300px; overflow-y: auto; padding-right: 10px;">
                <p>1. <strong>Aceptación:</strong> Al adquirir nuestros servicios, el usuario acepta estos términos.</p>
                <p>2. <strong>Acceso:</strong> El acceso a la plataforma es personal e intransferible. Está prohibido compartir credenciales.</p>
                <p>3. <strong>Suscripción:</strong> El servicio se brinda bajo una modalidad de suscripción recurrente mensual.</p>
                <p>4. <strong>Responsabilidad:</strong> No garantizamos el aprendizaje fluido del idioma sin la supervisión constante y el involucramiento activo del padre o apoderado en el uso de la herramienta.</p>
            </div>
        </div>
    </div>

    <div id="modal-devoluciones" class="modal-overlay">
        <div class="modal-box">
            <button class="close-modal" onclick="closeModal('modal-devoluciones')">&times;</button>
            <h3 style="margin-bottom: 15px;">Política de Cambios y Devoluciones</h3>
            <div style="font-size: 14px; color: #555;">
                <p>Ofrecemos una <strong>Garantía de Satisfacción de 7 días</strong>.</p>
                <br>
                <p>Si durante los primeros 7 días calendario desde la fecha de compra inicial el usuario no está satisfecho con la plataforma, puede solicitar el reembolso íntegro de su dinero enviando un correo electrónico detallando el motivo a soporte@myworldingles.simpledomai123n.online.</p>
                <br>
                <p>Pasado este plazo de 7 días, no se emitirán reembolsos parciales ni totales bajo ninguna circunstancia. No se realizan cambios de un curso a otro una vez activada la licencia.</p>
            </div>
        </div>
    </div>

    <script>
        // Funciones de control de modales
        function openCheckout(curso) {
            document.getElementById('checkout-title').innerText = "Comprar " + curso;
            document.getElementById('modal-checkout').style.display = 'flex';
        }
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        // Cerrar modal al hacer click afuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
            }
        }

        // Simulación de conexión con tu backend de pagos
        document.getElementById('checkout-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const childName = document.getElementById('child_name').value.trim();
            const parentPhone = document.getElementById('parent_phone').value.trim();

            const btn = document.getElementById('btn-comprar');
            btn.innerText = "Procesando pago..."; 
            btn.disabled = true;
            btn.style.background = "#555";

            // Llamada real al script que ya arreglaste en Docker
            fetch('app/process_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    token: "simulacion_aprobada", 
                    child_name: childName, 
                    parent_phone: parentPhone 
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("¡Pago exitoso! Cuenta creada. Redirigiendo a tus cursos..."); 
                    // Como el pago simulado fue exitoso e inició sesión en el backend, recargar nos enviará a course.php
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
            btn.innerText = "Pagar S/ 14.90 (Simulador)"; 
            btn.disabled = false;
            btn.style.background = "var(--success)";
        }
    </script>
</body>
</html>