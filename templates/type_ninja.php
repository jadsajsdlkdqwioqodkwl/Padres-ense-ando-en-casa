<?php
// templates/type_ninja.php
// IMPORTANTE: Ya no se llama a session_start() ni headers. Este archivo es inyectado en lesson.php

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: #1E293B; z-index: 100000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center; box-sizing: border-box; padding: 20px;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    /* FIX CRÍTICO: Modales seguros anclados de forma absoluta dentro de su contenedor, sin !important que bloqueen la pantalla */
    #tutorial-modal.modal-overlay {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%;
        box-sizing: border-box; padding: 20px;
        display: none; justify-content: center; align-items: center;
        background: rgba(15, 23, 42, 0.85); z-index: 999999;
    }
    #tutorial-modal.modal-overlay.active { display: flex; }
    
    #tutorial-modal .modal-content {
        width: 100% !important; max-width: 480px !important;
        box-sizing: border-box !important; margin: 0 auto !important;
        background: white; padding: clamp(20px, 5vw, 40px); border-radius: 24px;
        text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        border: 4px solid var(--brand-blue, #1E3A8A);
    }

    /* Encabezado del juego estandarizado (Layout Fase 2) */
    .game-header-bar { display: flex; justify-content: space-between; align-items: center; margin: 0 auto 20px auto; width: 100%; max-width: 800px; background: #ffffff; border: 2px solid #E2E8F0; border-radius: 16px; padding: 15px 25px; box-sizing: border-box; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05); }

    /* FIX: Tablero de ninja 100% responsivo y sin sobrepasar márgenes */
    .ninja-board { 
        position: relative; width: 100%; max-width: 800px; min-height: 450px; height: 60vh; max-height: 600px; 
        background: radial-gradient(circle at center, #1E293B 0%, #0F172A 100%); 
        border-radius: 24px; overflow: hidden; border: 4px solid #1E3A8A; 
        margin: 0 auto 20px auto; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15); 
        cursor: crosshair; touch-action: none; box-sizing: border-box;
    }
    .target-hud { position: absolute; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,255,255,0.1); border: 2px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 10px 40px; border-radius: 50px; text-align: center; z-index: 10; width: max-content; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    
    .ninja-item { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; user-select: none; z-index: 5; text-shadow: 0 5px 15px rgba(0,0,0,0.5); touch-action: none; }
    .ninja-emoji { font-size: clamp(50px, 12vw, 80px); filter: drop-shadow(0 10px 10px rgba(0,0,0,0.4)); pointer-events: none; }
    .ninja-word { background: #ffffff; color: #1E3A8A; font-weight: 900; padding: 4px 16px; border-radius: 50px; font-size: clamp(12px, 3vw, 18px); margin-top: -5px; border: 3px solid #1E3A8A; pointer-events: none; }
    
    .slash-effect { position: absolute; background: white; height: 6px; border-radius: 3px; box-shadow: 0 0 15px #38BDF8, 0 0 30px #38BDF8; pointer-events: none; transform-origin: left center; z-index: 100; opacity: 0; transition: opacity 0.3s; }
    .sliced-left { animation: sliceLeft 0.5s forwards; }
    .sliced-right { animation: sliceRight 0.5s forwards; }
    
    @keyframes sliceLeft { to { transform: translate(-60px, 60px) rotate(-25deg); opacity: 0; } }
    @keyframes sliceRight { to { transform: translate(60px, 60px) rotate(25deg); opacity: 0; } }

    /* Ajuste Extra Móvil para el HUD */
    @media (max-width: 480px) {
        .target-hud { padding: 8px 25px; top: 10px; }
        .target-hud > div:first-child { font-size: 20px !important; }
        .target-hud > div:last-child { font-size: 18px !important; }
    }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Conviértete en Ninja jugando en formato vertical.</p>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh; padding: 10px; box-sizing: border-box; width: 100%; max-width: 1000px; margin: 0 auto;">
    <div class="game-area text-center mx-auto" style="width: 100%; box-sizing: border-box; display: flex; flex-direction: column; align-items: center;">
        
        <div class="game-header-bar">
            <h3 style="margin: 0; color: var(--brand-blue); font-size: clamp(20px, 5vw, 26px); font-weight: 900;">⚔️ Word Ninja</h3>
            <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
        </div>

        <div class="ninja-board" id="ninja-board">
            
            <div id="tutorial-modal" class="modal-overlay active">
                <div class="modal-content">
                    <h2 class="modal-title" style="margin-bottom: 10px;">¡Conviértete en Ninja! 🥷</h2>
                    <p class="modal-text" id="tut-context" style="margin-bottom: 10px;">Corta la figura correcta 3 veces:</p>
                    <div style="font-size: 3rem; margin: 10px 0;" id="tut-emoji"></div>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin: 15px 0; flex-wrap: wrap;">
                        <div style="font-size: clamp(2.5rem, 8vw, 3.5rem); font-weight: 900; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3);" id="tut-word">WORD</div>
                        <button class="btn-audio-huge" id="btn-tut-audio" title="Escuchar pronunciación" style="width: clamp(50px, 10vw, 65px); height: clamp(50px, 10vw, 65px); font-size: clamp(20px, 5vw, 26px); margin: 0;">🔊</button>
                    </div>

                    <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="tut-trans">(Traducción)</p>
                    <p style="font-size: 14px; color: #475569; background: #F8FAFC; padding: 15px; border-radius: 12px; font-style: italic; margin-bottom: 25px; border: 1px solid #E2E8F0; width: 100%; box-sizing: border-box;" id="tut-mnemonic">💡 Cargando consejo...</p>

                    <button id="btn-start" onclick="startGame()" class="btn-play bg-orange-500" style="margin-top: 0;">▶️ ¡Jugar Ahora!</button>
                </div>
            </div>

            <div class="target-hud">
                <div style="font-size: 24px; font-weight: 800; color: #ffffff;" id="hud-word">WORD</div>
                <div style="color: #FBBF24; font-size: 22px; font-weight: 900; letter-spacing: 5px;" id="score-display">0/3</div>
            </div>
            
            <div class="slash-effect" id="slash-fx"></div>
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
    
    const board = document.getElementById('ninja-board');
    const slashFx = document.getElementById('slash-fx');
    let activeItems = [];
    let lastTime = 0;
    let spawnTimer = 0;
    let targetWord = "";
    let targetEmoji = "";
    let itemsArray = []; 

    document.addEventListener('DOMContentLoaded', () => {
        if(roundsData.length === 0) {
            roundsData = [{
                target_word: 'APPLE', translation: 'Manzana', emoji: '🍎', mnemonic: 'Imagina a un ninja cortando una manzana.',
                items: [
                    {word: 'APPLE', content: '🍎', is_correct: true}, 
                    {word: 'BANANA', content: '🍌', is_correct: false}, 
                    {word: 'CAR', content: '🚗', is_correct: false}
                ]
            }];
        }
        loadRound(currentRoundIndex);
    });

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        targetEmoji = roundData.emoji || '📦';
        itemsArray = roundData.items || [];
        
        document.getElementById('tut-emoji').innerText = targetEmoji;
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        
        if(roundData.mnemonic) {
            document.getElementById('tut-mnemonic').innerText = "💡 " + roundData.mnemonic;
        } else {
            document.getElementById('tut-mnemonic').style.display = 'none';
        }
        document.getElementById('btn-tut-audio').onclick = () => { if(typeof playPronunciation === 'function') playPronunciation(targetWord); };

        if(roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;
        
        score = 0;
        document.getElementById('score-display').innerText = `0/3`;
        activeItems.forEach(i => i.el.remove());
        activeItems = [];

        // Restaurar el modal de forma segura (sin bloquear clics fuera)
        const modal = document.getElementById('tutorial-modal');
        modal.classList.add('active');
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.pointerEvents = 'auto';

        if (typeof twemoji !== 'undefined') twemoji.parse(document.getElementById('tutorial-modal'), { folder: 'svg', ext: '.svg' });
    }

    function startGame() {
        const modal = document.getElementById('tutorial-modal');
        modal.classList.remove('active');
        modal.style.opacity = '0';
        
        // Destruir el escudo invisible para dejar pasar los clics
        setTimeout(() => { 
            modal.style.display = 'none'; 
            modal.style.pointerEvents = 'none'; 
        }, 300);

        gameActive = true;
        lastTime = performance.now();
        requestAnimationFrame(gameLoop);
    }

    function gameLoop(timestamp) {
        if(!gameActive) return;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        
        spawnTimer += dt;
        if(spawnTimer > 1200) { 
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
        
        if(pool.length === 0) {
            if (isCorrect) pool = [{word: targetWord, content: targetEmoji, is_correct: true}];
            else pool = [{word: 'ERR', content: '❌', is_correct: false}];
        }
        
        let itemData = pool[Math.floor(Math.random() * pool.length)];
        
        let el = document.createElement('div');
        el.className = 'ninja-item';
        el.innerHTML = `
            <div class="ninja-emoji">${itemData.content}</div>
            <div class="ninja-word">${itemData.word || itemData.content}</div>
        `;
        
        let currentBoardWidth = board.offsetWidth;
        let startX = Math.random() * (currentBoardWidth - 100) + 50;
        let startY = board.offsetHeight;
        let velocityY = -(Math.random() * 0.4 + 1.1); 
        let velocityX = (currentBoardWidth / 2 - startX) * 0.0015; 
        
        el.style.transform = `translate(${startX}px, ${startY}px)`;
        board.appendChild(el);
        if (typeof twemoji !== 'undefined') twemoji.parse(el, { folder: 'svg', ext: '.svg' }); 
        
        // El evento de "corte" del ninja usa pointerdown y pointerenter para simular el deslizamiento
        el.addEventListener('pointerdown', (e) => sliceItem(e, el, isCorrect, itemData.content));
        el.addEventListener('pointerenter', (e) => { if(e.buttons > 0) sliceItem(e, el, isCorrect, itemData.content); });
        
        activeItems.push({ el: el, x: startX, y: startY, vx: velocityX, vy: velocityY, rotation: 0, vRot: (Math.random() - 0.5) * 0.3 });
    }

    function sliceItem(e, el, isCorrect, originalContent) {
        if(!gameActive || el.classList.contains('sliced')) return;
        el.classList.add('sliced'); 
        
        // Sonido base al deslizar/cortar
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('pop');
        
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
        
        el.innerHTML = `
            <div class="ninja-emoji sliced-left" style="position:absolute;">${originalContent}</div>
            <div class="ninja-emoji sliced-right" style="position:absolute; clip-path: inset(50% 0 0 0); margin-top:-60px;">${originalContent}</div>
        `;
        if (typeof twemoji !== 'undefined') twemoji.parse(el, { folder: 'svg', ext: '.svg' });

        if(isCorrect) {
            // Acierto!
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('correct');
            score++;
            document.getElementById('score-display').innerText = `${score}/3`;
            if(score >= maxScore) setTimeout(checkNextRound, 800);
        } else {
            // Fallo!
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('wrong');
            board.style.boxShadow = "inset 0 0 50px rgba(239, 68, 68, 0.8)";
            setTimeout(() => board.style.boxShadow = "0 15px 35px rgba(28, 61, 106, 0.15)", 300);
            score = Math.max(0, score - 1); 
            document.getElementById('score-display').innerText = `${score}/3`;
        }
        setTimeout(() => el.remove(), 600);
    }

    function checkNextRound() {
        gameActive = false;
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            loadRound(currentRoundIndex);
        } else {
            executeWin();
        }
    }

    function executeWin() {
        // Victoria!
        if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') {
            unlockNextButton(<?php echo $lesson_id; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
        }
    }
</script>