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

function playTTS(text, lang = 'en-US') {
    if(!text) return;
    
    // ¡LA MAGIA!: Cancela cualquier audio que esté sonando o en cola.
    // Si el niño presiona 5 botones rápido, solo sonará el último al instante.
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    utterance.rate = 0.85; // Velocidad perfecta para aprender

    const voices = window.speechSynthesis.getVoices();
    let bestVoice = voices.find(v => v.lang.startsWith(lang.split('-')[0]) && (v.name.includes('Google') || v.name.includes('Female')));
    if(!bestVoice) bestVoice = voices.find(v => v.lang.startsWith(lang.split('-')[0]));
    
    if(bestVoice) utterance.voice = bestVoice;
    window.speechSynthesis.speak(utterance);
}

// Ahora ignora el español y solo reproduce la palabra clave en inglés
function playSpanglish(introEs, wordEn, transEs) {
    if (wordEn) {
        playTTS(wordEn, 'en-US');
    }
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}