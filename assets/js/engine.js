// assets/js/engine.js

// Función global para disparar confeti
function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ 
            particleCount: 200, 
            spread: 90, 
            origin: { y: 0.6 },
            colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50']
        });
    } else {
        console.warn("Librería de confeti no cargada.");
    }
}

// Congela la pantalla cuando el niño gana para que no rompa el juego haciendo clics locos
function lockGame() {
    const gameArea = document.querySelector('.game-area');
    if (gameArea) {
        gameArea.style.pointerEvents = 'none';
    }
}