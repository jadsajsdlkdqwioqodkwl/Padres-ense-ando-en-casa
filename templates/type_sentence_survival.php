<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: SENTENCE SURVIVAL (MULTIRONDA)
// ==========================================
$time_limit = $lesson_data['time_limit'] ?? 30; 
$reward_stars = $lesson['reward_stars'] ?? 15;

// Estructura Multironda: Adaptamos a JSON antiguo o array de rondas nuevo
$rounds = $lesson_data['rounds'] ?? [
    [
        'sentence' => $lesson_data['sentence'] ?? ['I', 'AM', 'HAPPY'],
        'translation' => $lesson_data['translation'] ?? 'Yo soy feliz',
        'distractors' => $lesson_data['distractors'] ?? ['YOU', 'SAD'],
        'context_es' => $lesson_data['context_es'] ?? "¡Construye el puente en el orden correcto para cruzar el río!"
    ]
];
?>

<style>
    /* ==========================================
       ENTORNO DE JUEGO
    ========================================== */
    .survival-board {
        position: relative; width: 100%; height: 350px; 
        background: linear-gradient(to bottom, #74b9ff 0%, #0984e3 100%);
        border-radius: 20px; overflow: hidden;
        border: 4px solid var(--primary); margin-bottom: 20px;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.3);
    }

    .round-indicator { 
        position: absolute; top: 15px; left: 15px; color: white; 
        font-weight: bold; font-size: 14px; z-index: 50; 
        background: var(--dark); padding: 5px 15px; border-radius: 20px;
    }

    /* Río y Plataformas */
    .river-waves {
        position: absolute; bottom: 0; width: 100%; height: 120px;
        background: #00cec9; opacity: 0.6; z-index: 5;
        animation: waveAction 3s infinite alternate;
    }
    .platform-start {
        position: absolute; bottom: 20px; left: 0; width: 80px; height: 150px;
        background: #55efc4; border-right: 10px solid #00b894; border-radius: 0 20px 0 0; z-index: 10;
    }
    .platform-end {
        position: absolute; bottom: 20px; right: 0; width: 80px; height: 150px;
        background: #55efc4; border-left: 10px solid #00b894; border-radius: 20px 0 0 0; z-index: 10;
    }

    /* Personaje CSS */
    .css-hero {
        position: absolute; bottom: 170px; left: 10px; width: 50px; height: 60px;
        background: var(--accent); border-radius: 20px 20px 5px 5px; z-index: 15;
        box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2); transition: left 0.5s ease, bottom 0.5s ease;
    }
    .css-hero::after {
        content: ''; position: absolute; top: 10px; right: 10px; width: 15px; height: 15px;
        background: white; border-radius: 50%; border: 3px solid var(--dark);
    }
    .hero-jump { animation: jumpArc 0.5s forwards; }
    .hero-fall { animation: fallDown 1s forwards; }

    /* ==========================================
       SLOTS Y BURBUJAS DE PALABRAS
    ========================================== */
    .sentence-slots {
        position: absolute; bottom: 170px; left: 80px; right: 80px;
        display: flex; justify-content: center; gap: 10px; z-index: 12;
    }
    .word-slot {
        height: 40px; min-width: 60px; padding: 0 15px;
        border: 3px dashed rgba(255,255,255,0.8); border-radius: 10px;
        background: rgba(0,0,0,0.1); display: flex; justify-content: center; align-items: center;
        color: white; font-weight: bold; font-size: 18px; transition: 0.3s;
    }
    .word-slot.filled {
        border-style: solid; border-color: var(--success); background: var(--success);
        box-shadow: 0 5px 0 #27ae60; transform: translateY(-5px);
    }

    .word-pool {
        display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; margin-top: 20px; min-height: 80px;
    }
    .word-bubble {
        padding: 12px 25px; background: white; color: var(--primary);
        border: 2px solid var(--primary); border-radius: 20px;
        font-size: 20px; font-weight: bold; cursor: pointer;
        box-shadow: 0 6px 0 var(--primary); transition: 0.1s, opacity 0.3s; user-select: none;
    }
    .word-bubble:active { transform: translateY(4px); box-shadow: 0 2px 0 var(--primary); }
    .word-bubble.used { opacity: 0; pointer-events: none; transform: scale(0); }

    /* ==========================================
       MODAL DE TUTORIAL
    ========================================== */
    .mission-modal {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s; padding: 20px; text-align: center;
    }
    .btn-action { 
        background: var(--success); color: white; border: none; padding: 15px 30px; 
        font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; 
        box-shadow: 0 6px 0 #27ae60; margin-top: 15px;
    }

    @keyframes waveAction { 0% { transform: translateY(0); } 100% { transform: translateY(10px); } }
    @keyframes jumpArc { 0% { transform: translateY(0); } 50% { transform: translateY(-40px); } 100% { transform: translateY(0); } }
    @keyframes fallDown { 0% { transform: translateY(0) rotate(0); } 100% { transform: translateY(200px) rotate(90deg); opacity: 0; } }
    @keyframes errorShake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary);">🌉 Sentence Survival</h3>
        <button onclick="giveHint()" style="background: #f1c40f; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #f39c12;" title="Pedir Pista">💡</button>
    </div>

    <div class="survival-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Ronda 1</div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">📜 Construye el Puente</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 10px;" id="tut-context">Cargando...</p>
            <div style="font-size: 28px; font-weight: bold; color: var(--dark); margin: 15px 0; background: var(--light); padding: 15px; border-radius: 15px;" id="tut-trans">
                Traducción
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡A Jugar!</button>
            </div>
        </div>

        <div class="river-waves"></div>
        <div class="platform-start"></div>
        <div class="platform-end"></div>
        
        <div class="css-hero" id="hero"></div>
        <div class="sentence-slots" id="slots-container"></div>
    </div>

    <div class="word-pool" id="pool-container"></div>
