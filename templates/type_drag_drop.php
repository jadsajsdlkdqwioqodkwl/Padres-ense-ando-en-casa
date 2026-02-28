<?php
$items = $lesson_data['items'] ?? [
    ['word' => 'EYES', 'img' => 'https://api.iconify.design/noto:eyes.svg'],
    ['word' => 'NOSE', 'img' => 'https://api.iconify.design/noto:nose.svg']
];
$reward_stars = $lesson['reward_stars'] ?? 3;
$words = array_column($items, 'word');
shuffle($words);
?>
<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>🖱️ ¡Arrastra la palabra a su dibujo! 🖱️</h3>
    
    <div style="display: flex; gap: 30px; justify-content: center; margin-top: 30px; flex-wrap: wrap;">
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($items as $item): ?>
                <div style="display: flex; align-items: center; gap: 15px; background: white; padding: 15px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <img src="<?php echo $item['img']; ?>" style="width: 60px; height: 60px;" alt="dibujo">
                    <div class="drop-zone" data-match="<?php echo $item['word']; ?>" 
                         style="width: 140px; height: 50px; border: 3px dashed #ccc; border-radius: 10px; display: flex; justify-content: center; align-items: center; background: #fafafa; font-weight: bold; color: #aaa; transition: 0.3s;">
                        Soltar aquí
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($words as $word): ?>
                <div class="drag-item" draggable="true" data-word="<?php echo $word; ?>" 
                     style="width: 140px; height: 50px; display: flex; justify-content: center; align-items: center; background: var(--accent); color: white; border-radius: 10px; font-weight: bold; font-size: 20px; cursor: grab; box-shadow: 0 4px 6px rgba(0,0,0,0.1); user-select: none;">
                    <?php echo htmlspecialchars($word); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="success-msg" style="display:none; color:var(--success); font-size:24px; font-weight:bold; margin-top:30px; animation: popOut 0.5s;">
        🎉 ¡Perfecto! +<?php echo $reward_stars; ?> Estrellas ⭐
    </div>
</div>

<script>
    const draggables = document.querySelectorAll('.drag-item');
    const dropZones = document.querySelectorAll('.drop-zone');
    let matchesCount = 0; const totalMatches = <?php echo count($items); ?>;

    draggables.forEach(d => { d.addEventListener('dragstart', () => d.classList.add('dragging')); d.addEventListener('dragend', () => d.classList.remove('dragging')); });

    dropZones.forEach(zone => {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--accent)'; });
        zone.addEventListener('dragleave', () => zone.style.borderColor = '#ccc');
        zone.addEventListener('drop', e => {
            e.preventDefault(); const dragged = document.querySelector('.dragging');
            if (dragged && dragged.getAttribute('data-word') === zone.getAttribute('data-match')) {
                if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime = 0; sfxCorrect.play(); }
                zone.innerHTML = ''; zone.appendChild(dragged);
                dragged.setAttribute('draggable', 'false'); dragged.style.boxShadow = 'none'; dragged.style.cursor = 'default';
                zone.style.borderColor = 'var(--success)'; zone.style.background = 'white';
                matchesCount++;
                if (matchesCount === totalMatches) {
                    if(typeof sfxWin !== 'undefined') sfxWin.play();
                    fireConfetti(); document.getElementById('success-msg').style.display = 'block';
                    unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
                }
            } else {
                if(typeof sfxWrong !== 'undefined') { sfxWrong.currentTime = 0; sfxWrong.play(); }
                zone.style.borderColor = 'red'; setTimeout(() => zone.style.borderColor = '#ccc', 500);
            }
        });
    });
</script>