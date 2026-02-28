<?php
$target_word = $lesson_data['word'] ?? 'APPLE';
$hint_image = $lesson_data['hint'] ?? '🍎';
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<div class="game-area text-center">
    <h3>Type the correct word! ⌨️</h3>
    <div style="font-size: 80px; margin: 20px 0;"><?php echo $hint_image; ?></div>
    
    <input type="text" id="writing-input" autocomplete="off" style="font-size: 30px; padding: 10px; width: 80%; max-width: 300px; text-align: center; border: 3px solid var(--primary); border-radius: 10px; text-transform: uppercase;">
    
    <div style="margin-top: 20px;">
        <button class="btn" style="background:var(--primary); color:white;" onclick="checkWord()">Check Answer</button>
    </div>
    
    <div id="success-msg-write" style="display:none; color:var(--success); font-size:24px; font-weight:bold; margin-top:20px;">
        🎉 Excellent! +<?php echo $reward_stars; ?> Stars! ⭐
    </div>
</div>

<script>
    function checkWord() {
        const inputVal = document.getElementById('writing-input').value.trim().toUpperCase();
        const target = '<?php echo strtoupper($target_word); ?>';
        
        if (inputVal === target) {
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            if(typeof sfxWin !== 'undefined') setTimeout(()=>sfxWin.play(), 500);
            if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('win');
            if(typeof confetti !== 'undefined') confetti();
            
            document.getElementById('writing-input').disabled = true;
            document.getElementById('success-msg-write').style.display = 'block';
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>);
        } else {
            if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
            if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('wrong');
            const inputEl = document.getElementById('writing-input');
            inputEl.style.borderColor = 'red';
            setTimeout(() => inputEl.style.borderColor = 'var(--primary)', 500);
        }
    }
</script>