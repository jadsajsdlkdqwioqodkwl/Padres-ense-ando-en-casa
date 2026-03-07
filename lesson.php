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

// POOL DE 30 PALABRAS CON FONÉTICA Y MNEMOTECNIAS
$pool_palabras = [
    ["en" => "Apple", "es" => "Manzana", "phonetic" => "ápol", "emoji" => "🍎", "mnemonic" => "Imagina a APOLLO comiendo una manzana."],
    ["en" => "Dog", "es" => "Perro", "phonetic" => "dog", "emoji" => "🐶", "mnemonic" => "Un DUX paseando a su perro."],
    ["en" => "Cat", "es" => "Gato", "phonetic" => "cat", "emoji" => "🐱", "mnemonic" => "Un gato manejando un CATamarán."],
    ["en" => "House", "es" => "Casa", "phonetic" => "jáus", "emoji" => "🏠", "mnemonic" => "El dr. HOUSE vive en esta casa."],
    ["en" => "Tree", "es" => "Árbol", "phonetic" => "tri", "emoji" => "🌳", "mnemonic" => "Hay TRES pájaros en el árbol."],
    ["en" => "Water", "es" => "Agua", "phonetic" => "uóter", "emoji" => "💧", "mnemonic" => "Me tomo mi water (uáter) embotellada."],
    ["en" => "Sun", "es" => "Sol", "phonetic" => "san", "emoji" => "☀️", "mnemonic" => "SAN Pedro brilla como el sol."],
    ["en" => "Moon", "es" => "Luna", "phonetic" => "mun", "emoji" => "🌙", "mnemonic" => "Los MUNdiales se juegan hasta la luna."],
    ["en" => "Car", "es" => "Auto", "phonetic" => "car", "emoji" => "🚗", "mnemonic" => "KARl maneja un auto muy rápido."],
    ["en" => "Book", "es" => "Libro", "phonetic" => "buk", "emoji" => "📖", "mnemonic" => "BUCanea un libro en la biblioteca."],
    ["en" => "Milk", "es" => "Leche", "phonetic" => "milk", "emoji" => "🥛", "mnemonic" => "Tomo milk (leche) con chocolate."],
    ["en" => "Bird", "es" => "Pájaro", "phonetic" => "berd", "emoji" => "🐦", "mnemonic" => "El pájaro es de color VERDe."],
    ["en" => "Fish", "es" => "Pez", "phonetic" => "fish", "emoji" => "🐟", "mnemonic" => "Un pez nadando en FISH (fichas)."],
    ["en" => "Star", "es" => "Estrella", "phonetic" => "star", "emoji" => "⭐", "mnemonic" => "La estrella quiere ESTAR en el cielo."],
    ["en" => "Shoe", "es" => "Zapato", "phonetic" => "shu", "emoji" => "👞", "mnemonic" => "SHU, espanta al bicho de tu zapato."],
    ["en" => "Door", "es" => "Puerta", "phonetic" => "dor", "emoji" => "🚪", "mnemonic" => "Abre la puerta con DORor (dolor)."],
    ["en" => "Boy", "es" => "Niño", "phonetic" => "boi", "emoji" => "👦", "mnemonic" => "VOY (boy) a jugar con ese niño."],
    ["en" => "Girl", "es" => "Niña", "phonetic" => "guerl", "emoji" => "👧", "mnemonic" => "La niña tira un GUERL (gol)."],
    ["en" => "Pen", "es" => "Lapicero", "phonetic" => "pen", "emoji" => "🖊️", "mnemonic" => "Me da PENa perder mi lapicero."],
    ["en" => "Bed", "es" => "Cama", "phonetic" => "bed", "emoji" => "🛏️", "mnemonic" => "VETE (bed) a la cama ya."],
    ["en" => "Hand", "es" => "Mano", "phonetic" => "jand", "emoji" => "✋", "mnemonic" => "Dame una JAND (mano) con esto."],
    ["en" => "Foot", "es" => "Pie", "phonetic" => "fut", "emoji" => "🦶", "mnemonic" => "Patea el FUTbol con el pie."],
    ["en" => "Eye", "es" => "Ojo", "phonetic" => "ai", "emoji" => "👁️", "mnemonic" => "¡AY! (Eye) me entró algo al ojo."],
    ["en" => "Nose", "es" => "Nariz", "phonetic" => "nous", "emoji" => "👃", "mnemonic" => "NO USes (Nose) tu nariz para empujar."],
    ["en" => "Mouth", "es" => "Boca", "phonetic" => "mauth", "emoji" => "👄", "mnemonic" => "Abre la MAUTH (boca) en el dentista."],
    ["en" => "Red", "es" => "Rojo", "phonetic" => "red", "emoji" => "🔴", "mnemonic" => "Tengo una RED (red) de color rojo."],
    ["en" => "Blue", "es" => "Azul", "phonetic" => "blu", "emoji" => "🔵", "mnemonic" => "El BLUtooth de mi celular es azul."],
    ["en" => "Green", "es" => "Verde", "phonetic" => "grin", "emoji" => "🟢", "mnemonic" => "El GRINch es de color verde."],
    ["en" => "Yellow", "es" => "Amarillo", "phonetic" => "ielou", "emoji" => "🟡", "mnemonic" => "El HIELO (Yellow) no es amarillo."],
    ["en" => "Black", "es" => "Negro", "phonetic" => "blak", "emoji" => "⚫", "mnemonic" => "La BLACkberry (mora) es casi negra."]
];

