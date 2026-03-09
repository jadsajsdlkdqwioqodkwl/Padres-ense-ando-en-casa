<?php
$time_limit = $lesson_data['time_limit'] ?? 20; 
$reward_stars = $lesson['reward_stars'] ?? 5;

$rounds = $lesson_data['rounds'] ?? [
    [
        'word' => strtoupper($lesson_data['word'] ?? 'APPLE'),
        'phonetic' => $lesson_data['phonetic'] ?? 'ápol',
        'translation' => $lesson_data['translation'] ?? 'Manzana',
        'distractors' => $lesson_data['distractors'] ?? ['X', 'Z', 'M'],
        'context_es' => $lesson_data['context_es'] ?? "El monstruo quiere nuestro pastel. ¡Escribe la palabra mágica!"
    ]
];
?>

<style>
    /* Estilos globales para Twemoji */
    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
    
    .game-board { position: relative; width: 100%; min-height: 350px; background: var(--bg-light); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin-bottom: 25px; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.1); }
    
    /* Posicionamiento fluido con porcentajes */
    .css-cake { position: absolute; right: 5%; bottom: 20px; width: 65px; height: 55px; background: #F472B6; border-radius: 12px 12px 0 0; border: 3px solid var(--brand-blue); z-index: 2; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.1); }
    .css-cake::before { content: ''; position: absolute; top: -15px; left: 25px; width: 6px; height: 16px; background: #EF4444; border-radius: 5px; box-shadow: 0 0 5px rgba(239, 68, 68, 0.5); } 
    
    .css-monster { position: absolute; left: 2%; bottom: 20px; width: 80px; height: 80px; background: #EF4444; border: 3px solid var(--brand-blue); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: morph 2s linear infinite, wobble 0.5s alternate infinite; transition: left 0.2s linear; z-index: 3; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2); }
    .css-monster::before, .css-monster::after { content: ''; position: absolute; top: 18px; width: 16px; height: 16px; background: white; border-radius: 50%; border: 3px solid var(--brand-blue); }
    .css-monster::before { left: 16px; } .css-monster::after { right: 16px; }
    .css-monster-mouth { position: absolute; bottom: 12px; left: 22px; width: 35px; height: 18px; background: var(--brand-blue); border-radius: 0 0 18px 18px; }
    
    .slot-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; padding: 0 10px; }
    .letter-slot { width: clamp(45px, 12vw, 60px); height: clamp(55px, 15vw, 70px); border: 3px dashed #CBD5E1; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 34px); font-weight: 800; background: var(--white); box-shadow: inset 0 3px 6px rgba(0,0,0,0.05); transition: 0.3s; color: var(--text-muted); }
    .letter-slot.filled { border-style: solid; border-color: var(--brand-green); background: #F0FDF4; color: var(--brand-green); transform: scale(1.05); box-shadow: 0 4px 10px rgba(104, 169, 62, 0.2); }
    
    .bubbles-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; min-height: 80px; padding: 0 10px; }
    .drag-bubble { width: clamp(50px, 14vw, 60px); height: clamp(50px, 14vw, 60px); background: var(--brand-orange); color: white; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 30px); font-weight: 800; cursor: pointer; box-shadow: 0 6px 0 #D97706, 0 10px 15px rgba(242, 156, 56, 0.3); transition: transform 0.1s, opacity 0.3s; user-select: none; touch-action: manipulation; z-index: 10; }
    .drag-bubble:active { transform: translateY(6px); box-shadow: 0 0px 0 #D97706, 0 2px 5px rgba(242, 156, 56, 0.3); }
    .drag-bubble.hidden { opacity: 0; pointer-events: none; transform: scale(0); }
    
    .mission-modal { overflow-y: auto; }
    
    .round-indicator { position: absolute; top: 15px; left: 15px; background: var(--brand-blue); color: white; font-weight: 700; padding: 6px 18px; border-radius: 50px; font-size: 14px; z-index: 50; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .danger-zone { animation: flashRed 1s infinite; }
    
    @keyframes morph { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 60% 50% 40% 50%; } }
    @keyframes wobble { from { transform: translateY(0) rotate(-5deg); } to { transform: translateY(-10px) rotate(5deg); } }
    @keyframes flashRed { 0%, 100% { background: var(--bg-light); } 50% { background: #FEE2E2; } }
    @keyframes zap { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.2) rotate(180deg); filter: brightness(5); } 100% { transform: scale(0); opacity: 0; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--brand-blue); font-size: 1.5rem;">🛡️ Word Defender</h3>
        <button onclick="giveHint()" style="background: #FBBF24; border: none; border-radius: 50%; width: 50px; height: 50px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #D97706, 0 4px 10px rgba(245, 158, 11, 0.3); transition: 0.2s;" title="Pedir Pista">💡</button>
    </div>

    <div class="game-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Ronda 1</div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--brand-blue); margin-top: 0; margin-bottom: 5px; font-size: 2rem;">📜 Misión</h2>
            <p style="color: #64748B; font-size: 16px; margin-bottom: 5px;" id="tut-context"></p>
            <div style="font-size: 35px; font-weight: 800; color: var(--brand-orange); margin: 10px 0; letter-spacing: 5px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);" id="tut-word">WORD</div>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px; font-weight: 600;" id="tut-trans">(Traducción)</p>
            
            <div class="modal-actions">
                <button class="btn btn-action" id="btn-start" onclick="startGame()" style="display: block;">▶️ ¡Jugar Ahora!</button>
            </div>
        </div>

        <div class="css-monster" id="monster"><div class="css-monster-mouth"></div></div>
        <div class="css-cake" id="cake"></div>
    </div>

    <div class="slot-container" id="slots-container"></div>
    <div class="bubbles-container" id="bubbles-container"></div>
</div>

<script>
    // Inicializador Twemoji Dinámico
    function applyTwemoji(node) {
        if (typeof twemoji !== 'undefined') {
            twemoji.parse(node, { folder: 'svg', ext: '.svg' });
        } else {
            const script = document.createElement('script');
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/twemoji.min.js";
            script.onload = () => twemoji.parse(node, { folder: 'svg', ext: '.svg' });
            document.head.appendChild(script);
        }
    }

    let roundsData = window.dynamicRoundsData || <?php echo json_encode($rounds); ?>;
    if (window.dynamicRoundsData) {
        roundsData = roundsData.map(r => ({
            word: r.target_word || r.word,
            phonetic: r.target_word || r.word,
            translation: r.translation,
            distractors: r.distractors || ['X', 'Z', 'M', 'Q'],
            context_es: r.context_es || "¡Defiende el pastel escribiendo la palabra!"
        }));
    }

    const timeLimit = <?php echo $time_limit; ?>; 
    let currentRoundIndex = 0;
    let currentCorrect = 0;
    let wordLength = 0;
    
    let gameActive = false;
    let monsterPos = 2; // Ahora en porcentaje (%)
    const targetPos = 75; // 75% es cerca del pastel
    const stepAmount = (targetPos - 2) / (timeLimit * 10); 
    let monsterInterval;
    
    const monster = document.getElementById('monster');
    const gameBoard = document.getElementById('game-board');

    loadRound(currentRoundIndex);

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        wordLength = round.word.length;
        currentCorrect = 0;
        
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-word').innerText = round.word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;

        monsterPos = 2;
        monster.style.left = monsterPos + '%';
        monster.style.animation = 'morph 2s linear infinite, wobble 0.5s alternate infinite';
        monster.style.transform = 'scale(1)';

        let slotsHTML = '';
        for(let i=0; i<wordLength; i++) {
            slotsHTML += `<div class="letter-slot" data-expected="${round.word[i]}" data-index="${i}" id="slot-${i}"></div>`;
        }
        document.getElementById('slots-container').innerHTML = slotsHTML;

        let letters = round.word.split('');
        let allChars = letters.concat(round.distractors || []).sort(() => Math.random() - 0.5);
        
        let bubblesHTML = '';
        allChars.forEach((char, idx) => {
            bubblesHTML += `<div class="drag-bubble" id="bubble-${idx}" data-char="${char}" onclick="handleBubbleClick(this)">${char}</div>`;
        });
        document.getElementById('bubbles-container').innerHTML = bubblesHTML;

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        
        applyTwemoji(document.body); // Renderizar Emojis UI
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 500);
        gameActive = true;
        startMonster();
    }

    function startMonster() {
        clearInterval(monsterInterval);
        monsterInterval = setInterval(() => {
            if (!gameActive) return;
            monsterPos += stepAmount;
            monster.style.left = monsterPos + '%';
            if (monsterPos > targetPos * 0.7) gameBoard.classList.add('danger-zone');
            if (monsterPos >= targetPos) gameOver(false);
        }, 100);
    }

    function handleBubbleClick(bubbleEl) {
        if (!gameActive || bubbleEl.classList.contains('hidden')) return;
        processMove(bubbleEl);
    }

    function processMove(bubbleEl) {
        if (!bubbleEl || currentCorrect >= wordLength) return;

        const draggedChar = bubbleEl.getAttribute('data-char');
        const currentSlot = document.getElementById('slot-' + currentCorrect);
        const expectedChar = currentSlot.getAttribute('data-expected');

        if (draggedChar === expectedChar) {
            currentSlot.innerText = draggedChar;
            currentSlot.classList.add('filled');
            bubbleEl.classList.add('hidden');
            currentCorrect++;
            
            monsterPos = Math.max(2, monsterPos - 10); // Retrocede 10%
            monster.style.left = monsterPos + '%';

            if (currentCorrect === wordLength) checkNextRound();
        } else {
            gameBoard.classList.add('danger-zone');
            bubbleEl.style.animation = 'shake 0.3s';
            setTimeout(() => { gameBoard.classList.remove('danger-zone'); bubbleEl.style.animation = 'none'; }, 300);
            monsterPos += 2; // Penalización avanza 2%
        }
    }

    function giveHint() {
        if(!gameActive || currentCorrect >= wordLength) return;
        const expectedChar = document.getElementById('slot-' + currentCorrect).getAttribute('data-expected');
        
        monsterPos += 4; // Cuesta 4% pedir pista
        monster.style.left = monsterPos + '%';
        if (monsterPos >= targetPos) gameOver(false);
        
        document.querySelectorAll('.drag-bubble').forEach(b => {
            if(!b.classList.contains('hidden') && b.getAttribute('data-char') === expectedChar) {
                b.style.transform = 'scale(1.2)';
                b.style.boxShadow = '0 0 15px #FBBF24';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.boxShadow = '0 6px 0 #D97706'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');
        monster.style.animation = 'zap 0.8s forwards'; 
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            setTimeout(() => { gameOver(true); }, 1000);
        }
    }

    function gameOver(isWin) {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');

        if (isWin) {
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id'] ?? 0; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
        } else {
            document.getElementById('cake').style.display = 'none';
            monster.style.transform = 'scale(1.5)';
            
            setTimeout(() => {
                alert("¡Oh no! El monstruo llegó al pastel. ¡Inténtalo de nuevo!");
                location.reload(); 
            }, 1000);
        }
    }
</script>