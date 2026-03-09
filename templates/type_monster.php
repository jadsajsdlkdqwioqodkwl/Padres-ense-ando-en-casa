<?php
// templates/type_monster.php
session_start();
require_once '../includes/config.php';

// Ciberseguridad: Prevenir acceso sin sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/head.php';
require_once '../includes/navbar.php';

$lesson_id = $lesson_id ?? ($lesson['id'] ?? 0);
$time_limit = $lesson_data['time_limit'] ?? 20; 
$reward_stars = $reward_stars ?? ($lesson['reward_stars'] ?? 5);

// Fallback de datos simulando la estructura de la BD
$rounds = $lesson_data['rounds'] ?? [
    [
        'word' => strtoupper($lesson_data['word'] ?? 'APPLE'),
        'phonetic' => $lesson_data['phonetic'] ?? 'ápol',
        'translation' => $lesson_data['translation'] ?? 'Manzana',
        'emoji' => $lesson_data['emoji'] ?? '🍎', // Nuevo campo para el objetivo dinámico
        'distractors' => $lesson_data['distractors'] ?? ['X', 'Z', 'M'],
        'context_es' => $lesson_data['context_es'] ?? "El monstruo quiere nuestro objeto. ¡Escribe la palabra mágica!"
    ]
];
?>

