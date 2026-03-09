<?php
// templates/type_frogs.php
// (Sin cabeceras repetidas. Este código es inyectado directamente en lesson.php)

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: #1E293B; z-index: 10000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    /* Río 100% Fluido */
    .river-board { position: relative; width: 100%; height: 65vh; min-height: 450px; max-height: 800px; background: linear-gradient(180deg, #38BDF8 0%, #0284C7 100%); border-radius: 24px; overflow: hidden; border: 4px solid #1E3A8A; margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); touch-action: pan-y; }
    
    .safe-bank { position: absolute; top: 0; left: 0; width: 100%; height: 12%; background: #68A93E; border-bottom: 8px solid #4D7C2D; z-index: 5; border-radius: 0 0 50% 50%; box-shadow: 0 10px 15px rgba(0,0,0,0.2); }

    .water-texture { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 50px 50px, rgba(255,255,255,0.2) 2px, transparent 3px), radial-gradient(circle at 150px 100px, rgba(255,255,255,0.1) 2px, transparent 3px); background-size: 200px 200px; animation: waterFlow 10s linear infinite; pointer-events: none; }
    
    .row-container { position: absolute; width: 100%; display: flex; justify-content: space-around; align-items: center; transition: bottom 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; transform: translateY(50%); }
    
    .lily-pad { width: clamp(70px, 20vw, 110px); height: clamp(70px, 20vw, 110px); background: #68A93E; border-radius: 50% 50% 50% 10%; transform: rotate(45deg); display: flex; justify-content: center; align-items: center; cursor: pointer; border: clamp(2px, 1vw, 4px) solid #4D7C2D; box-shadow: 0 10px 15px rgba(0,0,0,0.3); transition: 0.2s; touch-action: manipulation; }
    .lily-pad:active { transform: rotate(45deg) scale(0.95); box-shadow: 0 5px 5px rgba(0,0,0,0.3); }
    
    .pad-content { transform: rotate(-45deg); display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .pad-emoji { font-size: clamp(28px, 6vw, 45px); line-height: 1; margin-bottom: 2px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    .pad-word { background: #ffffff; color: #1E3A8A; font-size: clamp(10px, 2.5vw, 15px); font-weight: 900; padding: 2px 10px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); text-transform: uppercase; }

    /* Rana con posicionamiento central estricto mediante transform */
    .frog-player { position: absolute; width: clamp(50px, 12vw, 80px); height: clamp(50px, 12vw, 80px); display: flex; justify-content: center; align-items: center; font-size: clamp(45px, 11vw, 75px); line-height: 1; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 20; pointer-events: none; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.4)); transform: translate(-50%, 30%); }
    
    .frog-player.jumping { transform: translate(-50%, -30%) scale(1.3); filter: drop-shadow(0 25px 20px rgba(0,0,0,0.3)); z-index: 30; }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 30; pointer-events: none; }
    .target-display { background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); border: 4px solid #1E3A8A; color: #1E3A8A; padding: 8px 30px; border-radius: 50px; font-weight: 900; font-size: clamp(18px, 5vw, 26px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); text-transform: uppercase; }
    
    .splash-effect { position: absolute; width: clamp(60px, 15vw, 100px); height: clamp(60px, 15vw, 100px); background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; z-index: 15; opacity: 0; pointer-events: none; transform: translate(-50%, 50%) scale(0); }
    .splash-anim { animation: splash 0.5s ease-out forwards; }

    @keyframes waterFlow { from { background-position: 0 0; } to { background-position: 0 200px; } }
    @keyframes splash { 0% { transform: translate(-50%, 50%) scale(0.5); opacity: 1; } 100% { transform: translate(-50%, 50%) scale(1.8); opacity: 0; } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego espacial necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh;">
    <div class="game-area text-center mx-auto" style="max-width: 700px; padding: 10px; background: transparent; border: none; box-shadow: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="text-2xl font-black text-gray-800" style="margin:0; color:var(--brand-blue); font-size: clamp(20px, 5vw, 26px);">🐸 Salto de Ranita</h3>
            <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
        </div>

        <div class="river-board" id="river-board">
            <div class="safe-bank"></div>
            <div class="water-texture"></div>
            
            <div id="tutorial-modal" class="modal-overlay active">
                <div class="modal-content">
                    <h2 class="modal-title">¡Cruza el río! 🌊</h2>
                    <p class="modal-text" id="tut-context">Salta solo en las hojas que tengan esta palabra:</p>
                    <div style="font-size: 3rem; font-weight: 900; color: #F59E0B; letter-spacing: 2px; margin: 15px 0;" id="tut-word">WORD</div>
                    <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px;" id="tut-trans">(Traducción)</p>
                    <button id="btn-start" onclick="startGame()" class="btn-play bg-green-500">▶️ ¡Jugar Ahora!</button>
                </div>
            </div>

            <div class="hud-top"><div class="target-display" id="hud-word">WORD</div></div>
            <div id="rows-container"></div>
            <div class="splash-effect" id="splash"></div>
            <div class="frog-player" id="frog">🐸</div>
        </div>
    </div>
</main>

<script>
    let roundsData = window.dynamicRoundsData || [];
    let currentRoundIndex = 0;
    let roundData = null;
    let gameActive = false;
    let currentStep = 0;
    let isMoving = false; 
    const maxSteps = 3;
    
    const board = document.getElementById('river-board');
    const frog = document.getElementById('frog');
    const rowsContainer = document.getElementById('rows-container');
    const splash = document.getElementById('splash');
    let activeRows = [];
    let targetWord = "";

    document.addEventListener('DOMContentLoaded', () => {
        if(roundsData.length === 0) {
            roundsData = [{
                target_word: 'FROG', translation: 'Rana', emoji: '🐸', distractors: [{word: 'CAT', emoji: '🐱'}, {word: 'DOG', emoji: '🐶'}]
            }];
        }
        loadRound(currentRoundIndex);
    });

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        if (roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;

        frog.classList.remove('jumping');
        // Inicialización en base a % para responsividad perfecta
        frog.style.bottom = '8%';
        frog.style.left = '50%';
        frog.style.opacity = '1';
        
        rowsContainer.innerHTML = '';
        activeRows = [];
        currentStep = 0;
        isMoving = false;

        document.getElementById('tutorial-modal').classList.add('active');
        if (typeof twemoji !== 'undefined') twemoji.parse(document.getElementById('tutorial-modal'), { folder: 'svg', ext: '.svg' });
    }

    function startGame() {
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop'); 
        document.getElementById('tutorial-modal').classList.remove('active');
        for(let i=0; i<maxSteps; i++) createRow(i);
        gameActive = true;
    }

    function createRow(index) {
        // Cálculo porcentual: la primera fila sale en 25%, la segunda en 50%, la tercera en 75%
        let bottomPct = 25 + (index * 25); 
        const isCorrectPos = Math.floor(Math.random() * 3); 
        
        const row = document.createElement('div');
        row.className = 'row-container';
        row.style.bottom = bottomPct + '%';
        row.dataset.index = index;
        
        let html = '';
        for(let i=0; i<3; i++) {
            let isTarget = (i === isCorrectPos);
            let distObj = roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : {word: 'ERR', emoji: '🍂'};
            let wordDisplay = isTarget ? targetWord : (distObj.word || distObj);
            let emojiDisplay = isTarget ? (roundData.emoji || '⭐') : (distObj.emoji || '🍂');
            
            html += `
                <div class="lily-pad" onpointerdown="jumpTo(this, ${isTarget}, ${i}, ${index})">
                    <div class="pad-content">
                        <div class="pad-emoji">${emojiDisplay}</div>
                        <div class="pad-word">${wordDisplay}</div>
                    </div>
                </div>
            `;
        }
        row.innerHTML = html;
        rowsContainer.appendChild(row);
        if (typeof twemoji !== 'undefined') twemoji.parse(row, { folder: 'svg', ext: '.svg' });
        activeRows.push(row);
    }

    function jumpTo(element, isCorrect, xPos, rowIndex) {
        if(!gameActive || rowIndex !== currentStep || isMoving) return;
        isMoving = true; 
        
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop');

        const rectBoard = board.getBoundingClientRect();
        const rectPad = element.getBoundingClientRect();
        
        // Matemáticas Responsivas Puras: Obtenemos el porcentaje exacto de X para la rana
        const targetXPct = ((rectPad.left - rectBoard.left + rectPad.width/2) / rectBoard.width) * 100;
        
        // Y lo sacamos directamente del contenedor para alinear con CSS
        const parentRow = element.closest('.row-container');
        const targetYPct = parseFloat(parentRow.style.bottom);

        frog.classList.add('jumping');
        frog.style.left = targetXPct + '%';
        frog.style.bottom = targetYPct + '%';
        
        setTimeout(() => {
            frog.classList.remove('jumping');
            
            if(isCorrect) {
                if(typeof AudioManager !== 'undefined') AudioManager.playSound('correct');
                element.style.transform = 'rotate(45deg) scale(0.85)'; 
                element.style.background = '#FBBF24'; 
                currentStep++;
                
                if(currentStep < maxSteps) {
                    setTimeout(() => scrollRiverDown(), 200);
                } else {
                    setTimeout(executeWin, 500); 
                }
            } else {
                if(typeof AudioManager !== 'undefined') AudioManager.playSound('wrong');
                element.style.opacity = '0';
                
                splash.style.left = targetXPct + '%';
                splash.style.bottom = targetYPct + '%';
                splash.classList.remove('splash-anim');
                void splash.offsetWidth; 
                splash.classList.add('splash-anim');
                
                frog.style.opacity = '0';
                
                setTimeout(() => {
                    frog.style.bottom = '8%'; // Retorna al inicio flotante
                    frog.style.left = '50%'; 
                    frog.style.opacity = '1';
                    isMoving = false; 
                }, 800);
            }
        }, 400); 
    }

    function scrollRiverDown() {
        activeRows.forEach(row => {
            let currentBottom = parseFloat(row.style.bottom);
            row.style.bottom = (currentBottom - 25) + '%';
        });
        
        let frogBottom = parseFloat(frog.style.bottom);
        frog.style.bottom = (frogBottom - 25) + '%';
        
        setTimeout(() => { isMoving = false; }, 500);
    }

    function executeWin() {
        gameActive = false;
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
        
        frog.classList.add('jumping');
        frog.style.bottom = '90%'; // Salto final a la orilla segura (safe-bank)
        frog.style.left = '50%';
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            if(typeof fireConfetti !== 'undefined') setTimeout(fireConfetti, 400);
            if(typeof unlockNextButton !== 'undefined') {
                setTimeout(() => {
                    unlockNextButton(<?php echo $lesson_id; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
                }, 1000);
            }
        }
    }
</script>