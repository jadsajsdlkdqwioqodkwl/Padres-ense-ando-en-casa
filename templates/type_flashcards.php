<?php
// templates/type_flashcards.php
// Requiere JSON: "flashcards": [{"en": "Apple", "es": "Manzana", "ph": "[ápel]"}, ...]
$cards = $lesson_data['flashcards'] ?? [
    ['en' => 'Mom', 'es' => 'Mamá', 'ph' => '[mam]'],
    ['en' => 'Dad', 'es' => 'Papá', 'ph' => '[dad]']
];
$reward_stars = $lesson['reward_stars'] ?? 2;
?>
<style>
    .flashcard-container { perspective: 1000px; width: 100%; max-width: 350px; margin: 0 auto 20px auto; height: 250px; cursor: pointer; }
    .flashcard { position: relative; width: 100%; height: 100%; text-align: center; transition: transform 0.6s; transform-style: preserve-3d; }
    .flashcard.is-flipped { transform: rotateY(180deg); }
    .flashcard-face { position: absolute; width: 100%; height: 100%; -webkit-backface-visibility: hidden; backface-visibility: hidden; border-radius: 20px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
    .flashcard-front { background-color: var(--primary); color: white; font-size: 40px; font-weight: bold; }
    .flashcard-back { background-color: white; color: #333; transform: rotateY(180deg); border: 4px solid var(--primary); }
    .audio-btn-card { margin-top: 15px; background: #f0f0f0; border: none; padding: 10px 20px; border-radius: 30px; font-size: 16px; cursor: pointer; font-weight: bold; }
</style>

<div class="game-area text-center">
    <h3>Toca la tarjeta para voltearla 🎴</h3>
    
    <?php foreach ($cards as $index => $card): ?>
        <div class="flashcard-wrapper" id="fc-<?php echo $index; ?>" style="<?php echo $index > 0 ? 'display:none;' : ''; ?>">
            <div class="flashcard-container" onclick="this.querySelector('.flashcard').classList.toggle('is-flipped')">
                <div class="flashcard">
                    <div class="flashcard-front">
                        <?php echo $card['en']; ?>
                        <div style="font-size: 20px; color: #ffd700; margin-top: 10px; font-weight:normal;">Toca para voltear 🔄</div>
                    </div>
                    <div class="flashcard-back">
                        <span style="font-size: 35px; font-weight: bold; color: var(--primary);"><?php echo $card['es']; ?></span>
                        <span style="color: #d9534f; font-family: monospace; font-size: 24px; margin-top:10px;">Pronuncia: <?php echo $card['ph']; ?></span>
                    </div>
                </div>
            </div>
            <button class="audio-btn-card" onclick="playTTS('<?php echo addslashes($card['en']); ?>')">🔊 Escuchar pronunciación</button>
        </div>
    <?php endforeach; ?>
    
    <div style="margin-top: 30px;">
        <button class="btn" id="next-card-btn" style="background: var(--accent); color:white; width: 100%; max-width: 350px;">Siguiente Tarjeta ➡️</button>
    </div>
</div>

<script>
    let currentCard = 0;
    const totalCards = <?php echo count($cards); ?>;
    
    document.getElementById('next-card-btn').addEventListener('click', () => {
        document.getElementById(`fc-${currentCard}`).style.display = 'none';
        currentCard++;
        if (currentCard >= totalCards) {
            // Ya vio todas
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            triggerMascotReaction('win');
            fireConfetti();
            document.getElementById('next-card-btn').style.display = 'none';
            unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
            currentCard = 0; // Mostrar la primera por si quiere repasar
        }
        document.getElementById(`fc-${currentCard}`).style.display = 'block';
    });
</script>