<style>
    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
    
    .ninja-board { position: relative; width: 100%; min-height: 450px; height: 60vh; max-height: 600px; background: radial-gradient(circle at center, #1E293B 0%, #0F172A 100%); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15); cursor: crosshair; touch-action: none; }
    .target-hud { position: absolute; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 10px 40px; border-radius: 50px; text-align: center; z-index: 10; width: max-content; }
    
    .ninja-item { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; user-select: none; z-index: 5; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
    .ninja-emoji { font-size: clamp(40px, 10vw, 60px); filter: drop-shadow(0 10px 10px rgba(0,0,0,0.3)); pointer-events: none; }
    .ninja-word { background: var(--white); color: var(--brand-blue); font-weight: 800; padding: 4px 12px; border-radius: 50px; font-size: 16px; margin-top: -10px; border: 2px solid var(--brand-blue); pointer-events: none; }
    
    .slash-effect { position: absolute; background: white; height: 6px; border-radius: 3px; box-shadow: 0 0 15px #38BDF8, 0 0 30px #38BDF8; pointer-events: none; transform-origin: left center; z-index: 100; opacity: 0; transition: opacity 0.3s; }
    .sliced-left { animation: sliceLeft 0.5s forwards; }
    .sliced-right { animation: sliceRight 0.5s forwards; }
    .mission-modal { overflow-y: auto; }
    
    @keyframes sliceLeft { to { transform: translate(-50px, 50px) rotate(-20deg); opacity: 0; } }
    @keyframes sliceRight { to { transform: translate(50px, 50px) rotate(20deg); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--brand-blue); font-size: 1.8rem;">⚔️ Word Ninja</h3>
        <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
    </div>

    <div class="ninja-board" id="ninja-board">
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--white); margin-top: 0; font-size: 2.2rem;">¡Conviértete en Ninja!</h2>
            <p style="color: #94A3B8; font-size: 18px; margin-bottom: 15px;" id="tut-context">Corta la figura correcta 3 veces:</p>
            <div style="font-size: 45px; font-weight: 800; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.5);" id="tut-word">WORD</div>
            <p style="color: #64748B; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div class="modal-actions">
                <button class="btn btn-action" id="btn-start" onclick="startGame()" style="background: var(--brand-orange); color: white;">▶️ ¡Jugar Ahora!</button>
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
    function applyTwemoji(node) {
        if (typeof twemoji !== 'undefined') { twemoji.parse(node, { folder: 'svg', ext: '.svg' }); } 
        else {
            const script = document.createElement('script');
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/twemoji.min.js";
            script.onload = () => twemoji.parse(node, { folder: 'svg', ext: '.svg' });
            document.head.appendChild(script);
        }
    }

    let roundsData = window.dynamicRoundsData || [];
    let currentRoundIndex = 0;
    let roundData = null;
    let gameActive = false;
    let score = 0;
    const maxScore = 3;
    
    const board = document.getElementById('ninja-board');
    const slashFx = document.getElementById('slash-fx');
    let activeItems = [];
    let lastTime = 0;
    let spawnTimer = 0;
    let targetWord = "";
    let itemsArray = [];

    if (roundsData.length > 0) loadRound(currentRoundIndex);

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        itemsArray = roundData.items || [{content: '🍎', is_correct: true}, {content: '⭐', is_correct: false}];
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        if(roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;
        
        score = 0;
        document.getElementById('score-display').innerText = `0/3`;
        activeItems.forEach(i => i.el.remove());
        activeItems = [];

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
        applyTwemoji(document.body);
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 300);
        gameActive = true;
        lastTime = performance.now();
        requestAnimationFrame(gameLoop);
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
        
        for(let i = activeItems.length - 1; i >= 0; i--) {
            let item = activeItems[i];
            item.vy += 0.002 * dt; 
            item.x += item.vx * dt;
            item.y += item.vy * dt;
            item.rotation += item.vRot * dt;
            
            item.el.style.transform = `translate(${item.x}px, ${item.y}px) rotate(${item.rotation}deg)`;
            
            if(item.y > board.offsetHeight + 100) {
                item.el.remove();
                activeItems.splice(i, 1);
            }
        }
        requestAnimationFrame(gameLoop);
    }

    function spawnItem() {
        let isCorrect = Math.random() > 0.4;
        let pool = itemsArray.filter(i => i.is_correct === isCorrect);
        if(pool.length === 0) pool = itemsArray;
        
        let itemData = pool[Math.floor(Math.random() * pool.length)];
        let wordDisplay = isCorrect ? targetWord : (roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : 'ERR');
        
        let el = document.createElement('div');
        el.className = 'ninja-item';
        el.innerHTML = `
            <div class="ninja-emoji">${itemData.content}</div>
            <div class="ninja-word">${wordDisplay}</div>
        `;
        
        let startX = Math.random() * (board.offsetWidth - 100) + 50;
        let startY = board.offsetHeight;
        let velocityY = -(Math.random() * 0.5 + 1.2); 
        let velocityX = (board.offsetWidth / 2 - startX) * 0.0015;
        
        el.style.transform = `translate(${startX}px, ${startY}px)`;
        board.appendChild(el);
        applyTwemoji(el);
        
        el.addEventListener('pointerdown', (e) => sliceItem(e, el, isCorrect));
        el.addEventListener('pointerenter', (e) => { if(e.buttons > 0) sliceItem(e, el, isCorrect); });
        
        activeItems.push({ el: el, x: startX, y: startY, vx: velocityX, vy: velocityY, rotation: 0, vRot: (Math.random() - 0.5) * 0.5 });
    }

    function sliceItem(e, el, isCorrect) {
        if(!gameActive || el.classList.contains('sliced')) return;
        el.classList.add('sliced'); 
        
        let rect = board.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;
        slashFx.style.left = (x - 50) + 'px';
        slashFx.style.top = y + 'px';
        slashFx.style.width = '100px';
        slashFx.style.transform = `rotate(${(Math.random() - 0.5) * 90}deg)`;
        slashFx.style.opacity = '1';
        setTimeout(() => slashFx.style.opacity = '0', 200);

        let index = activeItems.findIndex(i => i.el === el);
        if(index > -1) activeItems.splice(index, 1);
        
        el.innerHTML = `<div class="ninja-emoji sliced-left" style="position:absolute;">${el.innerText.split('\n')[0]}</div>
                        <div class="ninja-emoji sliced-right" style="position:absolute; clip-path: inset(50% 0 0 0); margin-top:-60px;">${el.innerText.split('\n')[0]}</div>`;
        applyTwemoji(el);

        if(isCorrect) {
            score++;
            document.getElementById('score-display').innerText = `${score}/3`;
            if(score >= maxScore) setTimeout(checkNextRound, 800);
        } else {
            board.style.boxShadow = "inset 0 0 50px rgba(239, 68, 68, 0.8)";
            setTimeout(() => board.style.boxShadow = "0 15px 35px rgba(28, 61, 106, 0.15)", 300);
            score = Math.max(0, score - 1); 
            document.getElementById('score-display').innerText = `${score}/3`;
        }
        setTimeout(() => el.remove(), 500);
    }

    function checkNextRound() {
        gameActive = false;
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) loadRound(currentRoundIndex);
        else executeWin();
    }

    function executeWin() {
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id ?? 0; ?>, 10, <?php echo $lesson['module_id'] ?? 0; ?>);
    }
</script>