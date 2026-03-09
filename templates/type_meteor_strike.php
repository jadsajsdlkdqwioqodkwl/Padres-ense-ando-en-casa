<style>
    .ninja-board { position: relative; width: 100%; min-height: 500px; max-height: 80vh; max-width: 100%; background: radial-gradient(circle at center, #1E293B 0%, #0F172A 100%); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15); cursor: crosshair; touch-action: none; }    
    .target-hud { position: absolute; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 10px 40px; border-radius: 50px; text-align: center; z-index: 10; }
    
    .ninja-item { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; user-select: none; z-index: 5; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
    .ninja-emoji { font-size: 60px; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.3)); pointer-events: none; }
    .ninja-word { background: var(--white); color: var(--brand-blue); font-weight: 800; padding: 4px 12px; border-radius: 50px; font-size: 16px; margin-top: -10px; border: 2px solid var(--brand-blue); pointer-events: none; }
    
    .slash-effect { position: absolute; background: white; height: 6px; border-radius: 3px; box-shadow: 0 0 15px #38BDF8, 0 0 30px #38BDF8; pointer-events: none; transform-origin: left center; z-index: 100; opacity: 0; transition: opacity 0.3s; }
    
    .sliced-left { animation: sliceLeft 0.5s forwards; }
    .sliced-right { animation: sliceRight 0.5s forwards; }
    
    .mission-modal { overflow-y: auto; max-height: 100%; } /* Evita que el modal se corte en móvil */    .btn-action { background: var(--brand-orange); color: white; border: none; padding: 16px 35px; font-size: 18px; font-weight: 700; border-radius: 50px; cursor: pointer; box-shadow: 0 4px 14px rgba(242, 156, 56, 0.3); margin-top: 20px; transition: 0.2s; }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(242, 156, 56, 0.4); }
    
    @keyframes sliceLeft { to { transform: translate(-50px, 50px) rotate(-20deg); opacity: 0; } }
    @keyframes sliceRight { to { transform: translate(50px, 50px) rotate(20deg); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <h3 style="margin: 0; margin-bottom: 20px; color: var(--brand-blue); font-size: 1.8rem;">⚔️ Word Ninja</h3>

    <div class="ninja-board" id="ninja-board">
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--white); margin-top: 0; font-size: 2.2rem;">¡Corta las palabras!</h2>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px;">Corta por la mitad a la figura correcta 3 veces:</p>
            <div style="font-size: 45px; font-weight: 800; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.5);" id="tut-word">WORD</div>
            <p style="color: #64748B; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button class="btn-action" style="background: var(--brand-blue); box-shadow: 0 4px 14px rgba(28, 61, 106, 0.3);" onclick="playIntroAudio()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Cortar!</button>
            </div>
        </div>

        <div class="target-hud">
            <div style="font-size: 24px; font-weight: 800; color: var(--white);" id="hud-word">WORD</div>
            <div style="color: #FBBF24; font-size: 20px; font-weight: 800; letter-spacing: 5px;" id="score-display">0/3</div>
        </div>
        
        <div class="slash-effect" id="slash-fx"></div>
    </div>
</div>

<script>
    let roundData = window.dynamicRoundsData[0];
    let gameActive = false;
    let score = 0;
    const maxScore = 3;
    
    const board = document.getElementById('ninja-board');
    const slashFx = document.getElementById('slash-fx');
    
    let activeItems = [];
    let lastTime = 0;
    let spawnTimer = 0;
    
    // Configuración Inicial
    const targetWord = roundData.target_word || roundData.word;
    const itemsArray = roundData.items || [{content: '🍎', is_correct: true}, {content: '⭐', is_correct: false}]; // Fallback
    
    document.getElementById('tut-word').innerText = targetWord;
    document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
    document.getElementById('hud-word').innerText = targetWord;

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

    // FÍSICAS Y SPAWNER
    function gameLoop(timestamp) {
        if(!gameActive) return;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        
        spawnTimer += dt;
        if(spawnTimer > 1500) { // Spawnea cada 1.5s
            spawnItem();
            spawnTimer = 0;
        }
        
        // Actualizar posiciones (Gravedad)
        for(let i = activeItems.length - 1; i >= 0; i--) {
            let item = activeItems[i];
            item.vy += 0.002 * dt; // ANTES: 0.003. AHORA caen un poco más lento (gravedad lunar)
            item.x += item.vx * dt;
            item.y += item.vy * dt;
            item.rotation += item.vRot * dt;
            
            item.el.style.transform = `translate(${item.x}px, ${item.y}px) rotate(${item.rotation}deg)`;
            
            // Si cae debajo del tablero, remover
            if(item.y > board.offsetHeight + 100) {
                item.el.remove();
                activeItems.splice(i, 1);
            }
        }
        
        requestAnimationFrame(gameLoop);
    }

    function spawnItem() {
        // Elegir si es correcto o distractor
        let isCorrect = Math.random() > 0.4;
        let pool = itemsArray.filter(i => i.is_correct === isCorrect);
        if(pool.length === 0) pool = itemsArray; // Safety
        
        let itemData = pool[Math.floor(Math.random() * pool.length)];
        let wordDisplay = isCorrect ? targetWord : (roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : 'ERR');
        
        let el = document.createElement('div');
        el.className = 'ninja-item';
        // Envolvemos el contenido en dos mitades para el efecto de corte
        el.innerHTML = `
            <div class="ninja-emoji">${itemData.content}</div>
            <div class="ninja-word">${wordDisplay}</div>
        `;
        
        // FÍSICAS MEJORADAS: Saltan más alto y más tiempo en pantalla
        let startX = Math.random() * (board.offsetWidth - 100) + 50;
        let startY = board.offsetHeight;
        let velocityY = -(Math.random() * 0.5 + 1.2); // ANTES: 0.4 + 0.8. AHORA saltan más alto.
        let velocityX = (board.offsetWidth / 2 - startX) * 0.001;

        el.style.transform = `translate(${startX}px, ${startY}px)`;
        board.appendChild(el);
        
        // Eventos de corte (Mouse y Touch)
        el.addEventListener('pointerdown', (e) => sliceItem(e, el, isCorrect));
        el.addEventListener('pointerenter', (e) => {
            if(e.buttons > 0) sliceItem(e, el, isCorrect); // Soporta deslizar el dedo/mouse presionado
        });
        
        activeItems.push({ el: el, x: startX, y: startY, vx: velocityX, vy: velocityY, rotation: 0, vRot: (Math.random() - 0.5) * 0.5 });
    }

    function sliceItem(e, el, isCorrect) {
        if(!gameActive || el.classList.contains('sliced')) return;
        el.classList.add('sliced'); // Evitar doble corte
        
        // Dibujar efecto de corte
        let rect = board.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;
        slashFx.style.left = (x - 50) + 'px';
        slashFx.style.top = y + 'px';
        slashFx.style.width = '100px';
        slashFx.style.transform = `rotate(${(Math.random() - 0.5) * 90}deg)`;
        slashFx.style.opacity = '1';
        setTimeout(() => slashFx.style.opacity = '0', 200);

        // Remover de la física
        let index = activeItems.findIndex(i => i.el === el);
        if(index > -1) activeItems.splice(index, 1);
        
        // Efecto visual de partición
        el.innerHTML = `<div class="ninja-emoji sliced-left" style="position:absolute;">${el.innerText.split('\n')[0]}</div>
                        <div class="ninja-emoji sliced-right" style="position:absolute; clip-path: inset(50% 0 0 0); margin-top:-60px;">${el.innerText.split('\n')[0]}</div>`;

        if(isCorrect) {
            score++;
            document.getElementById('score-display').innerText = `${score}/3`;
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            if(typeof playTTS !== 'undefined') playTTS(roundData.phonetic || targetWord, false);
            
            if(score >= maxScore) {
                setTimeout(executeWin, 800);
            }
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            board.style.boxShadow = "inset 0 0 50px rgba(239, 68, 68, 0.8)";
            setTimeout(() => board.style.boxShadow = "0 15px 35px rgba(28, 61, 106, 0.15)", 300);
            score = Math.max(0, score - 1); // Penalización suave
            document.getElementById('score-display').innerText = `${score}/3`;
        }
        
        setTimeout(() => el.remove(), 500);
    }

    function executeWin() {
        gameActive = false;
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id; ?>, 10, <?php echo $lesson['module_id']; ?>);
    }
</script>