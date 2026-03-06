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

// AÑADIDO: Lógica para obtener las palabras seleccionadas EL DÍA ANTERIOR para el examen
$palabras_ayer = [];
if ($lesson['order_num'] > 1) {
    $stmtAyer = $pdo->prepare("
        SELECT p.selected_words 
        FROM progress p 
        JOIN lessons l ON p.lesson_id = l.id 
        WHERE p.user_id = ? AND l.module_id = ? AND l.order_num = ?
    ");
    // Buscamos el progreso de la lección anterior de este mismo módulo
    $stmtAyer->execute([$_SESSION['user_id'], $lesson['module_id'], $lesson['order_num'] - 1]);
    $json_ayer = $stmtAyer->fetchColumn();
    
    if ($json_ayer) {
        $palabras_ayer = json_decode($json_ayer, true) ?: [];
    }
}

// AÑADIDO: Pool de 10 palabras (En el futuro, esto se puede extraer de una tabla global de vocabulario según el nivel)
$pool_palabras = [
    ["en" => "Apple", "es" => "Manzana", "phonetic" => "ápol"],
    ["en" => "Dog", "es" => "Perro", "phonetic" => "dog"],
    ["en" => "Cat", "es" => "Gato", "phonetic" => "cat"],
    ["en" => "House", "es" => "Casa", "phonetic" => "jaus"],
    ["en" => "Tree", "es" => "Árbol", "phonetic" => "tri"],
    ["en" => "Water", "es" => "Agua", "phonetic" => "uáter"],
    ["en" => "Sun", "es" => "Sol", "phonetic" => "san"],
    ["en" => "Moon", "es" => "Luna", "phonetic" => "mun"],
    ["en" => "Car", "es" => "Auto", "phonetic" => "car"],
    ["en" => "Book", "es" => "Libro", "phonetic" => "buk"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    
    <style>
        /* Estilos para los nuevos modales */
        .overlay-fullscreen {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9); z-index: 9999; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            color: white; overflow-y: auto; padding: 20px;
        }
        .modal-box {
            background: white; color: #333; border-radius: 20px; padding: 30px;
            max-width: 800px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center; margin: auto;
        }
        .word-pool-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px; margin: 20px 0;
        }
        .pool-word {
            background: #f0f0f0; border: 3px solid #ccc; border-radius: 10px; padding: 15px;
            cursor: pointer; transition: 0.2s; font-weight: bold; font-size: 18px;
        }
        .pool-word.selected {
            background: #d4edda; border-color: #28a745; color: #155724;
            transform: scale(1.05); box-shadow: 0 4px 10px rgba(40,167,69,0.3);
        }
        .btn-large {
            background: #007bff; color: white; border: none; padding: 15px 30px;
            font-size: 20px; border-radius: 50px; cursor: pointer; font-weight: bold;
            transition: 0.3s; margin-top: 20px;
        }
        .btn-large:disabled { background: #ccc; cursor: not-allowed; }
        .btn-large:hover:not(:disabled) { background: #0056b3; transform: scale(1.05); }
        
        .mnemotecnia-card {
            border: 2px dashed #007bff; padding: 20px; border-radius: 15px;
            margin-bottom: 20px; background: #f8faff; text-align: left;
        }
        .listen-btn {
            background: #ffc107; border: none; border-radius: 50%; width: 40px; height: 40px;
            font-size: 20px; cursor: pointer; margin-left: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .mnemonic-input {
            width: 100%; padding: 15px; border-radius: 10px; border: 1px solid #ccc;
            font-size: 16px; margin-top: 10px; box-sizing: border-box;
        }
        
        /* Ocultar inicialmente el contenido del juego original */
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
            <p>Demuestra lo que aprendiste ayer para generar un regalo para tu papá.</p>
            <div id="exam-questions" style="text-align: left; margin: 20px 0;">
                </div>
            <button id="btn-submit-exam" class="btn-large" onclick="evaluarExamen()">Entregar Examen</button>
        </div>
    </div>
    
    <div id="diploma-modal" class="overlay-fullscreen" style="display: none;">
        <div class="modal-box">
            <h2>🎉 ¡Felicidades! 🎉</h2>
            <p>Aquí tienes tu foto de recompensa. ¡Guárdala y muéstrasela a papá!</p>
            <canvas id="diploma-canvas" width="600" height="400" style="display: block; width: 100%; border-radius: 15px; border: 5px solid #ffc107; margin-bottom: 20px;"></canvas>
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
                    <div class="pool-word" data-en="<?php echo htmlspecialchars($word['en']); ?>" data-es="<?php echo htmlspecialchars($word['es']); ?>" data-phonetic="<?php echo htmlspecialchars($word['phonetic']); ?>" onclick="toggleWordSelection(this)">
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
            <p>Escucha cómo suena y escribe una frase creativa o un truco para recordarla.</p>
            <div id="mnemotecnias-container">
                </div>
            <button class="btn-large" onclick="finalizarMnemotecnias()">¡Terminé, a jugar!</button>
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
                if (file_exists($template_file)) { 
                    include $template_file; 
                } 
                else { 
                    echo "<div style='color:red; text-align:center;'>Error: Falta archivo {$template_file}</div>"; 
                }
                ?>
            </div>
        </div>

        <?php include 'includes/controls.php'; ?>
        <?php include 'includes/footer.php'; ?>
    </div>
    <script>
    let palabrasSeleccionadas = [];
    
    // --- LÓGICA DEL POOL DE PALABRAS ---
    function toggleWordSelection(element) {
        if (element.classList.contains('selected')) {
            element.classList.remove('selected');
            palabrasSeleccionadas = palabrasSeleccionadas.filter(p => p.en !== element.dataset.en);
        } else {
            if (palabrasSeleccionadas.length >= 5) {
                alert("Ya seleccionaste 5 palabras. Deselecciona una para cambiar.");
                return;
            }
            element.classList.add('selected');
            palabrasSeleccionadas.push({
                en: element.dataset.en,
                es: element.dataset.es,
                phonetic: element.dataset.phonetic
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

    // --- LÓGICA DE MNEMOTECNIAS ---
    function construirModalMnemotecnias() {
        const container = document.getElementById('mnemotecnias-container');
        container.innerHTML = '';
        
        palabrasSeleccionadas.forEach((palabra, index) => {
            const card = document.createElement('div');
            card.className = 'mnemotecnia-card';
            card.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0; font-size: 24px;">${palabra.en} <span style="color: #666; font-size: 18px;">(${palabra.es})</span></h3>
                    </div>
                    <button class="listen-btn" onclick="reproducirAudioScreenreader('${palabra.phonetic}')" title="Escuchar pronunciación">🔊</button>
                </div>
                <input type="text" id="mnemonic-input-${index}" class="mnemonic-input" placeholder="Escribe aquí tu mnemotecnia o truco para recordarla...">
            `;
            container.appendChild(card);
        });
    }

    function reproducirAudioScreenreader(phoneticText) {
        if ('speechSynthesis' in window) {
            let utterance = new SpeechSynthesisUtterance(phoneticText);
            utterance.lang = 'es-ES'; 
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Tu navegador no soporta lectura en voz alta.");
        }
    }

    function finalizarMnemotecnias() {
        // Recopilar las mnemotecnias escritas para guardarlas
        let todasLlenas = true;
        palabrasSeleccionadas.forEach((palabra, index) => {
            const input = document.getElementById(`mnemonic-input-${index}`);
            palabra.mnemonic_text = input.value.trim();
            if(palabra.mnemonic_text === '') todasLlenas = false;
        });
        
        if(!todasLlenas) {
            if(!confirm("No has escrito mnemotecnias para todas las palabras. ¿Seguro que quieres continuar?")) {
                return;
            }
        }
        
        // CONEXIÓN AL BACKEND: Guardamos las 5 palabras elegidas para el examen de mañana
        fetch('app/save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                lesson_id: <?php echo $lesson_id; ?>,
                stars: 0, // Aún no gana las estrellas finales, pero aseguramos las palabras
                selected_words: palabrasSeleccionadas
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Palabras guardadas correctamente:", data);
            
            // Ocultar modal y mostrar el juego original
            document.getElementById('mnemotecnia-modal').style.display = 'none';
            document.getElementById('original-game-wrapper').style.display = 'block';
            
            // Ejecutar el script original que lanza la intro del juego
            startLessonTimerOriginal();
        })
        .catch(error => {
            console.error("Error guardando progreso:", error);
            alert("Hubo un problema guardando tus palabras. ¡Pero puedes jugar!");
            document.getElementById('mnemotecnia-modal').style.display = 'none';
            document.getElementById('original-game-wrapper').style.display = 'block';
            startLessonTimerOriginal();
        });
    }

    // --- LÓGICA DEL EXAMEN DEL DÍA ANTERIOR ---
    <?php if($lesson['order_num'] > 1): ?>
    document.addEventListener('DOMContentLoaded', () => {
        // Traemos las palabras reales desde PHP (Base de datos)
        const palabrasAyer = <?php echo json_encode($palabras_ayer); ?>;
        const examContainer = document.getElementById('exam-questions');
        let html = '';
        
        if (palabrasAyer && palabrasAyer.length > 0) {
            palabrasAyer.forEach((palabra, i) => {
                html += `
                <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 5px solid #007bff;">
                    <strong style="font-size: 18px;">¿Qué significa la palabra '${palabra.en}'?</strong><br>
                    <label style="display: block; margin-top: 10px; cursor: pointer;">
                        <input type="radio" name="q${i}" value="correct"> ${palabra.es}
                    </label>
                    <label style="display: block; margin-top: 5px; cursor: pointer;">
                        <input type="radio" name="q${i}" value="wrong"> Alguna otra cosa
                    </label>
                </div>`;
            });
        } else {
            html = '<p style="color: #666;">No se encontraron registros de palabras del día anterior. ¡Puedes continuar al día de hoy!</p>';
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
        
        ctx.fillStyle = "#17a2b8";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.strokeStyle = "#fff";
        ctx.lineWidth = 10;
        ctx.strokeRect(20, 20, canvas.width - 40, canvas.height - 40);
        
        ctx.fillStyle = "#fff";
        ctx.textAlign = "center";
        
        ctx.font = "bold 36px Arial";
        ctx.fillText("¡CERTIFICADO DE EXCELENCIA!", canvas.width/2, 100);
        
        ctx.font = "24px Arial";
        ctx.fillText("Por aprobar el examen de ayer con éxito.", canvas.width/2, 160);
        
        ctx.font = "italic 30px Arial";
        ctx.fillText("¡Papá, mira cuánto he aprendido!", canvas.width/2, 250);
        
        ctx.font = "18px Arial";
        let dateObj = new Date();
        ctx.fillText("Fecha: " + dateObj.toLocaleDateString(), canvas.width/2, 330);
    }

    function cerrarDiplomaYContinuar() {
        document.getElementById('diploma-modal').style.display = 'none';
        document.getElementById('pool-modal').style.display = 'flex';
    }
    <?php endif; ?>

    // --- ENVOLTORIO DE LA LÓGICA ORIGINAL ---
    document.addEventListener('DOMContentLoaded', function() {
        const originalModal = document.getElementById('parent-modal');
        if (originalModal && document.getElementById('original-game-wrapper').style.display === 'block') {
            originalModal.style.display = 'flex';
        }
    });

    function startLessonTimerOriginal() {
        if (typeof playSpanglishIntro === 'function') {
            playSpanglishIntro();
        }
    }
    </script>
</body>
</html>