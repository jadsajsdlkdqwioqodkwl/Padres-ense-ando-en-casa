<?php
require_once 'includes/config.php';

try {
    // Intentamos añadir la columna
    $pdo->exec("ALTER TABLE users ADD COLUMN subscription_expires_at DATETIME NULL;");
    echo "<h1>✅ ¡Éxito! Base de datos actualizada.</h1>";
    echo "<p>La columna para las fechas de expiración se ha añadido correctamente.</p>";
    echo "<a href='index.php'>Volver al inicio</a>";
} catch (PDOException $e) {
    // Si la columna ya existe, dará un error, lo cual también es bueno
    echo "<h1>✅ Todo en orden.</h1>";
    echo "<p>La base de datos ya estaba lista o ha respondido: " . $e->getMessage() . "</p>";
    echo "<a href='index.php'>Volver al inicio</a>";
}
?>