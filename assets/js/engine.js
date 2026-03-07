let isMusicPlaying = false;
const bgMusic = document.getElementById('bg-music');
if(bgMusic) bgMusic.volume = 0.15;

function toggleMusic() {
    const musicBtn = document.getElementById('music-toggle');
    if (!bgMusic || !musicBtn) return;
    if (isMusicPlaying) { bgMusic.pause(); musicBtn.innerText = '🔇'; } 
    else { bgMusic.play().catch(e => console.log("Requiere interacción")); musicBtn.innerText = '🎵'; }
    isMusicPlaying = !isMusicPlaying;
}

// Apagamos el TTS por completo. El padre enseñará la pronunciación.
function playTTS(text, forceSpanish = false) {
    return; 
}

function playSpanglish(introEs, wordEn, transEs) {
    return; 
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}