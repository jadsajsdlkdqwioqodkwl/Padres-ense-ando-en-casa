<?php
// includes/teaching_guide.php
// Requiere que $lesson_data['guide'] exista en el JSON de la BD.
$guide = $lesson_data['guide'] ?? [
    'intro' => '¡Hola! Antes de empezar a jugar, repasen estas palabras juntos.',
    'steps' => [] // Array por defecto si no hay pasos
];

// Si no hay pasos, no mostramos la guía
if (!empty($guide['steps'])):
?>
<style>
    .teaching-guide {
        background-color: #EEF2FF; padding: 20px; border-radius: 12px;
        border-left: 6px solid var(--primary); margin-bottom: 30px; width: 100%;
    }
    .guide-title { color: var(--primary); margin-top: 0; display: flex; align-items: center; gap: 10px;}
    .action-step { 
        display: flex; justify-content: space-between; align-items: center; 
        background: white; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .step-text { font-size: 16px; color: #333; }
    .phonetic { color: #d9534f; font-weight: bold; font-family: monospace; }
    .btn-listen { background: var(--accent); color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: bold;}
    .btn-listen:active { transform: scale(0.95); }
</style>

<div class="teaching-guide">
    <h3 class="guide-title">👨‍🏫 Guía para Papá / Mamá</h3>
    <p><?php echo htmlspecialchars($guide['intro']); ?></p>
    
    <div class="steps">
        <?php foreach($guide['steps'] as $step): ?>
            <div class="action-step">
                <div class="step-text">
                    ✅ Dile a tu hijo: <strong>"<?php echo $step['en']; ?>"</strong> <span class="phonetic"><?php echo $step['ph']; ?></span> 
                    <br><small style="color:#666;">(Significa: <?php echo $step['es']; ?>)</small>
                </div>
                <button class="btn-listen" onclick="playTTS('<?php echo addslashes($step['en']); ?>')">🔊 Escuchar</button>
            </div>
        <?php endforeach; ?>
    </div>
    <p style="margin-bottom:0; margin-top:15px; text-align:center;"><small>💡 <i>¡Cuando estén listos, empiecen el juego abajo!</i></small></p>
</div>
<?php endif; ?>