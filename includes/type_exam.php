<?php
// templates/type_exam.php
$questions = $lesson_data['questions'] ?? [
    ['q' => '¿Cómo se dice "Perro" en inglés?', 'options' => ['Cat', 'Dog', 'Bird'], 'answer' => 'Dog'],
    ['q' => '¿Qué significa "Apple"?', 'options' => ['Manzana', 'Pera', 'Plátano'], 'answer' => 'Manzana']
];
$reward_stars = $lesson['reward_stars'] ?? 10;
?>
<div class="game-area text-center">
    <h3>📝 Examen Final del Módulo</h3>
    
    <div id="quiz-container" style="background: white; padding: 30px; border-radius: 15px; border: 3px solid var(--primary); margin-top: 20px;">
        <h2 id="question-text" style="font-size: 28px; margin-bottom: 30px;"></h2>
        <div id="options-container" style="display: flex; flex-direction: column; gap: 15px;"></div>
    </div>

    <div id="success-msg-exam" style="display:none; color:var(--success); font-size:24px; font-weight:bold; margin-top:20px;">
        🎓 ¡Módulo Completado! +<?php echo $reward_stars; ?> Estrellas ⭐
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
            triggerMascotReaction('win');
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
            btn.style.cssText = 'padding: 15px; font-size: 20px; font-weight: bold; border-radius: 10px; border: 2px solid #ccc; background: #fafafa; cursor: pointer; transition: 0.2s;';
            btn.onclick = () => checkAnswer(btn, opt, q.answer);
            optionsDiv.appendChild(btn);
        });
    }

    function checkAnswer(btn, selected, correct) {
        if (selected === correct) {
            btn.style.background = 'var(--success)'; btn.style.color = 'white'; btn.style.borderColor = 'var(--success)';
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            triggerMascotReaction('correct');
            setTimeout(() => { currentQ++; loadQuestion(); }, 1000);
        } else {
            btn.style.background = 'red'; btn.style.color = 'white'; btn.style.borderColor = 'red';
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            triggerMascotReaction('wrong');
            setTimeout(() => { btn.style.background = '#fafafa'; btn.style.color = 'black'; btn.style.borderColor = '#ccc'; }, 800);
        }
    }

    loadQuestion(); // Iniciar quiz
</script>