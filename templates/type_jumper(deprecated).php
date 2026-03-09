<style>
    .river-board { 
        position: relative; 
        width: 100%; 
        min-height: 480px; 
        max-width: 100%; 
        background: linear-gradient(180deg, #38BDF8 0%, #0284C7 100%); 
        border-radius: 24px; 
        overflow: hidden; 
        border: 4px solid var(--brand-blue); 
        margin-bottom: 20px; 
        box-shadow: 0 15px 35px rgba(28, 61, 106, 0.2); 
        touch-action: pan-y; 
        display: flex; 
        flex-direction: column; 
        justify-content: flex-end; 
    }
    
    .water-texture { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 50px 50px, rgba(255,255,255,0.2) 2px, transparent 3px), radial-gradient(circle at 150px 100px, rgba(255,255,255,0.1) 2px, transparent 3px); background-size: 200px 200px; animation: waterFlow 10s linear infinite; pointer-events: none; }
    
    .row-container { position: absolute; width: 100%; height: 100px; display: flex; justify-content: space-around; align-items: center; transition: bottom 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 10; }
    
    .lily-pad { width: 90px; height: 90px; background: #68A93E; border-radius: 50% 50% 50% 10%; transform: rotate(45deg); display: flex; justify-content: center; align-items: center; cursor: pointer; border: 4px solid #4D7C2D; box-shadow: 0 10px 15px rgba(0,0,0,0.3); transition: 0.2s; position: relative; }
    .lily-pad:hover { transform: rotate(45deg) scale(1.05); }
    .lily-pad:active { transform: rotate(45deg) scale(0.95); box-shadow: 0 5px 5px rgba(0,0,0,0.3); }
    
    .pad-content { transform: rotate(-45deg); display: flex; flex-direction: column; align-items: center; pointer-events: none; }
    .pad-emoji { font-size: 30px; line-height: 1; margin-bottom: 2px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    .pad-word { background: var(--white); color: var(--brand-blue); font-size: 13px; font-weight: 800; padding: 2px 8px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

    .frog-player { position: absolute; width: 60px; height: 60px; font-size: 55px; line-height: 1; left: calc(50% - 30px); bottom: 10px; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); z-index: 20; pointer-events: none; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.4)); }
    .frog-player.jumping { transform: scale(1.3) translateY(-20px); filter: drop-shadow(0 20px 15px rgba(0,0,0,0.3)); }
    
    .hud-top { position: absolute; top: 15px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 30; pointer-events: none; }
    .target-display { background: var(--white); border: 4px solid var(--brand-blue); color: var(--brand-blue); padding: 10px 30px; border-radius: 50px; font-weight: 800; font-size: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    
    .mission-modal { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(5px); z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: opacity 0.3s; padding: 20px; text-align: center; overflow-y: auto; }
    .btn-action { background: var(--brand-green); color: white; border: none; padding: 16px 40px; font-size: 18px; font-weight: 700; border-radius: 50px; cursor: pointer; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); margin-top: 20px; transition: 0.2s; }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }

    .splash-effect { position: absolute; width: 100px; height: 100px; background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; z-index: 15; opacity: 0; pointer-events: none; transform: scale(0); }
    .splash-anim { animation: splash 0.5s ease-out forwards; }

    @keyframes waterFlow { from { background-position: 0 0; } to { background-position: 0 200px; } }
    @keyframes splash { 0% { transform: scale(0.5); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--brand-blue); font-size: 1.8rem;">🐸 Salto de Ranita</h3>
        <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
    </div>

    <div class="river-board" id="river-board">
        <div class="water-texture"></div>
        
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--brand-green); margin-top: 0; font-size: 2.2rem;">¡Cruza el río!</h2>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px;" id="tut-context">Salta solo en las hojas que tengan esta palabra:</p>
            <div style="font-size: 45px; font-weight: 800; color: var(--white); letter-spacing: 2px;" id="tut-word">WORD</div>
            <p style="color: #64748B; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button class="btn-action" style="background: var(--brand-blue);" onclick="playIntroAudio()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Saltar!</button>
            </div>
        </div>

        <div class="hud-top">
            <div class="target-display" id="hud-word">WORD</div>
        </div>

        <div id="rows-container"></div>
        
        <div class="splash-effect" id="splash"></div>
        <div class="frog-player" id="frog">🐸</div>
    </div>
</div>

