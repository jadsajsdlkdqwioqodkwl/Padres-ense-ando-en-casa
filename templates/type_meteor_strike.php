<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: METEOR STRIKE PRO (MULTIRONDA)
// ==========================================
$reward_stars = $lesson['reward_stars'] ?? 10;

// Estructura Multironda dinámica
$rounds = $lesson_data['rounds'] ?? [
    [
        'target_word' => $lesson_data['target_word'] ?? 'APPLE',
        'phonetic' => $lesson_data['phonetic'] ?? 'ápol',
        'translation' => $lesson_data['translation'] ?? 'Manzana',
        'speed' => $lesson_data['speed'] ?? 7,
        'context_es' => $lesson_data['context_es'] ?? "¡Alerta! Una lluvia de meteoritos amenaza al dinosaurio. Toca solo el meteorito que tenga...",
        'items' => $lesson_data['items'] ?? [
            ['id' => 1, 'content' => '🍎', 'is_correct' => true],
            ['id' => 2, 'content' => '🍌', 'is_correct' => false],
            ['id' => 3, 'content' => '🍇', 'is_correct' => false]
        ]
    ]
];
?>

<style>
    /* ==========================================
       ENTORNO DE JUEGO Y DIBUJOS CSS
    ========================================== */
    .meteor-board { position: relative; width: 100%; height: 420px; background: linear-gradient(to bottom, var(--dark) 0%, #2c3e50 100%); border-radius: 20px; overflow: hidden; border: 4px solid var(--primary); margin-bottom: 20px; box-shadow: inset 0 0 50px rgba(0,0,0,0.8); }
    .round-indicator { position: absolute; top: 15px; left: 15px; color: white; font-weight: bold; font-size: 14px; z-index: 50; background: var(--primary); padding: 5px 15px; border-radius: 20px; }
    .ground { position: absolute; bottom: 0; width: 100%; height: 50px; background: #4e342e; border-top: 8px solid #5d4037; z-index: 5; }
    .css-dino { position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%); width: 60px; height: 70px; background: var(--success); border-radius: 30px 30px 10px 10px; z-index: 10; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2); animation: dinoIdle 1s infinite alternate; transition: 0.3s; }
    .css-dino::before { content: ''; position: absolute; top: 15px; right: 15px; width: 10px; height: 10px; background: white; border-radius: 50%; border: 3px solid #333; }
    .css-dino::after { content: ''; position: absolute; top: 30px; right: 5px; width: 20px; height: 5px; background: #333; border-radius: 5px; }
    .css-dino.panic { background: #e74c3c; animation: shake 0.2s infinite; }
    .css-dino.dead { transform: translateX(-50%) scaleY(0.2); background: #333; bottom: 45px;}

    /* ==========================================
       METEORITOS
    ========================================== */
    .meteor { position: absolute; top: -100px; width: 75px; height: 75px; background: var(--accent); border-radius: 50%; cursor: pointer; z-index: 8; display: flex; justify-content: center; align-items: center; font-size: 45px; box-shadow: 0 0 20px #d35400, inset -5px -5px 0 rgba(0,0,0,0.3); transition: transform 0.1s; user-select: none; }
    .meteor::before { content: ''; position: absolute; top: -40px; left: 15px; width: 45px; height: 60px; background: linear-gradient(to top, #f39c12, transparent); border-radius: 50%; z-index: -1; opacity: 0.8; animation: flicker 0.2s infinite alternate; }
    .meteor:active { transform: scale(0.9); }
    .meteor.destroyed { pointer-events: none; animation: popExplosion 0.4s forwards; }
    .meteor.radar-glow { box-shadow: 0 0 40px #00d2d3, inset 0 0 20px white; border: 3px solid #00d2d3; }

    /* ==========================================
       INTERFAZ Y MODAL
    ========================================== */
    .hud { position: absolute; top: 15px; right: 15px; display: flex; gap: 15px; z-index: 15; align-items: center; }
    .target-box { background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 20px; font-size: 18px; font-weight: bold; color: var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid var(--accent); }
    .lives-box { font-size: 20px; letter-spacing: 3px; color: #e74c3c; text-shadow: 0 2px 4px rgba(0,0,0,0.5);}

    .mission-modal { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 15px; transition: opacity 0.5s; padding: 20px; text-align: center; }
    .btn-action { background: var(--success); color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 6px 0 #27ae60; margin-top: 15px; }
    .btn-action:active { transform: translateY(4px); box-shadow: 0 2px 0 #27ae60; }

    @keyframes dinoIdle { 0% { transform: translateX(-50%) translateY(0); } 100% { transform: translateX(-50%) translateY(5px); } }
    @keyframes shake { 0%, 100% { transform: translateX(-50%) rotate(0deg); } 25% { transform: translateX(-55%) rotate(-10deg); } 75% { transform: translateX(-45%) rotate(10deg); } }
    @keyframes flicker { 0% { height: 60px; opacity: 0.6; } 100% { height: 80px; opacity: 1; } }
    @keyframes popExplosion { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(1.5); filter: brightness(2); background: white; } 100% { transform: scale(0); opacity: 0; } }
    @keyframes radarScan { 0% { box-shadow: 0 0 0 rgba(0,210,211,0.5); } 50% { box-shadow: 0 0 50px rgba(0,210,211,0.8); } 100% { box-shadow: 0 0 0 rgba(0,210,211,0); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary);">☄️ Meteor Strike</h3>
        <button onclick="useRadar()" id="btn-radar" style="background: #00cec9; border: none; border-radius: 50%; width: 45px; height: 45px; font-size: 20px; cursor: pointer; box-shadow: 0 4px 0 #00b894; color: white;" title="Radar de Emergencia">📡</button>
    </div>

    <div class="meteor-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Stage 1</div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">🔭 El Observatorio</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 10px;" id="tut-context">Cargando...</p>
            <div style="font-size: 35px; font-weight: bold; color: var(--accent); margin: 15px 0; letter-spacing: 2px;" id="tut-word">
                WORD
            </div>
            <p style="color: #666; font-size: 20px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Iniciar Defensa!</button>
            </div>
        </div>

        <div class="hud" id="hud" style="display: none;">
            <div class="target-box" id="target-box">WORD</div>
            <div class="lives-box" id="lives">❤️❤️❤️</div>
        </div>

        <div class="css-dino" id="dino"></div>
        <div class="ground"></div>
    </div>
</div>

<script>
    const roundsData = <?php echo json_encode($rounds); ?>;
    const board = document.getElementById('game-board');
    const dino = document.getElementById('dino');
    const livesDisplay = document.getElementById('lives');
    
    let currentRoundIndex = 0;
    let roundItems = [];
    let currentSpeed = 7;
    
    let lives = 3;
    let gameActive = false;
    let currentItemIndex = 0;
    let activeMeteor = null;
    let fallInterval = null;
    let meteorY = -100;
    let currentIsCorrect = false;

    loadRound(currentRoundIndex);

    function loadRound(index) {
        const round = roundsData[index];
        roundItems = [...round.items].sort(() => Math.random() - 0.5); 
        currentSpeed = round.speed || 7;
        currentItemIndex = 0;

        document.getElementById('round-indicator').innerText = `Stage ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es || "Toca solo el meteorito que tenga...";
        document.getElementById('tut-word').innerText = round.target_word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        document.getElementById('target-box').innerText = round.target_word;

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('hud').style.display = 'none';
        document.getElementById('btn-start').style.display = 'none';

        dino.classList.remove('panic', 'dead');
        
        setTimeout(playSpanglishIntro, 500);
    }

   function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        // Fonética
        const phoneticToRead = round.phonetic || round.target_word;
        playSpanglish('', phoneticToRead, '');
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        document.getElementById('hud').style.display = 'flex';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 500);
        gameActive = true;
        setTimeout(spawnMeteor, 500);
    }

    function spawnMeteor() {
        if (!gameActive) return;
        
        if (currentItemIndex >= roundItems.length) {
            currentItemIndex = 0;
            roundItems.sort(() => Math.random() - 0.5);
        }

        const data = roundItems[currentItemIndex];
        currentItemIndex++;
        currentIsCorrect = data.is_correct;

        activeMeteor = document.createElement('div');
        activeMeteor.className = 'meteor';
        activeMeteor.innerHTML = data.content; 
        
        const randomX = Math.floor(Math.random() * (board.offsetWidth - 85)) + 10;
        activeMeteor.style.left = randomX + 'px';
        meteorY = -100;
        activeMeteor.style.top = meteorY + 'px';

        board.appendChild(activeMeteor);

        activeMeteor.addEventListener('mousedown', () => checkHit(data.is_correct));
        activeMeteor.addEventListener('touchstart', (e) => { e.preventDefault(); checkHit(data.is_correct); });

        const boardHeight = board.offsetHeight;
        const groundLevel = boardHeight - 80; 
        const step = groundLevel / (currentSpeed * 20); 

        clearInterval(fallInterval);
        fallInterval = setInterval(() => {
            if (!gameActive) return;
            
            meteorY += step;
            activeMeteor.style.top = meteorY + 'px';

            if (meteorY > groundLevel * 0.6) dino.classList.add('panic');
            else dino.classList.remove('panic');

            if (meteorY >= groundLevel) {
                clearInterval(fallInterval);
                if (data.is_correct) {
                    takeDamage("¡Dejaste caer el correcto!");
                } else {
                    activeMeteor.remove();
                    spawnMeteor();
                }
            }
        }, 50);
    }

    function checkHit(isCorrect) {
        if (!gameActive || !activeMeteor) return;

        clearInterval(fallInterval);
        activeMeteor.classList.add('destroyed');

        if (isCorrect) {
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();
            setTimeout(() => checkNextRound(), 400); 
        } else {
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            takeDamage("¡Ese no era el correcto!");
            
            setTimeout(() => {
                if (activeMeteor) activeMeteor.remove();
                if(gameActive) spawnMeteor();
            }, 500);
        }
    }

    function useRadar() {
        if(!gameActive || !activeMeteor) return;
        
        document.getElementById('btn-radar').style.animation = 'radarScan 1s';
        setTimeout(() => document.getElementById('btn-radar').style.animation = 'none', 1000);

        const targetPhonetic = roundsData[currentRoundIndex].phonetic || roundsData[currentRoundIndex].target_word;
        if(typeof playTTS !== 'undefined') {
            playTTS(targetPhonetic);
        }

        if(currentIsCorrect) {
            activeMeteor.classList.add('radar-glow');
            setTimeout(() => activeMeteor.classList.remove('radar-glow'), 1500);
        }

        meteorY += 60;
        activeMeteor.style.top = meteorY + 'px';
        activeMeteor.style.transition = 'top 0.2s';
        setTimeout(() => activeMeteor.style.transition = 'none', 200);
    }

    function takeDamage(msg) {
        lives--;
        let text = '';
        for(let i=0; i<lives; i++) text += '❤️';
        livesDisplay.innerText = text;
        
        board.style.boxShadow = "inset 0 0 50px rgba(231,76,60,0.8)";
        setTimeout(() => board.style.boxShadow = "inset 0 0 50px rgba(0,0,0,0.8)", 300);

        if (lives <= 0) executeLoss("¡El dinosaurio fue aplastado!");
    }

    function checkNextRound() {
        gameActive = false;
        dino.classList.remove('panic');
        
        const currentPhonetic = roundsData[currentRoundIndex].phonetic || roundsData[currentRoundIndex].target_word;
        if(typeof playTTS !== 'undefined') playTTS(currentPhonetic);
        if(typeof sfxWin !== 'undefined') sfxWin.play();

        currentRoundIndex++;

        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => {
                if (activeMeteor) activeMeteor.remove();
                loadRound(currentRoundIndex);
            }, 1000);
        } else {
            setTimeout(() => {
                executeWin();
            }, 800);
        }
    }

    function executeWin() {
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }

    function executeLoss(msg) {
        gameActive = false;
        clearInterval(fallInterval);
        dino.classList.add('dead');
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();
        
        setTimeout(() => {
            alert(msg + " ¡Inténtalo de nuevo!");
            location.reload();
        }, 1200);
    }
</script>