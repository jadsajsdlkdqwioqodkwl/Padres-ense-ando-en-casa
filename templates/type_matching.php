<?php
$pairs = $lesson_data['pairs'] ?? [
    ['left' => 'Dog', 'right_img' => 'https://api.iconify.design/noto:dog-face.svg', 'id' => 1],
    ['left' => 'Cat', 'right_img' => 'https://api.iconify.design/noto:cat-face.svg', 'id' => 2]
];
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<style>
    .match-container { display: flex; justify-content: space-between; gap: 20px; max-width: 600px; margin: 0 auto; padding-top: 20px;}
    .match-col { display: flex; flex-direction: column; gap: 25px; width: 48%; }
    
    .match-item { 
        background: white; border: 4px solid #e0e6ed; padding: 20px 10px; border-radius: 20px; 
        font-size: 28px; font-weight: bold; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
        display: flex; align-items: center; justify-content: center; position: relative;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        animation: floatCard 4s ease-in-out infinite;
    }
    
    /* Desfase de animación para que no floten todas igual */
    .match-item:nth-child(even) { animation-delay: -2s; }
    
    .match-item:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 15px 25px rgba(0,0,0,0.1); }
    .match-item.selected { border-color: var(--accent); background: #fff5f0; transform: scale(1.1); box-shadow: 0 0 20px rgba(255,127,80,0.4); z-index: 10;}
    
    /* Animación de Match Correcto */
    .match-item.matched { 
        animation: popOut 0.6s forwards; 
        pointer-events: none; 
    }
    
    /* Animación de Error */
    .match-item.error-shake { animation: shake 0.5s; border-color: #ff4757; background: #ffeaa7; color: #ff4757;}

    .mini-audio { position: absolute; left: -15px; top: -15px; background: var(--primary); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0,0,0,0.2);}

    @keyframes floatCard { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    @keyframes popOut { 
        0% { transform: scale(1.1); background: var(--success); border-color: var(--success); color: white; opacity: 1;} 
        50% { transform: scale(1.3); opacity: 0.8; }
        100% { transform: scale(0); opacity: 0; display: none; }
    }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 50% { transform: translateX(10px); } 75% { transform: translateX(-10px); } }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>✨ Toca la palabra y luego su dibujo ✨</h3>
    
    <div class="match-container">
        <div class="match-col">
            <?php foreach ($pairs as $pair): ?>
                <div class="match-item side-left" data-id="<?php echo $pair['id']; ?>" data-word="<?php echo htmlspecialchars($pair['left']); ?>">
                    <button class="mini-audio" onclick="event.stopPropagation(); playTTS('<?php echo addslashes($pair['left']); ?>')">🔊</button>
                    <?php echo $pair['left']; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="match-col">
            <?php 
            $shuffled = $pairs; shuffle($shuffled); 
            foreach ($shuffled as $pair): 
            ?>
                <div class="match-item side-right" data-id="<?php echo $pair['id']; ?>">
                    <img src="<?php echo $pair['right_img']; ?>" style="width: 70px; height: 70px;" alt="dibujo">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    let selectedLeft = null; let selectedRight = null; let matches = 0;
    const totalMatches = <?php echo count($pairs); ?>;

    document.querySelectorAll('.match-item').forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('matched')) return;
            
            if (this.classList.contains('side-left')) {
                document.querySelectorAll('.side-left').forEach(el => el.classList.remove('selected'));
                selectedLeft = this; this.classList.add('selected');
                playTTS(this.getAttribute('data-word')); 
            } else {
                document.querySelectorAll('.side-right').forEach(el => el.classList.remove('selected'));
                selectedRight = this; this.classList.add('selected');
            }

            if (selectedLeft && selectedRight) {
                if (selectedLeft.getAttribute('data-id') === selectedRight.getAttribute('data-id')) {
                    // Match Correcto!
                    selectedLeft.classList.add('matched');
                    selectedRight.classList.add('matched');
                    selectedLeft = null; selectedRight = null; matches++;
                    
                    if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
                    triggerMascotReaction('correct');

                    if (matches === totalMatches) {
                        setTimeout(() => {
                            if(typeof sfxWin !== 'undefined') sfxWin.play();
                            triggerMascotReaction('win');
                            fireConfetti();
                            unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
                        }, 600);
                    }
                } else {
                    // Error!
                    if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
                    triggerMascotReaction('wrong');
                    
                    const tempLeft = selectedLeft; const tempRight = selectedRight;
                    tempLeft.classList.add('error-shake'); tempRight.classList.add('error-shake');
                    
                    setTimeout(() => { 
                        tempLeft.classList.remove('selected', 'error-shake'); 
                        tempRight.classList.remove('selected', 'error-shake'); 
                    }, 500);
                    selectedLeft = null; selectedRight = null;
                }
            }
        });
    });
</script>