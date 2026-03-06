<style>
    .ghost-board { position: relative; width: 100%; height: 420px; background: #111; border-radius: 20px; overflow: hidden; border: 4px solid var(--primary); margin-bottom: 20px; box-shadow: inset 0 0 50px rgba(0,0,0,0.9); }
    .ghost-board.lights-on { background: #f1c40f !important; border-color: #f39c12; box-shadow: inset 0 0 30px rgba(255,255,255,0.8); }
    .mission-modal { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 15px; transition: opacity 0.5s; padding: 20px; text-align: center; }
    .btn-action { background: var(--success); color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; box-shadow: 0 6px 0 #27ae60; margin-top: 15px; }
    
    .ghost-entity { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: crosshair; user-select: none; z-index: 10; width: 80px; transition: transform 0.2s; }
    .ghost-entity:active { transform: scale(0.9); }
    .ghost-icon { font-size: 50px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5)); animation: float 2s infinite alternate; }
    .ghost-word { background: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 10px; font-weight: bold; font-size: 14px; margin-top: 5px; border: 1px solid rgba(255,255,255,0.3); }
    
    @keyframes float { 0% { transform: translateY(0); } 100% { transform: translateY(-10px); } }
    @keyframes zapFlash { 0% { opacity: 0; } 50% { opacity: 1; background: white; } 100% { opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    <h3 style="margin: 0; margin-bottom: 15px; color: var(--primary);">👻 Caza Fantasmas</h3>

    <div class="ghost-board" id="game-board">
        <div id="flash-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:80; opacity:0;"></div>

        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">🔎 Atrápalo en la oscuridad</h2>
            <p style="color: var(--text-muted); font-size: 18px; margin-bottom: 10px;">Encuentra y atrapa al fantasma que lleva esta palabra:</p>
            <div style="font-size: 35px; font-weight: bold; color: var(--accent); margin: 15px 0; letter-spacing: 2px;" id="tut-word">WORD</div>
            <p style="color: #666; font-size: 20px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary); box-shadow: 0 6px 0 #3b2a9e;" onclick="playSpanglishIntro()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Cazar!</button>
            </div>
        </div>
    </div>
</div>

<script>
    let roundsData = window.dynamicRoundsData;
    let currentRoundIndex = 0;
    let gameActive = false;
    let ghosts = [];
    let animationFrameId;
    
    const board = document.getElementById('game-board');
    
    // Efecto Linterna
    board.addEventListener('mousemove', (e) => {
        if(!gameActive) return;
        const rect = board.getBoundingClientRect();
        board.style.background = `radial-gradient(circle 120px at ${e.clientX - rect.left}px ${e.clientY - rect.top}px, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 50%, #111 100%)`;
    });
    board.addEventListener('touchmove', (e) => {
        if(!gameActive) return; e.preventDefault();
        const rect = board.getBoundingClientRect();
        board.style.background = `radial-gradient(circle 120px at ${e.touches[0].clientX - rect.left}px ${e.touches[0].clientY - rect.top}px, rgba(255,255,255,0.2) 0%, #111 100%)`;
    }, {passive: false});

    loadRound(currentRoundIndex);

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        board.classList.remove('lights-on');
        board.style.background = '#111';

        document.getElementById('tut-word').innerText = round.target_word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;

        // Crear Fantasmas
        document.querySelectorAll('.ghost-entity').forEach(e => e.remove());
        ghosts = [];
        
        let allWords = [round.target_word, ...round.distractors].sort(() => Math.random() - 0.5);
        
        allWords.forEach((word) => {
            let el = document.createElement('div');
            el.className = 'ghost-entity';
            el.innerHTML = `<div class="ghost-icon">👻</div><div class="ghost-word">${word}</div>`;
            el.onclick = () => catchGhost(word, round.target_word);
            
            // Posición inicial y velocidad aleatoria
            let x = Math.random() * (board.offsetWidth - 80);
            let y = Math.random() * (board.offsetHeight - 80);
            let vx = (Math.random() - 0.5) * 4;
            let vy = (Math.random() - 0.5) * 4;
            
            el.style.left = x + 'px';
            el.style.top = y + 'px';
            board.appendChild(el);
            
            ghosts.push({ el, x, y, vx, vy });
        });

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';
    }

    function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        if(typeof playTTS !== 'undefined') playTTS(roundsData[currentRoundIndex].target_word, false);
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 500);
        gameActive = true;
        animateGhosts();
    }

    function animateGhosts() {
        if(!gameActive) return;
        
        ghosts.forEach(g => {
            g.x += g.vx; g.y += g.vy;
            if (g.x <= 0 || g.x >= board.offsetWidth - 80) g.vx *= -1;
            if (g.y <= 0 || g.y >= board.offsetHeight - 80) g.vy *= -1;
            g.el.style.left = g.x + 'px';
            g.el.style.top = g.y + 'px';
        });
        
        animationFrameId = requestAnimationFrame(animateGhosts);
    }

    function catchGhost(clickedWord, targetWord) {
        if (!gameActive) return;
        if(typeof playTTS !== 'undefined') playTTS(clickedWord, false);

        if (clickedWord === targetWord) {
            gameActive = false;
            cancelAnimationFrame(animationFrameId);
            
            board.classList.add('lights-on');
            const flash = document.getElementById('flash-overlay');
            flash.style.animation = 'zapFlash 0.5s';
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();

            setTimeout(() => {
                currentRoundIndex++;
                if (currentRoundIndex < roundsData.length) { loadRound(currentRoundIndex); } 
                else { executeWin(); }
            }, 1500);
        } else {
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
        }
    }

    function executeWin() {
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id; ?>, 10, <?php echo $lesson['module_id']; ?>);
    }
</script>