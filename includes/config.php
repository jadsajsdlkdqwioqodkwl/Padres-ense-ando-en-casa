<?php
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

// Función global para obtener las estrellas actuales del niño
function getUserStars($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT total_stars FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 0;
}

// ==========================================
// 🛡️ SISTEMA DE PROTECCIÓN (REDIRECCIÓN)
// ==========================================
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'register.php']; // Páginas que no requieren login

// Si no hay sesión iniciada y no está en una página pública, lo mandamos al login
if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: login.php");
    exit;
}
?>