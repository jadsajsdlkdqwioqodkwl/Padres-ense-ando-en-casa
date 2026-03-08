<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: COLOR RESCUE PRO (MULTIRONDA)
// ==========================================
$time_limit = $lesson_data['time_limit'] ?? 15; 
$reward_stars = $lesson['reward_stars'] ?? 10;

// Estructura Multironda
$rounds = $lesson_data['rounds'] ?? [
    [
        'color_name' => 'Red', 'phonetic' => 'red', 'color_hex' => '#ff4757', 'item' => '🍎', 'translation' => 'Rojo',
        'context_es' => '¡El OVNI roba colores ataca! Pinta el dibujo antes de que se lo lleve.',
        'distractors' => [['name' => 'Blue', 'hex' => '#3742fa'], ['name' => 'Green', 'hex' => '#2ed573']]
    ]
];
?>

<style>
    .color-board { position: relative; width: 100%; height: 450px; background: #1E293B; border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15), inset 0 0 40px rgba(0,0,0,0.5); display: flex; flex-direction: column; justify-content: flex-end; }
    .round-indicator { position: absolute; top: 20px; left: 20px; color: white; font-weight: 700; font-size: 15px; z-index: 50; background: var(--brand-blue); padding: 6px 18px; border-radius: 50px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); }
    .tutorial-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); backdrop-filter: blur(4px); z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: opacity 0.5s; text-align: center; padding: 30px; }
    .tutorial-icon { font-size: 90px; margin-bottom: 10px; animation: bounce 2s infinite; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1)); }
    .tutorial-word { font-size: 45px; font-weight: 800; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .btn-action { margin-top: 25px; padding: 16px 45px; font-size: 18px; font-weight: 700; background: var(--brand-green); color: white; border: none; border-radius: 50px; cursor: pointer; box-shadow: 0 6px 15px rgba(104, 169, 62, 0.3); transition: 0.3s; }
    .btn-action:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(104, 169, 62, 0.4); }
    .btn-action:active { transform: translateY(2px); box-shadow: 0 2px 5px rgba(104, 169, 62, 0.3); }
    .css-ufo { position: absolute; top: -80px; left: 50%; transform: translateX(-50%); width: 130px; height: 55px; z-index: 20; transition: top linear; }
    .ufo-dome { position: absolute; top: 0; left: 35px; width: 60px; height: 35px; background: rgba(129, 236, 236, 0.7); border-radius: 30px 30px 0 0; border: 2px solid #00cec9; z-index: 2; box-shadow: inset 0 5px 10px rgba(255,255,255,0.5); }
    .ufo-base { position: absolute; bottom: 0; width: 100%; height: 28px; background: #94A3B8; border-radius: 20px; border: 3px solid #334155; box-shadow: inset 0 -6px 0 rgba(0,0,0,0.2), 0 10px 20px rgba(0,0,0,0.5); z-index: 3; }
    .ufo-lights { position: absolute; bottom: 6px; left: 15px; width: 100px; display: flex; justify-content: space-between; z-index: 4; }
    .ufo-light { width: 8px; height: 8px; background: #FEF08A; border-radius: 50%; animation: blink 0.5s infinite alternate; box-shadow: 0 0 5px #FEF08A; }
    .tractor-beam { position: absolute; top: 45px; left: 50%; transform: translateX(-50%); width: 90px; height: 0px; background: linear-gradient(to bottom, rgba(45, 212, 191, 0.8), rgba(45, 212, 191, 0.1)); clip-path: polygon(20% 0, 80% 0, 100% 100%, 0 100%); z-index: 1; transition: height linear; }
    .target-canvas { position: absolute; bottom: 130px; left: 50%; transform: translateX(-50%); font-size: 110px; z-index: 10; filter: grayscale(100%) brightness(1.5); transition: filter 1s, transform 0.3s; }
    .target-canvas.colored { filter: grayscale(0%) brightness(1); animation: celebrate 1s; }
    .target-canvas.abducted { bottom: 100%; opacity: 0; transition: bottom 1s, opacity 1s; }
    .paint-station { width: 100%; height: 110px; background: #334155; border-top: 6px solid #475569; display: flex; justify-content: center; align-items: center; gap: 25px; z-index: 30; box-shadow: 0 -10px 20px rgba(0,0,0,0.2); }
    .paint-bucket { position: relative; width: 65px; height: 65px; background: #F8FAFC; border: 4px solid #64748B; border-radius: 12px 12px 18px 18px; cursor: pointer; display: flex; align-items: flex-start; justify-content: center; box-shadow: 0 12px 0 rgba(0,0,0,0.2), 0 15px 15px rgba(0,0,0,0.3); transition: 0.2s; }
    .paint-bucket:hover { transform: translateY(-4px); box-shadow: 0 16px 0 rgba(0,0,0,0.2), 0 20px 20px rgba(0,0,0,0.4); }
    .paint-bucket:active { transform: translateY(10px); box-shadow: 0 2px 0 rgba(0,0,0,0.2), 0 5px 5px rgba(0,0,0,0.3); border-color: #94A3B8; }
    .paint-fill { width: 100%; height: 80%; border-radius: 6px 6px 12px 12px; border-bottom: 5px solid rgba(0,0,0,0.2); }
    .splat { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0); width: 150px; height: 150px; background: currentColor; clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); border-radius: 30%; z-index: 40; opacity: 0; pointer-events: none; }
    .splat-anim { animation: splatPop 0.6s forwards; }
    @keyframes blink { 0% { background: #FEF08A; box-shadow: 0 0 5px #FEF08A; } 100% { background: #FCA5A5; box-shadow: 0 0 10px #FCA5A5; } }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    @keyframes celebrate { 0% { transform: translateX(-50%) scale(1); } 50% { transform: translateX(-50%) scale(1.3) rotate(10deg); } 100% { transform: translateX(-50%) scale(1) rotate(0); } }
    @keyframes splatPop { 0% { transform: translate(-50%, -50%) scale(0); opacity: 1; } 50% { transform: translate(-50%, -50%) scale(1.5); opacity: 0.8; } 100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; } }
    @keyframes shakeScreen { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 50% { transform: translateX(10px); } 75% { transform: translateX(-10px); } }
    @keyframes ufoExplode { 0% { filter: brightness(1); } 50% { filter: brightness(5) hue-rotate(90deg) scale(1.2); opacity: 1;} 100% { transform: translateX(-50%) scale(0); opacity: 0; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <h3 style="margin: 0; margin-bottom: 20px; color: var(--brand-blue); font-size: 1.5rem;">🛸 Color Rescue</h3>

    <div class="color-board" id="game-board">
        <div class="round-indicator" id="round-indicator">Ronda 1/3</div>

        <div class="tutorial-overlay" id="tutorial-screen">
            <h2 style="color: var(--brand-blue); margin-top: 0; margin-bottom: 10px; font-size: 2rem;" id="tut-title">Misión</h2>
            <div class="tutorial-icon" id="tut-icon">🍎</div>
            <div class="tutorial-word" id="tut-word">RED</div>
            <p style="color: #64748B; font-size: 20px; margin-bottom: 20px; font-weight: 600;" id="tut-trans">(Rojo)</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button class="btn-action" style="background: var(--brand-blue); box-shadow: 0 4px 14px rgba(28, 61, 106, 0.3);" onclick="playLessonAudio()">🔊 Escuchar</button>
                <button class="btn-action" id="btn-start" onclick="startActionPhase()" style="display: none;">▶️ ¡Salvar Dibujo!</button>
            </div>
        </div>

        <div class="css-ufo" id="ufo">
            <div class="ufo-dome"></div><div class="ufo-base"></div>
            <div class="ufo-lights"><div class="ufo-light"></div><div class="ufo-light"></div><div class="ufo-light"></div></div>
            <div class="tractor-beam" id="beam"></div>
        </div>

        <div class="target-canvas" id="canvas-item">
            <span id="canvas-emoji">🍎</span>
            <div class="splat" id="splat-effect"></div>
        </div>

        <div class="paint-station" id="paint-station"></div>
    </div>
</div>

<script>
    let roundsData = window.dynamicRoundsData || <?php echo json_encode($rounds); ?>;
    
    if (window.dynamicRoundsData) {
        const brightColors = ['#EF4444', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#FCD34D'];
        
        roundsData = roundsData.map(r => {
            const randomColorHex = brightColors[Math.floor(Math.random() * brightColors.length)];
            return {
                color_name: r.target_word || r.word,
                phonetic: r.target_word || r.word,
                color_hex: randomColorHex,
                item: r.items ? r.items[0].content : '⭐',
                translation: r.translation,
                context_es: "¡Pinta la palabra antes de que se la lleven!",
                distractors: [
                    { name: 'X', hex: '#334155' },
                    { name: 'Y', hex: '#64748B' }
                ]
            };
        });
    }

    const timeLimit = <?php echo $time_limit; ?>;
    let currentRoundIndex = 0;
    
    const board = document.getElementById('game-board');
    const ufo = document.getElementById('ufo');
    const beam = document.getElementById('beam');
    const canvasItem = document.getElementById('canvas-item');
    const tutorialScreen = document.getElementById('tutorial-screen');
    const splat = document.getElementById('splat-effect');
    
    let gameActive = false;
    let ufoInterval = null;
    let ufoY = -80; 
    const targetY = 200; 
    const step = (targetY - ufoY) / (timeLimit * 10); 

    loadRound(currentRoundIndex);

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        document.getElementById('round-indicator').innerText = `Ronda ${index + 1}/${roundsData.length}`;
        
        document.getElementById('tut-title').innerText = round.context_es || "¡Salva el color!";
        document.getElementById('tut-icon').innerText = round.item;
        document.getElementById('tut-icon').style.color = round.color_hex;
        document.getElementById('tut-word').innerText = round.color_name;
        document.getElementById('tut-word').style.color = round.color_hex;
        document.getElementById('tut-trans').innerText = `(${round.translation})`;
        
        document.getElementById('canvas-emoji').innerText = round.item;
        canvasItem.classList.remove('colored', 'abducted');
        
        ufoY = -80;
        ufo.style.top = ufoY + 'px';
        ufo.style.animation = 'none';
        ufo.style.filter = 'brightness(1)';
        ufo.style.transform = 'translateX(-50%) scale(1)';
        ufo.style.opacity = '1';
        
        beam.style.height = '0px';
        beam.style.display = 'block';

        const station = document.getElementById('paint-station');
        station.innerHTML = '';
        
        let allColors = [...(round.distractors || [])];
        allColors.push({ name: round.color_name, hex: round.color_hex, phonetic: round.phonetic, correct: true });
        allColors.sort(() => Math.random() - 0.5); 

        allColors.forEach(c => {
            const isCorrect = c.correct ? '1' : '0';
            const phoneticToRead = c.phonetic || c.name; 
            station.innerHTML += `
                <div class="paint-bucket" data-correct="${isCorrect}" onclick="shootColor(this, '${c.hex}', '${phoneticToRead}')">
                    <div class="paint-fill" style="background: ${c.hex};"></div>
                </div>
            `;
        });

        tutorialScreen.style.display = 'flex';
        tutorialScreen.style.opacity = '1';
        document.getElementById('btn-start').style.display = 'none';
        
        setTimeout(playLessonAudio, 500);
    }

   function playLessonAudio() {
        document.getElementById('btn-start').style.display = 'block';
        const round = roundsData[currentRoundIndex];
        const textToRead = round.phonetic || round.color_name;
        if(typeof playTTS !== 'undefined') playTTS(textToRead, false);
    }

    function startActionPhase() {
        tutorialScreen.style.opacity = '0';
        setTimeout(() => { tutorialScreen.style.display = 'none'; }, 500);
        gameActive = true;
        
        ufoInterval = setInterval(() => {
            if (!gameActive) return;
            ufoY += step;
            ufo.style.top = ufoY + 'px';
            if(ufoY > 0) beam.style.height = (ufoY + 50) + 'px';
            if (ufoY >= targetY) executeLoss();
        }, 100);
    }

    function shootColor(bucketEl, hexColor, colorPhonetic) {
        if (!gameActive) return;

        if(typeof playTTS !== 'undefined') playTTS(colorPhonetic, false);
        const isCorrect = bucketEl.getAttribute('data-correct') === '1';

        if (isCorrect) {
            gameActive = false;
            clearInterval(ufoInterval);
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();

            splat.style.color = hexColor;
            splat.classList.add('splat-anim');

            setTimeout(() => {
                canvasItem.classList.add('colored');
                beam.style.display = 'none'; 
                ufo.style.animation = 'ufoExplode 0.8s forwards'; 
                
                setTimeout(() => {
                    splat.classList.remove('splat-anim'); 
                    checkNextRound(); 
                }, 1500);
            }, 300);

        } else {
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            board.style.animation = 'shakeScreen 0.4s';
            bucketEl.style.opacity = '0.3'; 
            ufoY += 30; 
            setTimeout(() => { board.style.animation = 'none'; }, 400);
        }
    }

    function checkNextRound() {
        currentRoundIndex++;
        if (currentRoundIndex < roundsData.length) {
            loadRound(currentRoundIndex);
        } else {
            executeWin();
        }
    }

    function executeWin() {
        if(typeof playTTS !== 'undefined') playTTS("Excellent!", false);
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
    }

    function executeLoss() {
        gameActive = false;
        clearInterval(ufoInterval);
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();
        beam.style.background = 'linear-gradient(to bottom, rgba(239, 68, 68, 0.8), rgba(239, 68, 68, 0.1))'; 
        canvasItem.classList.add('abducted');

        setTimeout(() => {
            alert("¡Oh no! El OVNI se robó el dibujo. ¡Inténtalo de nuevo!");
            location.reload();
        }, 1500);
    }
</script>