// LÓGICA DE JUEGOS DENTRO DEL CURSO (1 Sola Ronda)
$dynamic_rounds = [];
$template_file = '';
$current_word = null;

if ($step > 0 && $step <= 5) {
    $stmtWords = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND lesson_id = ?");
    $stmtWords->execute([$_SESSION['user_id'], $lesson_id]);
    $json_words = $stmtWords->fetchColumn();
    $saved_words = $json_words ? json_decode($json_words, true) : [];
    
    if (empty($saved_words) || !isset($saved_words[$step - 1])) {
        header("Location: lesson.php?id=" . $lesson_id); exit;
    }
    
    $current_word = $saved_words[$step - 1];
    $distractors = ["BIRD", "FISH", "STAR", "SHOE"]; shuffle($distractors);

    // BUCLE CAMBIADO A 1 SOLA RONDA PARA MÁS AGILIDAD
    for ($i = 0; $i < 1; $i++) {
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
                'color_name' => strtoupper($current_word['en']), 'color_hex' => '#000', 'item' => $current_word['emoji'], 'translation' => $current_word['es'],
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
        .overlay-fullscreen { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 20px; }
        .modal-box { 
            background: white; color: #333; border-radius: 20px; padding: 30px; 
            max-width: 800px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            text-align: center; margin: auto; 
            max-height: 85vh; overflow-y: auto; 
        }
        .word-pool-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin: 20px 0; }
        .pool-word { background: #f0f0f0; border: 3px solid #ccc; border-radius: 10px; padding: 15px; cursor: pointer; transition: 0.2s; font-weight: bold; font-size: 18px; }
        .pool-word.selected { background: #d4edda; border-color: #28a745; color: #155724; transform: scale(1.05); box-shadow: 0 4px 10px rgba(40,167,69,0.3); }
        .btn-large { background: #007bff; color: white; border: none; padding: 15px 30px; font-size: 20px; border-radius: 50px; cursor: pointer; font-weight: bold; transition: 0.3s; margin-top: 20px; display: inline-block; text-decoration: none; }
        .btn-large:disabled { background: #ccc; cursor: not-allowed; }
        .btn-large:hover:not(:disabled) { background: #0056b3; transform: scale(1.05); }
        .mnemotecnia-card { border: 2px dashed #007bff; padding: 20px; border-radius: 15px; margin-bottom: 20px; background: #f8faff; text-align: left; }
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
                        <div class="pool-word" data-en="<?php echo htmlspecialchars($word['en']); ?>" data-es="<?php echo htmlspecialchars($word['es']); ?>" data-phonetic="<?php echo htmlspecialchars($word['phonetic']); ?>" data-emoji="<?php echo htmlspecialchars($word['emoji']); ?>" data-mnemonic="<?php echo htmlspecialchars($word['mnemonic']); ?>" onclick="toggleWordSelection(this)">
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
                <p style="font-size: 18px; color: #555;">Papá/Mamá, enseña estos trucos y pronunciaciones antes de jugar.</p>
                <div id="mnemotecnias-container"></div>
                <button class="btn-large" onclick="finalizarMnemotecnias()">¡A jugar!</button>
            </div>
        </div>
        
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
        
        <div id="end-game-modal" class="overlay-fullscreen" style="display: none; z-index: 10000;">
            <div class="modal-box">
                <h2 style="color: #28a745;">🎉 ¡Nivel Completado! 🎉</h2>
                <div style="font-size: 60px;" id="end-emoji"></div>
                <h3 style="font-size: 30px; color: #007bff; margin: 10px 0;" id="end-word"></h3>
                <p style="font-size: 20px; font-weight: bold; color: #d9534f;" id="end-phonetic"></p>
                <p style="font-size: 18px; color: #555; background: #e9ecef; padding: 15px; border-radius: 10px; font-style: italic; margin-top: 15px;" id="end-mnemonic"></p>
                <button class="btn-large" id="btn-next-level">Siguiente Reto ➡️</button>
            </div>
        </div>
        
        <?php include 'includes/controls.php'; ?>
    <?php endif; ?>

    <script>
    const currentPlayingWord = <?php echo json_encode($current_word ?? null); ?>;

    // ==========================================
    // LÓGICA DE NAVEGACIÓN Y MODAL DE VICTORIA (JUEGOS)
    // ==========================================
    function unlockNextButton(lessonId, stars, moduleId) {
        if(currentPlayingWord) {
            // Mostrar modal de repaso antes de avanzar
            document.getElementById('end-emoji').innerText = currentPlayingWord.emoji;
            document.getElementById('end-word').innerText = currentPlayingWord.en + " = " + currentPlayingWord.es;
            document.getElementById('end-phonetic').innerText = "Se pronuncia: (" + currentPlayingWord.phonetic + ")";
            document.getElementById('end-mnemonic').innerText = "💡 " + currentPlayingWord.mnemonic;
            document.getElementById('end-game-modal').style.display = 'flex';

            document.getElementById('btn-next-level').onclick = () => {
                executeNextLevelAdvance(lessonId, stars, moduleId);
            };
        } else {
            executeNextLevelAdvance(lessonId, stars, moduleId);
        }
    }

    function executeNextLevelAdvance(lessonId, stars, moduleId) {
        let currentStep = <?php echo $step; ?>;
        if (currentStep < 5) {
            window.location.href = 'lesson.php?id=' + lessonId + '&step=' + (currentStep + 1);
        } else {
            fetch('app/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId, stars: <?php echo $lesson['reward_stars']; ?> })
            }).then(() => {
                window.location.href = 'course.php?module=' + moduleId;
            });
        }
    }

    // ==========================================
    // LÓGICA DE SELECCIÓN Y EXÁMENES (STEP 0)
    // ==========================================
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
                mnemonic: element.dataset.mnemonic,
                phonetic: element.dataset.phonetic
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
                <div style="margin-bottom: 10px;">
                    <h3 style="margin: 0; font-size: 24px; color: #007bff;">${palabra.emoji} ${palabra.en} <span style="color: #666;">= ${palabra.es}</span></h3>
                    <p style="color: #d9534f; font-weight: bold; margin: 5px 0;">Se pronuncia: (${palabra.phonetic})</p>
                </div>
                <div style="background: #e9ecef; padding: 10px; border-radius: 8px; font-style: italic;">💡 ${palabra.mnemonic}</div>
            `;
            container.appendChild(card);
        });
        document.getElementById('mnemotecnia-modal').style.display = 'flex';
    }

    function finalizarMnemotecnias() {
        fetch('app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_id: <?php echo $lesson_id; ?>, selected_words: palabrasSeleccionadas, just_words: true })
        }).then(() => {
            window.location.href = 'lesson.php?id=<?php echo $lesson_id; ?>&step=1';
        });
    }

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
            palabrasAyer.forEach(p => { 
                ctx.fillStyle = "#007bff"; 
                // Añadimos la pronunciación al texto del diploma
                const phonTxt = p.phonetic ? " (" + p.phonetic + ")" : "";
                ctx.fillText(p.en + phonTxt + " = " + p.es, canvas.width/2, startY); 
                startY += 45; 
            });
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