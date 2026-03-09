<?php
// includes/navbar.php
$user_info = (isset($_SESSION['user_id'])) ? getUserInfo($pdo, $_SESSION['user_id']) : null;
$current_stars = $user_info ? $user_info['total_stars'] : 0;
$child_name = $user_info ? $user_info['child_name'] : 'Explorador';
?>
<style>
    /* FIX: Navbar con marco visible, bordes definidos y centrada */
    .top-navbar-wrapper {
        width: 100%;
        padding: 0;
        margin-bottom: 25px;
        box-sizing: border-box;
    }

    .top-navbar { 
        display: flex; justify-content: space-between; align-items: center; 
        background: #ffffff;
        border: 2px solid #E2E8F0; /* Bordes visibles */
        border-radius: 16px; /* Bordes redondeados */
        padding: 15px 25px; 
        flex-wrap: wrap; gap: 15px; 
        width: 100%; max-width: 100%; 
        box-sizing: border-box; 
        box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05);
    }
    
    .nav-brand { 
        flex-shrink: 0; max-width: 100%;
    }
    .nav-brand a { 
        font-size: clamp(20px, 5vw, 24px); font-weight: 800; color: var(--brand-blue); 
        text-decoration: none; display: flex; align-items: center; gap: 10px; 
    }
    .nav-brand img { height: clamp(30px, 8vw, 40px); width: auto; max-width: 100%; }
    
    .nav-menu { 
        display: flex; gap: 10px; align-items: center; flex-wrap: wrap; 
        justify-content: flex-end; flex: 1; box-sizing: border-box;
    }
    
    .nav-link { 
        text-decoration: none; color: var(--brand-blue); font-weight: 700; 
        padding: clamp(8px, 2vw, 10px) clamp(15px, 4vw, 22px); border-radius: 50px; 
        transition: 0.3s; font-size: clamp(0.85rem, 2.5vw, 0.95rem); 
        background: var(--bg-light); border: 1px solid #E2E8F0; text-align: center;
        box-sizing: border-box; white-space: nowrap;
    }
    .nav-link:hover { background: #E2E8F0; transform: translateY(-2px); }
    
    .nav-logout { color: #E53E3E; border: 1px solid #FC8181; background: #FFF5F5; }
    .nav-logout:hover { background: #E53E3E; color: white; }
    
    .stars-badge { 
        background: #FFFBEB; border: 2px solid #FCD34D; padding: clamp(8px, 2vw, 10px) clamp(15px, 4vw, 20px); 
        border-radius: 50px; font-weight: 800; color: #D97706; 
        display: flex; align-items: center; gap: 8px; font-size: clamp(0.9rem, 2.5vw, 1rem); 
        box-shadow: 0 4px 6px rgba(252, 211, 77, 0.2); justify-content: center; box-sizing: border-box;
    }
    .user-badge { 
        font-weight: 700; color: var(--brand-blue); background: #E0E7FF; 
        padding: clamp(8px, 2vw, 10px) clamp(15px, 4vw, 20px); border-radius: 50px; 
        font-size: clamp(0.85rem, 2.5vw, 0.95rem); text-align: center; box-sizing: border-box; white-space: nowrap;
    }

    @media (max-width: 768px) {
        .top-navbar { flex-direction: column; text-align: center; justify-content: center; padding: 15px; }
        .nav-brand { width: 100%; display: flex; justify-content: center; margin-bottom: 10px; }
        .nav-menu { width: 100%; justify-content: center; gap: 8px; }
    }
    
    @media (max-width: 480px) {
        .nav-menu { flex-direction: row; width: 100%; justify-content: center; }
        .user-badge, .stars-badge { flex: 1 1 45%; }
        .nav-link { flex: 1 1 100%; width: 100%; }
    }
</style>

<div class="top-navbar-wrapper">
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
</div>