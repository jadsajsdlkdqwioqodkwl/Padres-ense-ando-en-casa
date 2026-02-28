<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: COLOR RESCUE
// ==========================================
$target_color_name = $lesson_data['target_color_name'] ?? 'Red'; // Palabra a enseñar
$target_color_hex = $lesson_data['target_color_hex'] ?? '#ff4757'; // Color real
$target_item_emoji = $lesson_data['target_item'] ?? '🍎'; // El objeto (ej. manzana)
$translation = $lesson_data['translation'] ?? 'Rojo';

// Distractores (Colores incorrectos)
$distractors = $lesson_data['distractors'] ?? [
    ['name' => 'Blue', 'hex' => '#3742fa'],
    ['name' => 'Green', 'hex' => '#2ed573'],
    ['name' => 'Yellow', 'hex' => '#eccc68']
];

// Preparamos los cubos de pintura mezclando el correcto con los distractores
$all_colors = $distractors;
$all_colors[] = ['name' => $target_color_name, 'hex' => $target_color_hex, 'correct' => true];
shuffle($all_colors);

$time_limit = $lesson_data['time_limit'] ?? 15; // Segundos antes de que el OVNI robe el dibujo
$reward_stars = $lesson['reward_stars'] ?? 10;
?>

<style>
    /* ==========================================
       ENTORNO Y VARIABLES
    ========================================== */
    .color-board {
        position: relative; width: 100%; height: 450px; 
        background: #1e272e; border-radius: 20px; overflow: hidden;
        border: 4px solid var(--primary); margin-bottom: 20px;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.8);
        display: flex; flex-direction: column; justify-content: flex-end;
    }

    /* ==========================================
       FASE 1: TUTORIAL (ENSEÑANZA OBLIGATORIA)
    ========================================== */
    .tutorial-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s;
    }
    .tutorial-icon { font-size: 80px; margin-bottom: 10px; animation: bounce 2s infinite; }
    .tutorial-word { font-size: 40px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;}
    .tutorial-btn {
        margin-top: 20px; padding: 15px 40px; font-size: 22px; font-weight: bold;
        background: var(--accent); color: white; border: none; border-radius: 30px;
        cursor: pointer; box-shadow: 0 6px 0 #d35400; transition: 0.2s;
    }
    .tutorial-btn:active { transform: translateY(6px); box-shadow: 0 0 0 #d35400; }

    /* ==========================================
       FASE 2: EL OVNI Y EL DIBUJO
    ========================================== */
    /* El OVNI (Dibujado 100% en CSS) */
    .css-ufo {
        position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
        width: 120px; height: 50px; z-index: 20;
        transition: top linear; /* Se controla con JS */
    }
    .ufo-dome {
        position: absolute; top: 0; left: 30px; width: 60px; height: 35px;
        background: rgba(129, 236, 236, 0.6); border-radius: 30px 30px 0 0;
        border: 2px solid #00cec9; z-index: 2;
    }
    .ufo-base {
        position: absolute; bottom: 0; width: 100%; height: 25px;
        background: #b2bec3; border-radius: 20px; border: 3px solid #2d3436;
        box-shadow: inset 0 -5px 0 rgba(0,0,0,0.2); z-index: 3;
    }
    .ufo-lights {
        position: absolute; bottom: 5px; left: 15px; width: 90px;
        display: flex; justify-content: space-between; z-index: 4;
    }
    .ufo-light { width: 8px; height: 8px; background: #ffeaa7; border-radius: 50%; animation: blink 0.5s infinite alternate; }
    
    /* Rayo Tractor */
    .tractor-beam {
        position: absolute; top: 40px; left: 50%; transform: translateX(-50%);
        width: 80px; height: 0px; /* Crece con JS */
        background: linear-gradient(to bottom, rgba(0, 206, 201, 0.8), rgba(0, 206, 201, 0.1));
        clip-path: polygon(20% 0, 80% 0, 100% 100%, 0 100%);
        z-index: 1; transition: height linear;
    }

    /* El Dibujo (Canvas) */
    .target-canvas {
        position: absolute; bottom: 120px; left: 50%; transform: translateX(-50%);
        font-size: 100px; z-index: 10;
        filter: grayscale(100%) brightness(1.5); /* Empieza sin color */
        transition: filter 1s, transform 0.3s;
    }
    .target-canvas.colored { filter: grayscale(0%) brightness(1); animation: celebrate 1s; }
    .target-canvas.abducted { bottom: 100%; opacity: 0; transition: bottom 1s, opacity 1s; }

    /* ==========================================
       INTERFAZ DE CUBOS DE PINTURA (ARMA)
    ========================================== */
    .paint-station {
        width: 100%; height: 100px; background: #2f3640; border-top: 5px solid #353b48;
        display: flex; justify-content: center; align-items: center; gap: 20px; z-index: 30;
    }
    
    .paint-bucket {
        position: relative; width: 60px; height: 60px;
        background: #f1f2f6; border: 4px solid #747d8c; border-radius: 10px 10px 15px 15px;
        cursor: pointer; display: flex; align-items: flex-start; justify-content: center;
        box-shadow: 0 10px 0 rgba(0,0,0,0.2); transition: 0.1s;
    }
    .paint-bucket:active { transform: translateY(8px); box-shadow: 0 2px 0 rgba(0,0,0,0.2); }
    
    /* Color dentro del cubo */
    .paint-fill {
        width: 100%; height: 80%; border-radius: 5px 5px 10px 10px;
        border-bottom: 5px solid rgba(0,0,0,0.2);
    }

    /* Salpicadura de victoria (Splat) */
    .splat {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0);
        width: 150px; height: 150px; background: currentColor;
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        border-radius: 30%; z-index: 40; opacity: 0; pointer-events: none;
    }
    .splat-anim { animation: splatPop 0.6s forwards; }

    /* ==========================================
       ANIMACIONES GENERALES
    ========================================== */
    @keyframes blink { 0% { background: #ffeaa7; } 100% { background: #ff7675; } }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    @keyframes celebrate { 0% { transform: translateX(-50%) scale(1); } 50% { transform: translateX(-50%) scale(1.3) rotate(10deg); } 100% { transform: translateX(-50%) scale(1) rotate(0); } }
    @keyframes splatPop { 0% { transform: translate(-50%, -50%) scale(0); opacity: 1; } 50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.8; } 100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; } }
    @keyframes shakeScreen { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 50% { transform: translateX(10px); } 75% { transform: translateX(-10px); } }
    @keyframes ufoExplode { 0% { filter: brightness(1); } 50% { filter: brightness(5) hue-rotate(90deg) scale(1.2); opacity: 1;} 100% { transform: translateX(-50%) scale(0); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>👽 ¡Rescata el Color!</h3>

    <div class="color-board" id="game-board">
        
        <div class="tutorial-overlay" id="tutorial-screen">
            <h2 style="color: #666; margin-bottom: 20px;">Escucha y Aprende</h2>
            <div class="tutorial-icon" style="color: <?php echo $target_color_hex; ?>;">
                <?php echo $target_item_emoji; ?>
            </div>
            <div class="tutorial-word" style="color: <?php echo $target_color_hex; ?>;">
                <?php echo htmlspecialchars($target_color_name); ?>
            </div>
            <p style="color: #888; font-size: 18px;">(<?php echo htmlspecialchars($translation); ?>)</p>
            
            <button class="tutorial-btn" onclick="startActionPhase()">
                ▶️ ¡Entendido, a jugar!
            </button>
            <p style="margin-top: 15px; color: var(--accent); cursor: pointer; font-weight: bold;" onclick="playLessonAudio()">
                🔊 Escuchar de nuevo
            </p>
        </div>

        <div class="css-ufo" id="ufo">
            <div class="ufo-dome"></div>
            <div class="ufo-base"></div>
            <div class="ufo-lights">
                <div class="ufo-light"></div><div class="ufo-light"></div><div class="ufo-light"></div>
            </div>
            <div class="tractor-beam" id="beam"></div>
        </div>

        <div class="target-canvas" id="canvas-item">
            <?php echo $target_item_emoji; ?>
            <div class="splat" id="splat-effect" style="color: <?php echo $target_color_hex; ?>;"></div>
        </div>

        <div class="paint-station">
            <?php foreach ($all_colors as $color): ?>
                <div class="paint-bucket" data-correct="<?php echo isset($color['correct']) ? '1' : '0'; ?>" onclick="shootColor(this, '<?php echo $color['hex']; ?>', '<?php echo addslashes($color['name']); ?>')">
                    <div class="paint-fill" style="background: <?php echo $color['hex']; ?>;"></div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
    // ==========================================
    // LÓGICA DEL JUEGO
    // ==========================================
    const timeLimit = <?php echo $time_limit; ?>;
    const board = document.getElementById('game-board');
    const ufo = document.getElementById('ufo');
    const beam = document.getElementById('beam');
    const canvasItem = document.getElementById('canvas-item');
    const tutorialScreen = document.getElementById('tutorial-screen');
    const splat = document.getElementById('splat-effect');
    
    let gameActive = false;
    let ufoInterval = null;
    let ufoY = -80; // Posición inicial fuera de pantalla
    const targetY = 200; // Nivel donde el rayo tractor alcanza el objeto
    const step = (targetY - ufoY) / (timeLimit * 10); // Movimiento cada 100ms

    // Reproducir audio automáticamente al cargar el tutorial
    setTimeout(playLessonAudio, 500);

    function playLessonAudio() {
        if(typeof playTTS !== 'undefined') playTTS('<?php echo addslashes($target_color_name); ?>');
    }

    // ==========================================
    // INICIAR LA ACCIÓN (CERRAR TUTORIAL)
    // ==========================================
    function startActionPhase() {
        tutorialScreen.style.opacity = '0';
        setTimeout(() => { tutorialScreen.style.display = 'none'; }, 500);
        
        gameActive = true;
        
        // El OVNI empieza a bajar
        ufoInterval = setInterval(() => {
            if (!gameActive) return;
            
            ufoY += step;
            ufo.style.top = ufoY + 'px';
            
            // El rayo tractor crece a medida que baja
            if(ufoY > 0) {
                beam.style.height = (ufoY + 50) + 'px';
            }

            // Condición de derrota: El rayo toca el objeto
            if (ufoY >= targetY) {
                executeLoss();
            }
        }, 100);
    }

    // ==========================================
    // DISPARAR PINTURA (INTERACCIÓN)
    // ==========================================
    function shootColor(bucketEl, hexColor, colorName) {
        if (!gameActive) return;

        // Leer el color que el niño tocó (Feedback constante)
        if(typeof playTTS !== 'undefined') playTTS(colorName);

        const isCorrect = bucketEl.getAttribute('data-correct') === '1';

        if (isCorrect) {
            // ¡Acierto!
            gameActive = false;
            clearInterval(ufoInterval);
            
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();

            // Animar el Splat (Salpicadura)
            splat.style.color = hexColor;
            splat.classList.add('splat-anim');

            // Devolver el color al dibujo y destruir el OVNI
            setTimeout(() => {
                canvasItem.classList.add('colored');
                beam.style.display = 'none'; // Apagar el rayo
                ufo.style.animation = 'ufoExplode 0.8s forwards'; // El OVNI huye o explota
                
                setTimeout(executeWin, 1000);
            }, 300);

        } else {
            // ¡Error!
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            
            // Penalización visual
            board.style.animation = 'shakeScreen 0.4s';
            bucketEl.style.opacity = '0.3'; // Desactivar el balde incorrecto
            
            // El OVNI baja de golpe un poco más rápido por el error
            ufoY += 20; 
            
            setTimeout(() => { board.style.animation = 'none'; }, 400);
        }
    }

    // ==========================================
    // VICTORIA Y DERROTA
    // ==========================================
    function executeWin() {
        // Reproducir la frase completa de victoria
        if(typeof playTTS !== 'undefined') playTTS('The <?php echo addslashes($target_item_emoji); ?> is <?php echo addslashes($target_color_name); ?>');
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        
        if(typeof unlockNextButton !== 'undefined') {
            unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
        }
    }

    function executeLoss() {
        gameActive = false;
        clearInterval(ufoInterval);
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();

        // El OVNI se roba el dibujo
        beam.style.background = 'linear-gradient(to bottom, rgba(231, 76, 60, 0.8), rgba(231, 76, 60, 0.1))'; // Rayo rojo de captura
        canvasItem.classList.add('abducted');

        setTimeout(() => {
            alert("¡Oh no! El OVNI se robó el dibujo. ¡Inténtalo más rápido!");
            location.reload();
        }, 1500);
    }
</script>