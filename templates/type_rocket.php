<?php
// templates/type_rocket.php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/head.php';
require_once '../includes/navbar.php';

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>
    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

    /* Seguro de Pantalla Horizontal (Landscape Warning) */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: #0F172A; z-index: 10000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    .space-board { position: relative; width: 100%; height: 65vh; min-height: 500px; max-height: 600px; background: linear-gradient(180deg, #0F172A 0%, #1E293B 60%, #1E3A8A 100%); border-radius: 24px; overflow: hidden; border: 4px solid #38BDF8; margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); touch-action: pan-y; display: flex; }
    
    .starfield { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(2px 2px at 20px 30px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 40px 70px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 50px 160px, #ffffff, rgba(0,0,0,0)), radial-gradient(2px 2px at 90px 40px, #ffffff, rgba(0,0,0,0)); background-repeat: repeat; animation: spaceScroll 20s linear infinite; opacity: 0.6; pointer-events: none; }
    
    .lane { flex: 1; height: 100%; border-right: 1px dashed rgba(255,255,255,0.1); position: relative; cursor: pointer; touch-action: manipulation; }
    .lane:last-child { border-right: none; }
    .lane:active { background: rgba(255,255,255,0.05); }

    .rocket-player { position: absolute; bottom: 20px; width: 80px; height: 100px; left: calc(50% - 40px); transition: left 0.15s cubic-bezier(0.175, 0.885, 0.32, 1.275), bottom 1s ease-in; z-index: 20; display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .rocket-body { font-size: clamp(50px, 12vw, 70px); line-height: 1; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); animation: floatRocket 1s infinite alternate; }
    .rocket-flame { width: 20px; height: 40px; background: #F59E0B; border-radius: 50% 50% 20% 20%; margin-top: -10px; animation: flameFlicker 0.1s infinite alternate; box-shadow: 0 0 20px #F59E0B, 0 0 40px #EF4444; }
    
    .falling-item { position: absolute; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; pointer-events: none; }
    .item-bubble { background: rgba(255,255,255,0.95); border: 3px solid #38BDF8; border-radius: 20px; padding: 10px 15px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
    .item-emoji { font-size: clamp(35px, 8vw, 45px); margin-bottom: 5px; }
    .item-word { font-weight: 900; color: #1E3A8A; font-size: clamp(12px, 3vw, 16px); text-transform: uppercase; }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: space-between; padding: 0 20px; z-index: 30; pointer-events: none; }
    .target-display { background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,0.3); color: white; padding: 8px 25px; border-radius: 50px; font-weight: 900; font-size: clamp(16px, 4vw, 22px); letter-spacing: 2px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .fuel-bar-container { width: clamp(100px, 25vw, 150px); height: 25px; background: rgba(0,0,0,0.5); border-radius: 50px; border: 2px solid rgba(255,255,255,0.4); overflow: hidden; position: relative; box-shadow: inset 0 2px 5px rgba(0,0,0,0.5); }
    .fuel-bar-fill { height: 100%; width: 0%; background: linear-gradient(90deg, #F59E0B, #4CAF50); transition: width 0.3s cubic-bezier(0.175, 0.885, 0.32, 1); }
    
    .hit-flash { animation: screenFlash 0.3s; }
    .blast-off { bottom: 120% !important; animation: none !important; }

    @keyframes spaceScroll { from { background-position: 0 0; } to { background-position: 0 480px; } }
    @keyframes floatRocket { from { transform: translateY(0); } to { transform: translateY(-5px); } }
    @keyframes flameFlicker { from { transform: scaleY(1); opacity: 0.8; } to { transform: scaleY(1.3); opacity: 1; } }
    @keyframes screenFlash { 0% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } 50% { box-shadow: inset 0 0 50px 20px rgba(239, 68, 68, 0.8); } 100% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } }
    @keyframes fuelFlash { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.5) drop-shadow(0 0 10px #4CAF50); } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego espacial necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh;">
    <div class="game-area text-center mx-auto" style="max-width: 700px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="text-2xl font-black text-gray-800">🚀 Misión Espacial</h3>
            <div id="round-counter" style="background: #1E3A8A; color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
        </div>

        <div class="space-board" id="space-board">
            <div class="starfield"></div>
            
            <div id="tutorial-modal" class="modal-overlay active">
                <div class="modal-content">
                    <h2 class="modal-title">¡Llena el Tanque! ⛽</h2>
                    <p class="modal-text" id="tut-context">Atrapa el combustible correcto y esquiva los meteoritos:</p>
                    <div style="font-size: 3rem; font-weight: 900; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3); margin: 10px 0;" id="tut-word">WORD</div>
                    <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px;" id="tut-trans">(Traducción)</p>
                    <button id="btn-start" onclick="startGame()" class="btn-play w-full bg-blue-500 hover:bg-blue-600">▶️ ¡Despegar!</button>
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
</main>

<script>
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
    const fallSpeed = 0.30; 

    document.addEventListener('DOMContentLoaded', () => {
        twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
        if(roundsData.length === 0) {
            roundsData = [{ target_word: 'STAR', translation: 'Estrella', distractors: ['MOON', 'SUN', 'SKY'] }];
        }
        window.addEventListener('resize', () => { if(gameActive) updateRocketPosition(); });
        loadRound(currentRoundIndex);
    });

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
        
        rocket.classList.remove('blast-off');
        document.getElementById('flame').style.transform = 'scaleY(1)';
        document.getElementById('flame').style.boxShadow = '0 0 20px #F59E0B, 0 0 40px #EF4444';
        updateRocketPosition();

        document.getElementById('tutorial-modal').classList.add('active');
        twemoji.parse(document.getElementById('tutorial-modal'), { folder: 'svg', ext: '.svg' });
    }

    function startGame() {
        document.getElementById('tutorial-modal').classList.remove('active');
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
        if(spawnTimer > 1500) { 
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
        twemoji.parse(el, { folder: 'svg', ext: '.svg' }); 
        
        fallingItems.push({ el: el, y: -100, lane: laneIndex, isCorrect: isCorrect, active: true });
    }

    function updateItems(dt) {
        const rocketY = board.offsetHeight - 120; 

        for(let i = fallingItems.length - 1; i >= 0; i--) {
            let item = fallingItems[i];
            if(!item.active) continue;

            item.y += fallSpeed * dt;
            item.el.style.top = item.y + 'px';
            
            if(item.y > rocketY && item.y < rocketY + 80 && item.lane === currentLane) {
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
            score++;
            fuelBar.style.width = (score / maxScore * 100) + '%';
            fuelBar.style.animation = 'fuelFlash 0.5s';
            setTimeout(() => fuelBar.style.animation = 'none', 500);
            
            if(score >= maxScore) {
                setTimeout(checkNextRound, 500);
            }
        } else {
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
        document.getElementById('flame').style.boxShadow = '0 0 50px #F59E0B, 0 0 80px #EF4444';
        rocket.classList.add('blast-off');
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            setTimeout(executeWin, 1500);
        }
    }

    function executeWin() {
        const modal = document.getElementById('tutorial-modal');
        modal.querySelector('.modal-title').innerHTML = "¡Viaje Exitoso! 🌌";
        modal.querySelector('.modal-text').innerHTML = "¡Llegamos a nuestro destino con el tanque lleno!";
        modal.querySelector('#tut-word').style.display = 'none';
        modal.querySelector('#tut-trans').style.display = 'none';
        modal.querySelector('.btn-play').innerHTML = "Aterrizar 🛬";
        modal.classList.add('active');
        twemoji.parse(modal, { folder: 'svg', ext: '.svg' });

        if(typeof fireConfetti !== 'undefined') fireConfetti();

        // Guardado real en la Base de Datos
        const payload = {
            lesson_id: <?php echo $lesson_id; ?>,
            stars: <?php echo $reward_stars; ?>
        };

        fetch('../app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            console.log("Progreso guardado correctamente:", data);
            if(typeof unlockNextButton !== 'undefined') {
                unlockNextButton(payload.lesson_id, payload.stars, <?php echo $lesson['module_id'] ?? 0; ?>);
            }
        })
        .catch(error => console.error("Error al guardar en DB:", error));
    }
    
    document.addEventListener('keydown', (e) => {
        if(!gameActive) return;
        if(e.key === 'ArrowLeft' && currentLane > 0) moveRocket(currentLane - 1);
        if(e.key === 'ArrowRight' && currentLane < 2) moveRocket(currentLane + 1);
    });
</script>

<?php require_once '../includes/footer.php'; ?>