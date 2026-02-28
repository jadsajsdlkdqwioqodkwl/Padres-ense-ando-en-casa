<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: COLOR RESCUE PRO (MULTIRONDA)
// ==========================================
$time_limit = $lesson_data['time_limit'] ?? 15; 
$reward_stars = $lesson['reward_stars'] ?? 10;

// Estructura Multironda: Si no hay rondas en el SQL, cargamos una por defecto
$rounds = $lesson_data['rounds'] ?? [
    [
        'color_name' => 'Red', 'color_hex' => '#ff4757', 'item' => '🍎', 'translation' => 'Rojo',
        'context_es' => '¡El OVNI roba colores ataca! Pinta el dibujo antes de que se lo lleve.',
        'distractors' => [['name' => 'Blue', 'hex' => '#3742fa'], ['name' => 'Green', 'hex' => '#2ed573']]
    ]
];
?>

<style>
    /* ==========================================
       ENTORNO Y VARIABLES
    ========================================== */
    .color-board {
        position: relative; width: 100%; height: 450px; 
        background: var(--dark); border-radius: 20px; overflow: hidden;
        border: 4px solid var(--primary); margin-bottom: 20px;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.8);
        display: flex; flex-direction: column; justify-content: flex-end;
    }

    .round-indicator { 
        position: absolute; top: 15px; left: 15px; color: white; 
        font-weight: bold; font-size: 16px; z-index: 50; 
        background: var(--primary); padding: 5px 15px; border-radius: 20px;
    }

    /* ==========================================
       MODAL TUTORIAL
    ========================================== */
    .tutorial-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s; text-align: center; padding: 20px;
    }
    .tutorial-icon { font-size: 80px; margin-bottom: 10px; animation: bounce 2s infinite; }
    .tutorial-word { font-size: 40px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 2px;}
    .btn-action { 
        margin-top: 20px; padding: 15px 40px; font-size: 20px; font-weight: bold; 
        background: var(--success); color: white; border: none; border-radius: 30px; 
        cursor: pointer; box-shadow: 0 6px 0 #27ae60; transition: 0.2s; 
    }
    .btn-action:active { transform: translateY(6px); box-shadow: 0 0 0 #27ae60; }

    /* ==========================================
       EL OVNI Y EL DIBUJO
    ========================================== */
    .css-ufo {
        position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
        width: 120px; height: 50px; z-index: 20; transition: top linear; 
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
    .ufo-light { 
        width: 8px; height: 8px; background: #ffeaa7; border-radius: 50%; 
        animation: blink 0.5s infinite alternate; 
    }
    
    .tractor-beam {
        position: absolute; top: 40px; left: 50%; transform: translateX(-50%);
        width: 80px; height: 0px; 
        background: linear-gradient(to bottom, rgba(0, 206, 201, 0.8), rgba(0, 206, 201, 0.1));
        clip-path: polygon(20% 0, 80% 0, 100% 100%, 0 100%);
        z-index: 1; transition: height linear;
    }

    .target-canvas {
        position: absolute; bottom: 120px; left: 50%; transform: translateX(-50%);
        font-size: 100px; z-index: 10; filter: grayscale(100%) brightness(1.5); 
        transition: filter 1s, transform 0.3s;
    }
    .target-canvas.colored { filter: grayscale(0%) brightness(1); animation: celebrate 1s; }
    .target-canvas.abducted { bottom: 100%; opacity: 0; transition: bottom 1s, opacity 1s; }

    /* ==========================================
       ESTACIÓN DE PINTURA DINÁMICA
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
    .paint-fill { width: 100%; height: 80%; border-radius: 5px 5px 10px 10px; border-bottom: 5px solid rgba(0,0,0,0.2); }

    .splat {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0);
        width: 150px; height: 150px; background: currentColor;
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        border-radius: 30%; z-index: 40; opacity: 0; pointer-events: none;
    }
    .splat-anim { animation: splatPop 0.6s forwards; }

    @keyframes blink { 0% { background: #ffeaa7; } 100% { background: #ff7675; } }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    @keyframes celebrate { 0% { transform: translateX(-50%) scale(1); } 50% { transform: translateX(-50%) scale(1.3) rotate(10deg); } 100% { transform: translateX(-50%) scale(1) rotate(0); } }
    @keyframes splatPop { 0% { transform: translate(-50%, -50%) scale(0); opacity: 1; } 50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.8; } 100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; } }
    @keyframes shakeScreen { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 50% { transform: translateX(10px); } 75% { transform: translateX(-10px); } }
    @keyframes ufoExplode { 0% { filter: brightness(1); } 50% { filter: brightness(5) hue-rotate(90deg) scale(1.2); opacity: 1;} 100% { transform: translateX(-50%) scale(0); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    <h3 style="margin: 0; margin-bottom: 15px; color: var(--primary);">🛸 Color Rescue</h3>

    <div class="color-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Ronda 1/3</div>

        <div class="tutorial-overlay" id="tutorial-screen">
            <h2 style="color: var(--primary); margin-top: 0; margin-bottom: 10px;" id="tut-title">Misión</h2>
            <div class="tutorial-icon" id="tut-icon">🍎</div>
            <div class="tutorial-word" id="tut-word">RED</div>
            <p style="color: var(--text-muted); font-size: 20px; margin-bottom: 15px;" id="tut-trans">(Rojo)</p>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playLessonAudio()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startActionPhase()" style="display: none;">▶️ ¡Salvar Dibujo!</button>
            </div>
        </div>

        <div class="css-ufo" id="ufo">
            <div class="ufo-dome"></div><div class="ufo-base"></div>
            <div class="ufo-lights"><div class="ufo-light"></div><div class="ufo-light"></div><div class="ufo-light"></div></div>
            <div class="tractor-beam" id="beam"></div>
        </div>

        <div class="target-canvas" id="canvas-item">
            <span id="canvas-emoji">🍎</span>
            <div class="splat" id="splat-effect"></div>
        </div>

        <div class="paint-station" id="paint-station"></div>
    </div>
</div>

<script>
    // ==========================================
    // ESTADO MULTIRONDA
    // ==========================================
    const roundsData = <?php echo json_encode($rounds); ?>;
    const timeLimit = <?php echo $time_limit; ?>;
    let currentRoundIndex = 0;
    
    const board = document.getElementById('game-board');
    const ufo = document.getElementById('ufo');
    const beam = document.getElementById('beam');
    const canvasItem = document.getElementById('canvas-item');
    const tutorialScreen = document.getElementById('tutorial-screen');
    const splat = document.getElementById('splat-effect');
    
    let gameActive = false;
    let ufoInterval = null;
    let ufoY = -80; 
    const targetY = 200; 
    const step = (targetY - ufoY) / (timeLimit * 10); 

    // Iniciar la primera ronda al cargar
    loadRound(currentRoundIndex);

    // ==========================================
    // CARGADOR DE RONDAS
    // ==========================================
    function loadRound(index) {
        const round = roundsData[index];
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        
        // 1. Actualizar Textos del Tutorial
        document.getElementById('tut-title').innerText = round.context_es || "¡Salva el color!";
        document.getElementById('tut-icon').innerText = round.item;
        document.getElementById('tut-icon').style.color = round.color_hex;
        document.getElementById('tut-word').innerText = round.color_name;
        document.getElementById('tut-word').style.color = round.color_hex;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        
        // 2. Actualizar el Escenario
        document.getElementById('canvas-emoji').innerText = round.item;
        canvasItem.classList.remove('colored', 'abducted');
        
        // Resetear OVNI
        ufoY = -80;
        ufo.style.top = ufoY + 'px';
        ufo.style.animation = 'none';
        ufo.style.filter = 'brightness(1)';
        ufo.style.transform = 'translateX(-50%) scale(1)';
        ufo.style.opacity = '1';
        
        beam.style.height = '0px';
        beam.style.display = 'block';

        // 3. Generar Botones de Pintura Dinámicamente
        const station = document.getElementById('paint-station');
        station.innerHTML = '';
        
        let allColors = [...round.distractors];
        allColors.push({ name: round.color_name, hex: round.color_hex, correct: true });
        allColors.sort(() => Math.random() - 0.5); // Barajar

        allColors.forEach(c => {
            const isCorrect = c.correct ? '1' : '0';
            station.innerHTML += `
                <div class="paint-bucket" data-correct="${isCorrect}" onclick="shootColor(this, '${c.hex}', '${c.name}')">
                    <div class="paint-fill" style="background: ${c.hex};"></div>
                </div>
            `;
        });

        // 4. Mostrar Pantalla de Tutorial y Audio
        tutorialScreen.style.display = 'flex';
        tutorialScreen.style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';
        
        setTimeout(playLessonAudio, 500);
    }

    // ==========================================
    // AUDIO SPANGLISH
    // ==========================================
   function playLessonAudio() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        
        // Usa el nuevo motor: (Contexto ES, Palabra EN, Significado ES)
        playSpanglish(
            round.context_es, 
            round.color_name, 
            "Que significa " + round.translation
        );
    }

    // ==========================================
    // MOTOR DE JUEGO (ACCIÓN)
    // ==========================================
    function startActionPhase() {
        tutorialScreen.style.opacity = '0';
        setTimeout(() => { tutorialScreen.style.display = 'none'; }, 500);
        gameActive = true;
        
        ufoInterval = setInterval(() => {
            if (!gameActive) return;
            ufoY += step;
            ufo.style.top = ufoY + 'px';
            if(ufoY > 0) beam.style.height = (ufoY + 50) + 'px';
            if (ufoY >= targetY) executeLoss();
        }, 100);
    }

    function shootColor(bucketEl, hexColor, colorName) {
        if (!gameActive) return;

        if(typeof playTTS !== 'undefined') {
            const u = new SpeechSynthesisUtterance(colorName); u.lang = 'en-US'; window.speechSynthesis.speak(u);
        }

        const isCorrect = bucketEl.getAttribute('data-correct') === '1';

        if (isCorrect) {
            gameActive = false;
            clearInterval(ufoInterval);
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();

            splat.style.color = hexColor;
            splat.classList.add('splat-anim');

            setTimeout(() => {
                canvasItem.classList.add('colored');
                beam.style.display = 'none'; 
                ufo.style.animation = 'ufoExplode 0.8s forwards'; 
                
                setTimeout(() => {
                    splat.classList.remove('splat-anim'); 
                    checkNextRound(); 
                }, 1500);
            }, 300);

        } else {
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            board.style.animation = 'shakeScreen 0.4s';
            bucketEl.style.opacity = '0.3'; 
            ufoY += 30; // Castigo
            setTimeout(() => { board.style.animation = 'none'; }, 400);
        }
    }

    function checkNextRound() {
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            loadRound(currentRoundIndex);
        } else {
            executeWin();
        }
    }

    // ==========================================
    // VICTORIA Y DERROTA
    // ==========================================
    function executeWin() {
        if(typeof playTTS !== 'undefined') {
            const u = new SpeechSynthesisUtterance("Excellent! You saved all the colors!"); u.lang = 'en-US'; window.speechSynthesis.speak(u);
        }
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }

    function executeLoss() {
        gameActive = false;
        clearInterval(ufoInterval);
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();

        beam.style.background = 'linear-gradient(to bottom, rgba(231, 76, 60, 0.8), rgba(231, 76, 60, 0.1))'; 
        canvasItem.classList.add('abducted');

        setTimeout(() => {
            alert("¡Oh no! El OVNI se robó el dibujo. ¡Inténtalo de nuevo!");
            location.reload();
        }, 1500);
    }
</script>