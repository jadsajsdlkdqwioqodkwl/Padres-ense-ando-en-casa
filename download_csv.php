<?php
// download_csv.php
session_start();
require_once 'includes/config.php';

// Ciberseguridad: Validar que el usuario esté logueado
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

// Configurar los headers para forzar la descarga de un archivo Excel-compatible CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Imprimible_' . str_replace(' ', '_', $row['module_title']) . '_' . str_replace(' ', '_', $row['title']) . '.csv"');

// Abrir el buffer de salida PHP
$output = fopen('php://output', 'w');

// BOM para que Excel lea el UTF-8 (Acentos, tildes, emojis) correctamente
fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

// Título general del archivo
fputcsv($output, ['========================================================']);
fputcsv($output, ['MATERIAL DE REPASO: ' . strtoupper($row['module_title']) . ' - ' . strtoupper($row['title'])]);
fputcsv($output, ['========================================================']);
fputcsv($output, []);

// 1. MATCHING (Unir palabra con significado)
fputcsv($output, ['=== 1. JUEGO DE MATCHING ===']);
fputcsv($output, ['Instrucción: Corta estas celdas y haz que tu hijo(a) una la palabra en inglés con su significado.']);
fputcsv($output, ['CORTAR LADO INGLÉS', '', 'CORTAR LADO ESPAÑOL']);
foreach ($words as $w) {
    fputcsv($output, [$w['en'], '', $w['es']]);
}
fputcsv($output, []);

// 2. WRITING (Plana de palabras)
fputcsv($output, ['=== 2. HORA DE ESCRIBIR (WRITING) ===']);
fputcsv($output, ['Instrucción: Que tu hijo(a) escriba la palabra en inglés en los espacios vacíos para practicar.']);
fputcsv($output, ['Palabra a copiar', 'Intento 1', 'Intento 2', 'Intento 3']);
foreach ($words as $w) {
    fputcsv($output, [$w['en'], '_________________', '_________________', '_________________']);
}
fputcsv($output, []);

// 3. FLASHCARDS PARA PAPÁ/MAMÁ (Pronunciación y Mnemotecnia)
fputcsv($output, ['=== 3. FLASHCARDS DE APOYO (PARA TI) ===']);
fputcsv($output, ['Instrucción: Lee la pronunciación en voz alta y recuérdale el truco de la mnemotecnia si se equivoca.']);
fputcsv($output, ['Palabra (Inglés)', 'Significado', 'Pronunciación', 'Truco / Mnemotecnia']);
foreach ($words as $w) {
    fputcsv($output, [
        $w['en'],
        $w['es'],
        '(' . $w['phonetic'] . ')',
        $w['mnemonic']
    ]);
}

fclose($output);
exit();
?>