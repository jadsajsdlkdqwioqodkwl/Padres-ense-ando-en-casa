document.addEventListener('DOMContentLoaded', () => {
    console.log("Motor de juegos English 15 cargado correctamente.");
    // Forzar la carga de voces en segundo plano
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

function triggerMascotReaction(type) {
    // Código de mascota (opcional, si lo sigues usando)
}

function playTTS(text) {
    if(!text) return;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US'; 
    utterance.rate = 0.85; // Un poco más lento para que el niño entienda bien

    // MAGIA: Buscar la mejor voz disponible
    const voices = window.speechSynthesis.getVoices();
    let bestVoice = voices.find(v => v.lang.startsWith('en') && (v.name.includes('Female') || v.name.includes('Google US English') || v.name.includes('Samantha')));
    
    // Si no encuentra una específica, agarra la primera en inglés
    if(!bestVoice) bestVoice = voices.find(v => v.lang.startsWith('en'));
    
    if(bestVoice) utterance.voice = bestVoice;
    
    window.speechSynthesis.speak(utterance);
}

function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#FF7F50', '#FFD700', '#4CAF50'] });
    }
}