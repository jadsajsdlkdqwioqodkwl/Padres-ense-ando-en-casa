<?php
require_once 'includes/config.php';

$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($pdo, $user_id);

$stmt = $pdo->prepare("SELECT selected_words FROM progress WHERE user_id = ? AND is_completed = 1 AND selected_words IS NOT NULL");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll();

$learned_items = [];

foreach ($completed_lessons as $row) {
    $words = json_decode($row['selected_words'], true) ?: [];
    foreach ($words as $w) {
        // Ahora guardamos la mnemotecnia, pronunciación y emoji si están disponibles (Retrocompatibilidad asegurada)
        $learned_items[] = [
            'en' => $w['en'] ?? '', 
            'es' => $w['es'] ?? '',
            'emoji' => $w['emoji'] ?? '⭐',
            'mnemonic' => $w['mnemonic'] ?? '¡Palabra desbloqueada!',
            'phonetic' => $w['phonetic'] ?? ''
        ];
    }
}

// Filtro para palabras únicas basándonos en el inglés
$unique_items = [];
foreach($learned_items as $item) {
    $unique_items[$item['en']] = $item;
}
$learned_items = array_values($unique_items);
$page_title = "Mis Trofeos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/head.php'; ?>
    <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <style>
        img.emoji { height: 1.2em; width: 1.2em; margin: 0 .05em 0 .1em; vertical-align: -0.1em; display: inline-block; pointer-events: none; }
        .trophy-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 40px; }
        .trophy-card { background: var(--white); border-radius: 16px; padding: 25px 20px; text-align: center; border: 2px solid #E2E8F0; transition: 0.3s; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(28, 61, 106, 0.05); display: flex; flex-direction: column; justify-content: space-between; }
        .trophy-card:hover { transform: translateY(-5px); border-color: var(--brand-orange); box-shadow: 0 15px 35px rgba(242, 156, 56, 0.15); }
        .trophy-en { font-size: 26px; font-weight: 800; color: var(--brand-blue); margin-bottom: 5px; }
        .trophy-es { color: #64748B; font-size: 16px; font-weight: 600; margin-bottom: 15px; }
        .trophy-icon { font-size: 45px; margin-bottom: 10px; }
        .trophy-mnemonic { background: #F0F9FF; border: 1px dashed var(--brand-lblue); padding: 12px; border-radius: 12px; font-size: 14px; color: #475569; font-style: italic; flex-grow: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
        .btn-trophy-audio { background: #DBEAFE; color: var(--brand-blue); border: 2px solid #3B82F6; border-radius: 50px; padding: 8px 20px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 16px; width: 100%; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }
        .btn-trophy-audio:hover { background: #BFDBFE; transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="container text-center">
        <?php include 'includes/navbar.php'; ?>
        <h1 style="font-size: 2.5rem; margin-bottom: 10px; margin-top: 20px;">🏆 Salón de la Fama</h1>
        <p style="font-size: 1.1rem; color: #64748B;">Aquí están las palabras que <strong style="color: var(--brand-blue);"><?php echo htmlspecialchars($user_info['child_name']); ?></strong> ha conquistado.</p>

        <?php if (empty($learned_items)): ?>
            <div style="padding: 40px; background: #FFFBEB; border-radius: 16px; margin-top: 30px; border: 1px solid #FDE68A;">
                <h2 style="color: var(--brand-orange); margin-bottom: 10px;">Aún no hay trofeos</h2>
                <p style="color: #64748B; font-size: 16px;">¡Completa tu primer día para desbloquear palabras aquí!</p>
            </div>
        <?php else: ?>
            <div class="trophy-grid">
                <?php foreach ($learned_items as $item): ?>
                    <div class="trophy-card">
                        <div>
                            <div class="trophy-icon"><?php echo htmlspecialchars($item['emoji']); ?></div>
                            <div class="trophy-en"><?php echo htmlspecialchars($item['en']); ?></div>
                            <div class="trophy-es">
                                = <?php echo htmlspecialchars($item['es']); ?> 
                                <?php if($item['phonetic']) echo "<br><span style='color:var(--brand-orange); font-size:14px;'>( " . htmlspecialchars($item['phonetic']) . " )</span>"; ?>
                            </div>
                        </div>
                        <div class="trophy-mnemonic">💡 <?php echo htmlspecialchars($item['mnemonic']); ?></div>
                        <button class="btn-trophy-audio" onclick="playPronunciation('<?php echo addslashes($item['en']); ?>')" title="Escuchar pronunciación">🔊 Escuchar</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    // Inyección ligera del motor de audio Dictionary API (Reutilizado desde lesson)
    const audioCache = {};
    async function playPronunciation(word) {
        if(!word) return;
        const cleanWord = word.trim().toLowerCase();
        
        const fallbackTTS = () => {
            let utterance = new SpeechSynthesisUtterance(cleanWord);
            utterance.lang = 'en-US';
            utterance.rate = 0.85; 
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
                        audioUrl = p.audio; break;
                    }
                }
            }

            if (audioUrl) {
                const audioObj = new Audio(audioUrl);
                audioCache[cleanWord] = audioObj;
                audioObj.play().catch(fallbackTTS);
            } else { fallbackTTS(); }
        } catch (error) { fallbackTTS(); }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof twemoji !== 'undefined') twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
    });
    </script>
    <script src="assets/js/engine.js"></script>
</body>
</html>