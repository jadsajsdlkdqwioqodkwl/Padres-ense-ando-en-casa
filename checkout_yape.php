<?php
// checkout_yape.php - Página de instrucciones de pago manual
$bump = isset($_GET['bump']) && $_GET['bump'] === 'true';
$parent_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
$parent_phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';

$monto = $bump ? "19.99" : "14.99";
$plan_texto = $bump ? "Plan Completo + Pack de Escritura" : "Plan Básico (Acceso a la App)";

// Mantenemos tu número de WhatsApp para el envío de la captura
$whatsapp_number = "51928529656"; 

// Generación de Mensaje prellenado para WhatsApp
$mensaje_ws = "Hola, acabo de transferir S/ $monto por Yape para My World.%0A%0A";
$mensaje_ws .= "👨‍👩‍👦 Mi nombre: $parent_name%0A";
$mensaje_ws .= "📱 Mi WhatsApp: $parent_phone%0A";
$mensaje_ws .= "📚 Plan: $plan_texto%0A%0A";
$mensaje_ws .= "Adjunto la captura de pago. Por favor envíenme el link de creación de cuenta.";
$link_whatsapp = "https://wa.me/{$whatsapp_number}?text={$mensaje_ws}";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Manual - My World</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #1C3D6A;   
            --brand-green: #68A93E;  
            --brand-orange: #F29C38; 
            --bg-light: #F8FAFC;     
            --text-main: #333333;
            --yape-color: #7B2CBF;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background: var(--bg-light); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .checkout-box { background: white; max-width: 500px; width: 100%; border-radius: 20px; box-shadow: 0 25px 50px rgba(28, 61, 106, 0.1); padding: 40px 30px; text-align: center; border-top: 6px solid var(--yape-color); }
        
        .checkout-box h1 { color: var(--brand-blue); font-size: 1.8rem; margin-bottom: 10px; }
        .checkout-box p.subtitle { color: #64748B; margin-bottom: 30px; font-size: 1.1rem; }
        
        .price-display { font-size: 3rem; font-weight: 800; color: var(--yape-color); margin: 20px 0; }
        .plan-badge { display: inline-block; background: #F3E8FF; color: #6B21A8; padding: 6px 15px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 20px; }
        
        .instructions-list { text-align: left; background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid #E2E8F0; margin-bottom: 30px; }
        .instructions-list p { margin-bottom: 15px; font-size: 1.05rem; display: flex; align-items: flex-start; gap: 10px; line-height: 1.5; }
        .instructions-list p:last-child { margin-bottom: 0; }
        .step-num { background: var(--brand-blue); color: white; width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center; border-radius: 50%; font-size: 0.8rem; font-weight: bold; flex-shrink: 0; margin-top: 3px; }
        
        /* Estilos del Botón de Copiar (Copy to Clipboard) */
        .copy-wrapper { display: flex; flex-direction: column; align-items: flex-start; gap: 5px; margin-top: 5px; width: 100%; }
        .btn-copy { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            background: #F3E8FF; 
            color: var(--yape-color); 
            border: 2px dashed #D8B4FE; 
            padding: 8px 16px; 
            border-radius: 10px; 
            font-size: 1.15rem; 
            font-weight: 800; 
            cursor: pointer; 
            transition: all 0.2s;
            width: fit-content;
        }
        .btn-copy:hover { background: #E9D5FF; border-color: var(--yape-color); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(123, 44, 191, 0.15); }
        .btn-copy:active { transform: translateY(0); }
        .btn-copy svg { width: 18px; height: 18px; fill: currentColor; }
        .copy-feedback { font-size: 0.85rem; color: #10B981; font-weight: 700; opacity: 0; transition: opacity 0.3s; margin-left: 5px; }
        
        .btn-whatsapp { display: flex; align-items: center; justify-content: center; gap: 10px; background: #25D366; color: white; padding: 18px 25px; border-radius: 50px; text-decoration: none; font-weight: 800; font-size: 1.2rem; transition: 0.3s; box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3); }
        .btn-whatsapp:hover { background: #128C7E; transform: translateY(-3px); box-shadow: 0 12px 30px rgba(37, 211, 102, 0.4); }
        
        .security-note { font-size: 0.85rem; color: #94A3B8; margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 5px; }

        @media (max-width: 480px) {
            .btn-copy { width: 100%; justify-content: center; } /* Botón ancho en móviles para no fallar el click */
            .copy-wrapper { align-items: stretch; }
        }
    </style>
</head>
<body>

    <div class="checkout-box">
        <h1>📱 Paso Final con Yape / Plin</h1>
        <p class="subtitle">Solo faltan dos pasos para habilitar el acceso de tu hijo.</p>
        
        <div class="plan-badge"><?php echo $plan_texto; ?></div>
        
        <div class="price-display">
            S/ <?php echo $monto; ?>
        </div>

        <div class="instructions-list">
            <p>
                <span class="step-num">1</span> 
                <span style="width: 100%;">
                    Abre tu app de Yape o Plin y transfiere <strong>S/ <?php echo $monto; ?></strong> a este número:
                    <div class="copy-wrapper">
                        <button type="button" class="btn-copy" id="copyBtn" onclick="copyYapeNumber('927633099')" aria-label="Copiar número de Yape">
                            927 633 099
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"/>
                            </svg>
                        </button>
                        <span id="copyFeedback" class="copy-feedback">¡Número copiado! ✅</span>
                    </div>
                    <small style="color: #64748B; display: block; margin-top: 5px;">(A nombre de Moises O.)</small>
                </span>
            </p>
            <p>
                <span class="step-num">2</span> 
                <span>Tómale una captura de pantalla (screenshot) a la transferencia exitosa.</span>
            </p>
            <p>
                <span class="step-num">3</span> 
                <span>Haz clic en el botón verde de abajo para enviarnos la captura y generarte tu link de acceso de inmediato.</span>
            </p>
        </div>

        <a href="<?php echo $link_whatsapp; ?>" target="_blank" class="btn-whatsapp">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.01 2.002c-5.522 0-9.998 4.477-9.998 9.998 0 1.983.58 3.823 1.583 5.39L2 22l4.735-1.543c1.536.93 3.327 1.465 5.275 1.465 5.523 0 10.002-4.477 10.002-9.998S17.533 2.002 12.01 2.002zm0 18.25c-1.636 0-3.155-.42-4.46-1.144l-.32-.178-3.05.992.813-2.97-.194-.31C3.998 14.862 3.493 13.29 3.493 11.602c0-4.707 3.82-8.528 8.527-8.528 4.707 0 8.528 3.82 8.528 8.528 0 4.706-3.82 8.527-8.528 8.527zm4.675-6.388c-.256-.128-1.516-.748-1.75-.833-.233-.085-.403-.128-.574.128-.17.255-.663.832-.812 1.002-.15.17-.3.19-.556.064-.256-.128-1.082-.4-2.06-1.272-.762-.68-1.277-1.52-1.427-1.776-.15-.255-.016-.393.112-.52.115-.116.256-.3.384-.45.128-.15.17-.255.255-.426.085-.17.042-.32-.02-.447-.064-.128-.574-1.383-.787-1.894-.207-.497-.42-.428-.574-.437-.15-.008-.32-.01-.49-.01-.17 0-.447.064-.68.32-.234.256-.895.875-.895 2.133 0 1.258.916 2.474 1.044 2.645.128.17 1.805 2.756 4.373 3.864 2.568 1.108 2.568.74 3.037.696.47-.042 1.516-.618 1.73-1.214.212-.596.212-1.107.15-1.214-.064-.108-.235-.172-.49-.302z"/>
            </svg>
            Enviar Captura de Pago
        </a>

        <div class="security-note">
            🔒 Proceso validado humanamente. El acceso es garantizado.
        </div>
    </div>

    <script>
        function copyYapeNumber(number) {
            // API del Portapapeles (Segura y moderna)
            navigator.clipboard.writeText(number).then(() => {
                const feedback = document.getElementById('copyFeedback');
                const btn = document.getElementById('copyBtn');
                
                // Mostrar feedback visual
                feedback.style.opacity = '1';
                btn.style.backgroundColor = '#D1FAE5'; // Verde éxito
                btn.style.borderColor = '#34D399';
                btn.style.color = '#065F46';
                
                // Ocultar feedback después de 2.5 segundos
                setTimeout(() => {
                    feedback.style.opacity = '0';
                    btn.style.backgroundColor = ''; 
                    btn.style.borderColor = '';
                    btn.style.color = '';
                }, 2500);
            }).catch(err => {
                console.error('Error al copiar al portapapeles: ', err);
                // Fallback amigable si el navegador del usuario bloquea el portapapeles
                alert(`Por favor, copia este número manualmente: ${number}`);
            });
        }
    </script>
</body>
</html>