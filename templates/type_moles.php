<style>
    /* Altura fluida y flex box asegurado */
    .game-board { position: relative; width: 100%; min-height: 550px; background: linear-gradient(180deg, #38BDF8 0%, #BAE6FD 40%, #86EFAC 40%, #22C55E 100%); border-radius: 24px; overflow: hidden; border: 4px solid var(--brand-blue); margin-bottom: 20px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15); display: flex; flex-direction: column; }
    
    .sky-zone { flex: 0.35; display: flex; justify-content: center; align-items: flex-start; padding-top: 20px; position: relative; }
    .sun { position: absolute; top: 10px; right: 20px; font-size: 50px; animation: spin 10s linear infinite; }
    .target-board { background: var(--white); border: 4px solid var(--brand-blue); padding: 10px 30px; border-radius: 50px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); text-align: center; z-index: 10; width: max-content; }
    
    /* Espacio amplio entre filas de topos para evitar glitch */
    .ground-zone { flex: 0.65; display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); gap: 15px; row-gap: 50px; padding: 20px 10px; }
    .hole-container { position: relative; width: 100%; height: 100%; display: flex; justify-content: center; align-items: flex-end; overflow: hidden; }
    .dirt-hole { position: absolute; bottom: 5px; width: 80%; height: 35px; background: #451A03; border-radius: 50%; box-shadow: inset 0 8px 15px rgba(0,0,0,0.6); z-index: 5; }
    
    .mole { position: absolute; bottom: -100px; width: 70px; height: 95px; background: #A16207; border-radius: 40px 40px 10px 10px; transition: bottom 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding-top: 10px; z-index: 4; box-shadow: inset -5px -5px 10px rgba(0,0,0,0.3); touch-action: manipulation; }
    .mole.active { bottom: 25px; } /* Sale por encima del hueco limpio */
    .mole-face { font-size: 28px; line-height: 1; margin-bottom: 5px; pointer-events: none; }
    .mole-sign { background: var(--white); color: var(--brand-blue); padding: 4px 10px; border-radius: 8px; font-weight: 800; border: 2px solid #713F12; font-size: clamp(12px, 3vw, 14px); box-shadow: 0 4px 6px rgba(0,0,0,0.2); pointer-events: none; }
    
    .score-stars { font-size: 30px; letter-spacing: 5px; color: #E2E8F0; }
    .score-stars.s1 { background: linear-gradient(to right, #FBBF24 33%, #E2E8F0 33%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .score-stars.s2 { background: linear-gradient(to right, #FBBF24 66%, #E2E8F0 66%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .score-stars.s3 { background: #FBBF24; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    @keyframes spin { 100% { transform: rotate(360deg); } }
    @keyframes whack { 0% { transform: scale(1); } 50% { transform: scale(0.8) translateY(20px); filter: brightness(0.5); } 100% { transform: scale(1) translateY(100px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent; padding-top: 5px; box-shadow: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--brand-blue); font-size: 1.8rem;">🔨 Atrapa al Topo</h3>
        <div id="round-counter" style="background: var(--brand-blue); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700;">1/1</div>
    </div>

    <div class="game-board" id="game-board">
        <div class="mission-modal" id="tutorial-modal">
            <h2 style="color: var(--brand-blue); margin-top: 0; font-size: 2.2rem;">¡Los topos traviesos!</h2>
            <p style="color: #64748B; font-size: 18px; margin-bottom: 15px;" id="tut-context">Golpea 3 veces al topo que tenga esta palabra:</p>
            <div style="font-size: 40px; font-weight: 800; color: var(--brand-orange); letter-spacing: 2px;" id="tut-word">WORD</div>
            <p style="color: #94A3B8; font-size: 20px; font-weight: 600; margin-bottom: 10px;" id="tut-trans">(Traducción)</p>
            
            <div class="modal-actions">
                <button class="btn btn-action" id="btn-start" onclick="startGame()" style="display: block;">▶️ ¡Jugar Ahora!</button>
            </div>
        </div>

        <div class="sky-zone">
            <div class="sun">☀️</div>
            <div class="target-board">
                <div style="font-size: 28px; font-weight: 800; color: var(--brand-blue);" id="hud-word">WORD</div>
                <div class="score-stars" id="score-display">★★★</div>
            </div>
        </div>
        
        <div class="ground-zone" id="holes-grid">
            </div>
    </div>
</div>

<script>
    let roundsData = window.dynamicRoundsData || [];
    let currentRoundIndex = 0;
    let roundData = null;
    let gameActive = false;
    let score = 0;
    const maxScore = 3;
    let moleInterval;
    let activeHole = -1;
    let targetWord = "";

    const grid = document.getElementById('holes-grid');
    for(let i=0; i<6; i++) {
        grid.innerHTML += `
            <div class="hole-container">
                <div class="mole" id="mole-${i}" onclick="whackMole(${i})">
                    <div class="mole-face">🐹</div>
                    <div class="mole-sign" id="sign-${i}">...</div>
                </div>
                <div class="dirt-hole"></div>
            </div>
        `;
    }

    if (roundsData.length > 0) loadRound(currentRoundIndex);

    function loadRound(index) {
        roundData = roundsData[index];
        targetWord = roundData.target_word || roundData.word;
        
        document.getElementById('tut-word').innerText = targetWord;
        document.getElementById('tut-trans').innerText = `(${roundData.translation})`;
        document.getElementById('hud-word').innerText = targetWord;
        if(roundData.context_es) document.getElementById('tut-context').innerText = roundData.context_es;
        document.getElementById('round-counter').innerText = `${index + 1}/${roundsData.length}`;
        
        score = 0;
        document.getElementById('score-display').className = 'score-stars';
        if(activeHole !== -1) {
            document.getElementById(`mole-${activeHole}`).classList.remove('active');
            activeHole = -1;
        }

        document.getElementById('tutorial-modal').style.display = 'flex';
        document.getElementById('tutorial-modal').style.opacity = '1';
    }

    function startGame() {
        document.getElementById('tutorial-modal').style.opacity = '0';
        setTimeout(() => document.getElementById('tutorial-modal').style.display = 'none', 300);
        gameActive = true;
        popMoleLoop();
    }

    function popMoleLoop() {
        if(!gameActive) return;
        
        if(activeHole !== -1) {
            let oldMole = document.getElementById(`mole-${activeHole}`);
            oldMole.classList.remove('active');
            oldMole.style.animation = 'none';
        }

        activeHole = Math.floor(Math.random() * 6);
        let isCorrect = Math.random() > 0.4;
        let wordToShow = isCorrect ? targetWord : (roundData.distractors ? roundData.distractors[Math.floor(Math.random() * roundData.distractors.length)] : 'ERR');
        
        document.getElementById(`sign-${activeHole}`).innerText = wordToShow;
        document.getElementById(`sign-${activeHole}`).dataset.correct = isCorrect ? '1' : '0';
        
        let moleEl = document.getElementById(`mole-${activeHole}`);
        moleEl.classList.add('active');
        moleEl.style.pointerEvents = 'auto';

        let stayTime = Math.random() * 800 + 800;
        moleInterval = setTimeout(() => {
            if(gameActive) popMoleLoop();
        }, stayTime);
    }

    function whackMole(holeIndex) {
        if(!gameActive || holeIndex !== activeHole) return;
        
        clearTimeout(moleInterval);
        const sign = document.getElementById(`sign-${holeIndex}`);
        const moleEl = document.getElementById(`mole-${holeIndex}`);
        moleEl.style.pointerEvents = 'none';

        if(sign.dataset.correct === '1') {
            score++;
            moleEl.style.animation = 'whack 0.4s forwards';
            document.getElementById('score-display').className = `score-stars s${score}`;
            
            if(score >= maxScore) {
                setTimeout(checkNextRound, 500);
            } else {
                setTimeout(popMoleLoop, 400);
            }
        } else {
            document.getElementById('game-board').style.boxShadow = "inset 0 0 50px rgba(239, 68, 68, 0.8)";
            setTimeout(() => document.getElementById('game-board').style.boxShadow = "0 15px 35px rgba(28, 61, 106, 0.15)", 300);
            moleEl.classList.remove('active');
            setTimeout(popMoleLoop, 400);
        }
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
        if(typeof fireConfetti !== 'undefined') fireConfetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson_id ?? 0; ?>, 10, <?php echo $lesson['module_id'] ?? 0; ?>);
    }
</script>