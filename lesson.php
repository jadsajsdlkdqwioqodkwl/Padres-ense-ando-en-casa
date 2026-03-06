<?php
// =========================================================================================
// 🤖 SYSTEM PROMPT FOR AI (NOTA IMBORRABLE PARA LA IA):
// 1. CRÍTICO: Al generar o modificar este código, DEBES MANTENER la integridad total del archivo.
//    NUNCA resumas el código, NUNCA omitas secciones de HTML/PHP, NUNCA uses "[...]" para acortar.
// 2. CRÍTICO: Este archivo es el núcleo de la experiencia de usuario (lesson.php). 
//    Cualquier cambio debe entregarse como el archivo COMPLETO y funcional de principio a fin.
// 3. CRÍTICO: La lógica de audio debe respetar siempre las propiedades "phonetic" definidas
//    en la base de datos para garantizar la pronunciación adaptada al español.
// =========================================================================================

require_once 'includes/config.php';
$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$stmt = $pdo->prepare("SELECT l.*, m.title as module_title FROM lessons l JOIN modules m ON l.module_id = m.id WHERE l.id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) { 
    header("Location: course.php?module=1"); 
    exit; 
}

$lesson_data = json_decode($lesson['content_data'], true) ?: [];
$page_title = $lesson['title'];
$module_title = $lesson['module_title'];

