<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE DEL JUEGO
// ==========================================
$word = strtoupper($lesson_data['word'] ?? 'APPLE');
$translation = $lesson_data['translation'] ?? 'Manzana';
$distractors = $lesson_data['distractors'] ?? ['X', 'Z', 'M'];
$time_limit = $lesson_data['time_limit'] ?? 15; // Segundos antes de que el monstruo llegue
$reward_stars = $lesson['reward_stars'] ?? 5;

$letters = str_split($word);
$scrambled = array_merge($letters, $distractors);
shuffle($scrambled);
?>

<style>
    /* ==========================================
       DIBUJOS CSS Y ANIMACIONES
    ========================================== */
    .game-board { position: relative; width: 100%; height: 250px; background: #e0f7fa; border-radius: 20px; overflow: hidden; border: 4px solid var(--primary); margin-bottom: 20px; }
    
    /* El Pastel (Objetivo) dibujado con CSS */
    .css-cake { position: absolute; right: 20px; bottom: 20px; width: 60px; height: 50px; background: #ff9ff3; border-radius: 10px 10px 0 0; border: 3px solid #333; z-index: 2;}
    .css-cake::before { content: ''; position: absolute; top: -15px; left: 25px; width: 5px; height: 15px; background: #ff4757; border-radius: 5px; } /* Vela */
    
    /* El Monstruo dibujado con CSS */
    .css-monster { 
        position: absolute; left: 10px; bottom: 20px; width: 70px; height: 70px; 
        background: #ff6b6b; border: 3px solid #333;
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        animation: morph 2s linear infinite, wobble 0.5s alternate infinite;
        transition: left 0.5s linear; z-index: 3;
    }
    .css-monster::before, .css-monster::after { /* Ojos */
        content: ''; position: absolute; top: 15px; width: 15px; height: 15px; background: white; border-radius: 50%; border: 2px solid #333;
    }
    .css-monster::before { left: 15px; } .css-monster::after { right: 15px; }
    .css-monster-mouth { position: absolute; bottom: 10px; left: 20px; width: 30px; height: 15px; background: #333; border-radius: 0 0 15px 15px; }

    /* Interfaz de Letras */
    .slot-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; }
    .letter-slot { width: 55px; height: 65px; border: 3px dashed #999; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 32px; font-weight: bold; background: white; box-shadow: inset 0 3px 6px rgba(0,0,0,0.1); }
    .letter-slot.filled { border-style: solid; border-color: var(--success); background: #f0fdf4; color: var(--success); }
    
    .bubbles-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; }
    .drag-bubble { width: 55px; height: 55px; background: var(--accent); color: white; border-radius: 15px; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold; cursor: grab; box-shadow: 0 6px 0 #d35400; transition: transform 0.1s; user-select: none; }
    .drag-bubble:active { cursor: grabbing; box-shadow: 0 2px 0 #d35400; transform: translateY(4px); }
    .drag-bubble.hidden { opacity: 0; pointer-events: none; }

    /* Efectos */
    .danger-zone { animation: flashRed 1s infinite; }
    @keyframes morph { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 60% 50% 40% 50%; } }
    @keyframes wobble { from { transform: translateY(0) rotate(-5deg); } to { transform: translateY(-10px) rotate(5deg); } }
    @keyframes flashRed { 0%, 100% { background: #e0f7fa; } 50% { background: #ffcccc; } }
    @keyframes zap { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.2) rotate(180deg); filter: brightness(5); } 100% { transform: scale(0); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>🛡️ ¡Detén al monstruo!</h3>
    <p style="color: #666; font-size: 18px; margin-bottom: 10px;">
        Arma la palabra en inglés: <strong style="color: var(--primary); font-size: 24px; cursor:pointer;" onclick="if(typeof playTTS !== 'undefined') playTTS('<?php echo $word; ?>')">🔊 <?php echo $translation; ?></strong>
    </p>

    <div class="game-board" id="game-board">
        <div class="css-monster" id="monster">
            <div class="css-monster-mouth"></div>
        </div>
        <div class="css-cake" id="cake"></div>
    </div>

    <div class="slot-container" id="slots-container">
        <?php foreach ($letters as $index => $l): ?>
            <div class="letter-slot" data-expected="<?php echo $l; ?>" data-index="<?php echo $index; ?>"></div>
        <?php endforeach; ?>
    </div>

    <div class="bubbles-container" id="bubbles-container">
        <?php foreach ($scrambled as $idx => $char): ?>
            <div class="drag-bubble" draggable="true" id="bubble-<?php echo $idx; ?>" data-char="<?php echo $char; ?>">
                <?php echo $char; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // ==========================================
    // LÓGICA DEL JUEGO Y ESTADO
    // ==========================================
    const wordLength = <?php echo count($letters); ?>;
    const timeLimit = <?php echo $time_limit; ?>; // Segundos
    let currentCorrect = 0;
    let gameActive = true;
    let monsterPos = 10; // Posición inicial izquierda (px)
    
    const monster = document.getElementById('monster');
    const gameBoard = document.getElementById('game-board');
    const boardWidth = gameBoard.offsetWidth;
    // El objetivo está a la derecha (~ boardWidth - 80px)
    const targetPos = boardWidth - 80; 
    const stepAmount = (targetPos - 10) / (timeLimit * 10); // Calculamos el paso por cada 100ms

    // Bucle del Monstruo (Se mueve cada 100ms)
    const monsterInterval = setInterval(() => {
        if (!gameActive) return;
        
        monsterPos += stepAmount;
        monster.style.left = monsterPos + 'px';

        // Alerta visual cuando está cerca (70% del camino)
        if (monsterPos > targetPos * 0.7) {
            gameBoard.classList.add('danger-zone');
        }

        // Condición de Derrota: El monstruo llega al pastel
        if (monsterPos >= targetPos) {
            gameOver(false);
        }
    }, 100);

    // ==========================================
    // SISTEMA DRAG AND DROP
    // ==========================================
    const bubbles = document.querySelectorAll('.drag-bubble');
    const slots = document.querySelectorAll('.letter-slot');

    bubbles.forEach(bubble => {
        bubble.addEventListener('dragstart', (e) => {
            if(!gameActive) { e.preventDefault(); return; }
            e.dataTransfer.setData('text/plain', bubble.id);
            bubble.style.opacity = '0.5';
            if(typeof playTTS !== 'undefined') playTTS(bubble.getAttribute('data-char')); // Refuerzo auditivo
        });
        bubble.addEventListener('dragend', () => { bubble.style.opacity = '1'; });
    });

    slots.forEach(slot => {
        slot.addEventListener('dragover', (e) => { e.preventDefault(); });
        slot.addEventListener('drop', (e) => {
            e.preventDefault();
            if (!gameActive || slot.classList.contains('filled')) return;

            const bubbleId = e.dataTransfer.getData('text/plain');
            const bubbleEl = document.getElementById(bubbleId);
            if (!bubbleEl) return;

            const draggedChar = bubbleEl.getAttribute('data-char');
            const expectedChar = slot.getAttribute('data-expected');

            // Lógica de Validación
            if (draggedChar === expectedChar) {
                // Acierto
                slot.innerText = draggedChar;
                slot.classList.add('filled');
                bubbleEl.classList.add('hidden');
                currentCorrect++;
                
                if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }

                // Empujar al monstruo un poco hacia atrás (recompensa táctica)
                monsterPos = Math.max(10, monsterPos - 30); 
                monster.style.left = monsterPos + 'px';

                // Condición de Victoria
                if (currentCorrect === wordLength) {
                    gameOver(true);
                }
            } else {
                // Error
                if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
                gameBoard.classList.add('danger-zone');
                setTimeout(() => gameBoard.classList.remove('danger-zone'), 300);
            }
        });
    });

    // ==========================================
    // FIN DEL JUEGO
    // ==========================================
    function gameOver(isWin) {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');

        if (isWin) {
            // Animación de derrota del monstruo (magia)
            monster.style.animation = 'zap 0.8s forwards';
            if(typeof playTTS !== 'undefined') playTTS('<?php echo $word; ?>');
            
            setTimeout(() => {
                if(typeof sfxWin !== 'undefined') sfxWin.play();
                if(typeof fireConfetti !== 'undefined') fireConfetti();
                // Liberar el siguiente nivel
                if(typeof unlockNextButton !== 'undefined') {
                    unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
                }
            }, 1000);
        } else {
            // El monstruo se come el pastel
            document.getElementById('cake').style.display = 'none';
            monster.style.transform = 'scale(1.5)';
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            
            setTimeout(() => {
                alert("¡Oh no! El monstruo se comió el pastel. ¡Inténtalo de nuevo!");
                location.reload(); // Reinicia el nivel
            }, 1000);
        }
    }
</script>