<?php
// templates/type_moles.php
$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    /* Seguro de Pantalla Horizontal */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: #1E293B; z-index: 100000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center; box-sizing: border-box; padding: 20px;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    /* MODALES GLOBALES FUERA DEL JUEGO */
    .modal-overlay {
        position: fixed !important; top: 0 !important; left: 0 !important;
        width: 100vw !important; height: 100vh !important;
        box-sizing: border-box !important; padding: 20px !important;
        display: none; justify-content: center; align-items: center;
        background: rgba(15, 23, 42, 0.85) !important; z-index: 999999 !important;
    }
    .modal-overlay.active { display: flex !important; }
    
    .modal-overlay .modal-content {
        width: 100% !important; max-width: 480px !important;
        box-sizing: border-box !important; margin: 0 auto !important;
        background: white; padding: clamp(20px, 5vw, 40px); border-radius: 24px;
        text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        border: 4px solid var(--brand-blue, #1E3A8A);
        pointer-events: auto;
    }

    /* FIX: Moles Game Container 100% Responsivo */
    .moles-game-container { 
        width: 100%; max-width: 700px; margin: 0 auto; padding: 2rem; 
        background: #8BC34A; border-radius: 24px; 
        box-shadow: inset 0 0 30px rgba(0,0,0,0.15), 0 10px 20px rgba(0,0,0,0.1); 
        border: 4px solid #558B2F; box-sizing: border-box; 
    }

    .moles-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; justify-items: center; align-items: center; width: 100%; box-sizing: border-box;}

    /* FIX CRÍTICO: El agujero ahora tiene translateZ(0) para forzar la máscara overflow en iOS/Safari */
    .mole-hole { 
        width: 100%; max-width: 140px; aspect-ratio: 1; background-color: #3E2723; 
        border-radius: 50%; position: relative; overflow: hidden; 
        border: 8px solid #5D4037; box-shadow: inset 0 15px 15px rgba(0,0,0,0.6); 
        box-sizing: border-box; transform: translateZ(0); 
    }

    /* FIX VISUAL: El objeto ahora baja completamente (105%) y su opacidad se maneja para evitar bugs visuales */
    .word-entity { 
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        transform: translateY(105%); opacity: 0;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.1s; 
        cursor: pointer; user-select: none; display: flex; flex-direction: column; 
        justify-content: flex-end; align-items: center; padding-bottom: 10px; 
        z-index: 5; -webkit-tap-highlight-color: transparent; 
    }
    
    .word-entity.up { transform: translateY(0); opacity: 1; }
    
    .entity-emoji { font-size: clamp(40px, 10vw, 65px); filter: drop-shadow(0 5px 5px rgba(0,0,0,0.4)); pointer-events: none; margin-bottom: -5px; }
    
    .entity-text { background: white; color: #1E3A8A; font-weight: 900; font-size: clamp(10px, 3vw, 16px); padding: 2px 10px; border-radius: 20px; border: 2px solid #1E3A8A; pointer-events: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2); text-align: center; }

    @media (max-width: 600px) {
        .moles-game-container { padding: 1rem; margin-top: 1rem; }
        .moles-grid { gap: 10px; }
        .mole-hole { border-width: 5px; }
    }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
</div>

<div id="tutorial-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px;">¡Atrapa la Palabra! 🖐️</h2>
        <p class="modal-text" id="tut-context" style="margin-bottom: 10px;">Toca rápidamente los agujeros donde aparezca:</p>
        
        <div style="font-size: 3rem; margin: 10px 0;" id="tut-emoji"></div>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin: 15px 0; flex-wrap: wrap;">
            <div style="font-size: clamp(2.5rem, 8vw, 3.5rem); font-weight: 900; color: #38BDF8; letter-spacing: 2px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.3);" id="tut-word">WORD</div>
            <button class="btn-audio-huge" id="btn-tut-audio" title="Escuchar pronunciación" style="width: clamp(50px, 10vw, 65px); height: clamp(50px, 10vw, 65px); font-size: clamp(20px, 5vw, 26px); margin: 0;">🔊</button>
        </div>

        <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="tut-trans">(Traducción)</p>
        <p style="font-size: 14px; color: #475569; background: #F8FAFC; padding: 15px; border-radius: 12px; font-style: italic; margin-bottom: 25px; border: 1px solid #E2E8F0; width: 100%; box-sizing: border-box;" id="tut-mnemonic">💡 Cargando consejo...</p>

        <button id="btnStartGame" class="btn-play bg-green-500 hover:bg-green-600" style="margin-top: 0;">▶️ ¡Comenzar!</button>
    </div>
</div>

<div id="success-modal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title" style="margin-bottom: 10px; color: #10B981;">¡Atrapado! 🎯</h2>
        <div style="font-size: 4rem; margin: 10px 0;" id="succ-emoji">🖐️</div>
        <p style="font-size: 1.5rem; font-weight: 800; color: #1E3A8A; margin-bottom: 5px;" id="succ-word">WORD</p>
        <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;" id="succ-trans">(Traducción)</p>
        
        <p style="font-size: 15px; color: #065F46; background: #D1FAE5; padding: 15px; border-radius: 12px; font-weight: 600; margin-bottom: 25px; border: 1px solid #34D399; width: 100%; box-sizing: border-box;" id="succ-mnemonic">💡 Cargando recordatorio...</p>
        
        <button id="btn-next-round" onclick="goToNextRound()" class="btn-play bg-blue-500" style="margin-top: 0;">Siguiente ➡️</button>
    </div>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh; padding: 10px; box-sizing: border-box; width: 100%;">
    <div class="moles-game-container">
        <div class="flex justify-between items-center mb-6 px-4" style="flex-wrap: wrap; gap: 15px; text-align: center; justify-content: space-between;">
            <h3 class="text-2xl font-black text-white drop-shadow-md" style="margin: 0; font-size: clamp(16px, 4vw, 24px);">🎯 <span id="score">0</span>/5</h3>
            
            <div style="background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 50px; font-weight: 900; color: #1E3A8A; display:flex; align-items:center; gap:10px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                <span id="hud-target-emoji" style="font-size: clamp(20px, 5vw, 26px);"></span>
                <span id="hud-target-word" style="font-size: clamp(14px, 4vw, 18px);"></span>
            </div>

            <h3 class="text-2xl font-black text-white drop-shadow-md" style="margin: 0; font-size: clamp(16px, 4vw, 24px);">⏱️ <span id="timeLeft">30</span>s</h3>
        </div>

        <div class="moles-grid" id="molesGrid">
            <?php for($i=0; $i<9; $i++): ?>
            <div class="mole-hole">
                <div class="word-entity" id="hole-<?php echo $i; ?>"></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</main>

<script>
    const roundsDataMoles = window.dynamicRoundsData || [];
    let currentRoundIndexMoles = 0;
    let targetWordMoles = "";
    let targetEmojiMoles = "";
    let distractorsMoles = [];

    const startModalMoles = document.getElementById('tutorial-modal');
    const btnStartMoles = document.getElementById('btnStartGame');
    const scoreDisplayMoles = document.getElementById('score');
    const timeDisplayMoles = document.getElementById('timeLeft');
    const holesMoles = document.querySelectorAll('.word-entity');

    let lastHoleMoles;
    let timeUpMoles = false;
    let scoreMoles = 0;
    const maxScoreMoles = 5; 
    let timerMoles;
    let countdownMoles = 30;
    let isGameActive = false;

    document.addEventListener('DOMContentLoaded', () => {
        loadRoundMoles(currentRoundIndexMoles);
    });

    function loadRoundMoles(index) {
        const round = roundsDataMoles[index] || roundsDataMoles[0];
        targetWordMoles = round.target_word || round.word;
        targetEmojiMoles = round.emoji || '📦';
        distractorsMoles = round.distractors || [];

        // Modal Tutorial
        document.getElementById('tut-emoji').innerText = targetEmojiMoles;
        document.getElementById('tut-word').innerText = targetWordMoles;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        if(round.context_es) document.getElementById('tut-context').innerText = round.context_es;
        
        if(round.mnemonic) {
            document.getElementById('tut-mnemonic').innerText = "💡 " + round.mnemonic;
            document.getElementById('tut-mnemonic').style.display = 'block';
        } else {
            document.getElementById('tut-mnemonic').style.display = 'none';
        }

        document.getElementById('btn-tut-audio').onclick = () => {
            if(typeof playPronunciation === 'function') playPronunciation(targetWordMoles);
        };

        // Asignación de HUD
        document.getElementById('hud-target-emoji').innerText = targetEmojiMoles;
        document.getElementById('hud-target-word').innerText = targetWordMoles;
        
        startModalMoles.style.display = 'flex';
        setTimeout(() => startModalMoles.classList.add('active'), 50);

        if(typeof twemoji !== 'undefined') {
            twemoji.parse(startModalMoles, { folder: 'svg', ext: '.svg' });
            twemoji.parse(document.querySelector('.moles-game-container'), { folder: 'svg', ext: '.svg' });
        }
    }

    btnStartMoles.addEventListener('click', () => {
        startModalMoles.classList.remove('active');
        setTimeout(() => { startModalMoles.style.display = 'none'; }, 300);
        
        if(typeof attemptAutoplay === 'function') attemptAutoplay();
        setTimeout(startGameMoles, 500);
    });

    function randomTimeMoles(min, max) { return Math.round(Math.random() * (max - min) + min); }

    function randomHoleMoles(holesList) {
        const idx = Math.floor(Math.random() * holesList.length);
        const hole = holesList[idx];
        if (hole === lastHoleMoles) return randomHoleMoles(holesList); 
        lastHoleMoles = hole;
        return hole;
    }

    function generateEntityData() {
        const isTarget = Math.random() > 0.4; 
        if (isTarget || distractorsMoles.length === 0) {
            return { word: targetWordMoles, emoji: targetEmojiMoles, isCorrect: true };
        } else {
            const distractor = distractorsMoles[Math.floor(Math.random() * distractorsMoles.length)];
            return { word: distractor.word, emoji: distractor.emoji || '❓', isCorrect: false };
        }
    }

    function peepMoles() {
        if (!isGameActive) return;
        const time = randomTimeMoles(700, 1400); 
        const hole = randomHoleMoles(holesMoles);
        const entityData = generateEntityData();

        hole.innerHTML = `
            <div class="entity-emoji">${entityData.emoji}</div>
            <div class="entity-text">${entityData.word}</div>
        `;
        hole.dataset.isCorrect = entityData.isCorrect;
        if(typeof twemoji !== 'undefined') twemoji.parse(hole, { folder: 'svg', ext: '.svg' });

        hole.classList.add('up');

        setTimeout(() => {
            hole.classList.remove('up');
            // FIX: Limpiamos el HTML después de que termine de bajar para que no se vea nada en el agujero vacío
            setTimeout(() => { if(!hole.classList.contains('up')) hole.innerHTML = ''; }, 250);
            
            if (!timeUpMoles) peepMoles();
        }, time);
    }

    function startGameMoles() {
        scoreDisplayMoles.textContent = 0;
        timeDisplayMoles.textContent = countdownMoles;
        timeUpMoles = false;
        isGameActive = true;
        scoreMoles = 0;
        holesMoles.forEach(h => { h.classList.remove('up'); h.innerHTML = ''; });
        
        peepMoles();

        timerMoles = setInterval(() => {
            countdownMoles--;
            timeDisplayMoles.textContent = countdownMoles;
            if (countdownMoles <= 0) {
                clearInterval(timerMoles);
                timeUpMoles = true;
                isGameActive = false;
                if (scoreMoles < maxScoreMoles) gameOverMoles(false);
            }
        }, 1000);
    }

    function bonkMoles(e) {
        if (!e.isTrusted || !this.classList.contains('up') || !isGameActive) return; 

        const isCorrect = this.dataset.isCorrect === 'true';
        this.classList.remove('up');

        if (isCorrect) {
            scoreMoles++;
            scoreDisplayMoles.textContent = scoreMoles;
            
            this.innerHTML = `<div class="entity-emoji">💥</div>`;
            if(typeof twemoji !== 'undefined') twemoji.parse(this, { folder: 'svg', ext: '.svg' });
            setTimeout(() => { if(!this.classList.contains('up')) this.innerHTML = ''; }, 250);

            if(typeof AudioManager !== 'undefined') AudioManager.playSound('correct');

            if (scoreMoles >= maxScoreMoles) {
                timeUpMoles = true;
                isGameActive = false;
                clearInterval(timerMoles);
                setTimeout(() => gameOverMoles(true), 500);
            }
        } else {
            this.innerHTML = `<div class="entity-emoji">❌</div>`;
            if(typeof twemoji !== 'undefined') twemoji.parse(this, { folder: 'svg', ext: '.svg' });
            setTimeout(() => { if(!this.classList.contains('up')) this.innerHTML = ''; }, 250);

            if(typeof AudioManager !== 'undefined') AudioManager.playSound('wrong');
            
            countdownMoles = Math.max(0, countdownMoles - 2); 
            timeDisplayMoles.textContent = countdownMoles;
        }
    }

    holesMoles.forEach(hole => hole.addEventListener('pointerdown', bonkMoles));

    function gameOverMoles(isWin) {
        holesMoles.forEach(h => { h.classList.remove('up'); h.innerHTML = ''; });

        if (isWin) {
            if(typeof AudioManager !== 'undefined') AudioManager.playSound('win');
            
            const round = roundsDataMoles[currentRoundIndexMoles] || roundsDataMoles[0];
            document.getElementById('succ-emoji').innerText = round.emoji || '🎯';
            document.getElementById('succ-word').innerText = targetWordMoles;
            document.getElementById('succ-trans').innerText = `(${round.translation})`;
            
            if(round.mnemonic) {
                document.getElementById('succ-mnemonic').innerText = "💡 Recuerda: " + round.mnemonic;
                document.getElementById('succ-mnemonic').style.display = 'block';
            } else {
                document.getElementById('succ-mnemonic').style.display = 'none';
            }

            const modal = document.getElementById('success-modal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 50);
            if (typeof twemoji !== 'undefined') twemoji.parse(modal, { folder: 'svg', ext: '.svg' });

        } else {
            alert(`¡Tiempo Terminado! Atrapaste ${scoreMoles} de ${maxScoreMoles}. ¡Inténtalo de nuevo!`);
            location.reload();
        }
    }

    function goToNextRound() {
        document.getElementById('success-modal').classList.remove('active');
        setTimeout(() => { document.getElementById('success-modal').style.display = 'none'; }, 300);
        
        currentRoundIndexMoles++;
        if (currentRoundIndexMoles < roundsDataMoles.length) {
            loadRoundMoles(currentRoundIndexMoles);
        } else {
            finalWin();
        }
    }

    function finalWin() {
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') {
            unlockNextButton(<?php echo $lesson_id; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);
        }
    }
</script>