</div>

<script>
    // ==========================================
    // ESTADO DEL JUEGO MULTIRONDA
    // ==========================================
    const roundsData = <?php echo json_encode($rounds); ?>;
    let currentRoundIndex = 0;
    let currentWordIndex = 0;
    let currentSentence = [];
    
    let gameActive = false;
    const hero = document.getElementById('hero');
    
    // Iniciar el juego
    loadRound(currentRoundIndex);

    // ==========================================
    // CARGADOR DE RONDAS
    // ==========================================
    function loadRound(index) {
        const round = roundsData[index];
        currentSentence = round.sentence;
        currentWordIndex = 0;

        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-trans').innerText = `"${round.translation}"`;

        // 1. Resetear Héroe
        hero.style.left = '10px';
        hero.style.bottom = '170px';
        hero.classList.remove('hero-fall', 'hero-jump');

        // 2. Generar Slots (Puente vacío)
        let slotsHTML = '';
        currentSentence.forEach((word, i) => {
            slotsHTML += `<div class="word-slot" id="slot-${i}" data-expected="${word}">?</div>`;
        });
        document.getElementById('slots-container').innerHTML = slotsHTML;

        // 3. Generar Burbujas de Palabras (Barajadas)
        let allWords = [...currentSentence, ...(round.distractors || [])].sort(() => Math.random() - 0.5);
        let poolHTML = '';
        allWords.forEach((word, idx) => {
            poolHTML += `<div class="word-bubble" draggable="true" id="bubble-${idx}" data-word="${word}" onclick="handleWordClick(this)">${word}</div>`;
        });
        document.getElementById('pool-container').innerHTML = poolHTML;

        // 4. Mostrar Tutorial
        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';

        attachDragEvents();
        setTimeout(playSpanglishIntro, 500);
    }

    // ==========================================
    // SPANGLISH Y DRAG & DROP
    // ==========================================
