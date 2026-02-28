<?php
$user_info = (isset($_SESSION['user_id'])) ? getUserInfo($pdo, $_SESSION['user_id']) : null;
$current_stars = $user_info ? $user_info['total_stars'] : 0;
$child_name = $user_info ? $user_info['child_name'] : 'Explorador';
?>
<style>
    .top-navbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid var(--border-color); padding-bottom: 15px; flex-wrap: wrap; gap: 15px; }
    .nav-brand a { font-size: 22px; font-weight: bold; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 8px; }
    .nav-menu { display: flex; gap: 15px; align-items: center; flex-wrap: wrap;}
    .nav-link { text-decoration: none; color: var(--text-muted); font-weight: bold; padding: 8px 15px; border-radius: 20px; transition: 0.2s; font-size: 14px; background: #eee; }
    .nav-link:hover { background: #ddd; color: var(--primary); }
    .nav-logout { color: #d9534f; border: 1px solid #ffc9c9; background: #fff5f5; }
    .nav-logout:hover { background: #d9534f; color: white; }
    .stars-badge { background: #fffde7; border: 2px solid #ffe082; padding: 5px 15px; border-radius: 20px; font-weight: bold; color: #b89b00; display: flex; align-items: center; gap: 5px; font-size: 16px; }
    .user-badge { font-weight: bold; color: var(--primary); background: #eef2ff; padding: 5px 15px; border-radius: 20px; }
</style>
<div class="top-navbar">
    <div class="nav-brand"><a href="index.php">🐶 <span>English 15</span></a></div>
    <div class="nav-menu">
        <span class="user-badge">👤 <?php echo htmlspecialchars($child_name); ?></span>
        <div class="stars-badge">⭐ <span id="star-count"><?php echo $current_stars; ?></span></div>
        <a href="index.php" class="nav-link">⬅️ Módulos</a>
        <a href="trophies.php" class="nav-link" style="background: var(--accent); color: white;">🏆 Trofeos</a>
        <a href="logout.php" class="nav-link nav-logout">Salir</a>
    </div>
</div>