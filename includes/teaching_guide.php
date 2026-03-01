<?php
// includes/teaching_guide.php
$guide = $lesson_data['guide'] ?? ['intro' => '¡Es hora de preparar la misión en el mundo real!', 'steps' => []];

// Si el juego es de vocabulario simple, extraemos las palabras de las rondas para la lista de dibujo
$draw_list = [];
if (isset($lesson_data['rounds'])) {
    foreach ($lesson_data['rounds'] as $r) {
        if (isset($r['word'])) $draw_list[] = ['en' => $r['word'], 'es' => $r['translation'], 'ph' => $r['phonetic']];
        else if (isset($r['target_word'])) $draw_list[] = ['en' => $r['target_word'], 'es' => $r['translation'], 'ph' => $r['phonetic']];
        else if (isset($r['color_name'])) $draw_list[] = ['en' => $r['color_name'], 'es' => $r['translation'], 'ph' => $r['phonetic']];
    }
}
?>

<div id="parent-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
    <div style="background: white; padding: 30px; border-radius: 25px; max-width: 550px; width: 92%; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.3); border: 5px solid var(--primary);">
        
        <h2 style="color: var(--primary); margin-top: 0; text-align: center; font-size: 24px;">👨‍🏫 Misión para Papá y Mamá</h2>
        
        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #ffeeba;">
            <p style="margin: 0; font-weight: bold; font-size: 16px;">📝 Actividad en el Cuaderno:</p>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Antes de jugar, pide a tu hijo que dibuje estos objetos en su cuaderno y escriba su nombre en Inglés y Español al lado.</p>
        </div>

        <div style="max-height: 45vh; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;">
            <?php if (!empty($draw_list)): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <?php foreach ($draw_list as $item): ?>
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 12px; border: 2px solid #eee; text-align: center;">
                            <div style="font-size: 20px; font-weight: bold; color: var(--accent);">"<?php echo $item['en']; ?>"</div>
                            <div style="font-size: 13px; color: #666;"><?php echo $item['es']; ?></div>
                            <button onclick="playTTS('<?php echo $item['ph']; ?>')" style="margin-top: 8px; background: white; border: 1px solid #ddd; border-radius: 50%; width: 35px; height: 35px; cursor: pointer;">🔊</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Repasen juntos la lección de hoy.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 13px; color: #999; margin-bottom: 10px;">¿Ya terminaron los dibujos en el cuaderno?</p>
            <button onclick="document.getElementById('parent-modal').style.display='none'; startLessonTimer();" style="width: 100%; padding: 18px; background: var(--success); color: white; font-size: 20px; font-weight: bold; border: none; border-radius: 35px; cursor: pointer; box-shadow: 0 6px 0 #27ae60;">¡Sí, a jugar! 🚀</button>
        </div>
    </div>
</div>