function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        
        // Unimos el array ["I", "AM", "HAPPY"] en una sola frase "I AM HAPPY"
        const fullSentenceEN = round.sentence.join(' ');

        // Usa el nuevo motor: (Contexto ES, Palabra EN, Significado ES)
        playSpanglish(
            round.context_es, 
            fullSentenceEN, 
            "Que significa " + round.translation
        );
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 500);
        gameActive = true;
    }

    function attachDragEvents() {
        const bubbles = document.querySelectorAll('.word-bubble');
        const slots = document.querySelectorAll('.word-slot');

        bubbles.forEach(bubble => {
            bubble.addEventListener('dragstart', (e) => {
                if(!gameActive) { e.preventDefault(); return; }
                e.dataTransfer.setData('text/plain', bubble.id);
                bubble.style.opacity = '0.5';
                readWord(bubble.getAttribute('data-word'));
            });
            bubble.addEventListener('dragend', () => bubble.style.opacity = '1');
        });

        slots.forEach(slot => {
            slot.addEventListener('dragover', (e) => e.preventDefault());
            slot.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!gameActive) return;
                const bubbleId = e.dataTransfer.getData('text/plain');
                processWord(document.getElementById(bubbleId));
            });
        });
    }

    function handleWordClick(bubbleEl) {
        if (!gameActive || bubbleEl.classList.contains('used')) return;
        readWord(bubbleEl.getAttribute('data-word'));
        processWord(bubbleEl);
    }

    function readWord(word) {
        if(typeof playTTS !== 'undefined') {
            const u = new SpeechSynthesisUtterance(word); u.lang = 'en-US'; window.speechSynthesis.speak(u);
        }
    }

    // ==========================================
    // LÓGICA DE VALIDACIÓN (PUENTE)
    // ==========================================
    function processWord(bubbleEl) {
        if (!bubbleEl || currentWordIndex >= currentSentence.length) return;

        const selectedWord = bubbleEl.getAttribute('data-word');
        const currentSlot = document.getElementById('slot-' + currentWordIndex);
        const expectedWord = currentSlot.getAttribute('data-expected');

        if (selectedWord === expectedWord) {
            // Acierto
            currentSlot.innerText = selectedWord;
            currentSlot.classList.add('filled');
            bubbleEl.classList.add('used');
            
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }

            // Héroe avanza sobre la nueva tabla del puente
            hero.classList.add('hero-jump');
            setTimeout(() => hero.classList.remove('hero-jump'), 500);
            
            // Calcular avance
            const slotRect = currentSlot.getBoundingClientRect();
            const boardRect = document.getElementById('game-board').getBoundingClientRect();
            const newLeft = (slotRect.left - boardRect.left) + (slotRect.width / 2) - 25;
            hero.style.left = newLeft + 'px';

            currentWordIndex++;

            if (currentWordIndex === currentSentence.length) {
                checkNextRound();
            }

        } else {
            // Error
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            bubbleEl.style.animation = 'errorShake 0.3s';
            setTimeout(() => bubbleEl.style.animation = 'none', 300);
        }
    }

    // ==========================================
    // PISTAS Y TRANSICIÓN
    // ==========================================
    function giveHint() {
        if(!gameActive || currentWordIndex >= currentSentence.length) return;
        
        const expectedWord = currentSentence[currentWordIndex];
        readWord(expectedWord);
        
        document.querySelectorAll('.word-bubble').forEach(b => {
            if(!b.classList.contains('used') && b.getAttribute('data-word') === expectedWord) {
                b.style.transform = 'scale(1.2)';
                b.style.boxShadow = '0 0 15px #f1c40f';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.boxShadow = '0 6px 0 var(--primary)'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        gameActive = false;
        
        // El héroe salta a la plataforma final
        setTimeout(() => {
            hero.style.left = 'calc(100% - 60px)';
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            
            const fullSentenceText = currentSentence.join(' ');
            if(typeof playTTS !== 'undefined') playTTS("¡Excelente! " + fullSentenceText);

            currentRoundIndex++;
            
            if (currentRoundIndex < roundsData.length) {
                setTimeout(() => loadRound(currentRoundIndex), 2500);
            } else {
                setTimeout(() => gameOver(true), 1500);
            }
        }, 800);
    }

    function gameOver(isWin) {
        if (isWin) {
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
        } else {
            hero.classList.add('hero-fall');
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            setTimeout(() => {
                alert("¡El puente se rompió! Inténtalo de nuevo.");
                location.reload(); 
            }, 1500);
        }
    }
</script>