<?php
// Recibimos variables del JSON, o usamos valores por defecto para evitar errores
$words = $lesson_data['words'] ?? ['NOSE', 'MOUTH', 'EYES'];
$match_ids = $lesson_data['match_ids'] ?? ['drop-eyes', 'drop-nose', 'drop-mouth'];
$reward_stars = $lesson['reward_stars'] ?? 3;
?>
<div class="game-area">
    <h3>Drag the words to the correct place! 🖱️</h3>
    <div class="draggable-items">
        <?php foreach ($words as $word): ?>
            <div class="drag-item" draggable="true" id="drag-<?php echo strtolower($word); ?>">
                <?php echo htmlspecialchars($word); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="face-board" style="position:relative; width:300px; height:350px; border:6px solid var(--primary); border-radius:50% 50% 45% 45%; margin:0 auto; background:white;">
        <div class="drop-zone" id="drop-eyes" data-match="drag-eyes" style="position:absolute; top:20%; left:15%; width:70%; height:60px; border:2px dashed #999; border-radius:30px; display:flex; justify-content:center; align-items:center; color:#999;">Drop Eyes</div>
        <div class="drop-zone" id="drop-nose" data-match="drag-nose" style="position:absolute; top:45%; left:35%; width:30%; height:60px; border:2px dashed #999; border-radius:50%; display:flex; justify-content:center; align-items:center; color:#999;">Nose</div>
        <div class="drop-zone" id="drop-mouth" data-match="drag-mouth" style="position:absolute; bottom:15%; left:25%; width:50%; height:60px; border:2px dashed #999; border-radius:30px; display:flex; justify-content:center; align-items:center; color:#999;">Mouth</div>
    </div>
    <div class="success-msg" id="success-msg" style="display:none; color:var(--success); font-size:24px; font-weight:bold; margin-top:20px;">
        🎉 Perfect! +<?php echo $reward_stars; ?> Stars! ⭐
    </div>
</div>

<script>
    const draggables = document.querySelectorAll('.drag-item');
    const dropZones = document.querySelectorAll('.drop-zone');
    let matchesCount = 0;

    draggables.forEach(d => {
        d.addEventListener('dragstart', () => d.classList.add('dragging'));
        d.addEventListener('dragend', () => d.classList.remove('dragging'));
    });

    dropZones.forEach(zone => {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--accent)'; });
        zone.addEventListener('dragleave', () => zone.style.borderColor = '#999');
        zone.addEventListener('drop', e => {
            e.preventDefault();
            const dragged = document.querySelector('.dragging');
            if (dragged && dragged.id === zone.getAttribute('data-match')) {
                if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime = 0; sfxCorrect.play(); }
                if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('correct');
                zone.innerHTML = ''; zone.appendChild(dragged);
                dragged.setAttribute('draggable', 'false'); dragged.style.cursor = 'default';
                zone.style.borderColor = 'var(--success)';
                matchesCount++;
                if (matchesCount === 3) {
                    if(typeof sfxWin !== 'undefined') sfxWin.play();
                    if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('win');
                    if(typeof confetti !== 'undefined') confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } });
                    document.getElementById('success-msg').style.display = 'block';
                    if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>);
                }
            } else {
                if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime = 0; sfxWrong.play(); }
                if(typeof triggerMascotReaction !== 'undefined') triggerMascotReaction('wrong');
                zone.style.borderColor = 'red'; setTimeout(() => zone.style.borderColor = '#999', 500);
            }
        });
    });
</script>