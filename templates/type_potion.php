<style>
    .potion-board { position: relative; width: 100%; height: 420px; background: #2c3e50; border-radius: 20px; overflow: hidden; border: 4px solid var(--primary); margin-bottom: 20px; box-shadow: inset 0 0 50px rgba(0,0,0,0.8); display: flex; flex-direction: column; justify-content: space-between; }
    
    .shelves { display: flex; justify-content: space-around; padding: 20px; background: rgba(0,0,0,0.3); border-bottom: 5px solid #34495e; min-height: 120px; }
    .ingredient { cursor: pointer; display: flex; flex-direction: column; align-items: center; transition: 0.3s; z-index: 20; }
    .ingredient:active { transform: scale(0.9); }
    .bottle-icon { font-size: 45px; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.5)); }
    .bottle-label { background: white; color: var(--dark); padding: 2px 8px; border-radius: 5px; font-weight: bold; font-size: 14px; margin-top: 5px; border: 2px solid var(--primary); }
    
    .cauldron-area { flex: 1; display: flex; justify-content: center; align-items: flex-end; padding-bottom: 20px; position: relative; }
    .cauldron { font-size: 100px; z-index: 10; transition: 0.3s; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.8)); }
    .cauldron.bubbling { animation: shake 0.5s infinite; filter: drop-shadow(0 0 30px #2ed573); }
    .cauldron.error { animation: shake 0.2s infinite; filter: drop-shadow(0 0 30px #e74c3c); }
    
    .mission-modal { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 15px; padding: 20px; text-align: center; }
    .btn-action { background: var(--success); color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 30px; cursor: pointer; margin-top: 15px; }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px;">
    <h3 style="margin: 0; margin-bottom: 15px; color: var(--primary);">🧪 Poción Mágica</h3>

    <div class="potion-board" id="game-board">
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--primary); margin-top: 0;">📜 La Receta Secreta</h2>
            <p style="color: var(--text-muted); font-size: 18px;">Añade al caldero el ingrediente que significa:</p>
            <div style="font-size: 30px; font-weight: bold; color: var(--accent); margin: 15px 0;" id="tut-trans">(Traducción)</div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button class="btn-action" style="background: var(--primary);" onclick="playSpanglishIntro()">🔊 Escuchar en Inglés</button>
                <button class="btn-action" id="btn-start" onclick="startGame()" style="display: none;">▶️ ¡Cocinar!</button>
            </div>
        </div>

        <div class="shelves" id="ingredients-shelf"></div>
        <div class="cauldron-area">
            <div class="cauldron" id="cauldron">🍲</div>
        </div>
    </div>
</div>

<script>
    let roundsData = window.dynamicRoundsData;
    let currentRoundIndex = 0;
    let gameActive = false;
    
    const cauldron = document.getElementById('cauldron');
    const shelf = document.getElementById('ingredients-shelf');

    loadRound(currentRoundIndex);

    function loadRound(index) {
        if (!roundsData || !roundsData[index]) return;
        const round = roundsData[index];
        cauldron.className = 'cauldron';
        
        document.getElementById('tut-trans').innerText = `"${round.translation}"`;

        shelf.innerHTML = '';
        let allWords = [round.target_word, ...round.distractors].sort(() => Math.random() - 0.5);
        const bottleColors = ['🧪','🧫','🏺','🍷'];
        
        allWords.forEach((word, i) => {
            let el = document.createElement('div');
            el.className = 'ingredient';
            el.innerHTML = `<div class="bottle-icon">${bottleColors[i%4]}</div><div class="bottle-label">${word}</div>`;
            el.onclick = () => addIngredient(el, word, round.target_word);
            shelf.appendChild(el);
        });

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('btn-start').style.display = 'none';
    }

    function playSpanglishIntro() {
        document.getElementById('btn-start').style.display = 'block';
        if(typeof playTTS !== 'undefined') playTTS(roundsData[currentRoundIndex].target_word, false);
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.display = 'none';
        gameActive = true;
    }

    function addIngredient(el, clickedWord, targetWord) {
        if (!gameActive) return;
        if(typeof playTTS !== 'undefined') playTTS(clickedWord, false);

        el.style.transform = "translateY(150px) scale(0.5)";
        el.style.opacity = "0";

        setTimeout(() => {
            if (clickedWord === targetWord) {
                gameActive = false;
                cauldron.classList.add('bubbling');
                if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();
                setTimeout(() => {
                    currentRoundIndex++;
                    if (currentRoundIndex < roundsData.length) { loadRound(currentRoundIndex); } 
                    else { executeWin(); }
                }, 1500);
            } else {
                if(typeof sfxWrong !== 'undefined') sfxWrong.play();
                cauldron.classList.add('error');
                setTimeout(() => {
                    cauldron.classList.remove('error');
                    el.style.transform = "translateY(0) scale(1)";
                    el.style.opacity = "1";
                }, 800);
            }
        }, 300);
    }

    function executeWin() {
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id; ?>, 10, <?php echo $lesson['module_id']; ?>);
    }
</script>