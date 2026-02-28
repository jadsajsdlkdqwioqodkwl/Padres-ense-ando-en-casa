<?php
// templates/type_matching.php
// Requiere JSON: "pairs": [{"left": "Dog", "right": "🐶", "id": 1}, ...]
$pairs = $lesson_data['pairs'] ?? [
    ['left' => 'Dog', 'right' => '🐶', 'id' => 1],
    ['left' => 'Cat', 'right' => '🐱', 'id' => 2]
];
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<style>
    .match-container { display: flex; justify-content: space-between; gap: 20px; max-width: 600px; margin: 0 auto; }
    .match-col { display: flex; flex-direction: column; gap: 15px; width: 48%; }
    .match-item { 
        background: white; border: 3px solid #ddd; padding: 20px 10px; border-radius: 15px; 
        font-size: 26px; font-weight: bold; cursor: pointer; transition: 0.2s; 
        display: flex; align-items: center; justify-content: center; position: relative;
    }
    .match-item.selected { border-color: var(--accent); background: #fff5f0; transform: scale(1.05); box-shadow: 0 4px 10px rgba(255,127,80,0.2); }
    .match-item.matched { border-color: var(--success); background: #e8f5e9; color: #a5d6a7; pointer-events: none; opacity: 0.6; }
    .mini-audio { position: absolute; left: -15px; top: -10px; background: var(--primary); color: white; border: none; border-radius: 50%; width: 35px; height: 35px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;}
</style>

<div class="game-area text-center">
    <h3>Toca una palabra y luego su dibujo 🔗</h3>
    
    <div class="match-container" style="margin-top:30px;">
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
                <div class="match-item side-right" data-id="<?php echo $pair['id']; ?>" style="font-size: 40px;">
                    <?php echo $pair['right']; ?>
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
            
            // Selección visual
            if (this.classList.contains('side-left')) {
                document.querySelectorAll('.side-left').forEach(el => el.classList.remove('selected'));
                selectedLeft = this; this.classList.add('selected');
                playTTS(this.getAttribute('data-word')); // Lee la palabra al tocarla
            } else {
                document.querySelectorAll('.side-right').forEach(el => el.classList.remove('selected'));
                selectedRight = this; this.classList.add('selected');
            }

            // Comprobar coincidencia
            if (selectedLeft && selectedRight) {
                if (selectedLeft.getAttribute('data-id') === selectedRight.getAttribute('data-id')) {
                    // ¡Correcto!
                    selectedLeft.classList.replace('selected', 'matched');
                    selectedRight.classList.replace('selected', 'matched');
                    selectedLeft = null; selectedRight = null; matches++;
                    
                    if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
                    triggerMascotReaction('correct');

                    if (matches === totalMatches) {
                        if(typeof sfxWin !== 'undefined') sfxWin.play();
                        triggerMascotReaction('win');
                        fireConfetti();
                        unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
                    }
                } else {
                    // Incorrecto
                    if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
                    triggerMascotReaction('wrong');
                    
                    selectedLeft.classList.remove('selected'); selectedLeft.style.borderColor = 'red';
                    selectedRight.classList.remove('selected'); selectedRight.style.borderColor = 'red';
                    
                    setTimeout(() => { 
                        selectedLeft.style.borderColor = '#ddd'; 
                        selectedRight.style.borderColor = '#ddd'; 
                        selectedLeft = null; selectedRight = null; 
                    }, 600);
                }
            }
        });
    });
</script>