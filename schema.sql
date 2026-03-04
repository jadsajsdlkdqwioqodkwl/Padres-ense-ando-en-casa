SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    child_name VARCHAR(50) NOT NULL, 
    parent_phone VARCHAR(20) UNIQUE, 
    total_stars INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS modules (
    id INT PRIMARY KEY, 
    title VARCHAR(100) NOT NULL, 
    color_theme VARCHAR(20) DEFAULT '#2B3A67', 
    order_num INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lessons (
    id INT PRIMARY KEY, 
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
    -- AÑADIDO: Columna para guardar las 5 palabras elegidas y sus mnemotecnias
    selected_words JSON NULL, 
    PRIMARY KEY (user_id, lesson_id), 
    FOREIGN KEY (user_id) REFERENCES users(id), 
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limpiamos las lecciones base antiguas para actualizarlas a la nueva versión de semanas
DELETE FROM progress WHERE lesson_id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14);
DELETE FROM lessons WHERE id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14);
DELETE FROM modules WHERE id IN (1,2);

-- DATA INICIAL
INSERT IGNORE INTO users (id, child_name, parent_phone, total_stars) VALUES (1, 'Explorador', '+51928529656', 0);

-- AÑADIDO Y EDITADO: Reestructuración a Semanas (Módulos 1 y 2)
INSERT IGNORE INTO modules (id, title, color_theme, order_num) VALUES (1, 'Semana 1: Mi Entorno', '#FF9F43', 1);
INSERT IGNORE INTO modules (id, title, color_theme, order_num) VALUES (2, 'Semana 2: La Naturaleza', '#10AC84', 2);

-- SEMANA 1 (7 Días enfocados en vocabulario, sin gramática)
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 1, 'Vocabulario Básico', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito correcto"}, "rounds": [{"target_word": "APPLE", "phonetic": "ápol", "translation": "Manzana", "items": [{"id": 1, "content": "🍎", "is_correct": true}, {"id": 2, "content": "🍌", "is_correct": false}]}], "speed": 6}', 5, 1),
(2, 1, 'Cosas de Casa', 'color_rescue', '{"guide": {"intro": "Lanza la pintura correcta"}, "rounds": [{"color_name": "Red", "phonetic": "red", "color_hex": "#ff4757", "item": "🏠", "translation": "Casa Roja"}]}', 5, 2),
(3, 1, 'Mi Familia', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito"}, "rounds": [{"target_word": "MOM", "phonetic": "mam", "translation": "Mamá", "items": [{"id": 1, "content": "👩", "is_correct": true}, {"id": 2, "content": "👨", "is_correct": false}]}], "speed": 6}', 5, 3),
(4, 1, 'Juguetes', 'color_rescue', '{"guide": {"intro": "Lanza la pintura correcta"}, "rounds": [{"color_name": "Blue", "phonetic": "blú", "color_hex": "#3742fa", "item": "🚗", "translation": "Auto Azul"}]}', 5, 4),
(5, 1, 'Comida', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito"}, "rounds": [{"target_word": "BREAD", "phonetic": "bred", "translation": "Pan", "items": [{"id": 1, "content": "🍞", "is_correct": true}, {"id": 2, "content": "🥩", "is_correct": false}]}], "speed": 7}', 5, 5),
(6, 1, 'Ropa', 'color_rescue', '{"guide": {"intro": "Lanza la pintura"}, "rounds": [{"color_name": "Yellow", "phonetic": "iélou", "color_hex": "#f1c40f", "item": "👕", "translation": "Polo Amarillo"}]}', 5, 6),
(7, 1, 'Día de Repaso', 'meteor_strike', '{"guide": {"intro": "Día final de la semana"}, "rounds": [{"target_word": "SHOES", "phonetic": "shus", "translation": "Zapatos", "items": [{"id": 1, "content": "👟", "is_correct": true}, {"id": 2, "content": "🧢", "is_correct": false}]}], "speed": 8}', 10, 7);

-- SEMANA 2 (7 Días enfocados en vocabulario, sin gramática)
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(8, 2, 'Animales', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito correcto"}, "rounds": [{"target_word": "DOG", "phonetic": "dog", "translation": "Perro", "items": [{"id": 1, "content": "🐶", "is_correct": true}, {"id": 2, "content": "🐱", "is_correct": false}]}], "speed": 6}', 5, 1),
(9, 2, 'Colores del Bosque', 'color_rescue', '{"guide": {"intro": "Lanza la pintura correcta"}, "rounds": [{"color_name": "Green", "phonetic": "grin", "color_hex": "#2ed573", "item": "🌳", "translation": "Árbol Verde"}]}', 5, 2),
(10, 2, 'El Clima', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito"}, "rounds": [{"target_word": "SUN", "phonetic": "san", "translation": "Sol", "items": [{"id": 1, "content": "☀️", "is_correct": true}, {"id": 2, "content": "🌧️", "is_correct": false}]}], "speed": 6}', 5, 3),
(11, 2, 'Frutas Naturales', 'color_rescue', '{"guide": {"intro": "Lanza la pintura"}, "rounds": [{"color_name": "Orange", "phonetic": "óranch", "color_hex": "#e67e22", "item": "🍊", "translation": "Naranja"}]}', 5, 4),
(12, 2, 'Insectos', 'meteor_strike', '{"guide": {"intro": "Toca el meteorito"}, "rounds": [{"target_word": "BUG", "phonetic": "bag", "translation": "Insecto", "items": [{"id": 1, "content": "🐛", "is_correct": true}, {"id": 2, "content": "🦅", "is_correct": false}]}], "speed": 7}', 5, 5),
(13, 2, 'Flores', 'color_rescue', '{"guide": {"intro": "Lanza la pintura"}, "rounds": [{"color_name": "Pink", "phonetic": "pinc", "color_hex": "#fd79a8", "item": "🌸", "translation": "Flor Rosa"}]}', 5, 6),
(14, 2, 'Explorador', 'meteor_strike', '{"guide": {"intro": "Día final de la semana"}, "rounds": [{"target_word": "RIVER", "phonetic": "ríver", "translation": "Río", "items": [{"id": 1, "content": "🌊", "is_correct": true}, {"id": 2, "content": "🔥", "is_correct": false}]}], "speed": 8}', 10, 7);

SET FOREIGN_KEY_CHECKS = 1;