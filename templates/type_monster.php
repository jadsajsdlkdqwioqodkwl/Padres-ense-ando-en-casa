<?php
// templates/type_monster.php
$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$time_limit = $lesson_data['time_limit'] ?? 20; 
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    /* Seguro de Pantalla Horizontal */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: #1E293B; z-index: 100000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center; box-sizing: border-box; padding: 20px;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    /* MODALES GLOBALES FUERA DEL JUEGO */
    .modal-overlay {
        position: fixed !important; top: 0 !important; left: 0 !important;
        width: 100vw !important; height: 100vh !important;
        box-sizing: border-box !important; padding: 20px !important;
        display: none; justify-content: center; align-items: center;
        background: rgba(15, 23, 42, 0.85) !important; z-index: 999999 !important;
    }
    .modal-overlay.active { display: flex !important; }
    
    .modal-overlay .modal-content {
        width: 100% !important; max-width: 480px !important;
        box-sizing: border-box !important; margin: 0 auto !important;
        background: white; padding: clamp(20px, 5vw, 40px); border-radius: 24px;
        text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        border: 4px solid var(--brand-blue, #1E3A8A);
        pointer-events: auto;
    }

    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
    
    /* Layout Maestro: Control estricto de los bordes del tablero de juego */
    .game-board { position: relative; width: 100%; max-width: 800px; min-height: 350px; background: #F8FAFC; border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin: 0 auto 25px auto; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.1); box-sizing: border-box; }
    
    /* MONSTRUO CSS */
    .css-monster { position: absolute; left: 2%; bottom: 20px; width: 80px; height: 80px; background: #EF4444; border: 3px solid var(--brand-blue); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: morph 2s linear infinite, wobble 0.5s alternate infinite; transition: left 0.2s linear, transform 0.3s; z-index: 3; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2); pointer-events: none; }
    .css-monster::before, .css-monster::after { content: ''; position: absolute; top: 18px; width: 16px; height: 16px; background: white; border-radius: 50%; border: 3px solid var(--brand-blue); }
    .css-monster::before { left: 16px; } .css-monster::after { right: 16px; }
    .css-monster-mouth { position: absolute; bottom: 12px; left: 22px; width: 35px; height: 18px; background: var(--brand-blue); border-radius: 0 0 18px 18px; transition: height 0.3s, border-radius 0.3s; }
    
    /* OBJETIVO DINÁMICO */
    .twemoji-target { position: absolute; right: 5%; bottom: 10px; font-size: 4.5rem; z-index: 2; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.2)); transition: transform 0.3s; pointer-events: none; }
    
    .slot-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; padding: 0 10px; z-index: 5; position: relative; width: 100%; box-sizing: border-box; }
    .letter-slot { width: clamp(45px, 12vw, 60px); height: clamp(55px, 15vw, 70px); border: 3px dashed #CBD5E1; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 34px); font-weight: 800; background: #ffffff; box-shadow: inset 0 3px 6px rgba(0,0,0,0.05); transition: 0.3s; color: #94A3B8; }
    .letter-slot.filled { border-style: solid; border-color: #4CAF50; background: #F0FDF4; color: #4CAF50; transform: scale(1.05); box-shadow: 0 4px 10px rgba(76, 175, 80, 0.2); }
    
    .bubbles-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; min-height: 80px; padding: 0 10px; position: relative; width: 100%; box-sizing: border-box; }
    .drag-bubble { width: clamp(50px, 14vw, 60px); height: clamp(50px, 14vw, 60px); background: #F59E0B; color: white; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 30px); font-weight: 800; cursor: pointer; box-shadow: 0 6px 0 #D97706, 0 10px 15px rgba(245, 158, 11, 0.3); transition: transform 0.1s, opacity 0.3s; user-select: none; touch-action: manipulation; z-index: 10; }
    .drag-bubble:active { transform: translateY(6px); box-shadow: 0 0px 0 #D97706, 0 2px 5px rgba(245, 158, 11, 0.3); }
    .drag-bubble.hidden { opacity: 0; pointer-events: none; transform: scale(0); }
    
    .round-indicator { position: absolute; top: 15px; left: 15px; background: var(--brand-blue, #1E3A8A); color: white; font-weight: 700; padding: 6px 18px; border-radius: 50px; font-size: 14px; z-index: 50; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .danger-zone { animation: flashRed 1s infinite; }
    
    .game-header-bar { display: flex; justify-content: space-between; align-items: center; margin: 0 auto 20px auto; width: 100%; max-width: 800px; background: #ffffff; border: 2px solid #E2E8F0; border-radius: 16px; padding: 15px 25px; box-sizing: border-box; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05); }

    @keyframes morph { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 60% 50% 40% 50%; } }
    @keyframes wobble { from { transform: translateY(0) rotate(-5deg); } to { transform: translateY(-10px) rotate(5deg); } }
    @keyframes flashRed { 0%, 100% { background: #F8FAFC; } 50% { background: #FEE2E2; } }
    @keyframes zap { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.2) rotate(180deg); filter: brightness(5); } 100% { transform: scale(0); opacity: 0; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<div id="tutorial-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px;">📜 Misión</h2>
        <p class="modal-text" id="tut-context" style="margin-bottom: 10px;"></p>
        <div style="font-size: 4rem; margin: 10px 0;" id="tut-emoji-display">🎯</div>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin: 10px 0; flex-wrap: wrap;">
            <div style="font-size: 2.5rem; font-weight: 900; color: var(--brand-orange); letter-spacing: 5px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);" id="tut-word">WORD</div>
            <button class="btn-audio-huge" id="btn-tut-audio" title="Escuchar pronunciación" style="width: 50px; height: 50px; font-size: 20px; margin: 0;">🔊</button>
        </div>

        <p style="color: #94A3B8; font-size: 1.2rem; margin-bottom: 15px; font-weight: 600;" id="tut-trans">(Traducción)</p>
        <p style="font-size: 14px; color: #475569; background: #F8FAFC; padding: 15px; border-radius: 12px; font-style: italic; margin-bottom: 25px; border: 1px solid #E2E8F0; width: 100%; box-sizing: border-box;" id="tut-mnemonic">💡 Cargando consejo...</p>

        <button class="btn-play bg-orange-500" id="btn-start" onclick="startGame()" style="margin-top: 0;">▶️ ¡Proteger!</button>
    </div>
</div>

<div id="success-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px; color: #10B981;">¡A salvo! 🛡️</h2>
        <div style="font-size: 4rem; margin: 10px 0;" id="succ-emoji">🎯</div>
        <p style="font-size: 1.5rem; font-weight: 800; color: #1E3A8A; margin-bottom: 5px;" id="succ-word">WORD</p>
        <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="succ-trans">(Traducción)</p>
        
        <p style="font-size: 15px; color: #065F46; background: #D1FAE5; padding: 15px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; border: 1px solid #34D399; width: 100%; box-sizing: border-box;" id="succ-mnemonic">💡 Cargando recordatorio...</p>
        
        <button id="btn-next-round" onclick="goToNextRound()" class="btn-play bg-blue-500" style="margin-top: 0;">Siguiente ➡️</button>
    </div>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh; padding: 10px; box-sizing: border-box; width: 100%; max-width: 1000px; margin: 0 auto;">
    <div class="game-area text-center mx-auto" style="width: 100%; box-sizing: border-box; display: flex; flex-direction: column; align-items: center;">
        
        <div class="game-header-bar">
            <h3 style="margin: 0; color: var(--brand-blue); font-size: clamp(20px, 5vw, 26px); font-weight: 900;">🛡️ Word Defender</h3>
            <button onclick="giveHint()" style="background: #FBBF24; border: none; border-radius: 50%; width: 50px; height: 50px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #D97706, 0 4px 10px rgba(245, 158, 11, 0.3); transition: 0.2s;" title="Pedir Pista">💡</button>
        </div>

        <div class="game-board" id="game-board">
            <div class="round-indicator" id="round-indicator">Ronda 1</div>
            <div class="css-monster" id="monster"><div class="css-monster-mouth" id="monster-mouth"></div></div>
            <div class="twemoji-target" id="dynamic-target">🎯</div>
        </div>

        <div class="slot-container" id="slots-container" style="max-width: 800px; margin: 0 auto 25px auto;"></div>
        <div class="bubbles-container" id="bubbles-container" style="max-width: 800px; margin: 0 auto;"></div>
    </div>
</main>

<script>
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

    let roundsData = window.dynamicRoundsData || [];
    
    roundsData = roundsData.map(r => ({
        word: r.target_word || r.word || 'APPLE',
        phonetic: r.phonetic || '',
        translation: r.translation || 'Manzana',
        emoji: r.emoji || r.content || '🍎',
        distractors: r.distractors || ['X', 'Z', 'M', 'Q'],
        context_es: r.context_es || "¡Defiende el objeto escribiendo la palabra!",
        mnemonic: r.mnemonic || ''
    }));

    const timeLimit = <?php echo $time_limit; ?>; 
    let currentRoundIndex = 0;
    let currentCorrect = 0;
    let wordLength = 0;
    let currentTargetWord = '';
    
    let gameActive = false;
    let monsterPos = 2; 
    const targetPos = 75; 
    const stepAmount = (targetPos - 2) / (timeLimit * 10); 
    let monsterInterval;
    
    const monster = document.getElementById('monster');
    const monsterMouth = document.getElementById('monster-mouth');
    const dynamicTarget = document.getElementById('dynamic-target');
    const gameBoard = document.getElementById('game-board');

    if(roundsData.length > 0) loadRound(currentRoundIndex);

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        wordLength = round.word.length;
        currentCorrect = 0;
        currentTargetWord = round.word; 
        
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-word').innerText = round.word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        
        if(round.mnemonic) {
            document.getElementById('tut-mnemonic').innerText = "💡 " + round.mnemonic;
            document.getElementById('tut-mnemonic').style.display = 'block';
        } else {
            document.getElementById('tut-mnemonic').style.display = 'none';
        }

        document.getElementById('btn-tut-audio').onclick = function() {
            if(typeof playPronunciation === 'function') playPronunciation(currentTargetWord);
        };
        
        document.getElementById('tut-emoji-display').innerText = round.emoji;
        dynamicTarget.innerText = round.emoji;
        dynamicTarget.style.display = 'block';
        dynamicTarget.style.transform = 'scale(1)';

        monsterPos = 2;
        monster.style.left = monsterPos + '%';
        monster.style.animation = 'morph 2s linear infinite, wobble 0.5s alternate infinite';
        monster.style.transform = 'scale(1)';
        monsterMouth.style.height = '18px'; 

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

        document.getElementById('tutorial-modal').classList.add('active');
        applyTwemoji(document.body);
    }

    function startGame() {
        document.getElementById('tutorial-modal').classList.remove('active');
        if(typeof attemptAutoplay === 'function') attemptAutoplay();
        gameActive = true;
        startMonster();
    }

    function startMonster() {
        clearInterval(monsterInterval);
        monsterInterval = setInterval(() => {
            if (!gameActive) return;
            monsterPos += stepAmount;
            monster.style.left = monsterPos + '%';
            
            if (monsterPos > targetPos * 0.7) {
                gameBoard.classList.add('danger-zone');
                monsterMouth.style.height = '30px'; 
                monsterMouth.style.borderRadius = '50%';
            } else {
                gameBoard.classList.remove('danger-zone');
                monsterMouth.style.height = '18px';
                monsterMouth.style.borderRadius = '0 0 18px 18px';
            }

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
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop');
            currentSlot.innerText = draggedChar;
            currentSlot.classList.add('filled');
            bubbleEl.classList.add('hidden');
            currentCorrect++;
            
            monsterPos = Math.max(2, monsterPos - 12); 
            monster.style.left = monsterPos + '%';

            if (currentCorrect === wordLength) checkNextRound();
        } else {
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('wrong');
            gameBoard.classList.add('danger-zone');
            bubbleEl.style.animation = 'shake 0.3s';
            setTimeout(() => { gameBoard.classList.remove('danger-zone'); bubbleEl.style.animation = 'none'; }, 300);
            monsterPos += 2; 
        }
    }

    function giveHint() {
        if(!gameActive || currentCorrect >= wordLength) return;
        const expectedChar = document.getElementById('slot-' + currentCorrect).getAttribute('data-expected');
        
        monsterPos += 4; 
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
        
        setTimeout(() => { showSuccessModal(); }, 1000);
    }

    function showSuccessModal() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
        
        const round = roundsData[currentRoundIndex];
        document.getElementById('succ-emoji').innerText = round.emoji || '🎯';
        document.getElementById('succ-word').innerText = round.word;
        document.getElementById('succ-trans').innerText = `(${round.translation})`;
        
        if(round.mnemonic) {
            document.getElementById('succ-mnemonic').innerText = "💡 Recuerda: " + round.mnemonic;
            document.getElementById('succ-mnemonic').style.display = 'block';
        } else {
            document.getElementById('succ-mnemonic').style.display = 'none';
        }

        const modal = document.getElementById('success-modal');
        modal.classList.add('active');
        if (typeof twemoji !== 'undefined') twemoji.parse(modal, { folder: 'svg', ext: '.svg' });
    }

    function goToNextRound() {
        document.getElementById('success-modal').classList.remove('active');
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            loadRound(currentRoundIndex);
        } else {
            gameOver(true);
        }
    }

    function gameOver(isWin) {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');

        if (isWin) {
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            if(typeof unlockNextButton !== 'undefined') {
                unlockNextButton(<?php echo $lesson_id ?? 0; ?>, <?php echo $reward_stars ?? 5; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
            }
        } else {
            dynamicTarget.style.display = 'none';
            monster.style.transform = 'scale(1.5)';
            monsterMouth.style.height = '5px'; 
            
            setTimeout(() => {
                alert("¡Oh no! El monstruo alcanzó el objeto. ¡Inténtalo de nuevo!");
                location.reload(); 
            }, 1200);
        }
    }
</script>