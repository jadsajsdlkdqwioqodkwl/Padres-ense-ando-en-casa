<?php
// templates/type_rocket.php
// Limpio de cabeceras. Inyectado desde lesson.php
$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    /* Seguro de Pantalla Horizontal */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: #1E293B; z-index: 10000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

    .space-board { position: relative; width: 100%; height: 60vh; min-height: 480px; max-height: 750px; background: linear-gradient(180deg, #0F172A 0%, #1E293B 60%, var(--brand-blue) 100%); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-lblue); margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); touch-action: pan-y; display: flex; }
    
    .starfield { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(2px 2px at 20px 30px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 40px 70px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 50px 160px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 90px 40px, #ffffff, rgba(0,0,0,0)); background-repeat: repeat; animation: spaceScroll 20s linear infinite; opacity: 0.5; pointer-events: none; }
    
    .lane { flex: 1; height: 100%; border-right: 1px dashed rgba(255,255,255,0.1); position: relative; cursor: pointer; touch-action: manipulation; }
    .lane:last-child { border-right: none; }
    .lane:active { background: rgba(255,255,255,0.05); }

    .rocket-player { position: absolute; bottom: 20px; width: 80px; height: 100px; left: calc(50% - 40px); transition: left 0.15s ease-out, bottom 1s ease-in; z-index: 20; display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .rocket-body { font-size: clamp(50px, 12vw, 65px); line-height: 1; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); animation: floatRocket 1s infinite alternate; }
    .rocket-flame { width: 20px; height: 40px; background: #F29C38; border-radius: 50% 50% 20% 20%; margin-top: -10px; animation: flameFlicker 0.1s infinite alternate; box-shadow: 0 0 20px #F29C38, 0 0 40px #EF4444; }
    
    .falling-item { position: absolute; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; pointer-events: none; }
    .item-bubble { background: rgba(255,255,255,0.9); border: 3px solid var(--brand-lblue); border-radius: 20px; padding: 10px 15px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    .item-emoji { font-size: clamp(30px, 8vw, 40px); margin-bottom: 5px; }
    .item-word { font-weight: 800; color: var(--brand-blue); font-size: clamp(12px, 3vw, 16px); text-transform: uppercase; }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: space-between; padding: 0 20px; z-index: 30; pointer-events: none; }
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

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--brand-blue); font-size: 1.8rem;">🚀 Misión Espacial</h3>
        <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
    </div>

    <div class="space-board" id="space-board">
        <div class="starfield"></div>
        
        <div id="tutorial-modal" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.95); z-index:100; display:flex; flex-direction:column; justify-content:center; align-items:center;">
            <h2 style="color: var(--white); margin-top: 0; font-size: 2.2rem;">¡Llena el Tanque!</h2>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px;" id="tut-context">Atrapa el combustible correcto:</p>
            <div style="font-size: 45px; font-weight: 800; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.5);" id="tut-word">WORD</div>
            <p style="color: #64748B; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <button class="btn-large" id="btn-start" onclick="startGame()" style="background: var(--brand-orange); color: white; margin-top: 0;">▶️ ¡Despegar!</button>
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
    
    // FIX DE ESCALADO EXTREMO (IPAD PRO BLINDSPOT)
    // Queremos que cualquier objeto tarde exactamente 2800ms en cruzar la pantalla completa, 
    // sin importar si la pantalla mide 400px de alto o 1000px de alto.
    const timeToCrossScreenMs = 2800; 
    let dynamicFallSpeed = 0; 

    // Al recargar o cambiar de tamaño, actualizamos la velocidad
    window.addEventListener('resize', () => { 
        dynamicFallSpeed = board.offsetHeight / timeToCrossScreenMs;
        if(gameActive) updateRocketPosition(); 
    });

    if (roundsData.length > 0) loadRound(currentRoundIndex);

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        if(roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;

        score = 0;
        fuelBar.style.width = '0%';
        itemsContainer.innerHTML = '';
        fallingItems = [];
        dynamicFallSpeed = board.offsetHeight / timeToCrossScreenMs; // Calculamos por primera vez
        
        rocket.classList.remove('blast-off');
        document.getElementById('flame').style.transform = 'scaleY(1)';
        document.getElementById('flame').style.boxShadow = '0 0 20px #F29C38, 0 0 40px #EF4444';
        updateRocketPosition();

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        
        applyTwemoji(document.body);
    }

    function startGame() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop'); // Sonido UI
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => { document.getElementById('tutorial-modal').style.display = 'none'; }, 300);
        
        gameActive = true;
        lastTime = performance.now();
        requestAnimationFrame(gameLoop);
    }

    function moveRocket(laneIndex) {
        if(!gameActive) return;
        currentLane = laneIndex;
        updateRocketPosition();
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

            // FÍSICA PROPORCIONAL: Usa la velocidad dinámica en lugar de una constante
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
            // INTEGRACIÓN DE AUDIO EXITOSA
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('correct');

            score++;
            fuelBar.style.width = (score / maxScore * 100) + '%';
            fuelBar.style.animation = 'fuelFlash 0.5s';
            setTimeout(() => fuelBar.style.animation = 'none', 500);
            
            if(score >= maxScore) {
                setTimeout(checkNextRound, 500);
            }
        } else {
            // INTEGRACIÓN DE AUDIO FALLIDA
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
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            setTimeout(executeWin, 1500);
        }
    }

    function executeWin() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') {
            unlockNextButton(<?php echo $lesson_id ?? 0; ?>, <?php echo $reward_stars ?? 5; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
        }
    }
    
    document.addEventListener('keydown', (e) => {
        if(!gameActive) return;
        if(e.key === 'ArrowLeft' && currentLane > 0) moveRocket(currentLane - 1);
        if(e.key === 'ArrowRight' && currentLane < 2) moveRocket(currentLane + 1);
    });
</script>