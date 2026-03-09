<?php
// templates/type_moles.php
// (Sin cabeceras repetidas. Este código es inyectado directamente en lesson.php)

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);
?>

<style>
    /* Seguro de Pantalla Horizontal */
    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: #1E293B; z-index: 10000; color: white; justify-content: center; 
        align-items: center; flex-direction: column; text-align: center;
    }
    @media screen and (max-height: 450px) and (orientation: landscape) {
        #landscape-warning { display: flex !important; }
        .game-wrapper { display: none !important; }
    }

    .moles-game-container { width: 100%; max-width: 700px; margin: 0 auto; padding: 2rem; background: #8BC34A; border-radius: 24px; box-shadow: inset 0 0 30px rgba(0,0,0,0.15), 0 10px 20px rgba(0,0,0,0.1); border: 4px solid #558B2F; }

    .moles-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; justify-items: center; align-items: center; }

    .mole-hole { width: 100%; max-width: 140px; aspect-ratio: 1; background-color: #3E2723; border-radius: 50%; position: relative; overflow: hidden; border: 8px solid #5D4037; box-shadow: inset 0 15px 15px rgba(0,0,0,0.6); }

    .word-entity { position: absolute; bottom: -120%; left: 50%; transform: translateX(-50%); transition: bottom 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; user-select: none; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; width: 100%; height: 100%; -webkit-tap-highlight-color: transparent; padding-bottom: 10px; z-index: 5; }
    
    .word-entity.up { bottom: 0%; }
    
    .entity-emoji { font-size: clamp(45px, 10vw, 65px); filter: drop-shadow(0 5px 5px rgba(0,0,0,0.4)); pointer-events: none; margin-bottom: -5px; }
    
    .entity-text { background: white; color: #1E3A8A; font-weight: 900; font-size: clamp(12px, 3vw, 16px); padding: 2px 12px; border-radius: 20px; border: 2px solid #1E3A8A; pointer-events: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }

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

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh;">
    
    <div id="startGameModal" class="modal-overlay active">
        <div class="modal-content">
            <h2 class="modal-title">¡Atrapa la Palabra! 🖐️</h2>
            <p class="modal-text" id="tut-context">Toca rápidamente los agujeros donde aparezca:</p>
            <div style="font-size: 3rem; margin: 10px 0;" id="tut-emoji"></div>
            <div style="font-size: 2.5rem; font-weight: 900; color: #38BDF8; margin-bottom: 5px;" id="tut-word"></div>
            <p style="color: #64748B; font-size: 1.2rem; font-weight: 600; margin-bottom: 25px;" id="tut-trans"></p>
            <button id="btnStartGame" class="btn-play w-full bg-green-500 hover:bg-green-600">▶️ ¡Comenzar!</button>
        </div>
    </div>

    <div class="moles-game-container">
        <div class="flex justify-between items-center mb-6 px-4" style="flex-wrap: wrap; gap: 15px; text-align: center;">
            <h3 class="text-2xl font-black text-white drop-shadow-md" style="margin: 0; font-size: clamp(16px, 4vw, 24px);">🎯 <span id="score">0</span>/5</h3>
            
            <div style="background: rgba(255,255,255,0.9); padding: 5px 15px; border-radius: 50px; font-weight: 900; color: #1E3A8A; display:flex; align-items:center; gap:10px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                <span id="hud-target-emoji" style="font-size: clamp(20px, 5vw, 26px);"></span>
                <span id="hud-target-word" style="font-size: clamp(14px, 4vw, 18px);"></span>
                <button class="btn-audio-huge" style="width: 35px; height: 35px; font-size: 16px; border-width: 2px;" onclick="playMolesAudio()">🔊</button>
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
    // FIX DE LOGICA: Integrar la ronda dinámica en vez de un fallback quemado
    const roundsDataMoles = window.dynamicRoundsData || [];
    const lessonDataMoles = roundsDataMoles[0] || { target_word: 'ERROR', translation: 'Error', emoji: '❌', distractors: [] };
    
    const targetWordMoles = lessonDataMoles.target_word || lessonDataMoles.word;
    const targetEmojiMoles = lessonDataMoles.emoji || '📦';
    const distractorsMoles = lessonDataMoles.distractors || [];

    const startModalMoles = document.getElementById('startGameModal');
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

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('tut-emoji').innerText = targetEmojiMoles;
        document.getElementById('tut-word').innerText = targetWordMoles;
        document.getElementById('tut-trans').innerText = `(${lessonDataMoles.translation})`;
        if(lessonDataMoles.context_es) document.getElementById('tut-context').innerText = lessonDataMoles.context_es;
        
        // Asignación de HUD
        document.getElementById('hud-target-emoji').innerText = targetEmojiMoles;
        document.getElementById('hud-target-word').innerText = targetWordMoles;
        
        twemoji.parse(startModalMoles, { folder: 'svg', ext: '.svg' });
        twemoji.parse(document.querySelector('.moles-game-container'), { folder: 'svg', ext: '.svg' });
    });

    function playMolesAudio() {
        if(typeof playPronunciation !== 'undefined') {
            playPronunciation(targetWordMoles);
        }
    }

    btnStartMoles.addEventListener('click', () => {
        startModalMoles.classList.remove('active');
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
        const time = randomTimeMoles(700, 1400); 
        const hole = randomHoleMoles(holesMoles);
        const entityData = generateEntityData();

        hole.innerHTML = `
            <div class="entity-emoji">${entityData.emoji}</div>
            <div class="entity-text">${entityData.word}</div>
        `;
        hole.dataset.isCorrect = entityData.isCorrect;
        twemoji.parse(hole, { folder: 'svg', ext: '.svg' });

        hole.classList.add('up');

        setTimeout(() => {
            hole.classList.remove('up');
            if (!timeUpMoles) peepMoles();
        }, time);
    }

    function startGameMoles() {
        scoreDisplayMoles.textContent = 0;
        timeDisplayMoles.textContent = countdownMoles;
        timeUpMoles = false;
        scoreMoles = 0;
        peepMoles();

        timerMoles = setInterval(() => {
            countdownMoles--;
            timeDisplayMoles.textContent = countdownMoles;
            if (countdownMoles <= 0) {
                clearInterval(timerMoles);
                timeUpMoles = true;
                if (scoreMoles < maxScoreMoles) gameOverMoles(false);
            }
        }, 1000);
    }

    function bonkMoles(e) {
        if (!e.isTrusted || !this.classList.contains('up')) return; 

        const isCorrect = this.dataset.isCorrect === 'true';
        this.classList.remove('up');

        if (isCorrect) {
            scoreMoles++;
            scoreDisplayMoles.textContent = scoreMoles;
            
            this.innerHTML = `<div class="entity-emoji">💥</div>`;
            twemoji.parse(this, { folder: 'svg', ext: '.svg' });

            if (scoreMoles >= maxScoreMoles) {
                timeUpMoles = true;
                clearInterval(timerMoles);
                setTimeout(() => gameOverMoles(true), 500);
            }
        } else {
            this.innerHTML = `<div class="entity-emoji">❌</div>`;
            twemoji.parse(this, { folder: 'svg', ext: '.svg' });
            countdownMoles = Math.max(0, countdownMoles - 2); 
            timeDisplayMoles.textContent = countdownMoles;
        }
    }

    holesMoles.forEach(hole => hole.addEventListener('pointerdown', bonkMoles));

    function gameOverMoles(isWin) {
        startModalMoles.classList.add('active');
        const modalTitle = startModalMoles.querySelector('.modal-title');
        const modalText = startModalMoles.querySelector('.modal-text');
        
        if (isWin) {
            modalTitle.innerHTML = "¡Excelente Reflejo! 🏆";
            modalText.innerHTML = `Atrapaste todos los correctos.`;
            startModalMoles.querySelector('#tut-emoji').style.display = 'none';
            startModalMoles.querySelector('#tut-word').style.display = 'none';
            startModalMoles.querySelector('#tut-trans').style.display = 'none';
            btnStartMoles.innerHTML = "Siguiente Misión ➡️";
            btnStartMoles.onclick = () => { /* Prevent default reload */ };

            twemoji.parse(startModalMoles, { folder: 'svg', ext: '.svg' });
            if(typeof fireConfetti !== 'undefined') fireConfetti();

            // Se delega el guardado a la función global de lesson.php
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id'] ?? 0; ?>);

        } else {
            modalTitle.innerHTML = "¡Tiempo Terminado! ⏳";
            modalText.innerHTML = `Solo atrapaste ${scoreMoles} de ${maxScoreMoles}. ¡Tú puedes hacerlo mejor!`;
            btnStartMoles.innerHTML = "Reintentar 🔄";
            btnStartMoles.onclick = () => location.reload();
            twemoji.parse(startModalMoles, { folder: 'svg', ext: '.svg' });
        }
    }
</script>