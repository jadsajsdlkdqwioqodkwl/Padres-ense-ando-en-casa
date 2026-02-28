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

// FIX: Añadido soporte para Callbacks (onEndCallback) para evitar bloqueos
function playTTS(text, lang = 'en-US', onEndCallback = null) {
    if(!text) {
        if(onEndCallback) onEndCallback();
        return;
    }
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    utterance.rate = 0.85;

    const voices = window.speechSynthesis.getVoices();
    let bestVoice = voices.find(v => v.lang.startsWith(lang.split('-')[0]) && (v.name.includes('Google') || v.name.includes('Female')));
    if(!bestVoice) bestVoice = voices.find(v => v.lang.startsWith(lang.split('-')[0]));
    
    if(bestVoice) utterance.voice = bestVoice;
    
    if(onEndCallback) utterance.onend = onEndCallback;
    
    window.speechSynthesis.speak(utterance);
}

// FIX: Motor Spanglish encadenado. Garantiza que primero hable español, LUEGO inglés, LUEGO español.
function playSpanglish(introEs, wordEn, transEs) {
    if (introEs) {
        playTTS(introEs, 'es-ES', () => {
            setTimeout(() => {
                if (wordEn) playTTS(wordEn, 'en-US', () => {
                    setTimeout(() => { if (transEs) playTTS(transEs, 'es-ES'); }, 500);
                });
            }, 300);
        });
    } else if (wordEn) {
        playTTS(wordEn, 'en-US', () => {
            setTimeout(() => { if (transEs) playTTS(transEs, 'es-ES'); }, 500);
        });
    }
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}