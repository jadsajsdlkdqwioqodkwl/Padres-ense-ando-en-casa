// ==========================================
// SOUNDTRACK & ENGINE (RENOVADO FASE 1)
// ==========================================
let isMusicPlaying = false;
// Crea un elemento de audio en memoria. Solo necesitas colocar un archivo 'soundtrack.mp3' en tu carpeta de assets.
const bgMusic = new Audio('assets/soundtrack.mp3'); 
bgMusic.loop = true;
bgMusic.volume = 0.15;

function toggleMusic() {
    const musicBtn = document.getElementById('music-toggle');
    if (isMusicPlaying) { 
        bgMusic.pause(); 
        if(musicBtn) musicBtn.innerText = '🔇'; 
    } else { 
        bgMusic.play().catch(e => console.log("Requiere interacción del usuario para reproducir audio.")); 
        if(musicBtn) musicBtn.innerText = '🎵'; 
    }
    isMusicPlaying = !isMusicPlaying;
}

// ==========================================
// ELIMINACIÓN DE AUDIO (Rastros silenciados)
// ==========================================
// Mantenemos las funciones declaradas pero vacías para garantizar CERO BUGS 
// si alguna plantilla antigua aún intenta llamarlas.
function playTTS(text, forceSpanish = false) { return; }
function playSpanglish(introEs, wordEn, transEs) { return; }
function playIntroAudio() { return; } 

// ==========================================
// EFECTOS
// ==========================================
function fireConfetti() {
    if (typeof confetti !== 'undefined') {
        confetti({ particleCount: 200, spread: 90, origin: { y: 0.6 }, colors: ['#2B3A67', '#F29C38', '#68A93E', '#5CB2E4'] });
    }
}