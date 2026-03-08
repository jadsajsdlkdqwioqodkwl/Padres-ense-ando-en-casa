<?php
$user_info = (isset($_SESSION['user_id'])) ? getUserInfo($pdo, $_SESSION['user_id']) : null;
$current_stars = $user_info ? $user_info['total_stars'] : 0;
$child_name = $user_info ? $user_info['child_name'] : 'Explorador';
?>
<style>
    .top-navbar { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 35px; border-bottom: 2px solid var(--border-color); 
        padding-bottom: 20px; flex-wrap: wrap; gap: 20px; 
    }
    .nav-brand a { 
        font-size: 24px; font-weight: 800; color: var(--brand-blue); 
        text-decoration: none; display: flex; align-items: center; gap: 10px; 
    }
    .nav-brand img { height: 40px; width: auto; }
    
    .nav-menu { display: flex; gap: 15px; align-items: center; flex-wrap: wrap;}
    
    .nav-link { 
        text-decoration: none; color: var(--brand-blue); font-weight: 700; 
        padding: 10px 22px; border-radius: 50px; transition: 0.3s; font-size: 0.95rem; 
        background: var(--bg-light); border: 1px solid #E2E8F0;
    }
    .nav-link:hover { background: #E2E8F0; transform: translateY(-2px); }
    
    .nav-logout { color: #E53E3E; border: 1px solid #FC8181; background: #FFF5F5; }
    .nav-logout:hover { background: #E53E3E; color: white; }
    
    .stars-badge { 
        background: #FFFBEB; border: 2px solid #FCD34D; padding: 8px 20px; 
        border-radius: 50px; font-weight: 800; color: #D97706; 
        display: flex; align-items: center; gap: 8px; font-size: 1rem; 
        box-shadow: 0 4px 6px rgba(252, 211, 77, 0.2);
    }
    .user-badge { 
        font-weight: 700; color: var(--brand-blue); background: #E0E7FF; 
        padding: 8px 20px; border-radius: 50px; font-size: 0.95rem;
    }
</style>

<div class="top-navbar">
    <div class="nav-brand">
        <a href="dashboard.php">
            <img src="assets/logo-myworld.svg" alt="My World" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span style="display:none;">🚀 My World</span>
        </a>
    </div>
    <div class="nav-menu">
        <span class="user-badge">👤 <?php echo htmlspecialchars($child_name); ?></span>
        <div class="stars-badge">⭐ <span id="star-count"><?php echo $current_stars; ?></span></div>
        <a href="dashboard.php" class="nav-link">⬅️ Módulos</a>
        <a href="trophies.php" class="nav-link" style="background: var(--brand-orange); color: white; border: none; box-shadow: 0 4px 10px rgba(242, 156, 56, 0.3);">🏆 Trofeos</a>
        <a href="logout.php" class="nav-link nav-logout">Salir</a>
    </div>
</div>