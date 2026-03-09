<style>
    .space-board { touch-action: pan-y; /* Permite scroll vertical pero detecta toques */ }
    
    .starfield { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(2px 2px at 20px 30px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 40px 70px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 50px 160px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 90px 40px, #ffffff, rgba(0,0,0,0)); background-repeat: repeat; animation: spaceScroll 20s linear infinite; opacity: 0.5; pointer-events: none; }
    
    .lane { flex: 1; height: 100%; border-right: 1px dashed rgba(255,255,255,0.1); position: relative; cursor: pointer; }
    .lane:last-child { border-right: none; }
    .lane:active { background: rgba(255,255,255,0.05); }

    .rocket-player { position: absolute; bottom: 20px; width: 80px; height: 100px; left: calc(50% - 40px); transition: left 0.15s ease-out, bottom 1s ease-in; z-index: 20; display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .rocket-body { font-size: 65px; line-height: 1; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); animation: floatRocket 1s infinite alternate; }
    .rocket-flame { width: 20px; height: 40px; background: #F29C38; border-radius: 50% 50% 20% 20%; margin-top: -10px; animation: flameFlicker 0.1s infinite alternate; box-shadow: 0 0 20px #F29C38, 0 0 40px #EF4444; }
    
    .falling-item { position: absolute; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; transition: top linear; pointer-events: none; }
    .item-bubble { background: rgba(255,255,255,0.9); border: 3px solid var(--brand-lblue); border-radius: 20px; padding: 10px 15px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    .item-emoji { font-size: 40px; margin-bottom: 5px; }
    .item-word { font-weight: 800; color: var(--brand-blue); font-size: 16px; text-transform: uppercase; }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: space-between; padding: 0 20px; z-index: 30; pointer-events: none; }
    .target-display { background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border: 2px solid rgba(255,255,255,0.2); color: white; padding: 8px 25px; border-radius: 50px; font-weight: 800; font-size: 20px; letter-spacing: 2px; }
    .fuel-bar-container { width: 120px; height: 25px; background: rgba(0,0,0,0.5); border-radius: 50px; border: 2px solid rgba(255,255,255,0.2); overflow: hidden; position: relative; }
    .fuel-bar-fill { height: 100%; width: 0%; background: linear-gradient(90deg, #F29C38, #68A93E); transition: width 0.3s ease-out; }
    
    .mission-modal { overflow-y: auto; }
    .btn-action { background: var(--brand-green); color: white; border: none; padding: 16px 40px; font-size: 18px; font-weight: 700; border-radius: 50px; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); margin-top: 20px; transition: 0.2s; }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }

    .hit-flash { animation: screenFlash 0.3s; }
    .blast-off { bottom: 120% !important; animation: none !important; }

    @keyframes spaceScroll { from { background-position: 0 0; } to { background-position: 0 480px; } }
    @keyframes floatRocket { from { transform: translateY(0); } to { transform: translateY(-5px); } }
    @keyframes flameFlicker { from { transform: scaleY(1); opacity: 0.8; } to { transform: scaleY(1.3); opacity: 1; } }
    @keyframes screenFlash { 0% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } 50% { box-shadow: inset 0 0 50px 20px rgba(239, 68, 68, 0.8); } 100% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } }
    @keyframes fuelFlash { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.5) drop-shadow(0 0 10px #68A93E); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <h3 style="margin: 0; margin-bottom: 20px; color: var(--brand-blue); font-size: 1.8rem;">🚀 Misión Espacial</h3>

    <div class="space-board" id="space-board">
        <div class="starfield"></div>
        
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--white); margin-top: 0; font-size: 2.2rem;">¡Llena el Tanque!</h2>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px;">Mueve el cohete para atrapar el combustible correcto:</p>
            <div style="font-size: 45px; font-weight: 800; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.5);" id="tut-word">WORD</div>
            <p style="color: #64748B; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button class="btn-action" style="background: var(--brand-blue); box-shadow: 0 4px 14px rgba(28, 61, 106, 0.3);" onclick="playIntroAudio()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Despegar!</button>
            </div>
        </div>

        <div class="hud-top">
            <div class="target-display" id="hud-word">WORD</div>
            <div class="fuel-bar-container"><div class="fuel-bar-fill" id="fuel-bar"></div></div>
        </div>

        <div class="lane" onpointerdown="moveRocket(0)"></div>
        <div class="lane" onpointerdown="moveRocket(1)"></div>
        <div class="lane" onpointerdown="moveRocket(2)"></div>

        <div class="rocket-player" id="rocket">
            <div class="rocket-body">🚀</div>
            <div class="rocket-flame" id="flame"></div>
        </div>
        
        <div id="items-container"></div>
    </div>
</div>

<script>
    let roundData = window.dynamicRoundsData[0];
    let gameActive = false;
    let score = 0;
    const maxScore = 3;
    let currentLane = 1; // 0: Izquierda, 1: Centro, 2: Derecha
    
    const board = document.getElementById('space-board');
    const rocket = document.getElementById('rocket');
    const itemsContainer = document.getElementById('items-container');
    const fuelBar = document.getElementById('fuel-bar');
    
    let fallingItems = [];
    let gameLoopId;
    let lastTime = 0;
    let spawnTimer = 0;
    const fallSpeed = 0.25; // Píxeles por milisegundo

    // Configuración Inicial
    const targetWord = roundData.target_word || roundData.word;
    document.getElementById('tut-word').innerText = targetWord;
    document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
    document.getElementById('hud-word').innerText = targetWord;

    // Reposicionar cohete al iniciar
    updateRocketPosition();

    function playIntroAudio() {
        document.getElementById('btn-start').style.display = 'block';
        if(typeof playTTS !== 'undefined') playTTS(roundData.phonetic || targetWord, false);
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 300);
        gameActive = true;
        requestAnimationFrame(gameLoop);
    }

    function moveRocket(laneIndex) {
        if(!gameActive) return;
        currentLane = laneIndex;
        updateRocketPosition();
    }

    function updateRocketPosition() {
        const laneWidth = board.offsetWidth / 3;
        const targetX = (laneWidth * currentLane) + (laneWidth / 2) - 40; // 40 es la mitad del cohete
        rocket.style.left = targetX + 'px';
    }

    function gameLoop(timestamp) {
        if(!gameActive) return;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        
        spawnTimer += dt;
        if(spawnTimer > 1800) { // Nuevo item cada 1.8 segundos
            spawnItem();
            spawnTimer = 0;
        }
        
        updateItems(dt);
        
        gameLoopId = requestAnimationFrame(gameLoop);
    }

    function spawnItem() {
        const laneIndex = Math.floor(Math.random() * 3);
        const isCorrect = Math.random() > 0.4;
        
        let wordDisplay = isCorrect ? targetWord : (roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : 'ERR');
        let emojiDisplay = isCorrect ? '⭐' : '☄️';

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
        
        fallingItems.push({
            el: el,
            y: -100,
            lane: laneIndex,
            isCorrect: isCorrect,
            active: true
        });
    }

    function updateItems(dt) {
        const rocketY = board.offsetHeight - 120; // Aproximación de colisión

        for(let i = fallingItems.length - 1; i >= 0; i--) {
            let item = fallingItems[i];
            if(!item.active) continue;

            item.y += fallSpeed * dt;
            item.el.style.top = item.y + 'px';
            
            // Colisión con el cohete
            if(item.y > rocketY && item.y < rocketY + 60 && item.lane === currentLane) {
                handleCollision(item);
                continue;
            }
            
            // Eliminar si sale de la pantalla
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
            score++;
            fuelBar.style.width = (score / maxScore * 100) + '%';
            fuelBar.style.animation = 'fuelFlash 0.5s';
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            if(typeof playTTS !== 'undefined') playTTS(roundData.phonetic || targetWord, false);
            setTimeout(() => fuelBar.style.animation = 'none', 500);
            
            if(score >= maxScore) {
                setTimeout(executeWin, 500);
            }
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            board.classList.add('hit-flash');
            setTimeout(() => board.classList.remove('hit-flash'), 300);
            score = Math.max(0, score - 1);
            fuelBar.style.width = (score / maxScore * 100) + '%';
        }
    }

    function executeWin() {
        gameActive = false;
        cancelAnimationFrame(gameLoopId);
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        
        // Animación de despegue
        document.getElementById('flame').style.transform = 'scaleY(2.5)';
        document.getElementById('flame').style.boxShadow = '0 0 50px #F29C38, 0 0 80px #EF4444';
        rocket.classList.add('blast-off');
        
        itemsContainer.innerHTML = ''; // Limpiar meteoros
        
        setTimeout(() => {
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id; ?>, 10, <?php echo $lesson['module_id']; ?>);
        }, 1500);
    }
    
    // Controles de teclado opcionales
    document.addEventListener('keydown', (e) => {
        if(!gameActive) return;
        if(e.key === 'ArrowLeft' && currentLane > 0) moveRocket(currentLane - 1);
        if(e.key === 'ArrowRight' && currentLane < 2) moveRocket(currentLane + 1);
    });
</script>