<script>
    let roundsData = window.dynamicRoundsData || [];
    let currentRoundIndex = 0;
    let roundData = null;
    let gameActive = false;
    let currentStep = 0;
    const maxSteps = 3; // Cuantos saltos debe dar para cruzar
    
    const board = document.getElementById('river-board');
    const frog = document.getElementById('frog');
    const rowsContainer = document.getElementById('rows-container');
    const splash = document.getElementById('splash');
    
    let activeRows = [];
    let targetWord = "";

    // Iniciar el juego
    if (roundsData.length > 0) {
        loadRound(currentRoundIndex);
    }

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        
        if (roundData.context_es) {
            document.getElementById('tut-context').innerText = roundData.context_es;
        }

        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;

        // Reiniciar estado visual
        frog.classList.remove('jumping');
        frog.style.bottom = '10px';
        frog.style.left = 'calc(50% - 30px)';
        frog.style.opacity = '1';
        rowsContainer.innerHTML = '';
        activeRows = [];
        currentStep = 0;

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';
    }

    function playIntroAudio() {
        document.getElementById('btn-start').style.display = 'block';
        if(typeof playTTS !== 'undefined') playTTS(roundData.phonetic || targetWord, false);
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => { document.getElementById('tutorial-modal').style.display = 'none'; }, 300);
        
        // Crear las 3 filas de salto
        for(let i=0; i<maxSteps; i++) {
            createRow(i);
        }
        
        gameActive = true;
    }

    function createRow(index) {
        let yPos = 120 + (index * 110); // Distancia vertical entre filas
        const isCorrectPos = Math.floor(Math.random() * 3); // 0, 1 o 2 (Izquierda, Centro, Derecha)
        
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
                <div class="lily-pad" onclick="jumpTo(this, ${isTarget}, ${i}, ${index})">
                    <div class="pad-content">
                        <div class="pad-emoji">${emojiDisplay}</div>
                        <div class="pad-word">${wordDisplay}</div>
                    </div>
                </div>
            `;
        }
        row.innerHTML = html;
        rowsContainer.appendChild(row);
        activeRows.push(row);
    }

    function jumpTo(element, isCorrect, xPos, rowIndex) {
        if(!gameActive || rowIndex !== currentStep) return;
        
        // Mover la rana a las coordenadas visuales de la hoja
        const rectBoard = board.getBoundingClientRect();
        const rectPad = element.getBoundingClientRect();
        
        const targetX = (rectPad.left - rectBoard.left) + (rectPad.width/2) - 30; // 30 es la mitad de la rana
        const targetY = board.offsetHeight - (rectPad.top - rectBoard.top) - (rectPad.height/2) - 30;

        frog.classList.add('jumping');
        frog.style.left = targetX + 'px';
        frog.style.bottom = targetY + 'px';
        
        if(typeof playTTS !== 'undefined') playTTS(roundData.phonetic || targetWord, false);

        setTimeout(() => {
            frog.classList.remove('jumping');
            
            if(isCorrect) {
                if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
                element.style.transform = 'rotate(45deg) scale(0.8)'; // Hundimiento ligero
                element.style.background = '#FBBF24'; // Brilla dorado
                
                currentStep++;
                
                // Mover todas las filas hacia abajo (efecto de avance)
                if(currentStep < maxSteps) {
                    setTimeout(() => scrollRiverDown(), 400);
                } else {
                    setTimeout(executeWin, 500);
                }
            } else {
                // Error - ¡Al agua!
                if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
                element.style.opacity = '0';
                
                // Efecto Splash
                splash.style.left = (targetX - 20) + 'px';
                splash.style.bottom = (targetY - 20) + 'px';
                splash.classList.remove('splash-anim');
                void splash.offsetWidth; // Restart anim
                splash.classList.add('splash-anim');
                
                frog.style.opacity = '0';
                frog.style.bottom = '10px';
                
                setTimeout(() => {
                    frog.style.opacity = '1';
                    frog.style.left = 'calc(50% - 30px)'; // Volver al inicio o paso anterior
                }, 800);
            }
        }, 400); // Tiempo que dura el salto
    }

    function scrollRiverDown() {
        // Desplaza el contenedor y ajusta la rana
        activeRows.forEach(row => {
            let currentBottom = parseInt(row.style.bottom);
            row.style.bottom = (currentBottom - 110) + 'px';
        });
        
        let frogBottom = parseInt(frog.style.bottom);
        frog.style.bottom = (frogBottom - 110) + 'px';
    }

    function executeWin() {
        gameActive = false;
        
        // La rana salta fuera de la pantalla (victoria)
        frog.classList.add('jumping');
        frog.style.bottom = '120%';
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        
        // Comprobar si hay más rondas
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            setTimeout(() => {
                if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id ?? 0; ?>, 10, <?php echo $lesson['module_id'] ?? 0; ?>);
            }, 1000);
        }
    }
</script>