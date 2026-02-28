<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: EXAMEN FINAL (BOSS BATTLE)
// ==========================================
$questions = $lesson_data['questions'] ?? [
    ['q' => '¿Cómo se dice "Perro"?', 'options' => ['Cat', 'Dog', 'Bird'], 'answer' => 'Dog'],
    ['q' => '¿Qué significa "Apple"?', 'options' => ['Manzana', 'Pera', 'Plátano'], 'answer' => 'Manzana']
];
$time_per_question = $lesson_data['time_limit'] ?? 10; // Segundos por pregunta
$player_lives = $lesson_data['lives'] ?? 3;
$reward_stars = $lesson['reward_stars'] ?? 10;
$total_questions = count($questions);
?>

<style>
    /* ==========================================
       ESCENARIO Y BARRAS DE VIDA
    ========================================== */
    .battle-arena {
        position: relative; width: 100%; height: 280px;
        background: linear-gradient(to bottom, #192a56, #273c75);
        border-radius: 20px; border: 4px solid var(--primary);
        margin-bottom: 20px; overflow: hidden;
        box-shadow: inset 0 0 30px rgba(0,0,0,0.5);
    }

    .health-bar-container {
        position: absolute; top: 15px; width: 40%; height: 20px;
        background: rgba(0,0,0,0.5); border-radius: 10px; border: 2px solid white;
    }
    .health-bar-container.player { left: 15px; }
    .health-bar-container.boss { right: 15px; }
    
    .health-fill {
        height: 100%; border-radius: 8px; transition: width 0.3s ease-out;
    }
    .health-fill.player { background: #2ecc71; width: 100%; }
    .health-fill.boss { background: #e74c3c; width: 100%; float: right; }

    /* ==========================================
       PERSONAJES CSS (JUGADOR Y JEFE)
    ========================================== */
    .css-hero {
        position: absolute; bottom: 30px; left: 40px; width: 60px; height: 60px;
        background: #3498db; border-radius: 50%; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2);
        animation: floatHero 2s infinite alternate; z-index: 10;
    }
    .css-hero::after { /* Ojo del héroe */
        content: ''; position: absolute; top: 15px; right: 15px; width: 15px; height: 15px;
        background: white; border-radius: 50%; border: 3px solid #2c3e50;
    }

    .css-boss {
        position: absolute; bottom: 30px; right: 40px; width: 100px; height: 120px;
        background: #8e44ad; border-radius: 20px 20px 10px 10px;
        box-shadow: inset -10px -10px 0 rgba(0,0,0,0.3);
        animation: floatBoss 3s infinite alternate; z-index: 10;
        transition: transform 0.2s;
    }
    .css-boss::before, .css-boss::after { /* Ojos del jefe */
        content: ''; position: absolute; top: 30px; width: 25px; height: 15px;
        background: #f1c40f; border-radius: 50%; border: 3px solid #2c3e50;
    }
    .css-boss::before { left: 20px; transform: rotate(15deg); }
    .css-boss::after { right: 20px; transform: rotate(-15deg); }
    .css-boss-mouth {
        position: absolute; bottom: 20px; left: 20px; right: 20px; height: 15px;
        background: #2c3e50; border-radius: 10px;
    }

    /* Animaciones de daño y ataque */
    .hero-attack { animation: dashRight 0.3s forwards; }
    .boss-attack { animation: dashLeft 0.3s forwards; }
    .take-damage { animation: flashHit 0.4s; filter: brightness(2) drop-shadow(0 0 10px red); }
    .boss-defeated { animation: sinkDown 1.5s forwards; }

    /* ==========================================
       INTERFAZ DE PREGUNTAS Y TIEMPO
    ========================================== */
    .question-panel {
        background: white; border-radius: 20px; padding: 25px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center;
        border: 4px solid #eee;
    }
    
    .timer-container {
        width: 100%; height: 10px; background: #eee; border-radius: 5px; margin-bottom: 20px; overflow: hidden;
    }
    .timer-bar {
        height: 100%; background: #f39c12; width: 100%; transition: width linear;
    }

    .question-text { font-size: 24px; color: var(--primary); margin-bottom: 20px; font-weight: bold; }
    
    .options-grid { display: grid; grid-template-columns: 1fr; gap: 15px; }
    .opt-btn {
        padding: 15px; font-size: 20px; font-weight: bold; border-radius: 15px;
        border: 3px solid #dcdde1; background: #f5f6fa; color: #2f3640;
        cursor: pointer; transition: 0.2s; box-shadow: 0 5px 0 #dcdde1;
    }
    .opt-btn:active:not(:disabled) { transform: translateY(5px); box-shadow: 0 0 0 #dcdde1; }
    .opt-btn:disabled { cursor: not-allowed; opacity: 0.8; }
    .opt-btn.correct { background: #2ecc71; color: white; border-color: #27ae60; box-shadow: 0 5px 0 #27ae60; }
    .opt-btn.wrong { background: #e74c3c; color: white; border-color: #c0392b; box-shadow: 0 5px 0 #c0392b; }

    @keyframes floatHero { 0% { transform: translateY(0); } 100% { transform: translateY(-10px); } }
    @keyframes floatBoss { 0% { transform: translateY(0); } 100% { transform: translateY(-15px); } }
    @keyframes dashRight { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(100px); } }
    @keyframes dashLeft { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(-100px); } }
    @keyframes flashHit { 0%, 100% { opacity: 1; } 50% { opacity: 0.2; } }
    @keyframes sinkDown { 0% { transform: translateY(0) rotate(0); filter: grayscale(0); } 100% { transform: translateY(150px) rotate(45deg); filter: grayscale(1); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding: 0;">
    <h3 style="margin-top:0;">⚔️ ¡Derrota al Jefe Final! ⚔️</h3>

    <div class="battle-arena" id="arena">
        <div class="health-bar-container player"><div class="health-fill player" id="hp-player"></div></div>
        <div class="health-bar-container boss"><div class="health-fill boss" id="hp-boss"></div></div>
        
        <div class="css-hero" id="hero"></div>
        <div class="css-boss" id="boss"><div class="css-boss-mouth"></div></div>
    </div>

    <div class="question-panel" id="quiz-panel">
        <div class="timer-container"><div class="timer-bar" id="timer-bar"></div></div>
        <div class="question-text" id="q-text">Cargando...</div>
        <div class="options-grid" id="options-container"></div>
    </div>

    <div id="success-msg" style="display:none; color:var(--success); font-size:28px; font-weight:bold; margin-top:20px;">
        🏆 ¡Jefe Derrotado! +<?php echo $reward_stars; ?> Estrellas ⭐
    </div>
</div>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    const timeLimit = <?php echo $time_per_question; ?>;
    const maxLives = <?php echo $player_lives; ?>;
    const totalQ = questions.length;
    
    let currentQ = 0;
    let currentLives = maxLives;
    let bossHpPercentage = 100;
    let questionTimer;
    let isTransitioning = false;

    const hpPlayer = document.getElementById('hp-player');
    const hpBoss = document.getElementById('hp-boss');
    const hero = document.getElementById('hero');
    const boss = document.getElementById('boss');
    const timerBar = document.getElementById('timer-bar');

    function loadQuestion() {
        if (currentQ >= totalQ) {
            winGame(); return;
        }

        isTransitioning = false;
        const q = questions[currentQ];
        
        // Leer la pregunta (opcional)
        if(typeof playTTS !== 'undefined') playTTS(q.q);

        document.getElementById('q-text').innerText = q.q;
        const optionsDiv = document.getElementById('options-container');
        optionsDiv.innerHTML = '';

        // Randomizar opciones
        let shuffledOptions = [...q.options].sort(() => Math.random() - 0.5);

        shuffledOptions.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'opt-btn';
            btn.innerText = opt;
            btn.onclick = () => handleAnswer(btn, opt === q.answer);
            optionsDiv.appendChild(btn);
        });

        startTimer();
    }

    function startTimer() {
        timerBar.style.transition = 'none';
        timerBar.style.width = '100%';
        
        setTimeout(() => {
            timerBar.style.transition = `width ${timeLimit}s linear`;
            timerBar.style.width = '0%';
        }, 50);

        questionTimer = setTimeout(() => {
            if(!isTransitioning) handleAnswer(null, false, true); // Timeout
        }, timeLimit * 1000);
    }

    function handleAnswer(btn, isCorrect, isTimeout = false) {
        if (isTransitioning) return;
        isTransitioning = true;
        clearTimeout(questionTimer);
        timerBar.style.transition = 'none';

        // Desactivar botones
        document.querySelectorAll('.opt-btn').forEach(b => b.disabled = true);

        if (isCorrect) {
            // Ataque del Héroe
            if(btn) btn.classList.add('correct');
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            
            hero.classList.add('hero-attack');
            setTimeout(() => {
                hero.classList.remove('hero-attack');
                boss.classList.add('take-damage');
                
                // Reducir vida del jefe
                bossHpPercentage -= (100 / totalQ);
                hpBoss.style.width = Math.max(0, bossHpPercentage) + '%';
                
                setTimeout(() => boss.classList.remove('take-damage'), 400);
            }, 150);

            setTimeout(() => { currentQ++; loadQuestion(); }, 1200);

        } else {
            // Ataque del Jefe
            if(btn) btn.classList.add('wrong');
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            
            // Mostrar cuál era la correcta
            const q = questions[currentQ];
            document.querySelectorAll('.opt-btn').forEach(b => {
                if(b.innerText === q.answer) b.classList.add('correct');
            });

            boss.classList.add('boss-attack');
            setTimeout(() => {
                boss.classList.remove('boss-attack');
                hero.classList.add('take-damage');
                
                // Reducir vida del jugador
                currentLives--;
                hpPlayer.style.width = (currentLives / maxLives * 100) + '%';
                
                setTimeout(() => hero.classList.remove('take-damage'), 400);

                if (currentLives <= 0) {
                    setTimeout(loseGame, 500);
                } else {
                    // Pasa a la siguiente pregunta aunque falló (o lo haces repetir, según prefieras)
                    setTimeout(() => { currentQ++; loadQuestion(); }, 1500);
                }
            }, 150);
        }
    }

    function winGame() {
        document.getElementById('quiz-panel').style.display = 'none';
        document.getElementById('success-msg').style.display = 'block';
        
        boss.classList.add('boss-defeated');
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }

    function loseGame() {
        document.getElementById('quiz-panel').innerHTML = '<h2 style="color:#e74c3c;">¡El Jefe te ha vencido!</h2><button class="opt-btn" onclick="location.reload()" style="margin-top:20px;">🔄 Intentar de Nuevo</button>';
    }

    // Iniciar
    loadQuestion();
</script>