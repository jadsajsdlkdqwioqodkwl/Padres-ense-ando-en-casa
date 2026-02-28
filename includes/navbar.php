<?php
$current_stars = (function_exists('getUserStars') && isset($_SESSION['user_id'])) ? getUserStars($pdo, $_SESSION['user_id']) : 0;
?>
<style>
    .top-hud {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;
    }
    .stars-counter { font-size: 24px; font-weight: bold; color: #FFD700; text-shadow: 1px 1px 0 #b89b00; }
    .companion-area {
        display: flex; align-items: center; gap: 10px; background: #eef2ff;
        padding: 10px 20px; border-radius: 30px; font-weight: bold; color: var(--primary);
    }
    .companion-avatar { font-size: 30px; }
</style>

<div class="top-hud">
    <div class="companion-area">
        <span class="companion-avatar" id="mascot">🐶</span>
        <span id="mascot-text">¡A jugar!</span>
    </div>
    <div class="stars-counter">⭐ <span id="star-count"><?php echo $current_stars; ?></span></div>
</div>