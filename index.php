<?php
require_once 'includes/config.php';

// Obtenemos las estrellas del usuario actual
$user_id = $_SESSION['user_id'] ?? 1;
// Si la tabla users existe, saca las estrellas. Si no, pon 0 por ahora.
$total_stars = 0; 
try {
    $stmt = $pdo->prepare("SELECT total_stars FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $total_stars = $stmt->fetchColumn() ?: 0;
} catch(Exception $e) {}

$page_title = "Home";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .dashboard { text-align: center; padding: 40px 20px; }
        .big-mascot { font-size: 100px; margin: 20px 0; animation: float 3s ease-in-out infinite; }
        .star-display { font-size: 30px; font-weight: bold; color: #FFD700; background: #fffde7; padding: 15px 30px; border-radius: 40px; display: inline-block; border: 2px solid #ffe082; margin-bottom: 30px; }
        .btn-start { background: var(--success); color: white; font-size: 24px; padding: 15px 40px; text-decoration: none; border-radius: 30px; box-shadow: 0 6px 0 #388e3c; display: inline-block; }
        .btn-start:hover { transform: translateY(2px); box-shadow: 0 4px 0 #388e3c; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    </style>
</head>
<body>
    <div class="container dashboard">
        <h1>Welcome to English 15! 🚀</h1>
        <div class="big-mascot">🐶</div>
        <div class="star-display">You have <?php echo $total_stars; ?> ⭐</div>
        <p style="font-size: 18px; color: var(--text-muted); margin-bottom: 30px;">Ready to learn new words and play fun games?</p>
        <a href="course.php" class="btn-start">Play Now ➡️</a>
    </div>
</body>
</html>