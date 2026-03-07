<?php
require_once 'includes/config.php';

try {
    // Leemos el archivo schema.sql que ya corregiste
    $sql = file_get_contents('schema.sql');
    
    // Lo ejecutamos de golpe en la base de datos
    $pdo->exec($sql);
    
    echo "<h1 style='color: green;'>✅ ¡Base de datos reparada con éxito!</h1>";
    echo "<p>La tabla 'users' y todas las demás dependencias se han creado correctamente.</p>";
    echo "<a href='index.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ir al Inicio y Probar</a>";

} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ Hubo un error</h1>";
    echo "<p>Detalle técnico: " . $e->getMessage() . "</p>";
}
?>