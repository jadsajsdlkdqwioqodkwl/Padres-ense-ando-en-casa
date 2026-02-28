<?php
// includes/companion.php
?>
<style>
    .companion-area {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #eef2ff;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: bold;
        color: var(--primary);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .companion-avatar { 
        font-size: 30px; 
        animation: bounce 2s infinite; 
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>

<div class="companion-area">
    <span class="companion-avatar" id="mascot">🐶</span>
    <span id="mascot-text">Let's play!</span>
</div>

<script>
    // Esta función es llamada desde type_drag_drop.php cuando hay un acierto o error
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