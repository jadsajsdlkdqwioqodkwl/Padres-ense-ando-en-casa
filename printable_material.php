<?php
// printable_material.php
session_start();
require_once 'includes/config.php';

// Seguridad: Validar sesión
if (!isset($_SESSION['user_id']) || !isset($_GET['lesson_id'])) {
    header("Location: login.php");
    exit();
}

$lesson_id = (int)$_GET['lesson_id'];
$user_id = $_SESSION['user_id'];

// Obtener la data de la lección
$stmt = $pdo->prepare("SELECT p.selected_words, l.title, m.title as module_title 
                       FROM progress p 
                       JOIN lessons l ON p.lesson_id = l.id
                       JOIN modules m ON l.module_id = m.id
                       WHERE p.user_id = ? AND p.lesson_id = ? AND p.is_completed = 1");
$stmt->execute([$user_id, $lesson_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || empty($row['selected_words'])) {
    die("Error: No se encontró información o la lección no ha sido completada.");
}

$words = json_decode($row['selected_words'], true) ?: [];

// Preparamos los arrays para el juego de Matching
$matching_en = [];
$matching_es = [];
foreach ($words as $w) {
    $matching_en[] = ['en' => $w['en'], 'emoji' => $w['emoji']];
    $matching_es[] = $w['es'];
}
// Mezclamos las respuestas en español para que el niño trace las líneas correctamente
shuffle($matching_es);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimible - <?php echo htmlspecialchars($row['module_title'] . " " . $row['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <style>
        :root { --brand-blue: #1C3D6A; --brand-green: #68A93E; --brand-orange: #F59E0B; }
        * { box-sizing: border-box; }
        body { 
            margin: 0; padding: 0; background: #E2E8F0; 
            font-family: 'Outfit', sans-serif; color: #333; 
        }
        
        /* REGLA MAESTRA PARA IMPRESIÓN A4 */
        @page { size: A4; margin: 0; }
        
        .page { 
            width: 210mm; min-height: 297mm; padding: 20mm; 
            margin: 10mm auto; border-radius: 5px; 
            background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            position: relative;
        }

        h1 { font-family: 'Fredoka', sans-serif; color: var(--brand-blue); text-align: center; margin-top: 0; font-size: 28px; border-bottom: 3px dashed #CBD5E1; padding-bottom: 15px; }
        h2 { font-family: 'Fredoka', sans-serif; color: var(--brand-green); font-size: 20px; margin-top: 30px; margin-bottom: 15px; }
        
        /* 1. MATCHING STYLES */
        .matching-grid { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .matching-col { width: 40%; display: flex; flex-direction: column; gap: 25px; }
        .match-item { background: #F8FAFC; border: 2px solid #E2E8F0; padding: 15px; border-radius: 12px; text-align: center; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .match-dot { width: 12px; height: 12px; background: #94A3B8; border-radius: 50%; display: inline-block; }
        .col-left .match-item { justify-content: space-between; }
        .col-right .match-item { justify-content: space-between; flex-direction: row-reverse; }

        /* 2. WRITING STYLES */
        .writing-row { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .writing-word { width: 150px; font-weight: bold; font-size: 20px; color: var(--brand-blue); display: flex; align-items: center; gap: 10px; }
        .writing-line { flex: 1; border-bottom: 2px dashed #94A3B8; height: 30px; margin: 0 10px; }

        /* 3. FLASHCARDS STYLES */
        .flashcards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .flashcard { border: 2px dashed #94A3B8; padding: 20px; border-radius: 16px; text-align: center; }
        .fc-en { font-size: 24px; font-weight: bold; color: var(--brand-blue); margin-bottom: 5px; }
        .fc-es { font-size: 18px; color: #64748B; font-weight: 600; margin-bottom: 10px; }
        .fc-ph { color: var(--brand-orange); font-weight: bold; font-size: 16px; margin-bottom: 15px; }
        .fc-mn { background: #F0F9FF; padding: 10px; border-radius: 8px; font-size: 14px; font-style: italic; color: #475569; }

        img.emoji { height: 1.5em; width: 1.5em; vertical-align: -0.2em; }

        /* BOTÓN DE IMPRESIÓN (No se ve en el papel) */
        .btn-print {
            position: fixed; bottom: 30px; right: 30px; background: var(--brand-blue); color: white;
            padding: 15px 30px; border-radius: 50px; font-family: 'Fredoka', sans-serif;
            font-size: 20px; cursor: pointer; border: none; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.3);
            transition: 0.3s; z-index: 1000;
        }
        .btn-print:hover { transform: translateY(-5px); background: #152d66; }

        /* MEDIA QUERY PARA OCULTAR BOTONES AL IMPRIMIR Y AJUSTAR MÁRGENES */
        @media print {
            body { background: white; }
            .page { margin: 0; border: none; box-shadow: none; width: 100%; height: 100%; page-break-after: always; padding: 10mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <button class="btn-print no-print" onclick="window.print()">🖨️ Imprimir Hoja A4</button>

    <div class="page">
        <h1>Material de Repaso: <?php echo htmlspecialchars($row['module_title'] . " - " . $row['title']); ?></h1>

        <h2>✏️ 1. Une con una línea (Matching)</h2>
        <p style="font-size: 14px; color: #64748B;">Instrucción: Traza una línea desde la palabra en inglés hacia su significado en español.</p>
        <div class="matching-grid">
            <div class="matching-col col-left">
                <?php foreach($matching_en as $en): ?>
                    <div class="match-item">
                        <span><?php echo $en['emoji'] . " " . $en['en']; ?></span>
                        <span class="match-dot"></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="matching-col col-right">
                <?php foreach($matching_es as $es): ?>
                    <div class="match-item">
                        <span><?php echo $es; ?></span>
                        <span class="match-dot"></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <h2 style="margin-top: 50px;">✍️ 2. Hora de escribir (Writing)</h2>
        <p style="font-size: 14px; color: #64748B;">Instrucción: Copia la palabra en inglés sobre las líneas punteadas.</p>
        <div style="margin-top: 25px;">
            <?php foreach($words as $w): ?>
                <div class="writing-row">
                    <div class="writing-word"><?php echo $w['emoji'] . " " . $w['en']; ?></div>
                    <div class="writing-line"></div>
                    <div class="writing-line"></div>
                    <div class="writing-line"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top: 50px;">🧠 3. Flashcards de Apoyo (Para Papá/Mamá)</h2>
        <p style="font-size: 14px; color: #64748B;">Instrucción: Puedes recortar estas tarjetas. Léelas en voz alta para practicar juntos la pronunciación y el truco mental.</p>
        <div class="flashcards-grid">
            <?php foreach($words as $w): ?>
                <div class="flashcard">
                    <div style="font-size: 40px; margin-bottom: 10px;"><?php echo $w['emoji']; ?></div>
                    <div class="fc-en"><?php echo $w['en']; ?></div>
                    <div class="fc-es">= <?php echo $w['es']; ?></div>
                    <div class="fc-ph">Se lee: (<?php echo $w['phonetic']; ?>)</div>
                    <div class="fc-mn">💡 <?php echo $w['mnemonic']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
        });
    </script>
</body>
</html>