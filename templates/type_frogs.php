<?php
// templates/type_frogs.php
// (Sin cabeceras repetidas. Este código es inyectado directamente en lesson.php)

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    /* Seguro de Pantalla Horizontal (Landscape Warning) */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: #1E293B; z-index: 10000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    .river-board { position: relative; width: 100%; min-height: 550px; max-width: 100%; background: linear-gradient(180deg, #38BDF8 0%, #0284C7 100%); border-radius: 24px; overflow: hidden; border: 4px solid #1E3A8A; margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); touch-action: pan-y; display: flex; flex-direction: column; justify-content: flex-end; }
    .water-texture { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 50px 50px, rgba(255,255,255,0.2) 2px, transparent 3px), radial-gradient(circle at 150px 100px, rgba(255,255,255,0.1) 2px, transparent 3px); background-size: 200px 200px; animation: waterFlow 10s linear infinite; pointer-events: none; }
    .row-container { position: absolute; width: 100%; height: 100px; display: flex; justify-content: space-around; align-items: center; transition: bottom 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; }
    
    .lily-pad { width: clamp(80px, 20vw, 100px); height: clamp(80px, 20vw, 100px); background: #68A93E; border-radius: 50% 50% 50% 10%; transform: rotate(45deg); display: flex; justify-content: center; align-items: center; cursor: pointer; border: 4px solid #4D7C2D; box-shadow: 0 10px 15px rgba(0,0,0,0.3); transition: 0.2s; position: relative; touch-action: manipulation; }
    .lily-pad:active { transform: rotate(45deg) scale(0.95); box-shadow: 0 5px 5px rgba(0,0,0,0.3); }
    
    .pad-content { transform: rotate(-45deg); display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .pad-emoji { font-size: clamp(30px, 6vw, 40px); line-height: 1; margin-bottom: 2px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    .pad-word { background: #ffffff; color: #1E3A8A; font-size: clamp(11px, 3vw, 14px); font-weight: 800; padding: 2px 10px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

    .frog-player { position: absolute; width: 60px; height: 60px; font-size: 60px; line-height: 1; left: calc(50% - 30px); bottom: 10px; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 20; pointer-events: none; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.4)); }
    .frog-player.jumping { transform: scale(1.3) translateY(-20px); filter: drop-shadow(0 20px 15px rgba(0,0,0,0.3)); }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 30; pointer-events: none; }
    .target-display { background: #ffffff; border: 4px solid #1E3A8A; color: #1E3A8A; padding: 10px 30px; border-radius: 50px; font-weight: 900; font-size: 26px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    
    .splash-effect { position: absolute; width: 100px; height: 100px; background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; z-index: 15; opacity: 0; pointer-events: none; transform: scale(0); }
    .splash-anim { animation: splash 0.5s ease-out forwards; }

    @keyframes waterFlow { from { background-position: 0 0; } to { background-position: 0 200px; } }
    @keyframes splash { 0% { transform: scale(0.5); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego espacial necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh;">
    <div class="game-area text-center mx-auto" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="text-2xl font-black text-gray-800">🐸 Salto de Ranita</h3>
            <div id="round-counter" style="background: #1E3A8A; color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
        </div>

        <div class="river-board" id="river-board">
            <div class="water-texture"></div>
            
            <div id="tutorial-modal" class="modal-overlay active">
                <div class="modal-content">
                    <h2 class="modal-title">¡Cruza el río! 🌊</h2>
                    <p class="modal-text" id="tut-context">Salta solo en las hojas que tengan esta palabra:</p>
                    <div style="font-size: 3rem; font-weight: 900; color: #F59E0B; letter-spacing: 2px; margin: 15px 0;" id="tut-word">WORD</div>
                    <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px;" id="tut-trans">(Traducción)</p>
                    <button id="btn-start" onclick="startGame()" class="btn-play w-full bg-green-500">▶️ ¡Jugar Ahora!</button>
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
                target_word: 'FROG', translation: 'Rana', distractors: ['CAT', 'DOG', 'BIRD']
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
        frog.style.bottom = '10px';
        frog.style.left = 'calc(50% - 30px)';
        frog.style.opacity = '1';
        rowsContainer.innerHTML = '';
        activeRows = [];
        currentStep = 0;

        document.getElementById('tutorial-modal').classList.add('active');
        twemoji.parse(document.getElementById('tutorial-modal'), { folder: 'svg', ext: '.svg' });
    }

    function startGame() {
        document.getElementById('tutorial-modal').classList.remove('active');
        for(let i=0; i<maxSteps; i++) createRow(i);
        gameActive = true;
    }

    function createRow(index) {
        let yPos = 120 + (index * 120); 
        const isCorrectPos = Math.floor(Math.random() * 3); 
        
        const row = document.createElement('div');
        row.className = 'row-container';
        row.style.bottom = yPos + 'px';
        row.dataset.index = index;
        
        let html = '';
        for(let i=0; i<3; i++) {
            let isTarget = (i === isCorrectPos);
            let wordDisplay = isTarget ? targetWord : (roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : 'ERR');
            let emojiDisplay = isTarget ? '⭐' : '🍂';
            
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
        twemoji.parse(row, { folder: 'svg', ext: '.svg' });
        activeRows.push(row);
    }

    function jumpTo(element, isCorrect, xPos, rowIndex) {
        if(!gameActive || rowIndex !== currentStep) return;
        
        const rectBoard = board.getBoundingClientRect();
        const rectPad = element.getBoundingClientRect();
        const targetX = (rectPad.left - rectBoard.left) + (rectPad.width/2) - 30; 
        const targetY = board.offsetHeight - (rectPad.top - rectBoard.top) - (rectPad.height/2) - 30;

        frog.classList.add('jumping');
        frog.style.left = targetX + 'px';
        frog.style.bottom = targetY + 'px';
        
        setTimeout(() => {
            frog.classList.remove('jumping');
            
            if(isCorrect) {
                element.style.transform = 'rotate(45deg) scale(0.8)'; 
                element.style.background = '#FBBF24'; 
                currentStep++;
                
                if(currentStep < maxSteps) {
                    setTimeout(() => scrollRiverDown(), 400);
                } else {
                    setTimeout(executeWin, 500);
                }
            } else {
                element.style.opacity = '0';
                splash.style.left = (targetX - 20) + 'px';
                splash.style.bottom = (targetY - 20) + 'px';
                splash.classList.remove('splash-anim');
                void splash.offsetWidth; 
                splash.classList.add('splash-anim');
                
                frog.style.opacity = '0';
                frog.style.bottom = '10px';
                
                setTimeout(() => {
                    frog.style.opacity = '1';
                    frog.style.left = 'calc(50% - 30px)'; 
                }, 800);
            }
        }, 400); 
    }

    function scrollRiverDown() {
        activeRows.forEach(row => {
            let currentBottom = parseInt(row.style.bottom);
            row.style.bottom = (currentBottom - 120) + 'px';
        });
        let frogBottom = parseInt(frog.style.bottom);
        frog.style.bottom = (frogBottom - 120) + 'px';
    }

    function executeWin() {
        gameActive = false;
        frog.classList.add('jumping');
        frog.style.bottom = '120%';
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            // Se invoca la función global de lesson.php
            if(typeof unlockNextButton !== 'undefined') {
                unlockNextButton(<?php echo $lesson_id; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
            }
        }
    }
</script>