<style>
    .floating-controls {
        position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 15px; z-index: 100; background: rgba(255, 255, 255, 0.95);
        padding: 15px 25px; border-radius: 50px; box-shadow: 0 15px 35px rgba(28, 61, 106, 0.15);
        border: 1px solid #E2E8F0; backdrop-filter: blur(10px);
    }
    .btn-bottom { padding: 14px 30px; font-size: 16px; font-weight: 700; border: none; border-radius: 50px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-restart { background: var(--bg-light); color: var(--brand-blue); border: 2px solid #E2E8F0; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
    .btn-restart:hover { background: #E2E8F0; transform: translateY(-2px); }
    .btn-next { background: #CBD5E1; color: white; cursor: not-allowed; transition: 0.3s; box-shadow: none; }
    .btn-next.active { background: var(--brand-green); cursor: pointer; animation: pulse-green 2s infinite; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); }
    .btn-next.active:hover { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); animation: none; }
    
    @keyframes pulse-green { 
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(104, 169, 62, 0.4); } 
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(104, 169, 62, 0); } 
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(104, 169, 62, 0); } 
    }
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