<script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>
    /* Estilos globales para Twemoji */
    img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
    
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

    .game-board { position: relative; width: 100%; min-height: 350px; background: #F8FAFC; border-radius: 24px; overflow: hidden; border: 4px solid #1E3A8A; margin-bottom: 25px; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.1); }
    
    /* EL MONSTRUO BELLO EN CSS RESTAURADO */
    .css-monster { position: absolute; left: 2%; bottom: 20px; width: 80px; height: 80px; background: #EF4444; border: 3px solid #1E3A8A; border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: morph 2s linear infinite, wobble 0.5s alternate infinite; transition: left 0.2s linear, transform 0.3s; z-index: 3; box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2); }
    .css-monster::before, .css-monster::after { content: ''; position: absolute; top: 18px; width: 16px; height: 16px; background: white; border-radius: 50%; border: 3px solid #1E3A8A; }
    .css-monster::before { left: 16px; } .css-monster::after { right: 16px; }
    .css-monster-mouth { position: absolute; bottom: 12px; left: 22px; width: 35px; height: 18px; background: #1E3A8A; border-radius: 0 0 18px 18px; transition: height 0.3s, border-radius 0.3s; }
    
    /* OBJETIVO DINÁMICO (Reemplaza al pastel estático) */
    .twemoji-target { position: absolute; right: 5%; bottom: 10px; font-size: 4.5rem; z-index: 2; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.2)); transition: transform 0.3s; }
    
    .slot-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; padding: 0 10px; }
    .letter-slot { width: clamp(45px, 12vw, 60px); height: clamp(55px, 15vw, 70px); border: 3px dashed #CBD5E1; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 34px); font-weight: 800; background: #ffffff; box-shadow: inset 0 3px 6px rgba(0,0,0,0.05); transition: 0.3s; color: #94A3B8; }
    .letter-slot.filled { border-style: solid; border-color: #4CAF50; background: #F0FDF4; color: #4CAF50; transform: scale(1.05); box-shadow: 0 4px 10px rgba(76, 175, 80, 0.2); }
    
    .bubbles-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; min-height: 80px; padding: 0 10px; }
    .drag-bubble { width: clamp(50px, 14vw, 60px); height: clamp(50px, 14vw, 60px); background: #F59E0B; color: white; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: clamp(24px, 6vw, 30px); font-weight: 800; cursor: pointer; box-shadow: 0 6px 0 #D97706, 0 10px 15px rgba(245, 158, 11, 0.3); transition: transform 0.1s, opacity 0.3s; user-select: none; touch-action: manipulation; z-index: 10; }
    .drag-bubble:active { transform: translateY(6px); box-shadow: 0 0px 0 #D97706, 0 2px 5px rgba(245, 158, 11, 0.3); }
    .drag-bubble.hidden { opacity: 0; pointer-events: none; transform: scale(0); }
    
    .round-indicator { position: absolute; top: 15px; left: 15px; background: #1E3A8A; color: white; font-weight: 700; padding: 6px 18px; border-radius: 50px; font-size: 14px; z-index: 50; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .danger-zone { animation: flashRed 1s infinite; }
    
    @keyframes morph { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 60% 50% 40% 50%; } }
    @keyframes wobble { from { transform: translateY(0) rotate(-5deg); } to { transform: translateY(-10px) rotate(5deg); } }
    @keyframes flashRed { 0%, 100% { background: #F8FAFC; } 50% { background: #FEE2E2; } }
    @keyframes zap { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.2) rotate(180deg); filter: brightness(5); } 100% { transform: scale(0); opacity: 0; } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 50% { transform: translateX(5px); } 75% { transform: translateX(-5px); } }
</style>

<div id="landscape-warning">
    <div style="font-size: 5rem; margin-bottom: 20px;">📱🔄</div>
    <h2 style="font-size: 2rem; margin-bottom: 10px;">¡Gira tu dispositivo!</h2>
    <p style="font-size: 1.2rem; color: #94A3B8;">Este juego necesita jugarse en formato vertical para una mejor experiencia.</p>
</div>

<main class="game-wrapper container mx-auto px-4 py-8" style="min-height: 85vh;">
    <div class="game-area text-center mx-auto" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="text-2xl font-black text-gray-800">🛡️ Word Defender</h3>
            <button onclick="giveHint()" style="background: #FBBF24; border: none; border-radius: 50%; width: 50px; height: 50px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 0 #D97706, 0 4px 10px rgba(245, 158, 11, 0.3); transition: 0.2s;" title="Pedir Pista">💡</button>
        </div>

        <div class="game-board" id="game-board">
            <div class="round-indicator" id="round-indicator">Ronda 1</div>

            <div id="tutorial-modal" class="modal-overlay active">
                <div class="modal-content">
                    <h2 class="modal-title">📜 Misión</h2>
                    <p class="modal-text" id="tut-context"></p>
                    <div style="font-size: 3rem; margin: 10px 0;" id="tut-emoji-display">🎯</div>
                    <div style="font-size: 2.5rem; font-weight: 900; color: #F59E0B; margin-bottom: 10px; letter-spacing: 5px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);" id="tut-word">WORD</div>
                    <p style="color: #64748B; font-size: 1.2rem; margin-bottom: 25px; font-weight: 600;" id="tut-trans">(Traducción)</p>
                    <button id="btn-start" onclick="startGame()" class="btn-play w-full bg-blue-600 hover:bg-blue-700">▶️ ¡Proteger!</button>
                </div>
            </div>

            <div class="css-monster" id="monster"><div class="css-monster-mouth" id="monster-mouth"></div></div>
            
            <div class="twemoji-target" id="dynamic-target">🎯</div>
        </div>

        <div class="slot-container" id="slots-container"></div>
        <div class="bubbles-container" id="bubbles-container"></div>
    </div>
</main>

<script>
    let roundsData = window.dynamicRoundsData || <?php echo json_encode($rounds); ?>;
    
    // Normalizador de datos
    roundsData = roundsData.map(r => ({
        word: r.target_word || r.word,
        translation: r.translation,
        emoji: r.emoji || r.content || '📦', // Soporta varios formatos de DB
        distractors: r.distractors || ['X', 'Z', 'M', 'Q'],
        context_es: r.context_es || "¡Defiende el objeto escribiendo la palabra!"
    }));

    const timeLimit = <?php echo $time_limit; ?>; 
    let currentRoundIndex = 0;
    let currentCorrect = 0;
    let wordLength = 0;
    
    let gameActive = false;
    let monsterPos = 2; 
    const targetPos = 75; 
    const stepAmount = (targetPos - 2) / (timeLimit * 10); 
    let monsterInterval;
    
    const monster = document.getElementById('monster');
    const monsterMouth = document.getElementById('monster-mouth');
    const dynamicTarget = document.getElementById('dynamic-target');
    const gameBoard = document.getElementById('game-board');

    document.addEventListener('DOMContentLoaded', () => {
        twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
        loadRound(currentRoundIndex);
    });

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        wordLength = round.word.length;
        currentCorrect = 0;
        
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        document.getElementById('tut-context').innerText = round.context_es;
        document.getElementById('tut-word').innerText = round.word;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        
        // Actualizamos el emoji objetivo dinámicamente
        document.getElementById('tut-emoji-display').innerText = round.emoji;
        dynamicTarget.innerText = round.emoji;
        dynamicTarget.style.display = 'block';
        dynamicTarget.style.transform = 'scale(1)';

        monsterPos = 2;
        monster.style.left = monsterPos + '%';
        monster.style.animation = 'morph 2s linear infinite, wobble 0.5s alternate infinite';
        monster.style.transform = 'scale(1)';
        monsterMouth.style.height = '18px'; // Boca normal

        let slotsHTML = '';
        for(let i=0; i<wordLength; i++) {
            slotsHTML += `<div class="letter-slot" data-expected="${round.word[i]}" data-index="${i}" id="slot-${i}"></div>`;
        }
        document.getElementById('slots-container').innerHTML = slotsHTML;

        let letters = round.word.split('');
        let allChars = letters.concat(round.distractors || []).sort(() => Math.random() - 0.5);
        
        let bubblesHTML = '';
        allChars.forEach((char, idx) => {
            bubblesHTML += `<div class="drag-bubble" id="bubble-${idx}" data-char="${char}" onclick="handleBubbleClick(this)">${char}</div>`;
        });
        document.getElementById('bubbles-container').innerHTML = bubblesHTML;

        document.getElementById('tutorial-modal').classList.add('active');
        twemoji.parse(document.getElementById('tutorial-modal'), { folder: 'svg', ext: '.svg' });
        twemoji.parse(gameBoard, { folder: 'svg', ext: '.svg' });
    }

    function startGame() {
        document.getElementById('tutorial-modal').classList.remove('active');
        gameActive = true;
        startMonster();
    }

    function startMonster() {
        clearInterval(monsterInterval);
        monsterInterval = setInterval(() => {
            if (!gameActive) return;
            monsterPos += stepAmount;
            monster.style.left = monsterPos + '%';
            
            // Si el monstruo se acerca, abre la boca
            if (monsterPos > targetPos * 0.7) {
                gameBoard.classList.add('danger-zone');
                monsterMouth.style.height = '30px'; 
                monsterMouth.style.borderRadius = '50%';
            } else {
                gameBoard.classList.remove('danger-zone');
                monsterMouth.style.height = '18px';
                monsterMouth.style.borderRadius = '0 0 18px 18px';
            }

            if (monsterPos >= targetPos) gameOver(false);
        }, 100);
    }

    function handleBubbleClick(bubbleEl) {
        if (!gameActive || bubbleEl.classList.contains('hidden')) return;
        processMove(bubbleEl);
    }

    function processMove(bubbleEl) {
        if (!bubbleEl || currentCorrect >= wordLength) return;

        const draggedChar = bubbleEl.getAttribute('data-char');
        const currentSlot = document.getElementById('slot-' + currentCorrect);
        const expectedChar = currentSlot.getAttribute('data-expected');

        if (draggedChar === expectedChar) {
            currentSlot.innerText = draggedChar;
            currentSlot.classList.add('filled');
            bubbleEl.classList.add('hidden');
            currentCorrect++;
            
            monsterPos = Math.max(2, monsterPos - 12); // Retroceso exitoso
            monster.style.left = monsterPos + '%';

            if (currentCorrect === wordLength) checkNextRound();
        } else {
            gameBoard.classList.add('danger-zone');
            bubbleEl.style.animation = 'shake 0.3s';
            setTimeout(() => { gameBoard.classList.remove('danger-zone'); bubbleEl.style.animation = 'none'; }, 300);
            monsterPos += 2; 
        }
    }

    function giveHint() {
        if(!gameActive || currentCorrect >= wordLength) return;
        const expectedChar = document.getElementById('slot-' + currentCorrect).getAttribute('data-expected');
        
        monsterPos += 4; 
        monster.style.left = monsterPos + '%';
        if (monsterPos >= targetPos) gameOver(false);
        
        document.querySelectorAll('.drag-bubble').forEach(b => {
            if(!b.classList.contains('hidden') && b.getAttribute('data-char') === expectedChar) {
                b.style.transform = 'scale(1.2)';
                b.style.boxShadow = '0 0 15px #FBBF24';
                setTimeout(() => { b.style.transform = 'scale(1)'; b.style.boxShadow = '0 6px 0 #D97706'; }, 1000);
            }
        });
    }

    function checkNextRound() {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');
        monster.style.animation = 'zap 0.8s forwards'; 
        
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            setTimeout(() => { loadRound(currentRoundIndex); }, 1500);
        } else {
            setTimeout(() => { gameOver(true); }, 1000);
        }
    }

    function gameOver(isWin) {
        gameActive = false;
        clearInterval(monsterInterval);
        gameBoard.classList.remove('danger-zone');

        if (isWin) {
            const modal = document.getElementById('tutorial-modal');
            modal.querySelector('.modal-title').innerHTML = "¡Defensa Exitosa! 🛡️";
            modal.querySelector('.modal-text').innerHTML = "¡Protegiste todos los objetos!";
            modal.querySelector('#tut-emoji-display').style.display = 'none';
            modal.querySelector('#tut-word').style.display = 'none';
            modal.querySelector('#tut-trans').style.display = 'none';
            modal.querySelector('.btn-play').innerHTML = "Continuar ➡️";
            modal.classList.add('active');
            twemoji.parse(modal, { folder: 'svg', ext: '.svg' });

            if(typeof fireConfetti !== 'undefined') fireConfetti();
            
            const payload = { lesson_id: <?php echo $lesson_id; ?>, stars: <?php echo $reward_stars; ?> };
            fetch('../app/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(typeof unlockNextButton !== 'undefined') unlockNextButton(payload.lesson_id, payload.stars, <?php echo $lesson['module_id'] ?? 0; ?>);
            }).catch(error => console.error(error));

        } else {
            // Animación de comerse el objeto
            dynamicTarget.style.display = 'none';
            monster.style.transform = 'scale(1.5)';
            monsterMouth.style.height = '5px'; // Cierra la boca, se lo comió
            
            setTimeout(() => {
                alert("¡Oh no! El monstruo alcanzó el objeto. ¡Inténtalo de nuevo!");
                location.reload(); 
            }, 1200);
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>