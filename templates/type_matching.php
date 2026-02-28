<?php
// Pares por defecto si el JSON está vacío
$pairs = $lesson_data['pairs'] ?? [
    ['left' => 'Dog', 'right' => '🐶', 'id' => 1],
    ['left' => 'Cat', 'right' => '🐱', 'id' => 2],
    ['left' => 'Pig', 'right' => '🐷', 'id' => 3]
];
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<style>
    .match-col { display: flex; flex-direction: column; gap: 15px; width: 45%; }
    .match-item { background: white; border: 2px solid #ccc; padding: 15px; border-radius: 10px; font-size: 24px; font-weight: bold; cursor: pointer; transition: 0.2s; user-select: none; }
    .match-item.selected { border-color: var(--accent); background: #ffeae0; transform: scale(1.05); }
    .match-item.matched { border-color: var(--success); background: #e8f5e9; color: #aaa; pointer-events: none; }
</style>

<div class="game-area text-center">
    <h3>Tap a word, then tap its match! 🔗</h3>
    <div style="display:flex; justify-content:space-between; margin-top:20px;">
        <div class="match-col" id="col-left">
            <?php foreach ($pairs as $pair): ?>
                <div class="match-item side-left" data-id="<?php echo $pair['id']; ?>"><?php echo $pair['left']; ?></div>
            <?php endforeach; ?>
        </div>
        <div class="match-col" id="col-right">
            <?php 
            $shuffled_right = $pairs; shuffle($shuffled_right); // Desordenar la columna derecha
            foreach ($shuffled_right as $pair): ?>
                <div class="match-item side-right" data-id="<?php echo $pair['id']; ?>"><?php echo $pair['right']; ?></div>
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
            
            // Lógica de selección
            if (this.classList.contains('side-left')) {
                document.querySelectorAll('.side-left').forEach(el => el.classList.remove('selected'));
                selectedLeft = this; this.classList.add('selected');
            } else {
                document.querySelectorAll('.side-right').forEach(el => el.classList.remove('selected'));
                selectedRight = this; this.classList.add('selected');
            }

            // Validar pareja
            if (selectedLeft && selectedRight) {
                if (selectedLeft.getAttribute('data-id') === selectedRight.getAttribute('data-id')) {
                    // Match!
                    selectedLeft.classList.replace('selected', 'matched');
                    selectedRight.classList.replace('selected', 'matched');
                    selectedLeft = null; selectedRight = null; matches++;
                    if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
                    
                    if (matches === totalMatches) {
                        if(typeof sfxWin !== 'undefined') sfxWin.play();
                        if(typeof confetti !== 'undefined') confetti();
                        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>);
                    }
                } else {
                    // Error
                    if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime=0; sfxWrong.play(); }
                    selectedLeft.classList.remove('selected'); selectedLeft.style.borderColor = 'red';
                    selectedRight.classList.remove('selected'); selectedRight.style.borderColor = 'red';
                    setTimeout(() => { selectedLeft.style.borderColor = '#ccc'; selectedRight.style.borderColor = '#ccc'; selectedLeft = null; selectedRight = null; }, 500);
                }
            }
        });
    });
</script>