<audio id="bg-music" loop src="https://assets.mixkit.co/music/preview/mixkit-happy-times-158.mp3"></audio>
<audio id="sfx-correct" src="https://assets.mixkit.co/sfx/preview/mixkit-animated-small-pop-2553.mp3"></audio>
<audio id="sfx-wrong" src="https://assets.mixkit.co/sfx/preview/mixkit-wrong-answer-fail-notification-946.mp3"></audio>
<audio id="sfx-win" src="https://assets.mixkit.co/sfx/preview/mixkit-winning-chimes-2015.mp3"></audio>

<script>
    // Referencias a los audios
    const bgMusic = document.getElementById('bg-music');
    const sfxCorrect = document.getElementById('sfx-correct');
    const sfxWrong = document.getElementById('sfx-wrong');
    const sfxWin = document.getElementById('sfx-win');
    
    bgMusic.volume = 0.2; // Volumen suave
    let isMusicPlaying = false;

    // Función para el botón de música
    function toggleMusic() {
        const musicBtn = document.getElementById('music-toggle');
        if (isMusicPlaying) { 
            bgMusic.pause(); 
            if(musicBtn) musicBtn.innerText = '🔇'; 
        } else { 
            bgMusic.play().catch(e => console.log("El navegador requiere interacción primero")); 
            if(musicBtn) musicBtn.innerText = '🎵'; 
        }
        isMusicPlaying = !isMusicPlaying;
    }

    // El cerebro de la Mascota
    function triggerMascotReaction(type) {
        const mascot = document.getElementById('mascot');
        const mascotText = document.getElementById('mascot-text');
        
        if(!mascot || !mascotText) return;

        if(type === 'correct') { 
            mascot.innerText = '😎'; 
            mascotText.innerText = 'Great!'; 
            setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = 'Keep going!'; }, 2000); 
        }
        if(type === 'wrong') { 
            mascot.innerText = '🤔'; 
            mascotText.innerText = 'Try again!'; 
            setTimeout(() => { mascot.innerText = '🐶'; mascotText.innerText = 'You can do it!'; }, 2000); 
        }
        if(type === 'win') { 
            mascot.innerText = '🥳'; 
            mascotText.innerText = 'You are a star!'; 
        }
    }
</script>