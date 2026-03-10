<?php
require_once 'includes/config.php';
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0; // 0 = Mnemotecnias, 1-5 = Juegos

// Ciberseguridad: Consulta preparada con validación estricta
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

// EXTRAER PALABRAS DE HOY PARA LOS JUEGOS Y EL CUADERNO
$saved_words = [];
$current_word = null;

$stmtWords = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND lesson_id = ?");
$stmtWords->execute([$_SESSION['user_id'], $lesson_id]);
$json_words = $stmtWords->fetchColumn();
$saved_words = $json_words ? json_decode($json_words, true) : [];

// LÓGICA DE JUEGOS DENTRO DEL CURSO
$dynamic_rounds = [];
$template_file = '';

if ($step > 0 && $step <= 5) {
    if (empty($saved_words) || !isset($saved_words[$step - 1])) {
        header("Location: lesson.php?id=" . $lesson_id); exit;
    }
    
    $current_word = $saved_words[$step - 1];

    $distractor_pool = array_filter($pool_palabras, function($p) use ($current_word) {
        return strtoupper($p['en']) !== strtoupper($current_word['en']);
    });
    shuffle($distractor_pool);
    $d1 = array_values($distractor_pool)[0];
    $d2 = array_values($distractor_pool)[1];
    $d3 = array_values($distractor_pool)[2];
    $d4 = array_values($distractor_pool)[3];

    for ($i = 0; $i < 1; $i++) {
        if ($step == 1) {
            $template_file = 'templates/type_frogs.php'; 
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'phonetic' => $current_word['phonetic'],
                'emoji' => $current_word['emoji'],
                'distractors' => [
                    ['word' => strtoupper($d1['en']), 'emoji' => $d1['emoji']],
                    ['word' => strtoupper($d2['en']), 'emoji' => $d2['emoji']]
                ],
                'context_es' => '¡Cruza el río saltando en la palabra!',
                'mnemonic' => $current_word['mnemonic']
            ];
        } elseif ($step == 2) {
            $template_file = 'templates/type_ninja.php';
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'phonetic' => $current_word['phonetic'],
                'emoji' => $current_word['emoji'],
                'items' => [
                    ['content' => $current_word['emoji'], 'is_correct' => true], 
                    ['content' => $d3['emoji'], 'is_correct' => false], 
                    ['content' => $d4['emoji'], 'is_correct' => false]
                ],
                'context_es' => 'Corta la figura correcta 3 veces:',
                'mnemonic' => $current_word['mnemonic']
            ];
        } elseif ($step == 3) {
            $template_file = 'templates/type_monster.php'; 
            $dynamic_rounds[] = [
                'word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'phonetic' => $current_word['phonetic'],
                'emoji' => $current_word['emoji'],
                'distractors' => [strtoupper($d1['en'][0]), strtoupper($d2['en'][0]), 'X', 'Z'],
                'context_es' => 'Atrapa las letras correctas de:',
                'mnemonic' => $current_word['mnemonic']
            ];
        } elseif ($step == 4) {
            $template_file = 'templates/type_moles.php'; 
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'phonetic' => $current_word['phonetic'],
                'emoji' => $current_word['emoji'],
                'distractors' => [
                    ['word' => strtoupper($d1['en']), 'emoji' => $d1['emoji']],
                    ['word' => strtoupper($d2['en']), 'emoji' => $d2['emoji']]
                ],
                'context_es' => 'Toca rápidamente los agujeros donde aparezca:',
                'mnemonic' => $current_word['mnemonic']
            ];
        } elseif ($step == 5) {
            $template_file = 'templates/type_rocket.php'; 
            $dynamic_rounds[] = [
                'target_word' => strtoupper($current_word['en']), 'translation' => $current_word['es'], 'phonetic' => $current_word['phonetic'],
                'emoji' => $current_word['emoji'],
                'distractors' => [
                    ['word' => strtoupper($d3['en']), 'emoji' => $d3['emoji']],
                    ['word' => strtoupper($d4['en']), 'emoji' => $d4['emoji']]
                ],
                'context_es' => 'Mueve el cohete para atrapar solo:',
                'mnemonic' => $current_word['mnemonic']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <style>
        img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
        
        /* Modales seguros sin desbordamiento derecho */
        .overlay-fullscreen { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(28, 61, 106, 0.95); backdrop-filter: blur(5px); 
            z-index: 9999; display: flex; flex-direction: column; align-items: center; 
            justify-content: center; color: white; padding: 20px; box-sizing: border-box; 
        }
        .modal-box { 
            background: var(--white); color: var(--text-main); border-radius: 24px; 
            padding: clamp(20px, 5vw, 40px); width: 100%; max-width: 600px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.2); text-align: center; margin: auto; 
            max-height: 85vh; overflow-y: auto; border-top: 6px solid var(--brand-blue);
            box-sizing: border-box; 
        }

        .btn-play { margin-top: 30px; padding: 16px 30px; background: var(--brand-orange, #F59E0B); color: white; border-radius: 50px; font-weight: 800; font-size: 1.2rem; border: none; cursor: pointer; transition: 0.3s; width: 100%; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3); box-sizing: border-box; }
        .btn-play:hover { background: #D97706; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(245, 158, 11, 0.4); }
        .btn-play.bg-green-500 { background: var(--brand-green, #10B981); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); }
        .btn-play.bg-green-500:hover { background: #059669; }

        /* BOTÓN DE AUDIO GIGANTE UX MEJORADO */
        .btn-audio-huge {
            font-size: clamp(25px, 5vw, 35px); background: #DBEAFE; color: #1E3A8A;
            border: 4px solid #3B82F6; border-radius: 50%;
            width: clamp(55px, 10vw, 75px); height: clamp(55px, 10vw, 75px);
            cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4); flex-shrink: 0; padding: 0;
        }
        .btn-audio-huge:hover { transform: scale(1.15) rotate(5deg); background: #BFDBFE; box-shadow: 0 12px 30px rgba(59, 130, 246, 0.5); }
        .btn-audio-huge:active { transform: scale(0.95); }

        /* TAMAÑO DEL BOTÓN DE MÚSICA DEL JUEGO */
        .btn-music-game {
            font-size: clamp(24px, 5vw, 32px); background: #F8FAFC;
            border: 3px solid #E2E8F0; border-radius: 50%;
            width: clamp(50px, 8vw, 65px); height: clamp(50px, 8vw, 65px);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); padding: 0;
        }
        .btn-music-game:hover { transform: scale(1.1) rotate(-10deg); border-color: var(--brand-lblue); }

        /* POOL DE PALABRAS ESTILO BURGER APILADAS */
        .word-pool-grid { 
            display: flex; flex-direction: column; gap: 15px; margin: 25px 0; 
            width: 100%; box-sizing: border-box; 
        }
        .pool-word { 
            background: var(--bg-light); border: 2px solid #E2E8F0; border-radius: 12px; 
            padding: 15px; cursor: pointer; transition: 0.3s; font-weight: 700; 
            font-size: 18px; color: var(--brand-blue); display: flex; align-items: center; 
            justify-content: center; gap: 15px; width: 100%; box-sizing: border-box;
        }
        .pool-word:hover { border-color: var(--brand-lblue); transform: translateY(-2px); }
        .pool-word.selected { background: #F0FDF4; border-color: var(--brand-green); color: var(--brand-green); transform: scale(1.02); box-shadow: 0 8px 20px rgba(104, 169, 62, 0.2); }
        
        .btn-large { background: var(--brand-green); color: white; border: none; padding: clamp(12px, 3vw, 16px) clamp(25px, 5vw, 35px); font-size: clamp(16px, 3vw, 18px); border-radius: 50px; cursor: pointer; font-weight: 700; transition: 0.3s; margin-top: 25px; display: inline-block; text-decoration: none; box-shadow: 0 4px 14px rgba(104, 169, 62, 0.3); width: 100%; box-sizing: border-box; }
        .btn-large:disabled { background: #CBD5E1; box-shadow: none; cursor: not-allowed; transform: none !important; }
        .btn-large:hover:not(:disabled) { background: #579232; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(104, 169, 62, 0.4); }
        
        .mnemotecnia-card { border: 2px dashed var(--brand-lblue); padding: clamp(15px, 4vw, 25px); border-radius: 16px; margin-bottom: 20px; background: #F0F9FF; text-align: left; width: 100%; box-sizing: border-box; }

        .exam-option-label {
            cursor: pointer; display: block; margin-bottom: 12px; font-size: 16px; 
            padding: 12px; background: #ffffff; border: 2px solid #E2E8F0; 
            border-radius: 12px; transition: all 0.2s;
        }
        .exam-option-label:hover { background: #F0F9FF; border-color: var(--brand-lblue); }
        .exam-option-label input[type="radio"] { transform: scale(1.3); margin-right: 10px; }
    </style>
</head>
<body>
    <?php include 'includes/audio_engine.php'; ?>

    <?php if($step == 0): ?>
        <?php if($lesson['order_num'] > 1): ?>
        <div id="exam-modal" class="overlay-fullscreen">
            <div class="modal-box" style="border-top-color: var(--brand-orange);">
                <h2 style="color: var(--brand-orange); font-size: clamp(1.8rem, 5vw, 2rem); margin-bottom: 10px;">📝 Examen de las Palabras de Ayer</h2>
                <div id="exam-questions" style="text-align: left; margin: 25px 0;"></div>
                <button id="btn-submit-exam" class="btn-large" onclick="evaluarExamen()">Entregar Examen</button>
            </div>
        </div>
        
        <div id="diploma-modal" class="overlay-fullscreen" style="display: none;">
            <div class="modal-box" style="border-top-color: #FBBF24;">
                <h2 style="color: var(--brand-blue); font-size: clamp(1.8rem, 5vw, 2.2rem); margin-bottom: 20px;">🎉 ¡Felicidades! 🎉</h2>
                <canvas id="diploma-canvas" width="600" height="600" style="display: block; width: 100%; max-width: 100%; border-radius: 16px; border: 4px solid #FBBF24; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); box-sizing: border-box; background: #FFFBEB;"></canvas>
                <div style="display: flex; gap: 15px; justify-content: center; flex-direction: column; width: 100%;">
                    <button id="btn-download-diploma" class="btn-large" style="background: var(--brand-blue); margin-top: 0; box-shadow: 0 4px 14px rgba(28, 61, 106, 0.3);" onclick="descargarDiploma()">📸 Descargar Foto</button>
                    <button class="btn-large" style="margin-top: 0;" onclick="cerrarDiplomaYContinuar()">Continuar al Día de Hoy ➡️</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div id="pool-modal" class="overlay-fullscreen" style="<?php echo ($lesson['order_num'] > 1) ? 'display: none;' : ''; ?>">
            <div class="modal-box">
                <h2 style="color: var(--brand-blue); font-size: clamp(1.8rem, 5vw, 2rem);">🎯 Tu Pool de Palabras</h2>
                <p style="color: #64748B; font-size: clamp(1rem, 3vw, 1.1rem);">Selecciona <strong>5 palabras</strong> para aprender el día de hoy.</p>
                <div class="word-pool-grid" id="pool-grid">
                    </div>
                <div style="display:flex; justify-content:center; align-items:center; gap:15px; margin-top:15px; width: 100%;">
                    <button onclick="changePoolPage(-1)" id="btn-prev-page" class="btn" style="padding: 10px 20px; margin:0; background: #CBD5E1; flex: 1;">⬅️</button>
                    <span id="page-indicator" style="font-weight:bold; color: var(--brand-blue); flex: 1;">1 / 5</span>
                    <button onclick="changePoolPage(1)" id="btn-next-page" class="btn" style="padding: 10px 20px; margin:0; background: var(--brand-lblue); flex: 1;">➡️</button>
                </div>
                <div style="font-size: 18px; font-weight: 700; color: var(--brand-blue); margin-top: 15px;">Seleccionadas: <span id="selection-count">0</span>/5</div>
                <button id="btn-confirm-pool" class="btn-large" disabled onclick="confirmarSeleccion()">Confirmar mis 5 palabras</button>
            </div>
        </div>

        <div id="mnemotecnia-modal" class="overlay-fullscreen" style="display: none;">
            <div class="modal-box" style="border-top-color: var(--brand-green);">
                <h2 style="color: var(--brand-green); font-size: clamp(1.8rem, 5vw, 2.2rem); margin-bottom: 10px;">🧠 Aprende con Mnemotecnias</h2>
                <p style="font-size: clamp(14px, 3vw, 16px); color: #64748B; margin-bottom: 30px;">Papá/Mamá, presiona el botón de audio para aprender la pronunciación y enséñale estos trucos antes de jugar.</p>
                <div id="mnemotecnias-container" style="width: 100%; box-sizing: border-box;"></div>
                <button class="btn-large" onclick="finalizarMnemotecnias()">¡A jugar!</button>
            </div>
        </div>
        
    <?php else: ?>
        <script> window.dynamicRoundsData = <?php echo json_encode($dynamic_rounds); ?>; </script>
        
        <div class="container">
            <?php include 'includes/navbar.php'; ?>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; flex-wrap: wrap; gap: 10px; background: #F0F9FF; padding: 15px 20px; border-radius: 12px; border: 1px solid var(--brand-lblue); box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05);">
                <div style="display: flex; align-items: center; gap: 15px; width: 100%; justify-content: space-between;">
                    <h1 style="margin: 0; font-size: clamp(20px, 5vw, 26px); color: var(--brand-blue); font-weight: 900;">🎮 Juego <?php echo $step; ?> de 5</h1>
                    <button id="music-toggle" class="btn-music-game" onclick="toggleMusic()" title="Música de fondo">🎵</button>
                </div>
            </div>
            
            <div class="game-wrapper">
                <?php 
                if (file_exists($template_file)) { include $template_file; } 
                else { echo "<div style='color:var(--brand-orange); font-weight:bold;'>Error: Falta archivo {$template_file}</div>"; }
                ?>
            </div>
        </div>
        
        <div id="end-game-modal" class="overlay-fullscreen" style="display: none; z-index: 10000;">
            <div class="modal-box" style="border-top-color: var(--brand-green);">
                <h2 style="color: var(--brand-green); font-size: clamp(1.8rem, 5vw, 2.2rem); margin-bottom: 20px;">🎉 ¡Nivel Completado! 🎉</h2>
                <div style="font-size: clamp(50px, 15vw, 70px); margin-bottom: 10px;" id="end-emoji"></div>
                
                <div style="display: flex; justify-content: center; align-items: center; gap: 20px; margin: 15px 0; flex-wrap: wrap;">
                    <h3 style="font-size: clamp(28px, 8vw, 38px); color: var(--brand-blue); margin: 0; font-weight: 900;" id="end-word"></h3>
                    <button class="btn-audio-huge" id="btn-end-audio" title="Escuchar pronunciación">🔊</button>
                </div>

                <p style="font-size: clamp(18px, 4vw, 22px); font-weight: 800; color: var(--brand-orange);" id="end-phonetic"></p>
                <p style="font-size: clamp(15px, 3.5vw, 18px); color: #475569; background: #F8FAFC; padding: 20px; border-radius: 12px; font-style: italic; margin-top: 20px; border: 1px solid #E2E8F0;" id="end-mnemonic"></p>
                <button class="btn-large" id="btn-next-level">Siguiente Reto ➡️</button>
            </div>
        </div>

        <div id="cuaderno-modal" class="overlay-fullscreen" style="display: none; z-index: 99999;">
            <div class="modal-box" style="border-top-color: var(--brand-blue); max-width: 700px;">
                <h2 style="color: var(--brand-blue); font-size: clamp(1.8rem, 5vw, 2.2rem); margin-bottom: 10px;">📓 ¡Hora de Copiar! 📓</h2>
                <p style="color: #64748B; font-size: clamp(1rem, 3vw, 1.1rem); margin-bottom: 20px;">Copia estas 5 palabras y sus trucos en tu cuaderno para que no las olvides en el examen de mañana.</p>
                
                <div style="text-align: left; background: #F8FAFC; padding: 15px; border-radius: 12px; border: 1px solid #E2E8F0; max-height: 50vh; overflow-y: auto; margin-bottom: 20px;">
                    <?php if(!empty($saved_words)): foreach($saved_words as $w): ?>
                        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #CBD5E1;">
                            <h3 style="margin: 0; color: var(--brand-blue); font-size: 20px;"><?php echo $w['emoji'] . " " . $w['en']; ?> = <?php echo $w['es']; ?></h3>
                            <p style="margin: 5px 0 0 0; color: var(--brand-orange); font-weight: bold; font-size: 14px;">Pronunciación: (<?php echo $w['phonetic']; ?>)</p>
                            <p style="margin: 5px 0 0 0; color: #475569; font-style: italic; font-size: 14px;">💡 <?php echo $w['mnemonic']; ?></p>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                
                <button class="btn-large" onclick="finalizarLeccion()">✅ ¡Ya las copié!</button>
            </div>
        </div>
        
        <?php include 'includes/controls.php'; ?>
    <?php endif; ?>

    <script>
    // --- LÓGICA DE API DICTIONARY Y FALLBACK CIBERSEGURO ---
    const audioCache = {};

    async function playPronunciation(word) {
        if(!word) return;
        const cleanWord = word.trim().toLowerCase();
        
        const fallbackTTS = () => {
            let utterance = new SpeechSynthesisUtterance(cleanWord);
            utterance.lang = 'en-US';
            utterance.rate = 0.85; 
            utterance.pitch = 1.1; 
            window.speechSynthesis.speak(utterance);
        };

        if (audioCache[cleanWord]) {
            audioCache[cleanWord].play().catch(fallbackTTS);
            return;
        }

        try {
            const response = await fetch(`https://api.dictionaryapi.dev/api/v2/entries/en/${cleanWord}`);
            if (!response.ok) throw new Error("Palabra no encontrada en API");
            const data = await response.json();
            
            let audioUrl = "";
            if(data[0] && data[0].phonetics) {
                for (let p of data[0].phonetics) {
                    if (p.audio && p.audio.length > 0) {
                        audioUrl = p.audio;
                        break;
                    }
                }
            }

            if (audioUrl) {
                const audioObj = new Audio(audioUrl);
                audioCache[cleanWord] = audioObj;
                audioObj.play().catch(fallbackTTS);
            } else {
                fallbackTTS();
            }
        } catch (error) {
            console.warn("API Dictionary falló, usando TTS:", error);
            fallbackTTS();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof twemoji !== 'undefined') twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
    });

    const currentPlayingWord = <?php echo json_encode($current_word ?? null); ?>;

    // EL SISTEMA AHORA INTEGRA LA LÓGICA DE DIPLOMA DEL CUADERNO
    function unlockNextButton(lessonId, stars, moduleId) {
        if(currentPlayingWord) {
            document.getElementById('end-emoji').innerText = currentPlayingWord.emoji;
            document.getElementById('end-word').innerText = currentPlayingWord.en + " = " + currentPlayingWord.es;
            document.getElementById('end-phonetic').innerText = "Se pronuncia: (" + currentPlayingWord.phonetic + ")";
            document.getElementById('end-mnemonic').innerText = "💡 " + currentPlayingWord.mnemonic;
            
            document.getElementById('end-game-modal').style.display = 'flex';
            if (typeof twemoji !== 'undefined') twemoji.parse(document.getElementById('end-game-modal'), { folder: 'svg', ext: '.svg' });

            document.getElementById('btn-end-audio').onclick = () => playPronunciation(currentPlayingWord.en);
            setTimeout(() => playPronunciation(currentPlayingWord.en), 600);

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
            // Muestra el Cuaderno Modal en vez de ir de golpe a course.php
            document.getElementById('end-game-modal').style.display = 'none';
            document.getElementById('cuaderno-modal').style.display = 'flex';
            if (typeof twemoji !== 'undefined') twemoji.parse(document.getElementById('cuaderno-modal'), { folder: 'svg', ext: '.svg' });
        }
    }

    // DISPARADO AL PRESIONAR "YA LAS COPIÉ" EN EL CUADERNO
    function finalizarLeccion() {
        fetch('app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_id: <?php echo $lesson_id; ?>, stars: <?php echo $lesson['reward_stars']; ?> })
        }).then(() => {
            window.location.href = 'course.php?module=<?php echo $lesson['module_id'] ?? 1; ?>';
        });
    }

    <?php if($step == 0): ?>
    const poolPalabras = <?php echo json_encode($pool_palabras); ?>;
    let palabrasSeleccionadas = [];
    let currentPage = 0;
    const wordsPerPage = 5; 

    function renderPool() {
        const grid = document.getElementById('pool-grid');
        grid.innerHTML = '';
        const start = currentPage * wordsPerPage;
        const end = start + wordsPerPage;
        const currentWords = poolPalabras.slice(start, end);
        
        currentWords.forEach(word => {
            const isSelected = palabrasSeleccionadas.some(p => p.en === word.en);
            grid.innerHTML += `<div class="pool-word ${isSelected ? 'selected' : ''}" data-en="${word.en}" data-es="${word.es}" data-phonetic="${word.phonetic}" data-emoji="${word.emoji}" data-mnemonic="${word.mnemonic}" onclick="toggleWordSelection(this)">
                <span style="font-size: 24px;">${word.emoji}</span><span>${word.en} = ${word.es}</span>
            </div>`;
        });
        
        const totalPages = Math.ceil(poolPalabras.length / wordsPerPage);
        document.getElementById('page-indicator').innerText = (currentPage + 1) + ' / ' + totalPages;
        document.getElementById('btn-prev-page').disabled = currentPage === 0;
        document.getElementById('btn-next-page').disabled = (currentPage + 1) >= totalPages;
        
        if (typeof twemoji !== 'undefined') twemoji.parse(grid, { folder: 'svg', ext: '.svg' });
    }

    function changePoolPage(dir) {
        currentPage += dir;
        renderPool();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('pool-grid')) renderPool();
    });

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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px; width: 100%;">
                    <div style="flex: 1; min-width: 150px;">
                        <h3 style="margin: 0; font-size: clamp(20px, 5vw, 28px); color: var(--brand-blue);">${palabra.emoji} ${palabra.en} <span style="color: #64748B; font-size: clamp(16px, 4vw, 22px);">= ${palabra.es}</span></h3>
                        <p style="color: var(--brand-orange); font-weight: 800; margin: 8px 0 0 0; font-size: clamp(15px, 3.5vw, 18px);">Se pronuncia: (${palabra.phonetic})</p>
                    </div>
                    <button class="btn-audio-huge" onclick="playPronunciation('${palabra.en}')" title="Escuchar pronunciación">🔊</button>
                </div>
                <div style="background: var(--white); padding: 18px; border-radius: 12px; font-style: italic; color: #475569; border: 1px solid #E2E8F0; font-size: clamp(14px, 3.5vw, 17px); width: 100%; box-sizing: border-box;">💡 ${palabra.mnemonic}</div>
            `;
            container.appendChild(card);
        });
        document.getElementById('mnemotecnia-modal').style.display = 'flex';
        if (typeof twemoji !== 'undefined') twemoji.parse(container, { folder: 'svg', ext: '.svg' });
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
                let distractores = ["Otra cosa", "Algo diferente", "Cualquier cosa"];
                let distractor = distractores[Math.floor(Math.random() * distractores.length)];
                
                let esPrimero = Math.random() > 0.5;
                let opt1 = esPrimero ? `<input type="radio" name="q${i}" value="correct"> ${palabra.es}` : `<input type="radio" name="q${i}" value="wrong"> ${distractor}`;
                let opt2 = !esPrimero ? `<input type="radio" name="q${i}" value="correct"> ${palabra.es}` : `<input type="radio" name="q${i}" value="wrong"> ${distractor}`;

                html += `<div style="margin-bottom: 15px; padding: 20px; background: #F8FAFC; border-radius: 12px; border-left: 6px solid var(--brand-blue); border: 1px solid #E2E8F0; border-left-width: 6px;">
                    <strong style="font-size: 18px; color: var(--brand-blue); display: block; margin-bottom: 15px;">¿Qué significa '${palabra.emoji} ${palabra.en}'?</strong>
                    <label class="exam-option-label">${opt1}</label>
                    <label class="exam-option-label">${opt2}</label>
                </div>`;
            });
        }
        examContainer.innerHTML = html;
        if (typeof twemoji !== 'undefined') twemoji.parse(examContainer, { folder: 'svg', ext: '.svg' });
    });

    function evaluarExamen() {
        let allCorrect = true;
        let totalQuestions = palabrasAyer.length;
        
        for (let i = 0; i < totalQuestions; i++) {
            const radios = document.getElementsByName('q' + i);
            let answered = false;
            let isCorrect = false;
            
            for (let radio of radios) {
                if (radio.checked) {
                    answered = true;
                    if (radio.value === 'correct') {
                        isCorrect = true;
                    }
                }
            }
            
            if (!answered || !isCorrect) {
                allCorrect = false;
                break;
            }
        }

        if (!allCorrect) {
            alert("¡Oh no! 😟 Algunas respuestas están en blanco o son incorrectas. ¡Revisa con tu hijo e intenten de nuevo!");
            return; 
        }

        document.getElementById('exam-modal').style.display = 'none';
        document.getElementById('diploma-modal').style.display = 'flex';
        
        const canvas = document.getElementById('diploma-canvas');
        const ctx = canvas.getContext('2d');
        
        ctx.fillStyle = "#FFFBEB"; ctx.fillRect(0, 0, canvas.width, canvas.height); 
        ctx.strokeStyle = "#FBBF24"; ctx.lineWidth = 15; ctx.strokeRect(15, 15, canvas.width - 30, canvas.height - 30);
        
        ctx.fillStyle = "#1C3D6A"; ctx.textAlign = "center";
        ctx.font = "bold 36px -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"; 
        ctx.fillText("🏆 REPORTE DE LOGROS 🏆", canvas.width/2, 80);
        
        let startY = 150;
        ctx.font = "bold 26px -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach(p => { 
                ctx.fillStyle = "#1C3D6A"; 
                const phonTxt = p.phonetic ? " (" + p.phonetic + ")" : "";
                ctx.fillText(p.en + phonTxt + " = " + p.es, canvas.width/2, startY); 
                startY += 55; 
            });
        }
        
        ctx.fillStyle = "#F29C38"; 
        ctx.font = "bold 24px -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        ctx.fillText("¡PREGÚNTAME ESTO EN LA CENA!", canvas.width/2, startY + 60);
    }

    function descargarDiploma() {
        const canvas = document.getElementById('diploma-canvas');
        const dataURL = canvas.toDataURL('image/png');
        
        const a = document.createElement('a');
        a.href = dataURL;
        a.download = 'Mi_Reporte_Ingles.png';
        document.body.appendChild(a); 
        a.click();
        document.body.removeChild(a);
        
        const btn = document.getElementById('btn-download-diploma');
        const originalText = btn.innerHTML;
        btn.innerHTML = "✅ ¡Descargado!";
        setTimeout(() => { btn.innerHTML = originalText; }, 2000);
    }

    function cerrarDiplomaYContinuar() {
        document.getElementById('diploma-modal').style.display = 'none';
        document.getElementById('pool-modal').style.display = 'flex';
    }
<?php endif; ?>
    <?php endif; ?>
    </script>
    
    <script>
        window.levelMusicUrl = <?php echo json_encode($lesson['level_music'] ?? null); ?>;
    </script>
    <script src="assets/js/engine.js"></script>
</body>
</html>