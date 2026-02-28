<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: EXAMEN FINAL (BOSS STAGES)
// ==========================================
$questions = $lesson_data['questions'] ?? [
    ['q' => '¿Cómo se dice "Perro"?', 'options' => ['Cat', 'Dog', 'Bird'], 'answer' => 'Dog'],
    ['q' => '¿Qué significa "Apple"?', 'options' => ['Manzana', 'Pera', 'Plátano'], 'answer' => 'Manzana']
];
$time_per_question = $lesson_data['time_limit'] ?? 10; 
$player_lives = $lesson_data['lives'] ?? 3;
$reward_stars = $lesson['reward_stars'] ?? 15;
$total_questions = count($questions);
?>

<style>
    /* ==========================================
       ESCENARIO Y BARRAS DE VIDA
    ========================================== */
    .battle-arena {
        position: relative; width: 100%; height: 280px;
        background: var(--dark);
        border-radius: 20px; border: 4px solid var(--primary);
        margin-bottom: 20px; overflow: hidden;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.8);
        transition: background 1s ease;
    }

    .boss-stage-indicator {
        position: absolute; top: 15px; left: 50%; transform: translateX(-50%);
        color: white; font-weight: bold; font-size: 14px; background: rgba(0,0,0,0.5);
        padding: 5px 15px; border-radius: 20px; z-index: 20; letter-spacing: 1px;
    }

    .health-bar-container {
        position: absolute; top: 15px; width: 35%; height: 20px;
        background: rgba(0,0,0,0.5); border-radius: 10px; border: 2px solid white; z-index: 20;
    }
    .health-bar-container.player { left: 15px; }
    .health-bar-container.boss { right: 15px; }
    
    .health-fill {
        height: 100%; border-radius: 8px; transition: width 0.3s ease-out;
    }
    .health-fill.player { background: var(--success); width: 100%; }
    .health-fill.boss { background: #e74c3c; width: 100%; float: right; }

    /* ==========================================
       PERSONAJES CSS (JUGADOR Y JEFE)
    ========================================== */
    .css-hero {
        position: absolute; bottom: 30px; left: 40px; width: 60px; height: 60px;
        background: var(--accent); border-radius: 50%; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2);
        animation: floatHero 2s infinite alternate; z-index: 10;
    }
    .css-hero::after { 
        content: ''; position: absolute; top: 15px; right: 15px; width: 15px; height: 15px;
        background: white; border-radius: 50%; border: 3px solid var(--dark);
    }

    .css-boss {
        position: absolute; bottom: 30px; right: 40px; width: 100px; height: 120px;
        background: var(--primary); border-radius: 20px 20px 10px 10px;
        box-shadow: inset -10px -10px 0 rgba(0,0,0,0.3), 0 0 20px var(--primary);
        animation: floatBoss 3s infinite alternate; z-index: 10;
        transition: transform 0.2s, background 0.5s, box-shadow 0.5s;
    }
    .css-boss::before, .css-boss::after { 
        content: ''; position: absolute; top: 30px; width: 25px; height: 15px;
        background: #f1c40f; border-radius: 50%; border: 3px solid var(--dark);
    }
    .css-boss::before { left: 20px; transform: rotate(15deg); }
    .css-boss::after { right: 20px; transform: rotate(-15deg); }
    .css-boss-mouth {
        position: absolute; bottom: 20px; left: 20px; right: 20px; height: 15px;
        background: var(--dark); border-radius: 10px; transition: height 0.3s;
    }

    /* Animaciones de Daño y Ataque */
    .hero-attack { animation: dashRight 0.3s forwards; }
    .boss-attack { animation: dashLeft 0.3s forwards; }
    .take-damage { animation: flashHit 0.4s; filter: brightness(2) drop-shadow(0 0 10px red); }
    .boss-defeated { animation: sinkDown 1.5s forwards; }

    /* ==========================================
       INTERFAZ DE PREGUNTAS Y TIEMPO
    ========================================== */
    .question-panel {
        background: white; border-radius: 20px; padding: 25px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center;
        border: 2px solid var(--border-color);
    }
    
    .timer-container {
        width: 100%; height: 10px; background: var(--light); border-radius: 5px; margin-bottom: 20px; overflow: hidden;
    }
    .timer-bar {
        height: 100%; background: var(--accent); width: 100%; transition: width linear;
    }

    .question-text { font-size: 24px; color: var(--primary); margin-bottom: 20px; font-weight: bold; }
    
    .options-grid { display: grid; grid-template-columns: 1fr; gap: 15px; }
    .opt-btn {
        padding: 15px; font-size: 20px; font-weight: bold; border-radius: 15px;
        border: 2px solid var(--border-color); background: var(--light); color: var(--dark);
        cursor: pointer; transition: 0.2s; box-shadow: 0 5px 0 var(--border-color);
    }
    .opt-btn:active:not(:disabled) { transform: translateY(5px); box-shadow: 0 0 0 var(--border-color); }
    .opt-btn:disabled { cursor: not-allowed; opacity: 0.8; }
    .opt-btn.correct { background: var(--success); color: white; border-color: #27ae60; box-shadow: 0 5px 0 #27ae60; }
    .opt-btn.wrong { background: #e74c3c; color: white; border-color: #c0392b; box-shadow: 0 5px 0 #c0392b; }

    @keyframes floatHero { 0% { transform: translateY(0); } 100% { transform: translateY(-10px); } }
    @keyframes floatBoss { 0% { transform: translateY(0); } 100% { transform: translateY(-15px); } }
    @keyframes dashRight { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(100px); } }
    @keyframes dashLeft { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(-100px); } }
    @keyframes flashHit { 0%, 100% { opacity: 1; } 50% { opacity: 0.2; } }
    @keyframes sinkDown { 0% { transform: translateY(0) rotate(0); filter: grayscale(1); } 100% { transform: translateY(150px) rotate(45deg); filter: grayscale(1); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding: 0;">
    <h3 style="margin-top:0; color: var(--primary);">⚔️ Batalla Final ⚔️</h3>

    <div class="battle-arena" id="arena">
        <div class="boss-stage-indicator" id="stage-indicator">STAGE 1</div>
        <div class="health-bar-container player"><div class="health-fill player" id="hp-player"></div></div>
        <div class="health-bar-container boss"><div class="health-fill boss" id="hp-boss"></div></div>
        
        <div class="css-hero" id="hero"></div>
        <div class="css-boss" id="boss"><div class="css-boss-mouth" id="boss-mouth"></div></div>
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
    // ==========================================
    // LÓGICA DE STAGES Y PREGUNTAS
    // ==========================================
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
    const bossMouth = document.getElementById('boss-mouth');
    const arena = document.getElementById('arena');
    const stageIndicator = document.getElementById('stage-indicator');
    const timerBar = document.getElementById('timer-bar');

    function loadQuestion() {
        if (currentQ >= totalQ) {
            winGame(); return;
        }

        updateBossStage(); // Evalúa en qué fase del jefe estamos

        isTransitioning = false;
        const q = questions[currentQ];
        
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

    // ==========================================
    // SISTEMA DE FASES DEL JEFE
    // ==========================================
    function updateBossStage() {
        // Calculamos el progreso basado en cuántas preguntas faltan
        let progress = currentQ / totalQ;

        if (progress >= 0.66) {
            stageIndicator.innerText = "STAGE 3: ¡FURIOSO!";
            boss.style.background = '#c0392b'; // Rojo oscuro
            boss.style.boxShadow = 'inset -10px -10px 0 rgba(0,0,0,0.3), 0 0 30px #e74c3c';
            bossMouth.style.height = '30px'; // Abre la boca
            arena.style.background = '#2c3e50';
            boss.style.animationDuration = '1s'; // Se mueve más rápido
        } else if (progress >= 0.33) {
            stageIndicator.innerText = "STAGE 2: ¡ENFADADO!";
            boss.style.background = '#d35400'; // Naranja
            boss.style.boxShadow = 'inset -10px -10px 0 rgba(0,0,0,0.3), 0 0 25px #e67e22';
            bossMouth.style.height = '20px';
            boss.style.animationDuration = '2s';
        } else {
            stageIndicator.innerText = "STAGE 1: NORMAL";
        }
    }

    // ==========================================
    // TEMPORIZADOR Y RESPUESTAS
    // ==========================================
    function startTimer() {
        timerBar.style.transition = 'none';
        timerBar.style.width = '100%';
        
        setTimeout(() => {
            timerBar.style.transition = `width ${timeLimit}s linear`;
            timerBar.style.width = '0%';
        }, 50);

        questionTimer = setTimeout(() => {
            if(!isTransitioning) handleAnswer(null, false, true); 
        }, timeLimit * 1000);
    }

    function handleAnswer(btn, isCorrect, isTimeout = false) {
        if (isTransitioning) return;
        isTransitioning = true;
        clearTimeout(questionTimer);
        timerBar.style.transition = 'none';

        document.querySelectorAll('.opt-btn').forEach(b => b.disabled = true);

        if (isCorrect) {
            if(btn) btn.classList.add('correct');
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            
            hero.classList.add('hero-attack');
            setTimeout(() => {
                hero.classList.remove('hero-attack');
                boss.classList.add('take-damage');
                
                // Daño calculado equitativamente para derrotarlo en la última pregunta
                bossHpPercentage -= (100 / totalQ);
                hpBoss.style.width = Math.max(0, bossHpPercentage) + '%';
                
                setTimeout(() => boss.classList.remove('take-damage'), 400);
            }, 150);

            setTimeout(() => { currentQ++; loadQuestion(); }, 1200);

        } else {
            if(btn) btn.classList.add('wrong');
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            
            // Mostrar respuesta correcta
            const q = questions[currentQ];
            document.querySelectorAll('.opt-btn').forEach(b => {
                if(b.innerText === q.answer) b.classList.add('correct');
            });

            boss.classList.add('boss-attack');
            setTimeout(() => {
                boss.classList.remove('boss-attack');
                hero.classList.add('take-damage');
                
                currentLives--;
                hpPlayer.style.width = (currentLives / maxLives * 100) + '%';
                
                setTimeout(() => hero.classList.remove('take-damage'), 400);

                if (currentLives <= 0) {
                    setTimeout(loseGame, 500);
                } else {
                    // Penaltí visual, pero pasamos a la siguiente pregunta para que no se tranque
                    setTimeout(() => { currentQ++; loadQuestion(); }, 1500);
                }
            }, 150);
        }
    }

    // ==========================================
    // VICTORIA Y DERROTA
    // ==========================================
    function winGame() {
        document.getElementById('quiz-panel').style.display = 'none';
        document.getElementById('success-msg').style.display = 'block';
        stageIndicator.style.display = 'none';
        
        hpBoss.style.width = '0%';
        boss.classList.add('boss-defeated');
        
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }

    function loseGame() {
        document.getElementById('quiz-panel').innerHTML = `
            <h2 style="color:#e74c3c;">¡El Jefe te ha vencido!</h2>
            <p style="color: var(--text-muted);">Te quedaste sin energía.</p>
            <button class="opt-btn" onclick="location.reload()" style="margin-top:20px; background: var(--primary); color: white; border-color: var(--primary);">🔄 Intentar de Nuevo</button>
        `;
    }

    // Iniciar combate
    loadQuestion();
</script>