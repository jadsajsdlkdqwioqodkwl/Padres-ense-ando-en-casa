<?php
$vocab = $lesson_data['vocabulary'] ?? [
    ['en' => 'Mom', 'es' => 'Mamá', 'ph' => '[MAM]'],
    ['en' => 'Dad', 'es' => 'Papá', 'ph' => '[DAD]']
];
?>
<div class="vocabulary-section">
    <div class="text-center" style="margin-bottom:20px;">
        <h3>📖 Read and Learn</h3>
        <p>Repasen este vocabulario juntos. ¡Haz clic en "Finish" al final para reclamar tus estrellas!</p>
    </div>
    
    <div class="vocab-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:15px;">
        <?php foreach ($vocab as $item): ?>
            <div style="background:white; border:2px solid #eee; padding:20px; text-align:center; border-radius:10px;">
                <span style="display:block; font-size:24px; font-weight:bold; color:var(--primary);"><?php echo $item['en']; ?></span>
                <span style="display:block; color:#d9534f; font-family:monospace; margin:5px 0;"><?php echo $item['ph']; ?></span>
                <span style="display:block; color:#666;"><?php echo $item['es']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center" style="margin-top: 30px;">
        <button class="btn" style="background:var(--success); color:white;" onclick="finishVocab()">✅ I finished reading!</button>
    </div>
</div>

<script>
    function finishVocab() {
        if(typeof sfxWin !== 'undefined') sfxWin.play();
        if(typeof confetti !== 'undefined') confetti();
        if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $lesson['reward_stars'] ?? 2; ?>);
    }
</script>