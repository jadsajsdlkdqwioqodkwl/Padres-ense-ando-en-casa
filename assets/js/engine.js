// assets/js/engine.js

document.addEventListener('DOMContentLoaded', () => {
    console.log("Motor de juegos English 15 cargado correctamente.");
});

// Variables globales de audio
let isMusicPlaying = false;
const bgMusic = document.getElementById('bg-music');
if(bgMusic) bgMusic.volume = 0.15; // Volumen suave para no aturdir

function toggleMusic() {
    const musicBtn = document.getElementById('music-toggle');
    if (!bgMusic || !musicBtn) return;

    if (isMusicPlaying) { 
        bgMusic.pause(); 
        musicBtn.innerText = '🔇'; 
    } else { 
        bgMusic.play().catch(e => console.log("Navegador requiere interacción previa")); 
        musicBtn.innerText = '🎵'; 
    }
    isMusicPlaying = !isMusicPlaying;
}

// Emociones de la mascota
function triggerMascotReaction(type) {
    const mascot = document.getElementById('mascot');
    const mascotText = document.getElementById('mascot-text');
    
    if(!mascot || !mascotText) return;

    if(type === 'correct') { 
        mascot.innerText = '😎'; 
        mascotText.innerText = '¡Genial!'; 
        setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = '¡Sigue así!'; }, 2000); 
    }
    if(type === 'wrong') { 
        mascot.innerText = '🤔'; 
        mascotText.innerText = '¡Intenta de nuevo!'; 
        setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = '¡Tú puedes!'; }, 2000); 
    }
    if(type === 'win') { 
        mascot.innerText = '🥳'; 
        mascotText.innerText = '¡Eres una estrella!'; 
    }
}

// Reproductor de voz nativo del navegador (Ideal para el padre)
function playTTS(text) {
    if(!text) return;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US'; // Pronunciación en inglés
    utterance.rate = 0.8; // Ligeramente más lento para que el niño entienda
    window.speechSynthesis.speak(utterance);
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ 
            particleCount: 200, 
            spread: 90, 
            origin: { y: 0.6 },
            colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50']
        });
    }
}