// assets/js/engine.js
// ==========================================
// SOUNDTRACK & ENGINE (MÚSICA DINÁMICA POR NIVEL)
// ==========================================
let isMusicPlaying = localStorage.getItem('mw_music_pref') === 'true';

// Determinar la pista de audio (específica del nivel o la global)
const globalMusicSrc = 'assets/soundtrack.mp3';
const musicSrc = (typeof window.levelMusicUrl !== 'undefined' && window.levelMusicUrl && window.levelMusicUrl.trim() !== '') 
                    ? window.levelMusicUrl 
                    : globalMusicSrc;

const bgMusic = new Audio(musicSrc); 
bgMusic.loop = true;
bgMusic.volume = 0.15;

function attemptAutoplay() {
    if (isMusicPlaying) {
        bgMusic.play().then(() => {
            updateFloatingMusicButton(true);
        }).catch(e => {
            console.warn("Autoplay bloqueado por el navegador. Esperando interacción del usuario.");
            isMusicPlaying = false; 
            updateFloatingMusicButton(false);
        });
    }
}

function toggleMusic() {
    isMusicPlaying = !isMusicPlaying;
    localStorage.setItem('mw_music_pref', isMusicPlaying);
    
    // FIX MAESTRO: Sincronizamos los efectos especiales (pop, correct, win) con el mismo botón
    if (typeof AudioManager !== 'undefined') {
        AudioManager.muted = !isMusicPlaying;
    }
    
    if (isMusicPlaying) { 
        bgMusic.play().catch(e => console.log("Requiere interacción.")); 
        updateFloatingMusicButton(true);
    } else { 
        bgMusic.pause(); 
        updateFloatingMusicButton(false);
    }
}

function updateFloatingMusicButton(isPlaying) {
    // 1. Actualiza el botón global flotante si existe
    let btn = document.getElementById('music-floating-btn');
    if(btn) {
        btn.innerHTML = isPlaying ? '🎵' : '🔇';
        if(!isPlaying) {
            btn.classList.add('pulse-anim');
        } else {
            btn.classList.remove('pulse-anim');
        }
    }
    
    // 2. FIX: Actualiza también el botón nativo del juego en lesson.php
    let gameBtn = document.getElementById('music-toggle');
    if(gameBtn) {
        gameBtn.innerHTML = isPlaying ? '🎵' : '🔇';
        // Parsear Twemoji en caso de que esté cargado
        if (typeof twemoji !== 'undefined') {
            twemoji.parse(gameBtn, { folder: 'svg', ext: '.svg' });
        }
    }
}

// Inyectar el botón flotante en toda la app
document.addEventListener("DOMContentLoaded", () => {
    // Sincronizar el estado del motor de efectos al cargar la página
    if (typeof AudioManager !== 'undefined') {
        AudioManager.muted = !isMusicPlaying;
    }

    // Sincronizar el botón interno del juego al cargar
    let gameBtn = document.getElementById('music-toggle');
    if(gameBtn) {
        gameBtn.innerHTML = isMusicPlaying ? '🎵' : '🔇';
    }

    if(!document.getElementById('music-floating-btn')) {
        const btn = document.createElement('button');
        btn.id = 'music-floating-btn';
        btn.title = "Activar/Desactivar Música";
        btn.innerHTML = isMusicPlaying ? '🎵' : '🔇';
        
        // Estilos invasivos pero amigables
        Object.assign(btn.style, {
            position: 'fixed', bottom: '20px', left: '20px',
            width: '60px', height: '60px', borderRadius: '50%',
            background: '#F29C38', color: 'white', border: '4px solid #FFFFFF',
            fontSize: '24px', cursor: 'pointer', zIndex: '99999',
            boxShadow: '0 4px 15px rgba(0,0,0,0.3)', transition: 'transform 0.2s'
        });
        
        btn.onclick = toggleMusic;
        document.body.appendChild(btn);
        
        // Animación CSS dinámica
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes pulse-music { 0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(242, 156, 56, 0.7); } 70% { transform: scale(1.1); box-shadow: 0 0 0 15px rgba(242, 156, 56, 0); } 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(242, 156, 56, 0); } }
            .pulse-anim { animation: pulse-music 2s infinite; }
            #music-floating-btn:active { transform: scale(0.9) !important; }
        `;
        document.head.appendChild(style);
    }

    // Intentar reproducir al primer clic en cualquier parte si estaba activo
    window.addEventListener('click', function initAudio() {
        if(localStorage.getItem('mw_music_pref') === 'true' && bgMusic.paused) {
            attemptAutoplay();
        }
        window.removeEventListener('click', initAudio);
    }, { once: true });
});

// ==========================================
// ELIMINACIÓN DE AUDIO (Rastros silenciados)
// ==========================================
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