$palabras_ayer = [];
if ($lesson['order_num'] > 1) {
    $stmtAyer = $pdo->prepare("
        SELECT p.selected_words 
        FROM progress p 
        JOIN lessons l ON p.lesson_id = l.id 
        WHERE p.user_id = ? AND l.module_id = ? AND l.order_num = ?
    ");
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .overlay-fullscreen { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; overflow-y: auto; padding: 20px; }
        .modal-box { background: white; color: #333; border-radius: 20px; padding: 30px; max-width: 800px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center; margin: auto; }
        .word-pool-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin: 20px 0; }
        .pool-word { background: #f0f0f0; border: 3px solid #ccc; border-radius: 10px; padding: 15px; cursor: pointer; transition: 0.2s; font-weight: bold; font-size: 18px; }
        .pool-word.selected { background: #d4edda; border-color: #28a745; color: #155724; transform: scale(1.05); box-shadow: 0 4px 10px rgba(40,167,69,0.3); }
        .btn-large { background: #007bff; color: white; border: none; padding: 15px 30px; font-size: 20px; border-radius: 50px; cursor: pointer; font-weight: bold; transition: 0.3s; margin-top: 20px; display: inline-block; text-decoration: none; }
        .btn-large:disabled { background: #ccc; cursor: not-allowed; }
        .btn-large:hover:not(:disabled) { background: #0056b3; transform: scale(1.05); }
        .mnemotecnia-card { border: 2px dashed #007bff; padding: 20px; border-radius: 15px; margin-bottom: 20px; background: #f8faff; text-align: left; }
        .listen-btn { background: #ffc107; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 20px; cursor: pointer; margin-left: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        #original-game-wrapper { display: none; }
        #diploma-canvas { display: none; }
    </style>
</head>
<body>
    <?php include 'includes/audio_engine.php'; ?>

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
            <p>Selecciona exactamente <strong>5 palabras</strong> que quieras aprender el día de hoy.</p>
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
            <p style="font-size: 18px; color: #555;"><strong>Toma tu cuaderno físico</strong> y copia estas palabras y sus trucos para recordarlas. ¡Toca el botón para escuchar cómo suenan!</p>
            <div id="mnemotecnias-container"></div>
            <button class="btn-large" onclick="finalizarMnemotecnias()">¡Ya las copié, a jugar!</button>
        </div>
    </div>

    <div id="original-game-wrapper">
        <div class="container">
            <?php include 'includes/navbar.php'; ?>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <a href="course.php?module=<?php echo $lesson['module_id']; ?>" style="background: var(--light); color: var(--dark); padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; border: 1px solid #ddd; transition: 0.2s;">⬅️ Volver</a>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <h1 style="margin: 0; font-size: 24px;">Día <?php echo $lesson['order_num'] . ': ' . htmlspecialchars($lesson['title']); ?></h1>
                    <button id="music-toggle" onclick="toggleMusic()" style="font-size: 24px; background: none; border: none; cursor: pointer; padding: 0;">🔇</button>
                    <button onclick="document.getElementById('parent-modal').style.display='flex'" style="font-size: 18px; background: var(--primary); color: white; border: none; cursor: pointer; border-radius: 50%; width: 35px; height: 35px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" title="Guía para Papá/Mamá">i</button>
                </div>
            </div>
            <?php include 'includes/teaching_guide.php'; ?> 
            <div class="game-wrapper">
                <?php 
                $template_type = $lesson['template_type'] ?? 'desconocido';
                $template_file = 'templates/type_' . $template_type . '.php';
                if (file_exists($template_file)) { include $template_file; } 
                else { echo "<div style='color:red; text-align:center;'>Error: Falta archivo {$template_file}</div>"; }
                ?>
            </div>
        </div>
        <?php include 'includes/controls.php'; ?>
        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
    let palabrasSeleccionadas = [];
    
    const originalFetch = window.fetch;
    window.fetch = function() {
        if (arguments[0] && arguments[0].includes('save_progress.php')) {
            let options = arguments[1];
            if (options && options.body) {
                try {
                    let bodyObj = JSON.parse(options.body);
                    bodyObj.selected_words = palabrasSeleccionadas;
                    options.body = JSON.stringify(bodyObj);
                } catch(e){}
            }
        }
        return originalFetch.apply(this, arguments);
    };

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
        construirModalMnemotecnias();
        document.getElementById('mnemotecnia-modal').style.display = 'flex';
    }

    function construirModalMnemotecnias() {
        const container = document.getElementById('mnemotecnias-container');
        container.innerHTML = '';
        palabrasSeleccionadas.forEach((palabra) => {
            const card = document.createElement('div');
            card.className = 'mnemotecnia-card';
            card.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px; color: #007bff;">${palabra.emoji} ${palabra.en} <span style="color: #666; font-size: 18px;">= ${palabra.es}</span></h3>
                    </div>
                    <button class="listen-btn" onclick="playTTS('${palabra.en}', false)" title="Escuchar pronunciación nativa">🔊</button>
                </div>
                <div style="background: #e9ecef; padding: 10px; border-radius: 8px; font-style: italic;">
                    💡 Truco: ${palabra.mnemonic}
                </div>
            `;
            container.appendChild(card);
        });
    }

    function finalizarMnemotecnias() {
        const poolEmojis = ['🍎','🐶','🐱','🏠','🌳','💧','☀️','🌙','🚗','📖','🥛','🐦','⚽','🎸','🚲'];
        let dynamicRounds = [];

        palabrasSeleccionadas.forEach(word => {
            for(let i=0; i<2; i++) {
                let dist1 = poolEmojis[Math.floor(Math.random()*poolEmojis.length)];
                let dist2 = poolEmojis[Math.floor(Math.random()*poolEmojis.length)];
                
                dynamicRounds.push({
                    target_word: word.en.toUpperCase(),
                    word: word.en.toUpperCase(),
                    translation: word.es,
                    context_es: "¡Encuentra y selecciona: " + word.es + "!",
                    speed: 6 + i,
                    items: [
                        {id: 1, content: word.emoji || '⭐', is_correct: true},
                        {id: 2, content: dist1, is_correct: false},
                        {id: 3, content: dist2, is_correct: false}
                    ]
                });
            }
        });

        dynamicRounds.sort(() => Math.random() - 0.5);
        window.dynamicRoundsData = dynamicRounds;
        
        if (typeof roundsData !== 'undefined') {
            roundsData = dynamicRounds;
            if (typeof loadRound === 'function') {
                currentRoundIndex = 0;
                loadRound(0);
            }
        }

        document.getElementById('mnemotecnia-modal').style.display = 'none';
        document.getElementById('original-game-wrapper').style.display = 'block';
    }

    <?php if($lesson['order_num'] > 1): ?>
    const palabrasAyer = <?php echo json_encode($palabras_ayer); ?>;
    
    document.addEventListener('DOMContentLoaded', () => {
        const examContainer = document.getElementById('exam-questions');
        let html = '';
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach((palabra, i) => {
                html += `
                <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 5px solid #007bff;">
                    <strong style="font-size: 18px;">¿Qué significa '${palabra.en}'?</strong><br>
                    <label style="display: block; margin-top: 10px;"><input type="radio" name="q${i}" value="correct"> ${palabra.es}</label>
                    <label style="display: block; margin-top: 5px;"><input type="radio" name="q${i}" value="wrong"> Otra cosa</label>
                </div>`;
            });
        } else {
             html = '<p style="color: #666;">No hay palabras registradas de ayer. ¡Avanza a seleccionar las de hoy!</p>';
        }
        examContainer.innerHTML = html;
    });

    function evaluarExamen() {
        document.getElementById('exam-modal').style.display = 'none';
        document.getElementById('diploma-modal').style.display = 'flex';
        generarDiplomaCanvas();
    }

    function generarDiplomaCanvas() {
        const canvas = document.getElementById('diploma-canvas');
        const ctx = canvas.getContext('2d');
        
        ctx.fillStyle = "#fff8e7"; ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = "#ffc107"; ctx.lineWidth = 15; ctx.strokeRect(15, 15, canvas.width - 30, canvas.height - 30);
        ctx.fillStyle = "#333"; ctx.textAlign = "center";
        ctx.font = "bold 32px Arial"; ctx.fillText("🏆 ¡REPORTE DE LOGROS! 🏆", canvas.width/2, 70);
        ctx.font = "20px Arial"; ctx.fillText("Aprobé mi examen de hoy con estas 5 palabras:", canvas.width/2, 110);
        
        let startY = 160;
        ctx.font = "bold 24px Arial";
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach(p => {
                ctx.fillStyle = "#007bff";
                ctx.fillText(p.en + " = " + p.es, canvas.width/2, startY);
                startY += 45;
            });
        }
        
        ctx.fillStyle = "#ff4757"; 
        ctx.font = "bold 26px Arial";
        ctx.fillText("🗣️ ¡PREGÚNTAME ESTAS 5 PALABRAS", canvas.width/2, startY + 40);
        ctx.fillText("DURANTE LA CENA!", canvas.width/2, startY + 75);
        ctx.fillStyle = "#666";
        ctx.font = "italic 18px Arial";
        ctx.fillText("Para verificar mi aprendizaje. ❤️", canvas.width/2, startY + 115);

        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('btn-download-diploma').href = dataURL;
    }

    function cerrarDiplomaYContinuar() {
        document.getElementById('diploma-modal').style.display = 'none';
        document.getElementById('pool-modal').style.display = 'flex';
    }
    <?php endif; ?>
    </script>
</body>
</html>