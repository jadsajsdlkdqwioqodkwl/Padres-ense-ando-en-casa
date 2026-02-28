<style>
    .floating-controls {
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 15px; z-index: 100; background: rgba(255, 255, 255, 0.95);
        padding: 10px 20px; border-radius: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .btn-bottom { padding: 12px 25px; font-size: 18px; font-weight: bold; border: none; border-radius: 30px; cursor: pointer; transition: all 0.2s; }
    .btn-restart { background: white; color: var(--primary); border: 2px solid var(--primary); }
    .btn-next { background: #ccc; color: white; cursor: not-allowed; transition: 0.3s; }
    .btn-next.active { background: var(--success); cursor: pointer; animation: pulse-green 1.5s infinite; }
    @keyframes pulse-green { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
</style>

<div class="floating-controls">
    <button class="btn-bottom btn-restart" onclick="location.reload()">🔄 Reiniciar</button>
    <button class="btn-bottom btn-next" id="btn-next" disabled>Siguiente Nivel ➡️</button>
</div>

<script>
    function unlockNextButton(lessonId, rewardStars, moduleId = 1) {
        const nextBtn = document.getElementById('btn-next');
        if(!nextBtn) return;
        nextBtn.disabled = false; nextBtn.classList.add('active');
        
        fetch('app/save_progress.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_id: lessonId, stars: rewardStars })
        }).then(res => res.json()).then(data => {
            const starCountDisplay = document.getElementById('star-count');
            if(starCountDisplay && data.status === 'success') {
                starCountDisplay.innerText = parseInt(starCountDisplay.innerText) + rewardStars;
            }
        });

        nextBtn.onclick = () => { window.location.href = 'course.php?module=' + moduleId; };
    }
</script>