<?php
require_once 'includes/config.php';
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0; // 0 = Mnemotecnias, 1-5 = Juegos

$stmt = $pdo->prepare("SELECT l.*, m.title as module_title FROM lessons l JOIN modules m ON l.module_id = m.id WHERE l.id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) { header("Location: course.php?module=1"); exit; }

$page_title = $lesson['title'];
$module_title = $lesson['module_title'];

// Lógica del Examen de Ayer
$palabras_ayer = [];
if ($lesson['order_num'] > 1 && $step == 0) {
    $stmtAyer = $pdo->prepare("SELECT p.selected_words FROM progress p JOIN lessons l ON p.lesson_id = l.id WHERE p.user_id = ? AND l.module_id = ? AND l.order_num = ?");
    $stmtAyer->execute([$_SESSION['user_id'], $lesson['module_id'], $lesson['order_num'] - 1]);
    $json_ayer = $stmtAyer->fetchColumn();
    if ($json_ayer) { $palabras_ayer = json_decode($json_ayer, true) ?: []; }
}

$pool_palabras = [
    ["en" => "Apple", "es" => "Manzana", "emoji" => "🍎", "mnemonic" => "Imagina a APOLLO comiendo una manzana."],
    ["en" => "Dog", "es" => "Perro", "emoji" => "🐶", "mnemonic" => "Un DUX (duque) paseando a su perro."],
    ["en" => "Cat", "es" => "Gato", "emoji" => "🐱", "mnemonic" => "Un gato manejando un CATamarán."],
    ["en" => "House", "es" => "Casa", "emoji" => "🏠", "mnemonic" => "El dr. HOUSE vive en esta casa."],
    ["en" => "Tree", "es" => "Árbol", "emoji" => "🌳", "mnemonic" => "Hay TRES pájaros en el árbol (Tree)."],
    ["en" => "Water", "es" => "Agua", "emoji" => "💧", "mnemonic" => "Me tomo mi water (uáter) embotellada."],
    ["en" => "Sun", "es" => "Sol", "emoji" => "☀️", "mnemonic" => "SAN Pedro brilla como el sol."],
    ["en" => "Moon", "es" => "Luna", "emoji" => "🌙", "mnemonic" => "Los MUNdiales se juegan hasta que sale la luna."],
    ["en" => "Car", "es" => "Auto", "emoji" => "🚗", "mnemonic" => "KARl maneja un auto muy rápido."],
    ["en" => "Book", "es" => "Libro", "emoji" => "📖", "mnemonic" => "BUCanea un libro en la biblioteca."]
];

// LÓGICA DE PLAYLIST (Si estamos en un juego)
$dynamic_rounds = [];
$template_file = '';

if ($step > 0 && $step <= 5) {
    $stmtWords = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND lesson_id = ?");
    $stmtWords->execute([$_SESSION['user_id'], $lesson_id]);
    $json_words = $stmtWords->fetchColumn();
    $saved_words = $json_words ? json_decode($json_words, true) : [];
    
    if (empty($saved_words) || !isset($saved_words[$step - 1])) {
        header("Location: lesson.php?id=" . $lesson_id);
        exit;
    }
    
    // Asignamos 1 palabra específica para este juego
    $current_word = $saved_words[$step - 1];
    
    $distractors = ["BIRD", "FISH", "STAR", "SHOE"];
    shuffle($distractors);

    // Generamos exactamente 2 rondas de esa palabra para el juego correspondiente
    for ($i = 0; $i < 2; $i++) {
        $speed = 6 + $i;
        if ($step == 1) {
            $template_file = 'templates/type_meteor_strike.php';
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'speed' => $speed,
                'items' => [['content' => $current_word['emoji'], 'is_correct' => true], ['content' => '⭐', 'is_correct' => false], ['content' => '❓', 'is_correct' => false]]
            ];
        } elseif ($step == 2) {
            $template_file = 'templates/type_color_rescue.php';
            $dynamic_rounds[] = [
                'color_name' => strtoupper($current_word['en']), 'color_hex' => '#'.substr(md5($current_word['en']), 0, 6), 'item' => $current_word['emoji'], 'translation' => $current_word['es'],
                'distractors' => [['name' => 'X', 'hex' => '#333'], ['name' => 'Y', 'hex' => '#777']]
            ];
        } elseif ($step == 3) {
            $template_file = 'templates/type_defender.php';
            $dynamic_rounds[] = [
                'word' => strtoupper($current_word['en']), 'translation' => $current_word['es'],
                'distractors' => ['X', 'Z', 'M', 'Q']
            ];
        } elseif ($step == 4) {
            $template_file = 'templates/type_detective.php';
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'],
                'distractors' => [$distractors[0], $distractors[1]]
            ];
        } elseif ($step == 5) {
            $template_file = 'templates/type_potion.php';
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'],
                'distractors' => [$distractors[2], $distractors[3]]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .overlay-fullscreen { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; overflow-y: auto; padding: 20px; }
        .modal-box { 
    background: white; color: #333; border-radius: 20px; padding: 30px; 
    max-width: 800px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
    text-align: center; margin: auto; 
    max-height: 85vh; /* AÑADIDO: Límite de altura */
    overflow-y: auto; /* AÑADIDO: Scroll interno */
}
        .word-pool-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin: 20px 0; }
        .pool-word { background: #f0f0f0; border: 3px solid #ccc; border-radius: 10px; padding: 15px; cursor: pointer; transition: 0.2s; font-weight: bold; font-size: 18px; }
        .pool-word.selected { background: #d4edda; border-color: #28a745; color: #155724; transform: scale(1.05); box-shadow: 0 4px 10px rgba(40,167,69,0.3); }
        .btn-large { background: #007bff; color: white; border: none; padding: 15px 30px; font-size: 20px; border-radius: 50px; cursor: pointer; font-weight: bold; transition: 0.3s; margin-top: 20px; display: inline-block; text-decoration: none; }
        .btn-large:disabled { background: #ccc; cursor: not-allowed; }
        .btn-large:hover:not(:disabled) { background: #0056b3; transform: scale(1.05); }
        .mnemotecnia-card { border: 2px dashed #007bff; padding: 20px; border-radius: 15px; margin-bottom: 20px; background: #f8faff; text-align: left; }
        .listen-btn { background: #ffc107; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 20px; cursor: pointer; margin-left: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <?php include 'includes/audio_engine.php'; ?>

    <?php if($step == 0): ?>
        <?php if($lesson['order_num'] > 1): ?>
        <div id="exam-modal" class="overlay-fullscreen">
            <div class="modal-box">
                <h2 style="color: #d9534f;">📝 Examen de las Palabras de Ayer</h2>
                <div id="exam-questions" style="text-align: left; margin: 20px 0;"></div>
                <button id="btn-submit-exam" class="btn-large" onclick="evaluarExamen()">Entregar Examen</button>
            </div>
        </div>
        
        <div id="diploma-modal" class="overlay-fullscreen" style="display: none;">
            <div class="modal-box">
                <h2>🎉 ¡Felicidades! 🎉</h2>
                <canvas id="diploma-canvas" width="600" height="600" style="display: block; width: 100%; border-radius: 15px; border: 5px solid #ffc107; margin-bottom: 20px;"></canvas>
                <a id="btn-download-diploma" class="btn-large" style="background: #28a745; margin-right: 10px;" href="#" download="Mi_Diploma_Ingles.png">Descargar Foto</a>
                <button class="btn-large" onclick="cerrarDiplomaYContinuar()">Continuar al Día de Hoy</button>
            </div>
        </div>
        <?php endif; ?>

        <div id="pool-modal" class="overlay-fullscreen" style="<?php echo ($lesson['order_num'] > 1) ? 'display: none;' : ''; ?>">
            <div class="modal-box">
                <h2 style="color: #007bff;">🎯 Tu Pool de Palabras</h2>
                <p>Selecciona <strong>5 palabras</strong> para aprender el día de hoy.</p>
                <div class="word-pool-grid" id="pool-grid">
                    <?php foreach($pool_palabras as $index => $word): ?>
                        <div class="pool-word" data-en="<?php echo htmlspecialchars($word['en']); ?>" data-es="<?php echo htmlspecialchars($word['es']); ?>" data-emoji="<?php echo htmlspecialchars($word['emoji']); ?>" data-mnemonic="<?php echo htmlspecialchars($word['mnemonic']); ?>" onclick="toggleWordSelection(this)">
                            <?php echo htmlspecialchars($word['en']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="font-size: 18px; font-weight: bold; margin-top: 10px;">Seleccionadas: <span id="selection-count">0</span>/5</div>
                <button id="btn-confirm-pool" class="btn-large" disabled onclick="confirmarSeleccion()">Confirmar mis 5 palabras</button>
            </div>
        </div>

        <div id="mnemotecnia-modal" class="overlay-fullscreen" style="display: none;">
            <div class="modal-box">
                <h2 style="color: #28a745;">🧠 Aprende con Mnemotecnias</h2>
                <p style="font-size: 18px; color: #555;">Copia estas palabras y sus trucos en tu cuaderno.</p>
                <div id="mnemotecnias-container"></div>
                <button class="btn-large" onclick="finalizarMnemotecnias()">¡Ya las copié, a jugar!</button>
            </div>
        </div>
<div id="mnemotecnia-modal" class="overlay-fullscreen" style="display: none;">
            <div class="modal-box">
                <h2 style="color: #28a745;">🧠 Aprende con Mnemotecnias</h2>
                <p style="font-size: 18px; color: #555;">Copia estas palabras y sus trucos en tu cuaderno.</p>
                <div id="mnemotecnias-container"></div>
                <button class="btn-large" onclick="finalizarMnemotecnias()">¡Ya las copié, a jugar!</button>
            </div>
        </div>
        
        <script src="assets/js/engine.js"></script>
    <?php else: ?>
        <script> window.dynamicRoundsData = <?php echo json_encode($dynamic_rounds); ?>; </script>
        
        <div class="container">
            <?php include 'includes/navbar.php'; ?>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <h1 style="margin: 0; font-size: 24px;">Juego <?php echo $step; ?> de 5</h1>
                    <button id="music-toggle" onclick="toggleMusic()" style="font-size: 24px; background: none; border: none; cursor: pointer; padding: 0;">🔇</button>
                </div>
            </div>
            
            <div class="game-wrapper">
                <?php 
                if (file_exists($template_file)) { include $template_file; } 
                else { echo "<div style='color:red;'>Error: Falta archivo {$template_file}</div>"; }
                ?>
            </div>
        </div>
        <?php include 'includes/controls.php'; ?>
    <?php endif; ?>

    <script>
    // ==========================================
    // LÓGICA DE NAVEGACIÓN (PLAYLIST)
    // ==========================================
    function unlockNextButton(lessonId, stars, moduleId) {
        let currentStep = <?php echo $step; ?>;
        if (currentStep < 5) {
            // Pasar al siguiente juego
            window.location.href = 'lesson.php?id=' + lessonId + '&step=' + (currentStep + 1);
        } else {
            // Juego 5 superado. Guardar estrellas y volver al mapa.
            fetch('app/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId, stars: <?php echo $lesson['reward_stars']; ?> })
            }).then(() => {
                window.location.href = 'course.php?module=' + moduleId;
            });
        }
    }

    <?php if($step == 0): ?>
    let palabrasSeleccionadas = [];

    function toggleWordSelection(element) {
        if (element.classList.contains('selected')) {
            element.classList.remove('selected');
            palabrasSeleccionadas = palabrasSeleccionadas.filter(p => p.en !== element.dataset.en);
        } else {
            if (palabrasSeleccionadas.length >= 5) { alert("Ya seleccionaste 5 palabras."); return; }
            element.classList.add('selected');
            palabrasSeleccionadas.push({
                en: element.dataset.en,
                es: element.dataset.es,
                emoji: element.dataset.emoji,
                mnemonic: element.dataset.mnemonic
            });
        }
        document.getElementById('selection-count').innerText = palabrasSeleccionadas.length;
        document.getElementById('btn-confirm-pool').disabled = palabrasSeleccionadas.length !== 5;
    }

    function confirmarSeleccion() {
        document.getElementById('pool-modal').style.display = 'none';
        const container = document.getElementById('mnemotecnias-container');
        container.innerHTML = '';
        palabrasSeleccionadas.forEach((palabra) => {
            const card = document.createElement('div');
            card.className = 'mnemotecnia-card';
            card.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div><h3 style="margin: 0; font-size: 24px; color: #007bff;">${palabra.emoji} ${palabra.en} <span style="color: #666;">= ${palabra.es}</span></h3></div>
                    <button class="listen-btn" onclick="playTTS('${palabra.en}', false)">🔊</button>
                </div>
                <div style="background: #e9ecef; padding: 10px; border-radius: 8px; font-style: italic;">💡 ${palabra.mnemonic}</div>
            `;
            container.appendChild(card);
        });
        document.getElementById('mnemotecnia-modal').style.display = 'flex';
    }

    function finalizarMnemotecnias() {
        // Guardamos solo las palabras y redirigimos al Juego 1
        fetch('app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_id: <?php echo $lesson_id; ?>, selected_words: palabrasSeleccionadas, just_words: true })
        }).then(() => {
            window.location.href = 'lesson.php?id=<?php echo $lesson_id; ?>&step=1';
        });
    }

    // Lógica visual del diploma
    <?php if($lesson['order_num'] > 1): ?>
    const palabrasAyer = <?php echo json_encode($palabras_ayer); ?>;
    document.addEventListener('DOMContentLoaded', () => {
        const examContainer = document.getElementById('exam-questions');
        let html = '';
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach((palabra, i) => {
                html += `<div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 5px solid #007bff;">
                    <strong>¿Qué significa '${palabra.en}'?</strong><br>
                    <label><input type="radio" name="q${i}" value="correct"> ${palabra.es}</label><br>
                    <label><input type="radio" name="q${i}" value="wrong"> Otra cosa</label>
                </div>`;
            });
        }
        examContainer.innerHTML = html;
    });

    function evaluarExamen() {
        document.getElementById('exam-modal').style.display = 'none';
        document.getElementById('diploma-modal').style.display = 'flex';
        
        const canvas = document.getElementById('diploma-canvas');
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = "#fff8e7"; ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = "#ffc107"; ctx.lineWidth = 15; ctx.strokeRect(15, 15, canvas.width - 30, canvas.height - 30);
        ctx.fillStyle = "#333"; ctx.textAlign = "center";
        ctx.font = "bold 32px Arial"; ctx.fillText("🏆 ¡REPORTE DE LOGROS! 🏆", canvas.width/2, 70);
        
        let startY = 160;
        ctx.font = "bold 24px Arial";
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach(p => { ctx.fillStyle = "#007bff"; ctx.fillText(p.en + " = " + p.es, canvas.width/2, startY); startY += 45; });
        }
        ctx.fillStyle = "#ff4757"; ctx.fillText("¡PREGÚNTAME ESTO EN LA CENA!", canvas.width/2, startY + 50);
        document.getElementById('btn-download-diploma').href = canvas.toDataURL('image/png');
    }

    function cerrarDiplomaYContinuar() {
        document.getElementById('diploma-modal').style.display = 'none';
        document.getElementById('pool-modal').style.display = 'flex';
    }
<?php endif; ?>
    <?php endif; ?>
    </script>
    
    <script src="assets/js/engine.js"></script>
</body>
</html>