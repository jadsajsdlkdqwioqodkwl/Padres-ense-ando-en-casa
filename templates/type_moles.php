<?php
// templates/type_moles.php
session_start();
require_once '../includes/config.php';

// Ciberseguridad
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/head.php';
require_once '../includes/navbar.php';

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);

// Fallback estructurado para el juego de atrapar la palabra
$lesson_data = $lesson_data ?? [
    'target_word' => 'APPLE',
    'translation' => 'Manzana',
    'emoji' => '🍎',
    'distractors' => [
        ['word' => 'CAR', 'emoji' => '🚗'],
        ['word' => 'DOG', 'emoji' => '🐶'],
        ['word' => 'STAR', 'emoji' => '⭐']
    ],
    'context_es' => '¡Golpea a la manzana cuando salga de su escondite!'
];
?>

<script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>
    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }

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

    /* Reemplazo del "Topo" por la "Entidad Dinámica" */
    .word-entity { position: absolute; bottom: -120%; left: 50%; transform: translateX(-50%); transition: bottom 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; user-select: none; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; width: 100%; height: 100%; -webkit-tap-highlight-color: transparent; padding-bottom: 10px; z-index: 5; }
    
    .word-entity.up { bottom: 0%; }
    
    .entity-emoji { font-size: clamp(45px, 10vw, 65px); filter: drop-shadow(0 5px 5px rgba(0,0,0,0.4)); pointer-events: none; margin-bottom: -5px; }
    
    .entity-text { background: white; color: #1E3A8A; font-weight: 900; font-size: clamp(12px, 3vw, 16px); padding: 2px 12px; border-radius: 20px; border: 2px solid #1E3A8A; pointer-events: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }

    /* Responsividad Mobile Extrema */
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
        <div class="flex justify-between items-center mb-6 px-4">
            <h3 class="text-2xl font-black text-white drop-shadow-md">Aciertos: <span id="score">0</span>/5 🎯</h3>
            <h3 class="text-2xl font-black text-white drop-shadow-md">⏱️ <span id="timeLeft">30</span>s</h3>
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
    const lessonData = <?php echo json_encode($lesson_data); ?>;
    const targetWord = lessonData.target_word || lessonData.word;
    const targetEmoji = lessonData.emoji || '📦';
    const distractors = lessonData.distractors || [];

    const startModal = document.getElementById('startGameModal');
    const btnStart = document.getElementById('btnStartGame');
    const scoreDisplay = document.getElementById('score');
    const timeDisplay = document.getElementById('timeLeft');
    const holes = document.querySelectorAll('.word-entity');

    let lastHole;
    let timeUp = false;
    let score = 0;
    const maxScore = 5; // Aciertos necesarios para ganar
    let timer;
    let countdown = 30;

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('tut-emoji').innerText = targetEmoji;
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${lessonData.translation})`;
        if(lessonData.context_es) document.getElementById('tut-context').innerText = lessonData.context_es;
        
        twemoji.parse(startModal, { folder: 'svg', ext: '.svg' });
    });

    btnStart.addEventListener('click', () => {
        startModal.classList.remove('active');
        setTimeout(startGame, 500);
    });

    function randomTime(min, max) { return Math.round(Math.random() * (max - min) + min); }

    function randomHole(holesList) {
        const idx = Math.floor(Math.random() * holesList.length);
        const hole = holesList[idx];
        if (hole === lastHole) return randomHole(holesList); 
        lastHole = hole;
        return hole;
    }

    function generateEntityData() {
        const isTarget = Math.random() > 0.4; // 60% probabilidad de ser el correcto
        if (isTarget || distractors.length === 0) {
            return { word: targetWord, emoji: targetEmoji, isCorrect: true };
        } else {
            const distractor = distractors[Math.floor(Math.random() * distractors.length)];
            return { word: distractor.word, emoji: distractor.emoji || '❓', isCorrect: false };
        }
    }

    function peep() {
        const time = randomTime(700, 1400); 
        const hole = randomHole(holes);
        const entityData = generateEntityData();

        // Inyectamos el contenido dinámico (emoji + palabra)
        hole.innerHTML = `
            <div class="entity-emoji">${entityData.emoji}</div>
            <div class="entity-text">${entityData.word}</div>
        `;
        hole.dataset.isCorrect = entityData.isCorrect;
        twemoji.parse(hole, { folder: 'svg', ext: '.svg' });

        hole.classList.add('up');

        setTimeout(() => {
            hole.classList.remove('up');
            if (!timeUp) peep();
        }, time);
    }

    function startGame() {
        scoreDisplay.textContent = 0;
        timeDisplay.textContent = countdown;
        timeUp = false;
        score = 0;
        peep();

        timer = setInterval(() => {
            countdown--;
            timeDisplay.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                timeUp = true;
                if (score < maxScore) gameOver(false);
            }
        }, 1000);
    }

    function bonk(e) {
        if (!e.isTrusted || !this.classList.contains('up')) return; 

        const isCorrect = this.dataset.isCorrect === 'true';
        this.classList.remove('up');

        if (isCorrect) {
            score++;
            scoreDisplay.textContent = score;
            
            // Efecto visual de explosión
            this.innerHTML = `<div class="entity-emoji">💥</div>`;
            twemoji.parse(this, { folder: 'svg', ext: '.svg' });

            if (score >= maxScore) {
                timeUp = true;
                clearInterval(timer);
                setTimeout(() => gameOver(true), 500);
            }
        } else {
            // Penalización visual si se equivoca
            this.innerHTML = `<div class="entity-emoji">❌</div>`;
            twemoji.parse(this, { folder: 'svg', ext: '.svg' });
            countdown = Math.max(0, countdown - 2); // Resta 2 segundos
            timeDisplay.textContent = countdown;
        }
    }

    holes.forEach(hole => hole.addEventListener('pointerdown', bonk));

    function gameOver(isWin) {
        startModal.classList.add('active');
        const modalTitle = startModal.querySelector('.modal-title');
        const modalText = startModal.querySelector('.modal-text');
        
        if (isWin) {
            modalTitle.innerHTML = "¡Excelente Reflejo! 🏆";
            modalText.innerHTML = `Atrapaste todos los correctos.`;
            startModal.querySelector('#tut-emoji').style.display = 'none';
            startModal.querySelector('#tut-word').style.display = 'none';
            startModal.querySelector('#tut-trans').style.display = 'none';
            btnStart.innerHTML = "Siguiente Misión ➡️";
            btnStart.onclick = () => { /* Prevent default reload */ };

            twemoji.parse(startModal, { folder: 'svg', ext: '.svg' });
            if(typeof fireConfetti !== 'undefined') fireConfetti();

            // GUARDADO EN BD
            const payload = { lesson_id: <?php echo $lesson_id; ?>, stars: <?php echo $reward_stars; ?> };
            fetch('../app/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(typeof unlockNextButton !== 'undefined') unlockNextButton(payload.lesson_id, payload.stars, <?php echo $lesson['module_id'] ?? 0; ?>);
            });

        } else {
            modalTitle.innerHTML = "¡Tiempo Terminado! ⏳";
            modalText.innerHTML = `Solo atrapaste ${score} de ${maxScore}. ¡Tú puedes hacerlo mejor!`;
            btnStart.innerHTML = "Reintentar 🔄";
            btnStart.onclick = () => location.reload();
            twemoji.parse(startModal, { folder: 'svg', ext: '.svg' });
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>