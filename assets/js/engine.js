document.addEventListener('DOMContentLoaded', () => {
    window.speechSynthesis.getVoices();
});

let isMusicPlaying = false;
const bgMusic = document.getElementById('bg-music');
if(bgMusic) bgMusic.volume = 0.15;

function toggleMusic() {
    const musicBtn = document.getElementById('music-toggle');
    if (!bgMusic || !musicBtn) return;
    if (isMusicPlaying) { bgMusic.pause(); musicBtn.innerText = '🔇'; } 
    else { bgMusic.play().catch(e => console.log("Navegador requiere interacción previa")); musicBtn.innerText = '🎵'; }
    isMusicPlaying = !isMusicPlaying;
}

function playTTS(text, forceSpanish = false) {
    if(!text) return;
    
    // Cancela cualquier audio que esté sonando
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    
    // Si forceSpanish es true (ej. para instrucciones), usa español. Si no, usa Inglés nativo.
    utterance.lang = forceSpanish ? 'es-ES' : 'en-US'; 
    utterance.rate = 0.9;

    const voices = window.speechSynthesis.getVoices();
    let bestVoice;
    
    if (forceSpanish) {
        bestVoice = voices.find(v => v.lang.startsWith('es'));
    } else {
        // Busca una voz nativa en inglés (femenina o de Google preferiblemente)
        bestVoice = voices.find(v => v.lang.startsWith('en') && (v.name.includes('Google') || v.name.includes('Female')));
        if(!bestVoice) bestVoice = voices.find(v => v.lang.startsWith('en'));
    }
    
    if(bestVoice) utterance.voice = bestVoice;
    window.speechSynthesis.speak(utterance);
}

// Para retrocompatibilidad con los juegos viejos, pero forzando el inglés real
function playSpanglish(introEs, wordEn, transEs) {
    if (wordEn) {
        // Leemos la palabra en INGLÉS REAL, ignorando la fonética antigua
        playTTS(wordEn, false);
    }
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}