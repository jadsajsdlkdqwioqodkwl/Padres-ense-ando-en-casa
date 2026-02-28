<?php
// ==========================================
// CONFIGURACIÓN: THE DETECTIVE (GRAMÁTICA VISUAL)
// ==========================================
$reward_stars = $lesson['reward_stars'] ?? 20;

// Estructura Multironda
$rounds = $lesson_data['rounds'] ?? [
    [
        'sentence' => ['THE', 'DOG', 'JUMPS'],
        'phonetics' => ['da', 'dog', 'yamps'],
        'target_word' => 'JUMPS',
        'target_type' => 'Verbo (Acción)',
        'translation' => 'El perro salta',
        'scene_emoji' => '🐕💨',
        'context_es' => '¡Encuentra el Verbo para encender la luz!'
    ],
    [
        'sentence' => ['THE', 'CAT', 'SLEEPS'],
        'phonetics' => ['da', 'cat", "slips'],
        'target_word' => 'CAT',
        'target_type' => 'Sujeto (Quién lo hace)',
        'translation' => 'El gato duerme',
        'scene_emoji' => '🐈💤',
        'context_es' => '¡Encuentra el Sujeto para encender la luz!'
    ]
];
?>

<style>
    /* ==========================================
       HABITACIÓN OSCURA Y LINTERNA
    ========================================== */
    .detective-board {
        position: relative; width: 100%; height: 420px; 
        background: radial-gradient(circle 80px at 50% 50%, rgba(255,255,255,0.15) 0%, #111 100%);
        border-radius: 20px; overflow: hidden;
        border: 4px solid var(--dark); margin-bottom: 20px;
        box-shadow: inset 0 0 50px rgba(0,0,0,0.9);
        transition: background 0.1s ease-out; cursor: crosshair;
    }
    .detective-board.lights-on {
        background: #f1c40f !important; border-color: #f39c12;
        box-shadow: inset 0 0 30px rgba(255,255,255,0.8);
        transition: background 0.5s ease-in, border-color 0.5s;
    }

    .round-indicator { 
        position: absolute; top: 15px; left: 15px; color: white; 
        font-weight: bold; font-size: 14px; z-index: 50; 
        background: rgba(0,0,0,0.6); padding: 5px 15px; border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .lights-on .round-indicator { background: var(--primary); border: none; }

    /* ==========================================
       INSTRUCCIONES Y ESCENA (Centro)
    ========================================== */
    .target-instruction {
        position: absolute; top: 20%; left: 50%; transform: translateX(-50%);
        width: 80%; text-align: center; pointer-events: none; z-index: 20;
    }
    .target-type {
        font-size: 28px; font-weight: bold; color: #f1c40f; 
        text-shadow: 0 2px 10px rgba(0,0,0,0.8); letter-spacing: 2px;
        background: rgba(0,0,0,0.5); padding: 5px 20px; border-radius: 30px; display: inline-block;
    }
    .lights-on .target-type { color: var(--primary); text-shadow: none; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    .scene-container {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.5);
        font-size: 120px; opacity: 0; pointer-events: none; z-index: 10;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .lights-on .scene-container { opacity: 1; transform: translate(-50%, -40%) scale(1); animation: floatEmoji 2s infinite alternate; }

    /* ==========================================
       PANEL DE PALABRAS (Abajo)
    ========================================== */
    .words-panel {
        position: absolute; bottom: 20px; width: 100%;
        display: flex; justify-content: center; gap: 10px; z-index: 30;
    }
    .word-btn {
        background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.3);
        color: rgba(255,255,255,0.5); padding: 15px 25px; border-radius: 15px;
        font-size: 22px; font-weight: bold; cursor: pointer; transition: 0.3s;
        backdrop-filter: blur(5px);
    }
    .word-btn:hover { background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.5); transform: translateY(-5px); }
    .word-btn.wrong { animation: errorShake 0.4s; background: rgba(231, 76, 60, 0.5); border-color: #e74c3c; color: white; }
    
    .lights-on .word-btn { background: white; color: var(--text-muted); border-color: #ddd; box-shadow: 0 5px 0 #ccc; pointer-events: none; }
    .lights-on .word-btn.correct { background: var(--success); color: white; border-color: #27ae60; box-shadow: 0 5px 0 #218c74; transform: scale(1.1) translateY(-10px); z-index: 40; }

    /* ==========================================
       MODAL TUTORIAL Y ANIMACIONES
    ========================================== */
    .mission-modal {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s; padding: 20px; text-align: center;
    }
    .btn-action { background: var(--success); color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 6px 0 #27ae60; margin-top: 15px; }

    @keyframes errorShake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
    @keyframes floatEmoji { 0% { transform: translate(-50%, -40%) translateY(0); } 100% { transform: translate(-50%, -40%) translateY(-15px); } }
    @keyframes zapFlash { 0% { opacity: 0; } 50% { opacity: 1; background: white; } 100% { opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary);">🕵️‍♂️ The Detective</h3>
        <button onclick="giveHint()" style="background: #f1c40f; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #f39c12;" title="Pedir Pista">💡</button>
    </div>

    <div class="detective-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Caso 1</div>

        <div id="flash-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:80; opacity:0;"></div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">🔎 La Habitación Oscura</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 10px;" id="tut-context"></p>
            <div style="font-size: 28px; font-weight: bold; color: var(--dark); margin: 15px 0; background: var(--light); padding: 15px; border-radius: 15px;" id="tut-trans">
                Traducción
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar Pista</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡A Investigar!</button>
            </div>
        </div>

        <div class="target-instruction">
            <div style="color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 5px; text-transform: uppercase;" id="instruction-title">Busca el...</div>
            <div class="target-type" id="target-display">OBJETIVO</div>
        </div>

        <div class="scene-container" id="scene-emoji"></div>
        <div class="words-panel" id="words-container"></div>
    </div>
</div>

<script>
    const roundsData = <?php echo json_encode($rounds); ?>;
    let currentRoundIndex = 0;
    let currentSentence = [];
    let currentPhoneticsMap = {}; 
    let gameActive = false;
    let isLightOn = false;
    
    const board = document.getElementById('game-board');
    
    // ==========================================
    // EFECTO LINTERNA (Sigue al mouse/dedo)
    // ==========================================
    function moveFlashlight(x, y) {
        if (isLightOn || !gameActive) return;
        const rect = board.getBoundingClientRect();
        const relX = x - rect.left;
        const relY = y - rect.top;
        board.style.background = `radial-gradient(circle 120px at ${relX}px ${relY}px, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 40%, #111 100%)`;
    }

    board.addEventListener('mousemove', (e) => moveFlashlight(e.clientX, e.clientY));
    board.addEventListener('touchmove', (e) => {
        if(e.touches.length > 0) {
            e.preventDefault(); // Evita scroll
            moveFlashlight(e.touches[0].clientX, e.touches[0].clientY);
        }
    }, {passive: false});

    board.addEventListener('mouseleave', () => {
        if(!isLightOn) board.style.background = 'radial-gradient(circle 80px at 50% 50%, rgba(255,255,255,0.15) 0%, #111 100%)';
    });

    // ==========================================
    // INICIAR RONDA
    // ==========================================
    loadRound(currentRoundIndex);

    function loadRound(index) {
        const round = roundsData[index];
        currentSentence = round.sentence;
        isLightOn = false;
        board.classList.remove('lights-on');
        board.style.background = 'radial-gradient(circle 80px at 50% 50%, rgba(255,255,255,0.15) 0%, #111 100%)';

        document.getElementById('round-indicator').innerText = `Caso ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-trans').innerText = `"${round.translation}"`;
        document.getElementById('target-display').innerText = round.target_type;
        document.getElementById('scene-emoji').innerText = round.scene_emoji;

        // Diccionario Fonético Inteligente
        currentPhoneticsMap = {};
        if (round.phonetics && round.phonetics.length === round.sentence.length) {
            round.sentence.forEach((w, i) => { currentPhoneticsMap[w.toUpperCase()] = round.phonetics[i]; });
        }

        // Generar Botones de Palabras
        let wordsHTML = '';
        currentSentence.forEach((word, idx) => {
            wordsHTML += `<button class="word-btn" id="word-${idx}" data-word="${word}" onclick="handleWordClick(this)">${word}</button>`;
        });
        document.getElementById('words-container').innerHTML = wordsHTML;

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';
    }

    function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        // Leer el tipo de objetivo en español y luego la oración en inglés
        if(typeof playTTS !== 'undefined') {
            const sentenceAudio = round.phonetics ? round.phonetics.join(' ') : round.sentence.join(' ');
            playTTS(sentenceAudio); // Directo al inglés para no alargar
        }
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 500);
        gameActive = true;
    }

    // ==========================================
    // LÓGICA DE DETECCIÓN
    // ==========================================
    function handleWordClick(btnEl) {
        if (!gameActive || isLightOn) return;

        const selectedWord = btnEl.getAttribute('data-word');
        const expectedWord = roundsData[currentRoundIndex].target_word;

        // Lee la palabra individual al tocarla
        if(typeof playTTS !== 'undefined') {
            const wordUpper = selectedWord.toUpperCase();
            const phoneticToRead = currentPhoneticsMap[wordUpper] || wordUpper;
            playTTS(phoneticToRead);
        }

        if (selectedWord === expectedWord) {
            // ¡ACIERTO! Encender luces
            isLightOn = true;
            gameActive = false;
            btnEl.classList.add('correct');
            
            // Efecto Flash
            const flash = document.getElementById('flash-overlay');
            flash.style.animation = 'none';
            void flash.offsetWidth; // trigger reflow
            flash.style.animation = 'zapFlash 0.5s';
            
            board.classList.add('lights-on');
            document.getElementById('instruction-title').innerText = "¡Encontraste el...";

            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }

            // Leer oración completa al resolver
            setTimeout(() => {
                const round = roundsData[currentRoundIndex];
                const fullAudio = round.phonetics ? round.phonetics.join(' ') : currentSentence.join(' ');
                if(typeof playTTS !== 'undefined') playTTS(fullAudio);
                
                setTimeout(checkNextRound, 2500);
            }, 800);

        } else {
            // ERROR
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            btnEl.classList.add('wrong');
            setTimeout(() => btnEl.classList.remove('wrong'), 400);
        }
    }

    function giveHint() {
        if(!gameActive || isLightOn) return;
        const expectedWord = roundsData[currentRoundIndex].target_word;
        
        document.querySelectorAll('.word-btn').forEach(b => {
            if(b.getAttribute('data-word') === expectedWord) {
                b.style.transform = 'scale(1.1)';
                b.style.borderColor = '#f1c40f';
                b.style.color = '#f1c40f';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.borderColor = 'rgba(255,255,255,0.3)'; b.style.color = 'rgba(255,255,255,0.5)'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        currentRoundIndex++;
        
        if (currentRoundIndex < roundsData.length) {
            loadRound(currentRoundIndex);
        } else {
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
        }
    }
</script>