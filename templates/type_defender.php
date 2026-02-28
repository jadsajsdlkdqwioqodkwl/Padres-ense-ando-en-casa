<?php
$time_limit = $lesson_data['time_limit'] ?? 20; 
$reward_stars = $lesson['reward_stars'] ?? 5;

$rounds = $lesson_data['rounds'] ?? [
    [
        'word' => strtoupper($lesson_data['word'] ?? 'APPLE'),
        'translation' => $lesson_data['translation'] ?? 'Manzana',
        'distractors' => $lesson_data['distractors'] ?? ['X', 'Z', 'M'],
        'context_es' => $lesson_data['context_es'] ?? "El monstruo quiere nuestro pastel. ¡Escribe la palabra mágica!"
    ]
];
?>

<style>
    .game-board { 
        position: relative; width: 100%; height: 320px; /* Un poco más alto para que el modal respire */
        background: var(--bg); border-radius: 20px; overflow: hidden; 
        border: 4px solid var(--primary); margin-bottom: 20px; 
    }
    
    .css-cake { position: absolute; right: 20px; bottom: 20px; width: 60px; height: 50px; background: #ff9ff3; border-radius: 10px 10px 0 0; border: 3px solid var(--dark); z-index: 2; }
    .css-cake::before { content: ''; position: absolute; top: -15px; left: 25px; width: 5px; height: 15px; background: #ff4757; border-radius: 5px; } 
    
    .css-monster { position: absolute; left: 10px; bottom: 20px; width: 70px; height: 70px; background: #ff6b6b; border: 3px solid var(--dark); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: morph 2s linear infinite, wobble 0.5s alternate infinite; transition: left 0.5s linear; z-index: 3; }
    .css-monster::before, .css-monster::after { content: ''; position: absolute; top: 15px; width: 15px; height: 15px; background: white; border-radius: 50%; border: 2px solid var(--dark); }
    .css-monster::before { left: 15px; } .css-monster::after { right: 15px; }
    .css-monster-mouth { position: absolute; bottom: 10px; left: 20px; width: 30px; height: 15px; background: var(--dark); border-radius: 0 0 15px 15px; }

    .slot-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; }
    .letter-slot { width: 55px; height: 65px; border: 3px dashed #999; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 32px; font-weight: bold; background: white; box-shadow: inset 0 3px 6px rgba(0,0,0,0.1); transition: 0.3s; }
    .letter-slot.filled { border-style: solid; border-color: var(--success); background: #f0fdf4; color: var(--success); transform: scale(1.05); }
    
    .bubbles-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; min-height: 80px;}
    .drag-bubble { width: 55px; height: 55px; background: var(--accent); color: white; border-radius: 15px; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold; cursor: pointer; box-shadow: 0 6px 0 #d35400; transition: transform 0.1s, opacity 0.3s; user-select: none; z-index: 10; }
    .drag-bubble:active { transform: translateY(4px); box-shadow: 0 2px 0 #d35400; }
    .drag-bubble.hidden { opacity: 0; pointer-events: none; transform: scale(0); }

    /* Modal pequeño y centrado dentro del tablero */
    .mission-modal {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.95); z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; transition: opacity 0.5s; padding: 15px; text-align: center;
    }
    .btn-action { 
        background: var(--success); color: white; border: none; padding: 12px 25px; 
        font-size: 18px; font-weight: bold; border-radius: 30px; cursor: pointer; 
        box-shadow: 0 6px 0 #27ae60; margin-top: 10px;
    }

    .round-indicator { position: absolute; top: 10px; left: 10px; background: var(--primary); color: white; font-weight: bold; padding: 5px 15px; border-radius: 20px; font-size: 14px; z-index: 50; }

    .danger-zone { animation: flashRed 1s infinite; }
    @keyframes morph { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 60% 50% 40% 50%; } }
    @keyframes wobble { from { transform: translateY(0) rotate(-5deg); } to { transform: translateY(-10px) rotate(5deg); } }
    @keyframes flashRed { 0%, 100% { background: var(--bg); } 50% { background: #ffcccc; } }
    @keyframes zap { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.2) rotate(180deg); filter: brightness(5); } 100% { transform: scale(0); opacity: 0; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary);">🛡️ Word Defender</h3>
        <button onclick="giveHint()" style="background: #f1c40f; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #f39c12;" title="Pedir Pista">💡</button>
    </div>

    <div class="game-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Ronda 1</div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0; margin-bottom: 5px;">📜 Misión</h2>
            <p style="color: var(--text-muted); font-size: 16px; margin-bottom: 5px;" id="tut-context"></p>
            <div style="font-size: 30px; font-weight: bold; color: var(--accent); margin: 5px 0; letter-spacing: 5px;" id="tut-word">WORD</div>
            <p style="color: #666; font-size: 18px; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; margin-top: 5px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡A Jugar!</button>
            </div>
        </div>

        <div class="css-monster" id="monster"><div class="css-monster-mouth"></div></div>
        <div class="css-cake" id="cake"></div>
    </div>

    <div class="slot-container" id="slots-container"></div>
    <div class="bubbles-container" id="bubbles-container"></div>
</div>

<script>
    const roundsData = <?php echo json_encode($rounds); ?>;
    const timeLimit = <?php echo $time_limit; ?>; 
    let currentRoundIndex = 0;
    let currentCorrect = 0;
    let wordLength = 0;
    
    let gameActive = false;
    let monsterPos = 10; 
    let monsterInterval;
    
    const monster = document.getElementById('monster');
    const gameBoard = document.getElementById('game-board');
    const boardWidth = gameBoard.offsetWidth;
    const targetPos = boardWidth - 80; 
    const stepAmount = (targetPos - 10) / (timeLimit * 10); 

    loadRound(currentRoundIndex);

    function loadRound(index) {
        const round = roundsData[index];
        wordLength = round.word.length;
        currentCorrect = 0;
        
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-word').innerText = round.word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;

        monsterPos = 10;
        monster.style.left = monsterPos + 'px';
        monster.style.animation = 'morph 2s linear infinite, wobble 0.5s alternate infinite';
        monster.style.transform = 'scale(1)';

        let slotsHTML = '';
        for(let i=0; i<wordLength; i++) {
            slotsHTML += `<div class="letter-slot" data-expected="${round.word[i]}" data-index="${i}" id="slot-${i}"></div>`;
        }
        document.getElementById('slots-container').innerHTML = slotsHTML;

        let letters = round.word.split('');
        let allChars = letters.concat(round.distractors).sort(() => Math.random() - 0.5);
        
        let bubblesHTML = '';
        allChars.forEach((char, idx) => {
            bubblesHTML += `<div class="drag-bubble" draggable="true" id="bubble-${idx}" data-char="${char}" onclick="handleBubbleClick(this)">${char}</div>`;
        });
        document.getElementById('bubbles-container').innerHTML = bubblesHTML;

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';

        attachDragEvents();
    }

   function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        playSpanglish('', round.word, '');
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
            monster.style.left = monsterPos + 'px';
            if (monsterPos > targetPos * 0.7) gameBoard.classList.add('danger-zone');
            if (monsterPos >= targetPos) gameOver(false);
        }, 100);
    }

    function attachDragEvents() {
        const bubbles = document.querySelectorAll('.drag-bubble');
        const slots = document.querySelectorAll('.letter-slot');

        bubbles.forEach(bubble => {
            bubble.addEventListener('dragstart', (e) => {
                if(!gameActive) { e.preventDefault(); return; }
                e.dataTransfer.setData('text/plain', bubble.id);
                bubble.style.opacity = '0.5';
                readLetter(bubble.getAttribute('data-char'));
            });
            bubble.addEventListener('dragend', () => { bubble.style.opacity = '1'; });
        });

        slots.forEach(slot => {
            slot.addEventListener('dragover', (e) => { e.preventDefault(); });
            slot.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!gameActive) return;
                const bubbleId = e.dataTransfer.getData('text/plain');
                processMove(document.getElementById(bubbleId));
            });
        });
    }

    function handleBubbleClick(bubbleEl) {
        if (!gameActive || bubbleEl.classList.contains('hidden')) return;
        readLetter(bubbleEl.getAttribute('data-char'));
        processMove(bubbleEl);
    }

    function readLetter(char) {
        if(typeof playTTS !== 'undefined') playTTS(char);
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
            
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }

            monsterPos = Math.max(10, monsterPos - 35); 
            monster.style.left = monsterPos + 'px';

            if (currentCorrect === wordLength) checkNextRound();
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            gameBoard.classList.add('danger-zone');
            bubbleEl.style.animation = 'shake 0.3s';
            setTimeout(() => { gameBoard.classList.remove('danger-zone'); bubbleEl.style.animation = 'none'; }, 300);
            monsterPos += 5;
        }
    }

    function giveHint() {
        if(!gameActive || currentCorrect >= wordLength) return;
        
        const expectedChar = document.getElementById('slot-' + currentCorrect).getAttribute('data-expected');
        readLetter(expectedChar);

        monsterPos += 10;
        monster.style.left = monsterPos + 'px';
        if (monsterPos >= targetPos) gameOver(false);
        
        document.querySelectorAll('.drag-bubble').forEach(b => {
            if(!b.classList.contains('hidden') && b.getAttribute('data-char') === expectedChar) {
                b.style.transform = 'scale(1.2)';
                b.style.boxShadow = '0 0 15px #f1c40f';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.boxShadow = '0 6px 0 #d35400'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');
        monster.style.animation = 'zap 0.8s forwards'; 
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();

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
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
        } else {
            document.getElementById('cake').style.display = 'none';
            monster.style.transform = 'scale(1.5)';
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            
            setTimeout(() => {
                alert("¡Oh no! El monstruo se comió el pastel. ¡Inténtalo de nuevo!");
                location.reload(); 
            }, 1000);
        }
    }
</script>