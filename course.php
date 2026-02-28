<div class="text-center">
            <h1><?php echo htmlspecialchars($module_title); ?></h1>
            <p>¡Selecciona una lección para empezar a jugar!</p>
        </div>

        <div class="level-grid">
            <?php 
            foreach ($lessons as $index => $lesson): 
                $is_completed = $lesson['is_completed'] ? true : false;
                $stars_display = $is_completed ? "⭐ " . $lesson['stars_earned'] : "🎁 " . $lesson['reward_stars'] . " Estrellas";
                
                // Quitamos el bloqueo para que en tu MVP puedas testear todos los juegos libres
                $locked_class = ''; 
                $completed_class = $is_completed ? 'completed' : '';
                $icon = $is_completed ? '✅' : '▶️';
            ?>
                <a href="lesson.php?id=<?php echo $lesson['id']; ?>" class="level-card <?php echo $completed_class . ' ' . $locked_class; ?>">
                    <div class="level-icon"><?php echo $icon; ?></div>
                    <h3>Lección <?php echo $lesson['order_num']; ?></h3>
                    <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
                    <p style="color: #666; font-weight: bold;"><?php echo $stars_display; ?></p>
                </a>
            <?php 
            endforeach; 
            ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?> </body>
</html>