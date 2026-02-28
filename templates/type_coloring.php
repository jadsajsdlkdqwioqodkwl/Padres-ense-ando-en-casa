<?php
$colors = $lesson_data['colors'] ?? ['#FF7F50', '#4CAF50', '#FFD700', '#2B3A67'];
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<div class="game-area text-center">
    <h3>¡Elige un color y toca para pintar! 🎨</h3>
    <div style="display:flex; justify-content:center; gap:15px; margin:20px 0;">
        <?php foreach ($colors as $index => $color): ?>
            <div class="color-swatch" style="background:<?php echo $color; ?>; width:50px; height:50px; border-radius:50%; cursor:pointer; border:4px solid <?php echo $index === 0 ? '#333' : 'transparent'; ?>;" data-color="<?php echo $color; ?>"></div>
        <?php endforeach; ?>
    </div>
    <svg viewBox="0 0 100 100" style="width:250px; height:250px; background:white; border-radius:10px; border:2px dashed #ccc; cursor:crosshair;">
        <path class="paintable" d="M50 5 L61 35 L95 35 L67 55 L78 85 L50 65 L22 85 L33 55 L5 35 L39 35 Z" fill="#FFFFFF" stroke="#333" stroke-width="2"/>
    </svg>
    <div id="success-msg-color" style="display:none; color:var(--success); font-size:24px; font-weight:bold; margin-top:20px;">
        🎉 Beautiful! +<?php echo $reward_stars; ?> Stars! ⭐
    </div>
</div>

<script>
    let currentColor = '<?php echo $colors[0]; ?>';
    const swatches = document.querySelectorAll('.color-swatch');
    const paintables = document.querySelectorAll('.paintable');
    let partsPainted = 0;

    swatches.forEach(swatch => {
        swatch.addEventListener('click', () => {
            swatches.forEach(s => s.style.borderColor = 'transparent');
            swatch.style.borderColor = '#333';
            currentColor = swatch.getAttribute('data-color');
        });
    });

    paintables.forEach(path => {
        path.addEventListener('click', function() {
            if (this.getAttribute('fill') === '#FFFFFF') partsPainted++;
            this.setAttribute('fill', currentColor);
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime=0; sfxCorrect.play(); }
            if (partsPainted === paintables.length) {
                if(typeof sfxWin !== 'undefined') sfxWin.play();
                if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('win');
                if(typeof confetti !== 'undefined') confetti();
                document.getElementById('success-msg-color').style.display = 'block';
                if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>);
            }
        });
    });
</script>