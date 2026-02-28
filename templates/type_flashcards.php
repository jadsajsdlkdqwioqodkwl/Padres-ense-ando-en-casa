<?php
$cards = $lesson_data['flashcards'] ?? [
    ['en' => 'Apple', 'es' => 'Manzana', 'ph' => '[ápel]'],
    ['en' => 'Sun', 'es' => 'Sol', 'ph' => '[san]']
];
?>
<style>
    .flashcard-container { perspective: 1000px; width: 100%; max-width: 300px; margin: 0 auto 20px auto; height: 200px; cursor: pointer; }
    .flashcard { position: relative; width: 100%; height: 100%; text-align: center; transition: transform 0.6s; transform-style: preserve-3d; }
    .flashcard.is-flipped { transform: rotateY(180deg); }
    .flashcard-face { position: absolute; width: 100%; height: 100%; -webkit-backface-visibility: hidden; backface-visibility: hidden; border-radius: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .flashcard-front { background-color: var(--primary); color: white; font-size: 32px; font-weight: bold; }
    .flashcard-back { background-color: white; color: var(--primary); transform: rotateY(180deg); border: 4px solid var(--primary); }
</style>

<div class="game-area text-center">
    <h3>Tap the card to flip! 🎴</h3>
    <?php foreach ($cards as $index => $card): ?>
        <div class="flashcard-container" id="fc-<?php echo $index; ?>" style="<?php echo $index > 0 ? 'display:none;' : ''; ?>">
            <div class="flashcard" onclick="this.classList.toggle('is-flipped')">
                <div class="flashcard-front"><?php echo $card['en']; ?></div>
                <div class="flashcard-back">
                    <span style="font-size: 28px; font-weight: bold;"><?php echo $card['es']; ?></span>
                    <span style="color: #d9534f; font-family: monospace; font-size: 20px;"><?php echo $card['ph']; ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <button class="btn btn-restart" id="next-card-btn" style="margin-top:20px;">Next Card 🔄</button>
</div>

<script>
    let currentCard = 0;
    const totalCards = <?php echo count($cards); ?>;
    
    document.getElementById('next-card-btn').addEventListener('click', () => {
        document.getElementById(`fc-${currentCard}`).style.display = 'none';
        currentCard++;
        if (currentCard >= totalCards) {
            // Terminó de verlas todas
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            if(typeof unlockNextButton !== 'undefined') unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $lesson['reward_stars'] ?? 1; ?>);
            currentCard = 0; // Reiniciar por si quiere volver a ver
        }
        document.getElementById(`fc-${currentCard}`).style.display = 'block';
    });
</script>