<?php
// ==========================================
// CONFIGURACIÓN: GRAMMAR TRAIN (TREN DE GRAMÁTICA)
// ==========================================
$reward_stars = $lesson['reward_stars'] ?? 20;

// Estructura Multironda para el Tren
$rounds = $lesson_data['rounds'] ?? [
    [
        'sentence' => ['I', 'HAVE', 'A', 'DOG'],
        'translations' => ['Yo', 'Tengo', 'Un', 'Perro'],
        'phonetics' => ['ai', 'jav', 'a', 'dog'],
        'sentence_phonetic' => 'ai jav a dog',
        'distractors' => ['CAT', 'HAS'],
        'distractors_phonetics' => ['cat', 'jas'],
        'context_es' => '¡Carga los vagones del tren uniendo el inglés con su significado!'
    ]
];
?>

<style>
    /* ==========================================
       ESCENARIO DEL TREN
    ========================================== */
    .train-board {
        position: relative; width: 100%; height: 400px; 
        background: linear-gradient(to bottom, #87CEEB 0%, #e0f7fa 70%, #7bc043 70%, #5c9432 100%);
        border-radius: 20px; overflow: hidden;
        border: 4px solid var(--primary); margin-bottom: 20px;
        box-shadow: inset 0 0 30px rgba(0,0,0,0.2);
    }

    .round-indicator { 
        position: absolute; top: 15px; left: 15px; color: white; 
        font-weight: bold; font-size: 14px; z-index: 50; 
        background: var(--dark); padding: 5px 15px; border-radius: 20px;
    }

    /* Vías del tren */
    .train-tracks {
        position: absolute; bottom: 30px; width: 100%; height: 15px;
        background: repeating-linear-gradient(90deg, #555 0px, #555 10px, transparent 10px, transparent 20px);
        border-top: 4px solid #333; border-bottom: 4px solid #333; z-index: 5;
    }

    /* Contenedor del tren animado */
    .train-container {
        position: absolute; bottom: 42px; left: 50%; transform: translateX(-50%);
        display: flex; align-items: flex-end; gap: 8px; z-index: 10;
        transition: left 1s ease-in-out;
    }
    .train-container.drive-away { animation: driveAway 2s forwards ease-in; }
    .train-container.drive-in { animation: driveIn 1s forwards ease-out; }

    /* Locomotora y Vagones */
    .locomotive {
        font-size: 80px; line-height: 0.8; filter: drop-shadow(5px 5px 0px rgba(0,0,0,0.3));
        animation: trainBounce 0.5s infinite alternate; z-index: 15;
    }
    .wagon-group { display: flex; flex-direction: column; align-items: center; position: relative;}
    .wagon {
        width: 85px; height: 75px; background: #e74c3c;
        border: 4px solid #c0392b; border-radius: 10px 10px 5px 5px;
        position: relative; display: flex; justify-content: center; align-items: center;
        box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2);
        animation: trainBounce 0.5s infinite alternate;
    }
    .wagon:nth-child(even) { background: #f1c40f; border-color: #f39c12; animation-delay: 0.1s;}
    .wagon:nth-child(3n) { background: #3498db; border-color: #2980b9; animation-delay: 0.2s;}

    /* Ruedas y Conectores */
    .wagon-wheels {
        position: absolute; bottom: -12px; width: 100%; display: flex; justify-content: space-around;
    }
    .wheel { width: 22px; height: 22px; background: #2c3e50; border-radius: 50%; border: 3px solid #7f8c8d; animation: spin 2s linear infinite; }
    .connector { position: absolute; right: -12px; bottom: 15px; width: 12px; height: 6px; background: #333; z-index: -1; }

    /* Traducción debajo del vagón */
    .wagon-translation {
        position: absolute; bottom: -45px; background: white; color: var(--primary);
        padding: 4px 10px; border-radius: 10px; font-weight: bold; font-size: 14px;
        border: 2px solid var(--primary); white-space: nowrap; box-shadow: 0 4px 0 #ccc;
    }

    /* Slots de destino */
    .word-slot {
        width: 85%; height: 70%; background: rgba(255,255,255,0.3);
        border: 3px dashed rgba(255,255,255,0.8); border-radius: 8px;
        display: flex; justify-content: center; align-items: center;
        color: white; font-weight: bold; font-size: 20px; transition: 0.3s;
    }
    .word-slot.filled { border-style: solid; background: white; color: var(--dark); box-shadow: inset 0 3px 5px rgba(0,0,0,0.2); }

    /* ==========================================
       BURBUJAS DE PALABRAS (DRAG)
    ========================================== */
    .word-pool { display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; margin-top: 20px; min-height: 80px; }
    .word-bubble {
        padding: 15px 30px; background: white; color: var(--primary);
        border: 3px solid var(--primary); border-radius: 20px;
        font-size: 24px; font-weight: bold; cursor: grab;
        box-shadow: 0 8px 0 var(--primary); transition: 0.1s, opacity 0.3s; user-select: none; z-index: 100;
    }
    .word-bubble:active { transform: translateY(6px); box-shadow: 0 2px 0 var(--primary); cursor: grabbing; }
    .word-bubble.used { opacity: 0; pointer-events: none; transform: scale(0); }

    /* MODAL TUTORIAL */
    .mission-modal {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s; padding: 20px; text-align: center;
    }
    .btn-action { background: var(--success); color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 6px 0 #27ae60; margin-top: 15px; }

    @keyframes trainBounce { 0% { transform: translateY(0); } 100% { transform: translateY(2px); } }
    @keyframes spin { 100% { transform: rotate(360deg); } }
    @keyframes driveAway { 0% { left: 50%; } 100% { left: 150%; } }
    @keyframes driveIn { 0% { left: -50%; } 100% { left: 50%; } }
    @keyframes errorShake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary);">🚂 Grammar Train</h3>
        <button onclick="giveHint()" style="background: #f1c40f; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #f39c12;" title="Pedir Pista">💡</button>
    </div>

    <div class="train-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Tren 1</div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">📜 Carga los Vagones</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 10px;" id="tut-context"></p>
            <div style="font-size: 28px; font-weight: bold; color: var(--dark); margin: 15px 0; background: var(--light); padding: 15px; border-radius: 15px;" id="tut-trans">
                Traducción
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡A Jugar!</button>
            </div>
        </div>

        <div class="train-tracks"></div>
        
        <div class="train-container" id="train">
            <div class="locomotive">🚂</div>
            <div id="wagons-container" style="display: flex; gap: 8px;"></div>
        </div>
    </div>

    <div class="word-pool" id="pool-container"></div>
</div>

<script>
    const roundsData = <?php echo json_encode($rounds); ?>;
    let currentRoundIndex = 0;
    let currentSentence = [];
    let currentPhoneticsMap = {}; 
    let loadedWagons = 0;
    let gameActive = false;
    
    const train = document.getElementById('train');
    
    loadRound(currentRoundIndex);

    function loadRound(index) {
        const round = roundsData[index];
        currentSentence = round.sentence;
        loadedWagons = 0;

        document.getElementById('round-indicator').innerText = `Tren ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        
        let transPhrase = round.translations ? round.translations.join(" ") : "Traducción";
        document.getElementById('tut-trans').innerText = `"${transPhrase}"`;

        // Construir Diccionario Fonético Inteligente
        currentPhoneticsMap = {};
        if (round.phonetics && round.phonetics.length === round.sentence.length) {
            round.sentence.forEach((w, i) => { currentPhoneticsMap[w.toUpperCase()] = round.phonetics[i]; });
        }
        if (round.distractors_phonetics && round.distractors) {
            round.distractors.forEach((w, i) => { currentPhoneticsMap[w.toUpperCase()] = round.distractors_phonetics[i]; });
        }

        // Generar Vagones
        let wagonsHTML = '';
        currentSentence.forEach((word, i) => {
            let translation = round.translations ? round.translations[i] : "...";
            wagonsHTML += `
                <div class="wagon-group">
                    <div class="wagon">
                        <div class="word-slot" id="slot-${i}" data-expected="${word}" data-index="${i}">?</div>
                        <div class="wagon-wheels"><div class="wheel"></div><div class="wheel"></div></div>
                        ${i < currentSentence.length - 1 ? '<div class="connector"></div>' : ''}
                    </div>
                    <div class="wagon-translation">${translation}</div>
                </div>
            `;
        });
        document.getElementById('wagons-container').innerHTML = wagonsHTML;

        // Generar Burbujas Desordenadas
        let allWords = [...currentSentence, ...(round.distractors || [])].sort(() => Math.random() - 0.5);
        let poolHTML = '';
        allWords.forEach((word, idx) => {
            poolHTML += `<div class="word-bubble" draggable="true" id="bubble-${idx}" data-word="${word}" onclick="handleWordClick(this)">${word}</div>`;
        });
        document.getElementById('pool-container').innerHTML = poolHTML;

        // Entrar a escena
        train.className = 'train-container drive-in';
        setTimeout(() => train.classList.remove('drive-in'), 1000);

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';

        attachDragEvents();
    }

    function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        const phoneticToRead = round.sentence_phonetic || round.sentence.join(' ');
        if(typeof playTTS !== 'undefined') playTTS(phoneticToRead);
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
                processWord(document.getElementById(bubbleId), slot);
            });
        });
    }

    function handleWordClick(bubbleEl) {
        if (!gameActive || bubbleEl.classList.contains('used')) return;
        readWord(bubbleEl.getAttribute('data-word'));
        // Si hace click, intenta ponerlo en el primer vagón vacío
        const nextEmptySlot = document.querySelector('.word-slot:not(.filled)');
        if(nextEmptySlot) processWord(bubbleEl, nextEmptySlot);
    }

    function readWord(word) {
        if(typeof playTTS !== 'undefined') {
            const wordUpper = word.toUpperCase();
            const phoneticToRead = currentPhoneticsMap[wordUpper] || wordUpper;
            playTTS(phoneticToRead);
        }
    }

    function processWord(bubbleEl, slotEl) {
        if (!bubbleEl || !slotEl || slotEl.classList.contains('filled')) return;

        const selectedWord = bubbleEl.getAttribute('data-word');
        const expectedWord = slotEl.getAttribute('data-expected');

        if (selectedWord === expectedWord) {
            slotEl.innerText = selectedWord;
            slotEl.classList.add('filled');
            bubbleEl.classList.add('used');
            loadedWagons++;
            
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }

            // Pequeño salto del vagón
            slotEl.parentElement.style.transform = "translateY(-10px)";
            setTimeout(() => slotEl.parentElement.style.transform = "translateY(0)", 200);

            if (loadedWagons === currentSentence.length) checkNextRound();
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            bubbleEl.style.animation = 'errorShake 0.3s';
            setTimeout(() => bubbleEl.style.animation = 'none', 300);
        }
    }

    function giveHint() {
        if(!gameActive) return;
        const nextEmptySlot = document.querySelector('.word-slot:not(.filled)');
        if(!nextEmptySlot) return;

        const expectedWord = nextEmptySlot.getAttribute('data-expected');
        readWord(expectedWord);
        
        document.querySelectorAll('.word-bubble').forEach(b => {
            if(!b.classList.contains('used') && b.getAttribute('data-word') === expectedWord) {
                b.style.transform = 'scale(1.2)';
                b.style.boxShadow = '0 0 15px #f1c40f';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.boxShadow = '0 8px 0 var(--primary)'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        gameActive = false;
        
        setTimeout(() => {
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            const round = roundsData[currentRoundIndex];
            if(typeof playTTS !== 'undefined') playTTS(round.sentence_phonetic || currentSentence.join(' '));

            // El tren se va de la estación
            train.className = 'train-container drive-away';

            currentRoundIndex++;
            
            if (currentRoundIndex < roundsData.length) {
                setTimeout(() => loadRound(currentRoundIndex), 2500);
            } else {
                setTimeout(() => executeWin(), 2000);
            }
        }, 800);
    }

    function executeWin() {
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }
</script>