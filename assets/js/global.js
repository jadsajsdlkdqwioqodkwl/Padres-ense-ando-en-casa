// assets/js/global.js

document.addEventListener('DOMContentLoaded', () => {
    console.log("English 15: Sistema cargado correctamente.");
});

function toggleMusic() {
    const bgMusic = document.getElementById('bg-music');
    const btn = document.getElementById('music-toggle');
    
    if (!bgMusic || !btn) return;

    if (bgMusic.paused) { 
        bgMusic.play().catch(e => console.log("Interacción requerida primero")); 
        btn.innerText = '🎵'; 
    } else { 
        bgMusic.pause(); 
        btn.innerText = '🔇'; 
    }
}