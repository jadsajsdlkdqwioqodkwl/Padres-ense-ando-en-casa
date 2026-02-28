<?php
// admin.php
require_once 'includes/config.php';

// ATENCIÓN: En producción, deberías proteger este archivo comprobando que $_SESSION['is_admin'] sea true.
// Para este MVP local, lo dejaremos accesible para que puedas crear contenido rápido.

// --- Lógica para Agregar Lección ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $module_id = (int)$_POST['module_id'];
    $title = trim($_POST['title']);
    $template_type = $_POST['template_type'];
    $reward_stars = (int)$_POST['reward_stars'];
    $order_num = (int)$_POST['order_num'];
    $content_data = $_POST['content_data']; // El JSON crudo

    // Validar que el JSON sea correcto antes de guardarlo
    if (json_decode($content_data) === null) {
        $error = "❌ Error: El JSON tiene un error de sintaxis. Revisa las comillas y comas.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$module_id, $title, $template_type, $content_data, $reward_stars, $order_num])) {
            $success = "✅ ¡Lección '$title' creada con éxito!";
        }
    }
}

// --- Lógica para Eliminar Lección ---
if (isset($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    // Primero borramos el progreso asociado para evitar errores de clave foránea
    $pdo->prepare("DELETE FROM progress WHERE lesson_id = ?")->execute([$id_to_delete]);
    $pdo->prepare("DELETE FROM lessons WHERE id = ?")->execute([$id_to_delete]);
    header("Location: admin.php");
    exit;
}

// Obtener lecciones existentes
$stmt = $pdo->query("SELECT l.*, m.title as module_title FROM lessons l JOIN modules m ON l.module_id = m.id ORDER BY l.module_id, l.order_num");
$lessons = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Creador - English 15</title>
    <style>
        :root { --primary: #2B3A67; --bg: #F0F4F8; --card: #FFF; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); padding: 20px; color: #333; }
        .admin-container { max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: var(--card); padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        h1, h2 { color: var(--primary); margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-family: monospace; }
        textarea { height: 250px; resize: vertical; }
        .btn { background: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; }
        .btn-danger { background: #d9534f; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 12px; }
        .lesson-list table { width: 100%; border-collapse: collapse; }
        .lesson-list th, .lesson-list td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .json-helpers { background: #eef2ff; padding: 15px; border-radius: 8px; font-size: 12px; margin-bottom: 20px; }
        .json-helpers pre { background: #333; color: #0f0; padding: 10px; border-radius: 5px; overflow-x: auto; cursor: pointer; }
    </style>
</head>
<body>

<div style="max-width: 1000px; margin: 0 auto 20px auto;">
    <h1>🛠️ Creador de Lecciones</h1>
    <a href="index.php" style="color: var(--primary);">⬅️ Volver a la App</a>
</div>

<div class="admin-container">
    
    <div class="card">
        <h2>Nueva Lección</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>ID del Módulo:</label>
                    <input type="number" name="module_id" value="1" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Orden (Ej: 1, 2, 3):</label>
                    <input type="number" name="order_num" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Estrellas ⭐️:</label>
                    <input type="number" name="reward_stars" value="3" required>
                </div>
            </div>

            <div class="form-group">
                <label>Título de la Lección (Para el niño):</label>
                <input type="text" name="title" placeholder="Ej: Los Animales" required>
            </div>

            <div class="form-group">
                <label>Tipo de Juego:</label>
                <select name="template_type">
                    <option value="flashcards">Flashcards (Tarjetas)</option>
                    <option value="matching">Matching (Unir Parejas)</option>
                    <option value="drag_drop">Drag & Drop (Arrastrar)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Datos (Formato JSON estricto):</label>
                <textarea name="content_data" required>{
    "guide": {
        "intro": "Vamos a aprender palabras nuevas.",
        "steps": [
            {"en": "Dog", "es": "Perro", "ph": "[dog]"}
        ]
    },
    "flashcards": [
        {"en": "Dog", "es": "Perro", "ph": "[dog]"}
    ]
}</textarea>
            </div>

            <button type="submit" class="btn">💾 Guardar Lección</button>
        </form>
    </div>

    <div>
        <div class="card json-helpers">
            <h3>💡 Chuletas de JSON (Copia y pega en la caja)</h3>
            
            <strong>Para Matching:</strong>
            <pre onclick="navigator.clipboard.writeText(this.innerText); alert('Copiado!');">
{
  "guide": {
    "intro": "Repasa antes de jugar",
    "steps": [{"en": "Cat", "es": "Gato", "ph": "[cat]"}]
  },
  "pairs": [
    {"id": 1, "left": "Dog", "right": "🐶"},
    {"id": 2, "left": "Cat", "right": "🐱"}
  ]
}</pre>

            <strong>Para Drag & Drop:</strong>
            <pre onclick="navigator.clipboard.writeText(this.innerText); alert('Copiado!');">
{
  "guide": {
    "intro": "Repasa antes de jugar",
    "steps": [{"en": "Nose", "es": "Nariz", "ph": "[nous]"}]
  },
  "items": [
    {"word": "NOSE", "icon": "👃"},
    {"word": "EYES", "icon": "👀"}
  ]
}</pre>
        </div>

        <div class="card lesson-list">
            <h2>Lecciones Existentes</h2>
            <table>
                <tr>
                    <th>Módulo</th>
                    <th>#</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
                <?php foreach ($lessons as $l): ?>
                <tr>
                    <td><?php echo $l['module_id']; ?></td>
                    <td><?php echo $l['order_num']; ?></td>
                    <td><?php echo $l['title']; ?></td>
                    <td><?php echo $l['template_type']; ?></td>
                    <td>
                        <a href="?delete=<?php echo $l['id']; ?>" class="btn-danger" onclick="return confirm('¿Borrar esta lección y el progreso asociado?');">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>
</body>
</html>