<?php
$questions = $lesson_data['questions'] ?? [
    ['q' => '¿Cómo se dice "Perro" en inglés?', 'options' => ['Cat', 'Dog', 'Bird'], 'answer' => 'Dog']
];
$reward_stars = $lesson['reward_stars'] ?? 10;
?>
<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>🎓 Examen Final del Módulo 🎓</h3>
    
    <div id="quiz-container" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-top: 20px; border-top: 5px solid var(--primary);">
        <h2 id="question-text" style="font-size: 28px; margin-bottom: 30px; color: var(--primary);"></h2>
        <div id="options-container" style="display: flex; flex-direction: column; gap: 15px;"></div>
    </div>

    <div id="success-msg-exam" style="display:none; color:var(--success); font-size:28px; font-weight:bold; margin-top:30px; animation: popOut 0.5s;">
        🏆 ¡Módulo Completado! +<?php echo $reward_stars; ?> Estrellas ⭐
    </div>
</div>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    let currentQ = 0;

    function loadQuestion() {
        if (currentQ >= questions.length) {
            document.getElementById('quiz-container').style.display = 'none';
            document.getElementById('success-msg-exam').style.display = 'block';
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            fireConfetti();
            unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
            return;
        }

        const q = questions[currentQ];
        document.getElementById('question-text').innerText = q.q;
        const optionsDiv = document.getElementById('options-container');
        optionsDiv.innerHTML = '';

        q.options.forEach(opt => {
            const btn = document.createElement('button');
            btn.innerText = opt;
            btn.style.cssText = 'padding: 15px; font-size: 22px; font-weight: bold; border-radius: 12px; border: 2px solid #e0e6ed; background: #fafafa; cursor: pointer; transition: 0.2s; color: #333;';
            btn.onclick = () => checkAnswer(btn, opt, q.answer);
            optionsDiv.appendChild(btn);
        });
    }

    function checkAnswer(btn, selected, correct) {
        if (selected === correct) {
            btn.style.background = 'var(--success)'; btn.style.color = 'white'; btn.style.borderColor = 'var(--success)';
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            setTimeout(() => { currentQ++; loadQuestion(); }, 1000);
        } else {
            btn.style.background = '#ff4757'; btn.style.color = 'white'; btn.style.borderColor = '#ff4757';
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            setTimeout(() => { btn.style.background = '#fafafa'; btn.style.color = '#333'; btn.style.borderColor = '#e0e6ed'; }, 800);
        }
    }
    loadQuestion();
</script>