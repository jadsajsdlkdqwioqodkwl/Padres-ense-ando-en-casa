<?php
$target_word = $lesson_data['word'] ?? 'APPLE';
$hint_img = $lesson_data['hint_img'] ?? 'https://api.iconify.design/noto:red-apple.svg';
$story_en = $lesson_data['story_en'] ?? 'I have a red APPLE.';
$story_es = $lesson_data['story_es'] ?? 'Tengo una MANZANA roja.';
$reward_stars = $lesson['reward_stars'] ?? 4;
?>
<style>
    .story-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 20px; position: relative; border-left: 6px solid var(--accent); }
    .story-en { font-size: 28px; font-weight: bold; color: var(--primary); margin-bottom: 10px; line-height: 1.4; }
    .story-es { font-size: 18px; color: #666; font-style: italic; }
    .highlight-word { color: var(--accent); background: #fff5f0; padding: 2px 8px; border-radius: 8px; border: 2px dashed var(--accent); }
    .writing-mode { display: none; animation: popOut 0.5s; }
    @keyframes popOut { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    
    <div id="reading-phase">
        <h3>📖 Hora de leer</h3>
        <div class="story-card">
            <button class="btn" style="position: absolute; top: -20px; right: -20px; background: var(--primary); color: white; border-radius: 50%; width: 50px; height: 50px; font-size: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);" onclick="playTTS('<?php echo addslashes($story_en); ?>')">🔊</button>
            <img src="<?php echo $hint_img; ?>" style="width: 100px; height: 100px; margin-bottom: 20px; animation: floatCard 3s infinite;" alt="dibujo">
            
            <div class="story-en">
                <?php echo str_ireplace($target_word, "<span class='highlight-word'>".strtoupper($target_word)."</span>", $story_en); ?>
            </div>
            <div class="story-es"><?php echo $story_es; ?></div>
        </div>
        <button class="btn" style="background: var(--success); color: white; font-size: 22px; width: 100%; max-width: 300px;" onclick="startWritingPhase()">¡A Escribir! ⌨️</button>
    </div>

    <div id="writing-phase" class="writing-mode">
        <h3>⌨️ ¡Escribe la palabra secreta!</h3>
        <div style="font-size: 24px; color: #666; margin-bottom: 20px;">¿Cómo se escribe <span style="color: var(--primary); font-weight: bold;"><?php echo $story_es; ?></span> en inglés?</div>
        
        <input type="text" id="writing-input" autocomplete="off" style="font-size: 40px; padding: 15px; width: 80%; max-width: 350px; text-align: center; border: 4px solid var(--primary); border-radius: 15px; text-transform: uppercase; font-weight: bold; color: var(--primary); box-shadow: inset 0 4px 6px rgba(0,0,0,0.05);" placeholder="???">
        
        <div style="margin-top: 30px;">
            <button class="btn" style="background:var(--primary); color:white; font-size: 22px; width: 100%; max-width: 300px;" onclick="checkWord()">Revisar 🔍</button>
        </div>
        
        <div id="success-msg-write" style="display:none; color:var(--success); font-size:28px; font-weight:bold; margin-top:30px;">
            🎉 ¡Excelente! +<?php echo $reward_stars; ?> Estrellas ⭐
        </div>
    </div>
</div>

<script>
    function startWritingPhase() {
        document.getElementById('reading-phase').style.display = 'none';
        document.getElementById('writing-phase').style.display = 'block';
        setTimeout(() => document.getElementById('writing-input').focus(), 100);
    }

    function checkWord() {
        const inputVal = document.getElementById('writing-input').value.trim().toUpperCase();
        const target = '<?php echo strtoupper($target_word); ?>';
        
        if (inputVal === target) {
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            setTimeout(()=> { if(typeof sfxWin !== 'undefined') sfxWin.play(); }, 500);
            fireConfetti();
            
            document.getElementById('writing-input').disabled = true;
            document.getElementById('writing-input').style.borderColor = 'var(--success)';
            document.getElementById('success-msg-write').style.display = 'block';
            unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            const inputEl = document.getElementById('writing-input');
            inputEl.style.borderColor = '#ff4757';
            inputEl.style.animation = 'shake 0.5s';
            setTimeout(() => { 
                inputEl.style.borderColor = 'var(--primary)'; 
                inputEl.style.animation = 'none'; 
            }, 600);
        }
    }
</script>