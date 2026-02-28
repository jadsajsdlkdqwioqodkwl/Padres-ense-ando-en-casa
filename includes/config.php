<?php
// includes/config.php
session_start();

define('DB_HOST', 'db_kids'); 
define('DB_USER', 'postgres'); 
define('DB_PASS', 'password');     
define('DB_NAME', 'kids_english_app'); 

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener información completa del usuario activo
function getUserInfo($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT child_name, total_stars FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// ==========================================
// 🛡️ SISTEMA DE PROTECCIÓN (REDIRECCIÓN)
// ==========================================
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'register.php']; 

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: login.php");
    exit;
}
?>