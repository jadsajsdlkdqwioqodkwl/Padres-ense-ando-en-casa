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

function playTTS(text) {
    if(!text) return;
    
    // ¡LA MAGIA!: Cancela cualquier audio que esté sonando o en cola para evitar retrasos.
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    
    // Forzamos SIEMPRE el español para tu estrategia fonética ("ápol", "iélou")
    utterance.lang = 'es-ES'; 
    utterance.rate = 0.85;

    // Buscamos una voz de Google o femenina en español para que suene más amigable
    const voices = window.speechSynthesis.getVoices();
    let bestVoice = voices.find(v => v.lang.startsWith('es') && (v.name.includes('Google') || v.name.includes('Female')));
    if(!bestVoice) bestVoice = voices.find(v => v.lang.startsWith('es'));
    
    if(bestVoice) utterance.voice = bestVoice;
    window.speechSynthesis.speak(utterance);
}

// Simplificamos Spanglish: Ahora solo lee la palabra principal fonética y ya.
function playSpanglish(introEs, wordEn, transEs) {
    if (wordEn) {
        playTTS(wordEn);
    }
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}