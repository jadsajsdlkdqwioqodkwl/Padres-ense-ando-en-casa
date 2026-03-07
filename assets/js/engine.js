let speechVoices = [];

// Precargar voces tan pronto como el navegador las tenga listas
window.speechSynthesis.onvoiceschanged = () => {
    speechVoices = window.speechSynthesis.getVoices();
};

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

function playTTS(text, forceSpanish = false) {
    if(!text) return;
    
    // Cancela audios atascados
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = forceSpanish ? 'es-ES' : 'en-US'; 
    utterance.rate = 0.85; // Un poco más lento para que los niños entiendan

    // Si aún no han cargado, forzamos la carga
    if (speechVoices.length === 0) {
        speechVoices = window.speechSynthesis.getVoices();
    }
    
    let bestVoice;
    
    if (forceSpanish) {
        // Busca español nativo
        bestVoice = speechVoices.find(v => v.lang.startsWith('es') && (v.name.includes('Google') || v.name.includes('Microsoft')));
        if(!bestVoice) bestVoice = speechVoices.find(v => v.lang.startsWith('es'));
    } else {
        // Busca INGLÉS NATIVO (Priorizando voces de alta calidad de Google o Microsoft)
        bestVoice = speechVoices.find(v => v.lang.startsWith('en') && (v.name.includes('Google') || v.name.includes('Microsoft') || v.name.includes('Samantha')));
        if(!bestVoice) bestVoice = speechVoices.find(v => v.lang.startsWith('en'));
    }
    
    if(bestVoice) utterance.voice = bestVoice;
    window.speechSynthesis.speak(utterance);
}

function playSpanglish(introEs, wordEn, transEs) {
    if (wordEn) { playTTS(wordEn, false); }
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}