<?php
// ==========================================
// CONFIGURACIÓN ESCALABLE: METEOR STRIKE
// ==========================================
$target_word = $lesson_data['target_word'] ?? 'APPLE';
$translation = $lesson_data['translation'] ?? 'Manzana';

// Array de items que caerán. Puedes meter URLs de imágenes en 'img' o usar emojis por ahora.
$falling_items = $lesson_data['items'] ?? [
    ['id' => 1, 'content' => '🍎', 'is_correct' => true],
    ['id' => 2, 'content' => '🍌', 'is_correct' => false],
    ['id' => 3, 'content' => '🍇', 'is_correct' => false],
    ['id' => 4, 'content' => '🍓', 'is_correct' => false],
    ['id' => 5, 'content' => '🍉', 'is_correct' => false]
];

// Mezclamos para que el orden de caída sea aleatorio
shuffle($falling_items);

$speed_seconds = $lesson_data['speed'] ?? 6; // Segundos que tarda en caer un meteorito (menor = más difícil)
$reward_stars = $lesson['reward_stars'] ?? 15;
?>

<style>
    /* ==========================================
       ENTORNO DE JUEGO Y DIBUJOS CSS
    ========================================== */
    .meteor-board {
        position: relative; width: 100%; height: 400px; 
        background: linear-gradient(to bottom, #2c3e50 0%, #34495e 100%);
        border-radius: 20px; overflow: hidden;
        border: 4px solid var(--primary); margin-bottom: 20px;
        box-shadow: inset 0 0 50px rgba(0,0,0,0.5);
    }

    /* Suelo */
    .ground {
        position: absolute; bottom: 0; width: 100%; height: 50px;
        background: #795548; border-top: 8px solid #8d6e63; z-index: 5;
    }

    /* El Dinosaurio (Dibujado con CSS) */
    .css-dino {
        position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%);
        width: 60px; height: 70px; background: #2ecc71;
        border-radius: 30px 30px 10px 10px; z-index: 10;
        box-shadow: inset -5px -5px 0 rgba(0,0,0,0.2);
        animation: dinoIdle 1s infinite alternate;
        transition: 0.3s;
    }
    .css-dino::before { /* Ojo */
        content: ''; position: absolute; top: 15px; right: 15px;
        width: 10px; height: 10px; background: white; border-radius: 50%;
        border: 3px solid #333;
    }
    .css-dino::after { /* Boca */
        content: ''; position: absolute; top: 30px; right: 5px;
        width: 20px; height: 5px; background: #333; border-radius: 5px;
    }
    .css-dino.panic { background: #e74c3c; animation: shake 0.2s infinite; }
    .css-dino.dead { transform: translateX(-50%) scaleY(0.2); background: #333; bottom: 45px;}

    /* ==========================================
       METEORITOS Y ANIMACIONES
    ========================================== */
    .meteor {
        position: absolute; top: -100px; /* Inicia fuera de pantalla */
        width: 70px; height: 70px; background: #e67e22;
        border-radius: 50%; cursor: pointer; z-index: 8;
        display: flex; justify-content: center; align-items: center;
        font-size: 40px; box-shadow: 0 0 20px #d35400, inset -5px -5px 0 rgba(0,0,0,0.3);
        transition: transform 0.1s; user-select: none;
    }
    
    /* La cola de fuego del meteorito */
    .meteor::before {
        content: ''; position: absolute; top: -40px; left: 15px;
        width: 40px; height: 60px; background: linear-gradient(to top, #f39c12, transparent);
        border-radius: 50%; z-index: -1; opacity: 0.8;
        animation: flicker 0.2s infinite alternate;
    }

    .meteor:active { transform: scale(0.9); }
    .meteor.destroyed { pointer-events: none; animation: popExplosion 0.4s forwards; }

    /* UI del Juego */
    .hud { position: absolute; top: 15px; left: 15px; right: 15px; display: flex; justify-content: space-between; z-index: 15; }
    .target-box { 
        background: rgba(255,255,255,0.9); padding: 10px 20px; border-radius: 20px; 
        font-size: 24px; font-weight: bold; color: var(--primary);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 3px solid var(--accent);
        cursor: pointer; animation: pulse 2s infinite;
    }
    .lives-box { font-size: 24px; letter-spacing: 5px; color: #e74c3c; text-shadow: 0 2px 4px rgba(0,0,0,0.5);}

    /* Keyframes */
    @keyframes dinoIdle { 0% { transform: translateX(-50%) translateY(0); } 100% { transform: translateX(-50%) translateY(5px); } }
    @keyframes shake { 0%, 100% { transform: translateX(-50%) rotate(0deg); } 25% { transform: translateX(-55%) rotate(-10deg); } 75% { transform: translateX(-45%) rotate(10deg); } }
    @keyframes flicker { 0% { height: 60px; opacity: 0.6; } 100% { height: 80px; opacity: 1; } }
    @keyframes popExplosion { 
        0% { transform: scale(1); filter: brightness(1); } 
        50% { transform: scale(1.5); filter: brightness(2); background: white; } 
        100% { transform: scale(0); opacity: 0; } 
    }
</style>

<div class="game-area text-center" style="border: none; background: transparent;">
    <h3>☄️ ¡Protege al Dinosaurio!</h3>
    <p style="color: #666; margin-bottom: 10px;">Toca el meteorito que tenga la palabra correcta.</p>

    <div class="meteor-board" id="game-board">
        <div class="hud">
            <div class="target-box" onclick="playAudioHint()">
                🔊 <?php echo htmlspecialchars($target_word); ?>
            </div>
            <div class="lives-box" id="lives">❤️❤️❤️</div>
        </div>

        <div class="css-dino" id="dino"></div>
        <div class="ground"></div>
    </div>
</div>

<script>
    // ==========================================
    // ESTADO DEL JUEGO Y VARIABLES
    // ==========================================
    const itemsData = <?php echo json_encode($falling_items); ?>;
    const baseSpeed = <?php echo $speed_seconds; ?>;
    const board = document.getElementById('game-board');
    const dino = document.getElementById('dino');
    const livesDisplay = document.getElementById('lives');
    
    let lives = 3;
    let gameActive = true;
    let currentItemIndex = 0;
    let activeMeteor = null;
    let fallInterval = null;
    let meteorY = -100;
    
    // Reproducir audio inicial
    setTimeout(playAudioHint, 500);

    function playAudioHint() {
        if(typeof playTTS !== 'undefined') playTTS('Find the <?php echo addslashes($target_word); ?>');
    }

    // ==========================================
    // MOTOR DE CAÍDA (PHYSICS SIMPLIFICADO)
    // ==========================================
    function spawnMeteor() {
        if (!gameActive || currentItemIndex >= itemsData.length) {
            if (gameActive && currentItemIndex >= itemsData.length) {
                // Si se acaban los items y no ha ganado, recargamos (para no dejarlo trabado)
                executeLoss("Se acabaron los meteoritos...");
            }
            return;
        }

        const data = itemsData[currentItemIndex];
        currentItemIndex++;

        // Crear elemento en el DOM
        activeMeteor = document.createElement('div');
        activeMeteor.className = 'meteor';
        
        // Si tienes imágenes en tu BD, cambia esto a un tag <img>
        activeMeteor.innerHTML = data.content; 
        
        // Posición X aleatoria (evitando que se salga de los bordes)
        const randomX = Math.floor(Math.random() * (board.offsetWidth - 80)) + 10;
        activeMeteor.style.left = randomX + 'px';
        meteorY = -100;
        activeMeteor.style.top = meteorY + 'px';

        board.appendChild(activeMeteor);

        // Lógica de clic (Disparar al meteorito)
        activeMeteor.addEventListener('mousedown', () => checkHit(data.is_correct));
        activeMeteor.addEventListener('touchstart', (e) => { e.preventDefault(); checkHit(data.is_correct); }); // Soporte Mobile

        // Iniciar caída
        const boardHeight = board.offsetHeight;
        const groundLevel = boardHeight - 70; // Altura del suelo + dino

        // Calculamos cuánto baja cada 50ms basado en la velocidad base
        const step = groundLevel / (baseSpeed * 20); 

        clearInterval(fallInterval);
        fallInterval = setInterval(() => {
            if (!gameActive) return;
            
            meteorY += step;
            activeMeteor.style.top = meteorY + 'px';

            // El dino se asusta cuando está cerca
            if (meteorY > groundLevel * 0.6) dino.classList.add('panic');
            else dino.classList.remove('panic');

            // Colisión con el suelo (El jugador no hizo nada)
            if (meteorY >= groundLevel) {
                clearInterval(fallInterval);
                if (data.is_correct) {
                    // Era el correcto y lo dejó caer = Pierde vida
                    takeDamage("¡Dejaste caer la respuesta correcta!");
                } else {
                    // Era falso y lo dejó caer = Bien, pasamos al siguiente
                    activeMeteor.remove();
                    spawnMeteor();
                }
            }
        }, 50);
    }

    // ==========================================
    // INTERACCIÓN Y CONSECUENCIAS
    // ==========================================
    function checkHit(isCorrect) {
        if (!gameActive || !activeMeteor) return;

        clearInterval(fallInterval); // Detener caída al tocar
        activeMeteor.classList.add('destroyed');

        if (isCorrect) {
            // ¡Victoria!
            if(typeof sfxCorrect !== 'undefined') sfxCorrect.play();
            setTimeout(() => executeWin(), 400);
        } else {
            // ¡Error! Tocó el falso
            if(typeof sfxWrong !== 'undefined') sfxWrong.play();
            takeDamage("¡Ese no era el correcto!");
            
            // Destruir el actual y lanzar el siguiente rápido
            setTimeout(() => {
                if (activeMeteor) activeMeteor.remove();
                if(gameActive) spawnMeteor();
            }, 500);
        }
    }

    function takeDamage(msg) {
        lives--;
        updateLivesDisplay();
        
        // Animación de daño al entorno
        board.style.boxShadow = "inset 0 0 50px rgba(255,0,0,0.8)";
        setTimeout(() => board.style.boxShadow = "inset 0 0 50px rgba(0,0,0,0.5)", 300);

        if (lives <= 0) {
            executeLoss("¡El dinosaurio fue aplastado!");
        }
    }

    function updateLivesDisplay() {
        let text = '';
        for(let i=0; i<lives; i++) text += '❤️';
        livesDisplay.innerText = text;
    }

    function executeWin() {
        gameActive = false;
        dino.classList.remove('panic');
        if(typeof playTTS !== 'undefined') playTTS('<?php echo addslashes($target_word); ?>');
        
        setTimeout(() => {
            if(typeof sfxWin !== 'undefined') sfxWin.play();
            if(typeof fireConfetti !== 'undefined') fireConfetti();
            
            // Liberar siguiente nivel
            if(typeof unlockNextButton !== 'undefined') {
                unlockNextButton(<?php echo $lesson['id']; ?>, <?php echo $reward_stars; ?>, <?php echo $lesson['module_id']; ?>);
            }
        }, 800);
    }

    function executeLoss(msg) {
        gameActive = false;
        clearInterval(fallInterval);
        dino.classList.add('dead');
        if(typeof sfxWrong !== 'undefined') sfxWrong.play();
        
        setTimeout(() => {
            alert(msg + " ¡Inténtalo de nuevo!");
            location.reload();
        }, 1200);
    }

    // Iniciar el ciclo de juego
    setTimeout(spawnMeteor, 1000);
</script>