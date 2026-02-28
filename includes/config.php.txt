<?php
// Iniciar sesión para mantener los datos del niño activos
session_start();

// Configuración de la Base de Datos (Conectando al contenedor 'db_kids' de Docker)
define('DB_HOST', 'db_kids'); 
define('DB_USER', 'postgres'); 
define('DB_PASS', 'password');     
define('DB_NAME', 'kids_english_app'); 

// Conexión segura usando PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// SIMULACIÓN DE LOGIN (Por ahora forzamos al usuario ID 1 que creamos en el SQL)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}

// Función global para obtener las estrellas actuales del niño
function getUserStars($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT total_stars FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}
?>