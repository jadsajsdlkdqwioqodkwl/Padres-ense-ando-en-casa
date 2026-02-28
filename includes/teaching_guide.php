<?php
$guide = $lesson_data['guide'] ?? ['intro' => '¡Hola! Antes de empezar, repasen estas palabras juntos.', 'steps' => []];
?>
<div id="parent-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(3px);">
    <div style="background: white; padding: 30px; border-radius: 20px; max-width: 500px; width: 90%; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        
        <button onclick="document.getElementById('parent-modal').style.display='none'" style="position: absolute; top: 15px; right: 15px; font-size: 24px; background: #eee; border: none; cursor: pointer; border-radius: 50%; width: 40px; height: 40px;">❌</button>
        
        <h3 style="color: var(--primary); margin-top: 0; display: flex; align-items: center; gap: 10px;">👨‍🏫 Guía para Padres</h3>
        <p style="color: #555;"><?php echo htmlspecialchars($guide['intro']); ?></p>
        
        <?php if (!empty($guide['steps'])): ?>
        <div style="max-height: 50vh; overflow-y: auto; padding-right: 10px;">
            <?php foreach($guide['steps'] as $step): ?>
                <div style="background: #f8f9fa; padding: 15px; margin-bottom: 12px; border-radius: 12px; border-left: 5px solid var(--accent); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 18px;"><strong>"<?php echo $step['en']; ?>"</strong> <span style="color: #d9534f; font-family: monospace;"><?php echo $step['ph']; ?></span></div>
                        <div style="font-size: 14px; color: #666;">Significa: <?php echo $step['es']; ?></div>
                    </div>
                    <button style="background: var(--accent); color: white; border: none; padding: 10px 15px; border-radius: 20px; cursor: pointer; font-weight: bold;" onclick="playTTS('<?php echo addslashes($step['en']); ?>', 'en-US')">🔊</button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 15px;">
                <strong>⭐ Acompaña a tu hijo a descubrir la misión.</strong>
            </div>
        <?php endif; ?>
        
        <button onclick="document.getElementById('parent-modal').style.display='none'" style="width: 100%; padding: 15px; background: var(--success); color: white; font-size: 18px; font-weight: bold; border: none; border-radius: 30px; margin-top: 20px; cursor: pointer;">¡Entendido!</button>
    </div>
</div>