<?php
// includes/controls.php
?>
<style>
    .floating-controls {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 15px;
        z-index: 100;
        background: rgba(255, 255, 255, 0.9);
        padding: 10px 20px;
        border-radius: 40px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        backdrop-filter: blur(5px);
    }
    .btn {
        padding: 12px 25px;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-restart { background: white; color: var(--primary); border: 2px solid var(--primary); }
    .btn-restart:hover { background: #f0f0f0; }
    .btn-music { background: var(--primary); color: white; border-radius: 50%; padding: 12px 18px; }
    
    .btn-next { background: #ccc; color: white; cursor: not-allowed; transition: 0.3s; }
    .btn-next.active { 
        background: var(--success); 
        cursor: pointer; 
        animation: pulse-green 1.5s infinite; 
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    }

    @keyframes pulse-green {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>

<div class="floating-controls">
    <button class="btn btn-restart" onclick="location.reload()">🔄 Restart</button>
    <button class="btn btn-music" id="music-toggle" onclick="typeof toggleMusic === 'function' ? toggleMusic() : console.log('Audio logic missing')">🎵</button>
    <button class="btn btn-next" id="btn-next" disabled>Next Lesson ➡️</button>
</div>

<script>
    // Esta función se dispara al momento de la victoria (confeti)
    function unlockNextButton(lessonId, rewardStars) {
        const nextBtn = document.getElementById('btn-next');
        if(!nextBtn) return;
        
        nextBtn.disabled = false;
        nextBtn.classList.add('active');
        
        // Petición AJAX (Fetch) al backend para guardar el progreso sin recargar la página
        fetch('app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                lesson_id: lessonId,
                stars: rewardStars
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Progreso guardado:", data);
            
            // Actualizamos el HUD de estrellas visualmente al instante
            const starCountDisplay = document.getElementById('star-count');
            if(starCountDisplay && data.status === 'success') {
                let current = parseInt(starCountDisplay.innerText);
                starCountDisplay.innerText = current + rewardStars;
            }
        })
        .catch(error => console.error('Error guardando progreso:', error));

        // Le damos la acción de ir al inicio o a la siguiente lección
        nextBtn.onclick = () => {
            window.location.href = 'index.php';
        };
    }
</script>