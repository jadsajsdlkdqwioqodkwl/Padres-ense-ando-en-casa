<?php
// templates/type_rocket.php
$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
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

    /* touch-action: none; permite deslizar el dedo en móviles sin scrollear la web */
    .space-board { position: relative; width: 100%; max-width: 100%; height: 60vh; min-height: 480px; max-height: 750px; background: linear-gradient(180deg, #0F172A 0%, #1E293B 60%, var(--brand-blue) 100%); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-lblue); margin: 0 auto 20px auto; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); touch-action: none; display: flex; box-sizing: border-box; cursor: pointer; }
    
    .starfield { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(2px 2px at 20px 30px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 40px 70px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 50px 160px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 90px 40px, #ffffff, rgba(0,0,0,0)); background-repeat: repeat; animation: spaceScroll 20s linear infinite; opacity: 0.5; pointer-events: none; }
    
    .lane { flex: 1; height: 100%; border-right: 1px dashed rgba(255,255,255,0.1); position: relative; pointer-events: none; }
    .lane:last-child { border-right: none; }

    .rocket-player { position: absolute; bottom: 20px; width: 80px; height: 100px; left: calc(50% - 40px); transition: left 0.15s ease-out, bottom 1s ease-in; z-index: 20; display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .rocket-body { font-size: clamp(50px, 12vw, 65px); line-height: 1; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); animation: floatRocket 1s infinite alternate; }
    .rocket-flame { width: 20px; height: 40px; background: #F29C38; border-radius: 50% 50% 20% 20%; margin-top: -10px; animation: flameFlicker 0.1s infinite alternate; box-shadow: 0 0 20px #F29C38, 0 0 40px #EF4444; }
    
    .falling-item { position: absolute; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; pointer-events: none; }
    .item-bubble { background: rgba(255,255,255,0.9); border: 3px solid var(--brand-lblue); border-radius: 20px; padding: 10px 15px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    .item-emoji { font-size: clamp(30px, 8vw, 40px); margin-bottom: 5px; }
    .item-word { font-weight: 800; color: var(--brand-blue); font-size: clamp(12px, 3vw, 16px); text-transform: uppercase; }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: space-between; padding: 0 20px; z-index: 30; pointer-events: none; box-sizing: border-box; }
    .target-display { background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border: 2px solid rgba(255,255,255,0.2); color: white; padding: 8px 25px; border-radius: 50px; font-weight: 800; font-size: clamp(16px, 4vw, 20px); letter-spacing: 2px; }
    .fuel-bar-container { width: clamp(80px, 25vw, 120px); height: 25px; background: rgba(0,0,0,0.5); border-radius: 50px; border: 2px solid rgba(255,255,255,0.2); overflow: hidden; position: relative; }
    .fuel-bar-fill { height: 100%; width: 0%; background: linear-gradient(90deg, #F29C38, #68A93E); transition: width 0.3s ease-out; }
    
    .hit-flash { animation: screenFlash 0.3s; }
    .blast-off { bottom: 120% !important; animation: none !important; }

    @keyframes spaceScroll { from { background-position: 0 0; } to { background-position: 0 480px; } }
    @keyframes floatRocket { from { transform: translateY(0); } to { transform: translateY(-5px); } }
    @keyframes flameFlicker { from { transform: scaleY(1); opacity: 0.8; } to { transform: scaleY(1.3); opacity: 1; } }
    @keyframes screenFlash { 0% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } 50% { box-shadow: inset 0 0 50px 20px rgba(239, 68, 68, 0.8); } 100% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } }
    @keyframes fuelFlash { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.5) drop-shadow(0 0 10px #68A93E); } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego espacial necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<div id="tutorial-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px;">¡Llena el Tanque! 🚀</h2>
        <p class="modal-text" id="tut-context" style="margin-bottom: 10px;">Mueve el cohete para atrapar solo:</p>
        
        <div style="font-size: 3rem; margin: 10px 0;" id="tut-emoji-display"></div>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin: 15px 0; flex-wrap: wrap;">
            <div style="font-size: clamp(2.5rem, 8vw, 3.5rem); font-weight: 900; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3);" id="tut-word">WORD</div>
            <button class="btn-audio-huge" id="btn-tut-audio" title="Escuchar pronunciación" style="width: clamp(50px, 10vw, 65px); height: clamp(50px, 10vw, 65px); font-size: clamp(20px, 5vw, 26px); margin: 0;">🔊</button>
        </div>

        <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="tut-trans">(Traducción)</p>
        <p style="font-size: 14px; color: #475569; background: #F8FAFC; padding: 15px; border-radius: 12px; font-style: italic; margin-bottom: 25px; border: 1px solid #E2E8F0; width: 100%; box-sizing: border-box;" id="tut-mnemonic">💡 Cargando consejo...</p>

        <button id="btn-start" onclick="startGame()" class="btn-play bg-orange-500" style="margin-top: 0;">▶️ ¡Despegar!</button>
    </div>
</div>

<div id="success-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px; color: #10B981;">¡Misión Cumplida! 🌌</h2>
        <div style="font-size: 4rem; margin: 10px 0;" id="succ-emoji">🚀</div>
        <p style="font-size: 1.5rem; font-weight: 800; color: #1E3A8A; margin-bottom: 5px;" id="succ-word">WORD</p>
        <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="succ-trans">(Traducción)</p>
        
        <p style="font-size: 15px; color: #065F46; background: #D1FAE5; padding: 15px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; border: 1px solid #34D399; width: 100%; box-sizing: border-box;" id="succ-mnemonic">💡 Cargando recordatorio...</p>
        
        <button id="btn-next-round" onclick="goToNextRound()" class="btn-play bg-blue-500" style="margin-top: 0;">Siguiente ➡️</button>
    </div>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh; padding: 10px; box-sizing: border-box; width: 100%;">
    <div class="game-area text-center mx-auto" style="max-width: 800px; border: none; background: transparent; padding-top: 5px; box-shadow: none; width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: var(--brand-blue); font-size: clamp(20px, 5vw, 26px); font-weight: 900;">🚀 Misión Espacial</h3>
            <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
        </div>

        <div class="space-board" id="space-board">
            <div class="starfield"></div>
            
            <div class="hud-top">
                <div class="target-display" id="hud-word">WORD</div>
                <div class="fuel-bar-container"><div class="fuel-bar-fill" id="fuel-bar"></div></div>
            </div>

            <div class="lane"></div><div class="lane"></div><div class="lane"></div>

            <div class="rocket-player" id="rocket">
                <div class="rocket-body">🚀</div>
                <div class="rocket-flame" id="flame"></div>
            </div>
            
            <div id="items-container"></div>
        </div>
    </div>
</main>

<script>
    function applyTwemoji(node) {
        if (typeof twemoji !== 'undefined') { twemoji.parse(node, { folder: 'svg', ext: '.svg' }); }
    }

    let roundsData = window.dynamicRoundsData || [];
    let currentRoundIndex = 0;
    let roundData = null;
    let gameActive = false;
    let score = 0;
    const maxScore = 3;
    let currentLane = 1; 
    let targetWord = "";
    
    const board = document.getElementById('space-board');
    const rocket = document.getElementById('rocket');
    const itemsContainer = document.getElementById('items-container');
    const fuelBar = document.getElementById('fuel-bar');
    
    let fallingItems = [];
    let gameLoopId;
    let lastTime = 0;
    let spawnTimer = 0;
    
    const timeToCrossScreenMs = 2800; 
    let dynamicFallSpeed = 0; 

    window.addEventListener('resize', () => { 
        dynamicFallSpeed = board.offsetHeight / timeToCrossScreenMs;
        if(gameActive) updateRocketPosition(); 
    });

    // LÓGICA TÁCTIL (Deslizar o Tocar)
    function handleTouchMove(e) {
        if(!gameActive) return;
        let clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const rect = board.getBoundingClientRect();
        const xPos = clientX - rect.left;
        const width = rect.width;
        
        if (xPos < width / 3) currentLane = 0;
        else if (xPos < (width * 2) / 3) currentLane = 1;
        else currentLane = 2;
        
        updateRocketPosition();
    }

    board.addEventListener('pointerdown', handleTouchMove);
    board.addEventListener('pointermove', (e) => {
        if (e.buttons > 0 || e.pointerType === 'touch') handleTouchMove(e);
    });

    if (roundsData.length > 0) loadRound(currentRoundIndex);

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        document.getElementById('tut-emoji-display').innerText = roundData.emoji || '📦';

        if(roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        if(roundData.mnemonic) {
            document.getElementById('tut-mnemonic').innerText = "💡 " + roundData.mnemonic;
            document.getElementById('tut-mnemonic').style.display = 'block';
        } else {
            document.getElementById('tut-mnemonic').style.display = 'none';
        }

        document.getElementById('btn-tut-audio').onclick = () => {
            if(typeof playPronunciation === 'function') playPronunciation(targetWord);
        };

        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;

        score = 0;
        fuelBar.style.width = '0%';
        itemsContainer.innerHTML = '';
        fallingItems = [];
        dynamicFallSpeed = board.offsetHeight / timeToCrossScreenMs; 
        
        rocket.classList.remove('blast-off');
        document.getElementById('flame').style.transform = 'scaleY(1)';
        document.getElementById('flame').style.boxShadow = '0 0 20px #F29C38, 0 0 40px #EF4444';
        updateRocketPosition();

        document.getElementById('tutorial-modal').classList.add('active');
        applyTwemoji(document.body);
    }

    function startGame() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop'); 
        document.getElementById('tutorial-modal').classList.remove('active');
        
        if(typeof attemptAutoplay === 'function') attemptAutoplay();

        gameActive = true;
        lastTime = performance.now();
        requestAnimationFrame(gameLoop);
    }

    function updateRocketPosition() {
        const laneWidth = board.offsetWidth / 3;
        const targetX = (laneWidth * currentLane) + (laneWidth / 2) - 40; 
        rocket.style.left = targetX + 'px';
    }

    function gameLoop(timestamp) {
        if(!gameActive) return;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        
        spawnTimer += dt;
        if(spawnTimer > 1800) { 
            spawnItem();
            spawnTimer = 0;
        }
        
        updateItems(dt);
        gameLoopId = requestAnimationFrame(gameLoop);
    }

    function spawnItem() {
        const laneIndex = Math.floor(Math.random() * 3);
        const isCorrect = Math.random() > 0.4;
        
        let wordDisplay = targetWord;
        let emojiDisplay = roundData.emoji || '📦';

        if (!isCorrect) {
            let dists = roundData.distractors || [];
            if (dists.length > 0) {
                let d = dists[Math.floor(Math.random() * dists.length)];
                wordDisplay = d.word || 'ERR';
                emojiDisplay = d.emoji || '❓';
            } else {
                wordDisplay = 'ERR';
                emojiDisplay = '☄️';
            }
        }

        const el = document.createElement('div');
        el.className = 'falling-item';
        el.innerHTML = `
            <div class="item-bubble">
                <div class="item-emoji">${emojiDisplay}</div>
                <div class="item-word">${wordDisplay}</div>
            </div>
        `;
        
        el.style.top = '-100px';
        const laneWidth = board.offsetWidth / 3;
        el.style.left = (laneWidth * laneIndex) + 'px';
        el.style.width = laneWidth + 'px';
        
        itemsContainer.appendChild(el);
        applyTwemoji(el); 
        
        fallingItems.push({
            el: el,
            y: -100,
            lane: laneIndex,
            isCorrect: isCorrect,
            active: true
        });
    }

    function updateItems(dt) {
        const rocketY = board.offsetHeight - 120; 

        for(let i = fallingItems.length - 1; i >= 0; i--) {
            let item = fallingItems[i];
            if(!item.active) continue;

            item.y += dynamicFallSpeed * dt;
            item.el.style.top = item.y + 'px';
            
            if(item.y > rocketY && item.y < rocketY + 60 && item.lane === currentLane) {
                handleCollision(item);
                continue;
            }
            
            if(item.y > board.offsetHeight) {
                item.el.remove();
                fallingItems.splice(i, 1);
            }
        }
    }

    function handleCollision(item) {
        item.active = false;
        item.el.remove();
        
        if(item.isCorrect) {
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('correct');

            score++;
            fuelBar.style.width = (score / maxScore * 100) + '%';
            fuelBar.style.animation = 'fuelFlash 0.5s';
            setTimeout(() => fuelBar.style.animation = 'none', 500);
            
            if(score >= maxScore) {
                setTimeout(checkNextRound, 500);
            }
        } else {
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('wrong');

            board.classList.add('hit-flash');
            setTimeout(() => board.classList.remove('hit-flash'), 300);
            score = Math.max(0, score - 1);
            fuelBar.style.width = (score / maxScore * 100) + '%';
        }
    }

    function checkNextRound() {
        gameActive = false;
        cancelAnimationFrame(gameLoopId);
        itemsContainer.innerHTML = '';
        
        document.getElementById('flame').style.transform = 'scaleY(2.5)';
        document.getElementById('flame').style.boxShadow = '0 0 50px #F29C38, 0 0 80px #EF4444';
        rocket.classList.add('blast-off');
        
        setTimeout(showSuccessModal, 1200);
    }

    function showSuccessModal() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
        
        document.getElementById('succ-emoji').innerText = roundData.emoji || '🚀';
        document.getElementById('succ-word').innerText = targetWord;
        document.getElementById('succ-trans').innerText = `(${roundData.translation})`;
        
        if(roundData.mnemonic) {
            document.getElementById('succ-mnemonic').innerText = "💡 Recuerda: " + roundData.mnemonic;
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
            finalWin();
        }
    }

    function finalWin() {
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') {
            unlockNextButton(<?php echo $lesson_id ?? 0; ?>, <?php echo $reward_stars ?? 5; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
        }
    }
    
    document.addEventListener('keydown', (e) => {
        if(!gameActive) return;
        if(e.key === 'ArrowLeft' && currentLane > 0) { currentLane--; updateRocketPosition(); }
        if(e.key === 'ArrowRight' && currentLane < 2) { currentLane++; updateRocketPosition(); }
    });
</script>