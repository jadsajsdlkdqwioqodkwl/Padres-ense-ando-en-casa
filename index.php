<?php 
// include 'includes/config.php'; // Listo para cuando conectemos la BD
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English 15 - Interactive App</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --primary: #2B3A67; 
            --accent: #FF7F50; 
            --bg: #F0F4F8;
            --card-bg: #FFFFFF;
            --success: #4CAF50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            padding-bottom: 100px; /* Espacio para botones fijos */
        }

        .container {
            background: var(--card-bg);
            width: 100%;
            max-width: 800px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
        }

        h1, h2, h3 { color: var(--primary); }

        /* --- SISTEMA DE ESTRELLAS Y MASCOTA --- */
        .top-hud {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .stars-counter { font-size: 24px; font-weight: bold; color: #FFD700; text-shadow: 1px 1px 0 #b89b00; }
        
        .companion-area {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #eef2ff;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
            color: var(--primary);
        }
        
        .companion-avatar { font-size: 30px; animation: bounce 2s infinite; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* --- GUÍA DEL PADRE --- */
        .teaching-guide {
            background-color: #EEF2FF;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid var(--primary);
            margin-bottom: 30px;
        }

        .action-step { display: flex; gap: 10px; margin-bottom: 10px; align-items: baseline; }
        .action-icon { font-size: 20px; }

        /* --- JUEGO INTERACTIVO DRAG & DROP --- */
        .game-area {
            text-align: center;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 12px;
            background: #fafafa;
            position: relative;
        }

        .draggable-items { display: flex; justify-content: center; gap: 20px; margin-bottom: 30px; }

        .drag-item {
            background: var(--accent);
            color: white;
            padding: 10px 25px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 30px;
            cursor: grab;
            user-select: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.1s;
        }

        .drag-item:active { cursor: grabbing; transform: scale(0.95); }

        .face-board {
            position: relative;
            width: 300px;
            height: 350px;
            border: 6px solid var(--primary);
            border-radius: 50% 50% 45% 45%;
            margin: 0 auto;
            background: white;
        }

        .drop-zone {
            position: absolute;
            background: rgba(0,0,0,0.05);
            border: 2px dashed #999;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: #999;
            transition: 0.2s;
        }

        .drop-zone.hover { background: rgba(255, 127, 80, 0.2); border-color: var(--accent); transform: scale(1.05); }

        #drop-eyes { top: 20%; left: 15%; width: 70%; height: 60px; border-radius: 30px; }
        #drop-nose { top: 45%; left: 35%; width: 30%; height: 60px; border-radius: 50%; }
        #drop-mouth { bottom: 15%; left: 25%; width: 50%; height: 60px; border-radius: 30px; }

        .success-msg {
            color: var(--success);
            font-size: 28px;
            font-weight: bold;
            margin-top: 20px;
            display: none;
            animation: popIn 0.5s ease;
        }

        @keyframes popIn { 0% { transform: scale(0); } 80% { transform: scale(1.1); } 100% { transform: scale(1); } }

        /* --- CONTROLES Y MÚSICA FLOTANTES --- */
        .floating-controls {
            position: fixed;
            bottom: 20px;
            display: flex;
            gap: 15px;
            z-index: 100;
        }

        .btn {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: 0.2s;
        }

        .btn-restart { background: white; color: var(--primary); border: 2px solid var(--primary); }
        .btn-music { background: var(--primary); color: white; border-radius: 50%; padding: 15px 20px; }
        .btn-next { background: #ccc; color: white; cursor: not-allowed; }
        .btn-next.active { background: var(--success); cursor: pointer; animation: bounce 1s infinite; }

        /* --- VOCABULARIO --- */
        .vocabulary-section { margin-top: 40px; border-top: 3px solid #eee; padding-top: 30px; }
        .vocab-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .vocab-card { border: 2px solid #ccc; padding: 15px; border-radius: 8px; text-align: center; }
        .vocab-en { font-size: 24px; font-weight: bold; color: var(--primary); display: block; }
        .vocab-ph { color: #d9534f; font-weight: bold; font-family: monospace; display: block; margin: 5px 0;}
        .vocab-es { color: #666; }
        .print-btn { background: var(--primary); color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 8px; cursor: pointer; margin-top: 20px; }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; border: none; }
            .teaching-guide, .game-area, .print-btn, .floating-controls, .top-hud { display: none; }
        }
    </style>
</head>
<body>

    <audio id="bg-music" loop src="https://assets.mixkit.co/music/preview/mixkit-happy-times-158.mp3"></audio>
    <audio id="sfx-correct" src="https://assets.mixkit.co/sfx/preview/mixkit-animated-small-pop-2553.mp3"></audio>
    <audio id="sfx-wrong" src="https://assets.mixkit.co/sfx/preview/mixkit-wrong-answer-fail-notification-946.mp3"></audio>
    <audio id="sfx-win" src="https://assets.mixkit.co/sfx/preview/mixkit-winning-chimes-2015.mp3"></audio>

    <div class="container">
        
        <div class="top-hud">
            <div class="companion-area">
                <span class="companion-avatar" id="mascot">🐶</span>
                <span id="mascot-text">Let's play!</span>
            </div>
            <div class="stars-counter">⭐ <span id="star-count">12</span></div>
        </div>

        <h2>Module 1: My World</h2>
        <h1>Lesson 2: My Face</h1>

        <div class="teaching-guide">
            <h3>👨‍🏫 Parents: How to teach this lesson</h3>
            <p>No solo leas las palabras. Usa el método de "Respuesta Física" para que tu hijo asocie la palabra con su cuerpo.</p>
            <div class="action-step"><span class="action-icon">👉</span><div><strong>Paso 1:</strong> Toca tus propios ojos y di: <em>"Look! Eyes. [ÁIS]"</em>.</div></div>
            <div class="action-step"><span class="action-icon">👃</span><div><strong>Paso 2:</strong> Toca suavemente la nariz de tu hijo y di: <em>"Nose! [NÓUS]"</em>.</div></div>
            <div class="action-step"><span class="action-icon">😃</span><div><strong>Paso 3:</strong> Sonríe en grande, señala tu boca y di: <em>"Mouth. [MÁUD]"</em>.</div></div>
        </div>

        <div class="game-area">
            <h3>Drag the words to the correct place! 🖱️</h3>
            
            <div class="draggable-items">
                <div class="drag-item" draggable="true" id="drag-nose">NOSE</div>
                <div class="drag-item" draggable="true" id="drag-mouth">MOUTH</div>
                <div class="drag-item" draggable="true" id="drag-eyes">EYES</div>
            </div>

            <div class="face-board">
                <div class="drop-zone" id="drop-eyes" data-match="drag-eyes">Drop Eyes Here</div>
                <div class="drop-zone" id="drop-nose" data-match="drag-nose">Nose</div>
                <div class="drop-zone" id="drop-mouth" data-match="drag-mouth">Mouth</div>
            </div>

            <div class="success-msg" id="success-msg">🎉 Perfect! +3 Stars! ⭐⭐⭐</div>
        </div>

        <div class="vocabulary-section">
            <h2>🖨️ Printable Vocabulary: My World</h2>
            <p>Imprime esta sección y pégala en la refrigeradora para repasar durante la semana.</p>
            <div class="vocab-grid">
                <div class="vocab-card"><span class="vocab-en">Eyes</span><span class="vocab-ph">[ÁIS]</span><span class="vocab-es">Ojos</span></div>
                <div class="vocab-card"><span class="vocab-en">Nose</span><span class="vocab-ph">[NÓUS]</span><span class="vocab-es">Nariz</span></div>
                <div class="vocab-card"><span class="vocab-en">Mouth</span><span class="vocab-ph">[MÁUD]</span><span class="vocab-es">Boca</span></div>
            </div>
            <button class="print-btn" onclick="window.print()">🖨️ Print Vocabulary</button>
        </div>
    </div>

    <div class="floating-controls">
        <button class="btn btn-restart" onclick="location.reload()">🔄 Restart</button>
        <button class="btn btn-music" id="music-toggle" onclick="toggleMusic()">🎵</button>
        <button class="btn btn-next" id="btn-next" disabled>Next Lesson ➡️</button>
    </div>

    <script>
        // --- MOTOR DE AUDIO Y MASCOTA ---
        const bgMusic = document.getElementById('bg-music');
        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxWin = document.getElementById('sfx-win');
        const mascot = document.getElementById('mascot');
        const mascotText = document.getElementById('mascot-text');
        
        bgMusic.volume = 0.2; // Música suave para no abrumar
        let isMusicPlaying = false;

        function toggleMusic() {
            if (isMusicPlaying) { bgMusic.pause(); document.getElementById('music-toggle').innerText = '🔇'; } 
            else { bgMusic.play(); document.getElementById('music-toggle').innerText = '🎵'; }
            isMusicPlaying = !isMusicPlaying;
        }

        function triggerMascotReaction(type) {
            if(type === 'correct') { mascot.innerText = '😎'; mascotText.innerText = 'Great!'; setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = 'Keep going!'; }, 2000); }
            if(type === 'wrong') { mascot.innerText = '🤔'; mascotText.innerText = 'Try again!'; setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = 'You can do it!'; }, 2000); }
            if(type === 'win') { mascot.innerText = '🥳'; mascotText.innerText = 'You are a star!'; }
        }

        // --- LÓGICA DEL JUEGO DRAG & DROP ---
        const draggables = document.querySelectorAll('.drag-item');
        const dropZones = document.querySelectorAll('.drop-zone');
        let matchesCount = 0;

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => draggable.classList.add('dragging'));
            draggable.addEventListener('dragend', () => draggable.classList.remove('dragging'));
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('hover'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('hover'); });
            
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('hover');
                const draggedElement = document.querySelector('.dragging');
                if (!draggedElement) return;

                const matchId = zone.getAttribute('data-match');

                if (draggedElement.id === matchId) {
                    // ACIERTO
                    sfxCorrect.currentTime = 0; sfxCorrect.play();
                    triggerMascotReaction('correct');
                    
                    zone.innerHTML = '';
                    zone.appendChild(draggedElement);
                    draggedElement.setAttribute('draggable', 'false');
                    draggedElement.style.cursor = 'default';
                    matchesCount++;

                    // VICTORIA
                    if (matchesCount === 3) {
                        sfxWin.play();
                        triggerMascotReaction('win');
                        document.getElementById('success-msg').style.display = 'block';
                        
                        // Lluvia de dopamina
                        confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } });
                        
                        // Actualizar estrellas visualmente
                        let currentStars = parseInt(document.getElementById('star-count').innerText);
                        document.getElementById('star-count').innerText = currentStars + 3;

                        // Desbloquear botón siguiente
                        const nextBtn = document.getElementById('btn-next');
                        nextBtn.disabled = false;
                        nextBtn.classList.add('active');
                        nextBtn.onclick = () => alert('¡Aquí usaríamos PHP para redirigir a lesson.php?id=3!');
                    }
                } else {
                    // ERROR
                    sfxWrong.currentTime = 0; sfxWrong.play();
                    triggerMascotReaction('wrong');
                    zone.style.borderColor = 'red';
                    setTimeout(() => zone.style.borderColor = '#999', 500);
                }
            });
        });
    </script>
</body>
</html>