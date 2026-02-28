<?php
$cards = $lesson_data['flashcards'] ?? [
    ['en' => 'Mom', 'es' => 'Mamá', 'ph' => '[mam]', 'img' => 'https://api.iconify.design/noto:woman.svg']
];
$reward_stars = $lesson['reward_stars'] ?? 2;
?>
<style>
    /* Efectos 3D y Contenedor */
    .flashcard-scene {
        width: 100%; max-width: 380px; height: 320px; margin: 30px auto;
        perspective: 1200px; /* Profundidad 3D */
    }
    
    .magic-card {
        width: 100%; height: 100%; position: relative;
        transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efecto rebote */
        transform-style: preserve-3d; cursor: pointer;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1); border-radius: 25px;
    }
    
    .magic-card.is-flipped { transform: rotateY(180deg); box-shadow: 0 15px 35px rgba(255,127,80,0.3); }
    
    .card-face {
        position: absolute; width: 100%; height: 100%;
        -webkit-backface-visibility: hidden; backface-visibility: hidden;
        border-radius: 25px; display: flex; flex-direction: column; 
        justify-content: center; align-items: center; padding: 20px;
        background: white; border: 4px solid var(--primary);
    }
    
    /* Cara Frontal (Inglés + Imagen) */
    .card-front { background: linear-gradient(135deg, var(--primary), #4A5D96); color: white; border: none; }
    .card-front img { width: 100px; height: 100px; margin-bottom: 20px; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.2)); animation: floatImg 3s ease-in-out infinite; }
    .card-word-en { font-size: 45px; font-weight: bold; letter-spacing: 2px; }
    
    /* Cara Trasera (Español + Pronunciación) */
    .card-back { transform: rotateY(180deg); background: #fffaf0; border-color: var(--accent); }
    .card-word-es { font-size: 40px; font-weight: bold; color: var(--primary); margin-bottom: 15px; }
    .card-ph { background: #ffeaa7; color: #d35400; padding: 8px 20px; border-radius: 20px; font-family: monospace; font-size: 22px; font-weight: bold; border: 2px dashed #f39c12; }
    
    /* Animaciones de Entrada y Salida (Vuelo) */
    .fly-in { animation: slideInRight 0.6s forwards cubic-bezier(0.2, 0.8, 0.2, 1); }
    .fly-out { animation: slideOutLeft 0.6s forwards cubic-bezier(0.8, -0.2, 1, 1); pointer-events: none; }
    
    @keyframes slideInRight { 0% { transform: translateX(150%) rotate(15deg); opacity: 0; } 100% { transform: translateX(0) rotate(0); opacity: 1; } }
    @keyframes slideOutLeft { 0% { transform: translateX(0) rotate(0); opacity: 1; } 100% { transform: translateX(-150%) rotate(-15deg); opacity: 0; } }
    @keyframes floatImg { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

    /* Barra de progreso de tarjetas */
    .card-progress { display: flex; justify-content: center; gap: 8px; margin-top: 10px; }
    .progress-dot { width: 12px; height: 12px; border-radius: 50%; background: #ccc; transition: 0.3s; }
    .progress-dot.active { background: var(--accent); transform: scale(1.3); }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>✨ ¡Toca la tarjeta para descubrir la magia! ✨</h3>
    
    <div class="card-progress" id="progress-dots">
        <?php foreach ($cards as $i => $c): ?>
            <div class="progress-dot <?php echo $i === 0 ? 'active' : ''; ?>" id="dot-<?php echo $i; ?>"></div>
        <?php endforeach; ?>
    </div>

    <div class="flashcard-scene fly-in" id="card-container">
        <div class="magic-card" id="active-card">
            <div class="card-face card-front">
                <img id="card-img" src="<?php echo $cards[0]['img'] ?? ''; ?>" alt="dibujo">
                <div class="card-word-en" id="card-en"><?php echo $cards[0]['en']; ?></div>
                <div style="font-size: 16px; color: #ffd700; margin-top: 20px; opacity: 0.8;">👆 Toca para voltear</div>
            </div>
            <div class="card-face card-back">
                <div class="card-word-es" id="card-es"><?php echo $cards[0]['es']; ?></div>
                <div class="card-ph" id="card-ph">🗣️ <?php echo $cards[0]['ph']; ?></div>
                <button class="btn" style="background: var(--primary); color: white; border-radius: 50%; width: 50px; height: 50px; margin-top: 25px; font-size: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);" onclick="event.stopPropagation(); playTTS(cardsData[currentIdx].en)">🔊</button>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <button class="btn" id="btn-next-card" style="background: var(--success); color: white; width: 100%; max-width: 350px; font-size: 22px; box-shadow: 0 6px 0 #388e3c; transition: 0.2s; opacity: 0.5; pointer-events: none;">Siguiente ➡️</button>
    </div>
</div>

<script>
    const cardsData = <?php echo json_encode($cards); ?>;
    let currentIdx = 0;
    
    const cardEl = document.getElementById('active-card');
    const containerEl = document.getElementById('card-container');
    const btnNext = document.getElementById('btn-next-card');
    let hasFlipped = false; // Exige que voltee la tarjeta antes de pasar a la siguiente

    // Al tocar la tarjeta
    cardEl.addEventListener('click', () => {
        cardEl.classList.toggle('is-flipped');
        
        // Si la voltea por primera vez, lee la palabra en inglés y desbloquea el botón "Siguiente"
        if (cardEl.classList.contains('is-flipped') && !hasFlipped) {
            playTTS(cardsData[currentIdx].en);
            hasFlipped = true;
            btnNext.style.opacity = '1';
            btnNext.style.pointerEvents = 'auto';
            if(typeof sfxCorrect !== 'undefined') { sfxCorrect.currentTime = 0; sfxCorrect.play(); }
        } else {
            // Sonido genérico de carta al regresar
            if(typeof sfxWrong !== 'undefined') { sfxWrong.volume = 0.2; sfxWrong.currentTime = 0; sfxWrong.play(); sfxWrong.volume = 1;}
        }
    });

    // Botón Siguiente
    btnNext.addEventListener('click', () => {
        if (!hasFlipped) return;
        
        // Animación de salida volando
        containerEl.classList.remove('fly-in');
        containerEl.classList.add('fly-out');
        
        setTimeout(() => {
            currentIdx++;
            if (currentIdx < cardsData.length) {
                // Actualizar info de la siguiente tarjeta
                document.getElementById('card-en').innerText = cardsData[currentIdx].en;
                document.getElementById('card-es').innerText = cardsData[currentIdx].es;
                document.getElementById('card-ph').innerText = '🗣️ ' + cardsData[currentIdx].ph;
                document.getElementById('card-img').src = cardsData[currentIdx].img || '';
                
                // Actualizar puntos
                document.querySelectorAll('.progress-dot').forEach(d => d.classList.remove('active'));
                document.getElementById('dot-' + currentIdx).classList.add('active');
                
                // Reiniciar estado
                cardEl.classList.remove('is-flipped');
                hasFlipped = false;
                btnNext.style.opacity = '0.5';
                btnNext.style.pointerEvents = 'none';
                
                // Entrar volando de nuevo
                containerEl.classList.remove('fly-out');
                void containerEl.offsetWidth; // Forzar reflow de CSS
                containerEl.classList.add('fly-in');
            } else {
                // Terminó todas las tarjetas
                containerEl.style.display = 'none';
                btnNext.style.display = 'none';
                document.getElementById('progress-dots').style.display = 'none';
                
                if(typeof sfxWin !== 'undefined') sfxWin.play();
                triggerMascotReaction('win');
                fireConfetti();
                
                const successMsg = document.createElement('div');
                successMsg.innerHTML = '🎉 ¡Completado! +<?php echo $reward_stars; ?> Estrellas ⭐';
                successMsg.style.cssText = 'color: var(--success); font-size: 28px; font-weight: bold; margin-top: 30px; animation: popOut 0.5s forwards;';
                document.querySelector('.game-area').appendChild(successMsg);
                
                unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
            }
        }, 500); // Espera a que termine la animación de salida
    });
</script>