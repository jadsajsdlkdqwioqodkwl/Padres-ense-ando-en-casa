<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE DEL JUEGO DE GRAMÁTICA
// ==========================================
// La oración correcta en el orden exacto.
$correct_syntax = $lesson_data['sentence'] ?? ['I', 'have', 'a', 'red', 'apple'];
$translation_hint = $lesson_data['translation'] ?? 'Yo tengo una manzana roja';
// Palabras trampa para forzar el Active Recall (conectores o verbos incorrectos)
$distractors = $lesson_data['distractors'] ?? ['has', 'an', 'blue']; 
$time_limit = $lesson_data['time_limit'] ?? 20; // Segundos antes de que el agua suba
$reward_stars = $lesson['reward_stars'] ?? 10;

// Mezclamos todas las palabras para el banco de palabras
$all_words = array_merge($correct_syntax, $distractors);
shuffle($all_words);
?>

<style>
    /* ==========================================
       DIBUJOS CSS Y ANIMACIONES DEL ENTORNO
    ========================================== */
    .grammar-board { 
        position: relative; width: 100%; height: 320px; 
        background: linear-gradient(to bottom, #87CEEB 0%, #e0f7fa 100%); 
        border-radius: 20px; overflow: hidden; 
        border: 4px solid var(--primary); margin-bottom: 25px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Plataformas de inicio y fin */
    .cliff { position: absolute; bottom: 0; width: 60px; height: 120px; background: #8d6e63; border-top: 10px solid #7cb342; z-index: 5; }
    .cliff.left { left: 0; border-right: 4px solid #5d4037; border-radius: 0 10px 0 0; }
    .cliff.right { right: 0; border-left: 4px solid #5d4037; border-radius: 10px 0 0 0; }

    /* El Personaje (Un fantasmita/blob lindo hecho con CSS) */
    .css-blob {
        position: absolute; left: 10px; bottom: 130px; width: 40px; height: 40px;
        background: white; border-radius: 50% 50% 40% 40%;
        box-shadow: inset -5px -5px 0 rgba(0,0,0,0.1);
        transition: left 0.5s ease-in-out, bottom 0.5s ease-in, transform 0.3s;
        z-index: 10;
        animation: breathe 2s infinite alternate;
    }
    .css-blob::before, .css-blob::after { /* Ojos */
        content: ''; position: absolute; top: 12px; width: 6px; height: 6px; background: #333; border-radius: 50%;
    }
    .css-blob::before { left: 10px; } .css-blob::after { right: 10px; }

    /* Animación del Agua (El Peligro) */
    .water-level {
        position: absolute; bottom: -50px; left: 0; width: 100%; height: 100px;
        background: rgba(41, 128, 185, 0.8);
        border-top: 4px dashed #fff;
        transition: bottom linear; /* Se animará por JS */
        z-index: 8;
    }
    .water-waves {
        position: absolute; top: -15px; left: 0; width: 200%; height: 15px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%232980b9" fill-opacity="0.8" d="M0,160L48,170.7C96,181,192,203,288,197.3C384,192,480,160,576,165.3C672,171,768,213,864,224C960,235,1056,213,1152,186.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') repeat-x;
        background-size: 50% 100%;
        animation: waveMove 3s linear infinite;
    }

    /* ==========================================
       INTERFAZ DE CONSTRUCCIÓN GRAMATICAL
    ========================================== */
    .bridge-container {
        position: absolute; bottom: 120px; left: 60px; right: 60px; height: 60px;
        display: flex; justify-content: center; align-items: center; gap: 8px; z-index: 6;
    }
    
    .word-slot {
        flex: 1; height: 45px; border-bottom: 6px dashed #bdc3c7; 
        display: flex; justify-content: center; align-items: center;
        font-size: 18px; font-weight: bold; color: transparent;
        transition: 0.3s;
    }
    .word-slot.filled {
        border-bottom: 6px solid #8e44ad; background: #9b59b6; color: white;
        border-radius: 8px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        box-shadow: 0 4px 0 #732d91; transform: translateY(-4px);
    }
    .word-slot.error { border-bottom-color: #e74c3c; background: #e74c3c; animation: shake 0.4s; }
    .word-slot.success { border-bottom-color: #2ecc71; background: #2ecc71; box-shadow: 0 4px 0 #27ae60;}

    /* Banco de Palabras */
    .word-bank { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 20px; min-height: 80px;}
    .drag-word {
        padding: 12px 20px; background: white; border: 3px solid var(--primary);
        border-radius: 20px; font-size: 20px; font-weight: bold; color: var(--primary);
        cursor: grab; box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        transition: transform 0.1s, background 0.2s; user-select: none;
    }
    .drag-word:active { cursor: grabbing; transform: scale(0.95); }
    .drag-word.used { visibility: hidden; opacity: 0; }

    /* Controles */
    .controls { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
    .btn-check { 
        background: var(--accent); color: white; border: none; padding: 15px 30px; 
        font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer;
        box-shadow: 0 6px 0 #d35400; transition: 0.2s; opacity: 0.5; pointer-events: none;
    }
    .btn-check.ready { opacity: 1; pointer-events: auto; animation: pulse 1.5s infinite; }
    .btn-check:active { transform: translateY(6px); box-shadow: 0 0 0 #d35400; }

    /* Animaciones */
    @keyframes breathe { 0% { height: 40px; transform: translateY(0); } 100% { height: 38px; transform: translateY(-2px); } }
    @keyframes waveMove { 0% { background-position-x: 0; } 100% { background-position-x: 1000px; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
    @keyframes splash { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5) rotate(180deg); opacity: 0.5; } 100% { transform: scale(0); opacity: 0; } }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>🌉 Construye el Puente</h3>
    <p style="color: #666; font-size: 18px; margin-bottom: 10px;">
        Ordena las palabras: <strong style="color: var(--primary); font-size: 22px;">"<?php echo $translation_hint; ?>"</strong>
    </p>

    <div class="grammar-board" id="game-board">
        <div class="cliff left"></div>
        <div class="css-blob" id="player-blob"></div>
        
        <div class="bridge-container" id="bridge-container">
            <?php foreach ($correct_syntax as $index => $word): ?>
                <div class="word-slot" data-index="<?php echo $index; ?>" data-expected="<?php echo htmlspecialchars($word); ?>"></div>
            <?php endforeach; ?>
        </div>

        <div class="cliff right"></div>
        
        <div class="water-level" id="water-level">
            <div class="water-waves"></div>
        </div>
    </div>

    <div class="word-bank" id="word-bank">
        <?php foreach ($all_words as $idx => $w): ?>
            <div class="drag-word" draggable="true" id="word-<?php echo $idx; ?>" data-word="<?php echo htmlspecialchars($w); ?>">
                <?php echo htmlspecialchars($w); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="controls">
        <button class="btn-check" id="btn-reset" style="background: #95a5a6; box-shadow: 0 6px 0 #7f8c8d; opacity: 1; pointer-events: auto;" onclick="resetBridge()">🔄 Limpiar</button>
        <button class="btn-check" id="btn-check" onclick="checkGrammar()">🏃‍♂️ ¡Cruzar!</button>
    </div>
</div>

<script>
    // ==========================================
    // LÓGICA DEL MOTOR DE GRAMÁTICA
    // ==========================================
    const totalSlots = <?php echo count($correct_syntax); ?>;
    const timeLimit = <?php echo $time_limit; ?>; // En segundos
    let slotsFilled = 0;
    let gameActive = true;
    let currentBridge = new Array(totalSlots).fill(null);
    let draggedElementId = null;

    const water = document.getElementById('water-level');
    const blob = document.getElementById('player-blob');
    const btnCheck = document.getElementById('btn-check');
    const slots = document.querySelectorAll('.word-slot');
    const words = document.querySelectorAll('.drag-word');

    // Iniciar el peligro: El agua sube gradualmente
    // Transition duration en CSS por JS para sincronizar el tiempo
    water.style.transitionDuration = timeLimit + 's';
    setTimeout(() => {
        if(gameActive) water.style.bottom = '120px'; // Sube hasta el nivel del puente
    }, 100);

    // Temporizador de muerte (Dumb ways to die)
    const deathTimer = setTimeout(() => {
        if(gameActive) executeFail('¡Oh no! El agua subió demasiado.');
    }, timeLimit * 1000);

    // ==========================================
    // LÓGICA DRAG & DROP
    // ==========================================
    words.forEach(word => {
        word.addEventListener('dragstart', (e) => {
            if(!gameActive) return e.preventDefault();
            draggedElementId = word.id;
            e.dataTransfer.setData('text/plain', word.id);
            setTimeout(() => word.style.opacity = '0.5', 0);
            
            // Refuerzo auditivo de la palabra al agarrarla
            if(typeof playTTS !== 'undefined') playTTS(word.getAttribute('data-word'));
        });
        word.addEventListener('dragend', () => {
            if(word.style.opacity !== '0') word.style.opacity = '1';
        });
    });

    slots.forEach((slot, index) => {
        slot.addEventListener('dragover', (e) => e.preventDefault());
        slot.addEventListener('drop', (e) => {
            e.preventDefault();
            if (!gameActive || currentBridge[index] !== null) return;

            const wordId = e.dataTransfer.getData('text/plain') || draggedElementId;
            const wordEl = document.getElementById(wordId);
            if (!wordEl) return;

            const text = wordEl.getAttribute('data-word');

            // Llenar el slot visualmente
            slot.innerText = text;
            slot.classList.add('filled');
            slot.setAttribute('data-source-id', wordId);
            
            // Ocultar del banco
            wordEl.classList.add('used');
            
            // Actualizar estado lógico
            currentBridge[index] = text;
            slotsFilled++;
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime = 0; sfxCorrect.play(); }

            // Si el puente está lleno, activar botón de comprobación
            if (slotsFilled === totalSlots) {
                btnCheck.classList.add('ready');
            }
        });
    });

    // ==========================================
    // VALIDACIÓN Y CONSECUENCIAS (ACTIVE RECALL)
    // ==========================================
    function checkGrammar() {
        if (!gameActive || slotsFilled < totalSlots) return;

        let isCorrect = true;
        slots.forEach((slot, index) => {
            const expected = slot.getAttribute('data-expected');
            if (currentBridge[index] !== expected) {
                isCorrect = false;
                slot.classList.add('error');
            } else {
                slot.classList.add('success');
            }
        });

        if (isCorrect) {
            executeWin();
        } else {
            // El puente se rompe, penalización cómica
            setTimeout(() => { executeFail('¡Uy! Esa oración estaba inestable.'); }, 600);
        }
    }

    function resetBridge() {
        if(!gameActive) return;
        slots.forEach((slot, index) => {
            if(currentBridge[index] !== null) {
                const sourceId = slot.getAttribute('data-source-id');
                document.getElementById(sourceId).classList.remove('used');
                slot.innerText = '';
                slot.classList.remove('filled', 'error', 'success');
                slot.removeAttribute('data-source-id');
                currentBridge[index] = null;
            }
        });
        slotsFilled = 0;
        btnCheck.classList.remove('ready');
    }

    function executeWin() {
        gameActive = false;
        clearTimeout(deathTimer);
        
        // Detener el agua
        const currentWaterPos = window.getComputedStyle(water).bottom;
        water.style.transitionDuration = '0s';
        water.style.bottom = currentWaterPos;

        // Leer la oración completa
        const fullSentence = currentBridge.join(' ');
        if(typeof playTTS !== 'undefined') playTTS(fullSentence);

        // Animar al personaje cruzando
        blob.style.left = 'calc(100% - 50px)'; // Se mueve a la derecha
        
        setTimeout(() => {
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            
            // Llamada asíncrona a tu sistema para liberar el siguiente botón
            if(typeof unlockNextButton !== 'undefined') {
                unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
            }
        }, 1200);
    }

    function executeFail(msg) {
        gameActive = false;
        clearTimeout(deathTimer);
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();

        // El puente visualmente "cae"
        slots.forEach(s => s.style.transform = 'translateY(50px) rotate(15deg)');
        
        // El personaje cae al agua
        blob.style.bottom = '40px';
        blob.style.animation = 'splash 0.8s forwards';

        setTimeout(() => {
            alert(msg + " ¡Inténtalo de nuevo!");
            location.reload();
        }, 1500);
    }
</script>