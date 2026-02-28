SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    child_name VARCHAR(50) NOT NULL, 
    parent_phone VARCHAR(20), 
    total_stars INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    title VARCHAR(100) NOT NULL, 
    color_theme VARCHAR(20) DEFAULT '#2B3A67', 
    order_num INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    module_id INT, 
    title VARCHAR(100) NOT NULL, 
    template_type VARCHAR(50) NOT NULL, 
    content_data JSON NOT NULL, 
    reward_stars INT DEFAULT 3, 
    order_num INT NOT NULL, 
    FOREIGN KEY (module_id) REFERENCES modules(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS progress (
    user_id INT, 
    lesson_id INT, 
    is_completed BOOLEAN DEFAULT FALSE, 
    stars_earned INT DEFAULT 0, 
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (user_id, lesson_id), 
    FOREIGN KEY (user_id) REFERENCES users(id), 
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DATA INICIAL
INSERT INTO users (child_name, parent_phone, total_stars) VALUES ('Explorador', '+51928529656', 0);
INSERT INTO modules (title, order_num) VALUES ('My World', 1);

-- 1. JUEGO DE DELETREO (Word Defender)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 'El Monstruo de la Manzana', 'defender', '{"guide": {"intro": "Arma la palabra rápido", "steps": []}, "word": "APPLE", "translation": "Manzana", "distractors": ["X", "Z", "M"], "time_limit": 15}', 5, 1);

-- 2. JUEGO DE GRAMÁTICA (Crazy Bridge)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 'Puente de Palabras', 'sentence_survival', '{"guide": {"intro": "Ordena la frase para cruzar el río", "steps": []}, "sentence": ["I", "have", "a", "red", "apple"], "translation": "Yo tengo una manzana roja", "distractors": ["has", "an", "blue"], "time_limit": 20}', 10, 2);

-- 3. JUEGO DE ESCUCHA RÁPIDA (Meteor Strike)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 'Lluvia de Meteoritos', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito correcto", "steps": []}, "target_word": "APPLE", "translation": "Manzana", "items": [{"id": 1, "content": "🍎", "is_correct": true}, {"id": 2, "content": "🍌", "is_correct": false}, {"id": 3, "content": "🍇", "is_correct": false}], "speed": 6}', 5, 3);

-- 4. JUEGO DE COLORES (Color Rescue)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 'Rescata el Color', 'color_rescue', '{"guide": {"intro": "Lanza la pintura correcta", "steps": []}, "target_color_name": "Red", "target_color_hex": "#ff4757", "target_item": "🍎", "translation": "Rojo", "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Green", "hex": "#2ed573"}], "time_limit": 15}', 5, 4);

-- 5. EXAMEN FINAL BATTLE
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 'Batalla Final', 'exam', '{"guide": {"intro": "¡Derrota al jefe!", "steps": []}, "questions": [{"q": "¿Cómo se dice Perro?", "options": ["Cat", "Dog", "Bird"], "answer": "Dog"}, {"q": "¿Qué significa Apple?", "options": ["Manzana", "Pera", "Plátano"], "answer": "Manzana"}], "time_limit": 8, "lives": 3}', 15, 5);