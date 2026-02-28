cat << 'EOF' > schema.sql
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, child_name VARCHAR(50) NOT NULL, parent_phone VARCHAR(20), total_stars INT DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS modules (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(100) NOT NULL, color_theme VARCHAR(20) DEFAULT '#2B3A67', order_num INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS lessons (id INT AUTO_INCREMENT PRIMARY KEY, module_id INT, title VARCHAR(100) NOT NULL, template_type VARCHAR(50) NOT NULL, content_data JSON NOT NULL, reward_stars INT DEFAULT 3, order_num INT NOT NULL, FOREIGN KEY (module_id) REFERENCES modules(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS progress (user_id INT, lesson_id INT, is_completed BOOLEAN DEFAULT FALSE, stars_earned INT DEFAULT 0, completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (user_id, lesson_id), FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (lesson_id) REFERENCES lessons(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (child_name, parent_phone, total_stars) VALUES ('Explorador', '+51928529656', 0);
INSERT INTO modules (title, order_num) VALUES ('My World', 1);

INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Mi Familia', 'flashcards', '{"guide": {"intro": "Hola, repasen estas palabras antes de jugar.", "steps": [{"en": "Mom", "es": "Mamá", "ph": "[mam]"}, {"en": "Dad", "es": "Papá", "ph": "[dad]"}]}, "flashcards": [{"en": "Mom", "es": "Mamá", "ph": "[mam]", "img": "https://api.iconify.design/noto:woman.svg"}, {"en": "Dad", "es": "Papá", "ph": "[dad]", "img": "https://api.iconify.design/noto:man.svg"}]}', 2, 1);
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Mi Cara', 'drag_drop', '{"guide": {"intro": "Estas son partes de la cara.", "steps": [{"en": "Eyes", "es": "Ojos", "ph": "[ais]"}, {"en": "Nose", "es": "Nariz", "ph": "[nous]"}, {"en": "Mouth", "es": "Boca", "ph": "[mauth]"}]}, "items": [{"word": "EYES", "img": "https://api.iconify.design/noto:eyes.svg"}, {"word": "NOSE", "img": "https://api.iconify.design/noto:nose.svg"}, {"word": "MOUTH", "img": "https://api.iconify.design/noto:mouth.svg"}]}', 3, 2);
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Animales', 'matching', '{"guide": {"intro": "Vamos a unir los animales.", "steps": [{"en": "Dog", "es": "Perro", "ph": "[dog]"}, {"en": "Cat", "es": "Gato", "ph": "[cat]"}]}, "pairs": [{"left": "Dog", "right_img": "https://api.iconify.design/noto:dog-face.svg", "id": 1}, {"left": "Cat", "right_img": "https://api.iconify.design/noto:cat-face.svg", "id": 2}]}', 3, 3);
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Pintar', 'coloring', '{"guide": {"intro": "Colores divertidos.", "steps": [{"en": "Red", "es": "Rojo", "ph": "[red]"}, {"en": "Blue", "es": "Azul", "ph": "[blu]"}]}, "colors": ["#FF7F50", "#4CAF50", "#FFD700", "#2B3A67"]}', 3, 4);

-- NUEVO JSON CON LA HISTORIA PARA ESCRIBIR
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Escribir', 'writing', '{"guide": {"intro": "Lee el cuento y luego escribe la palabra secreta.", "steps": [{"en": "Apple", "es": "Manzana", "ph": "[apol]"}]}, "word": "APPLE", "hint_img": "https://api.iconify.design/noto:red-apple.svg", "story_en": "I have a red APPLE.", "story_es": "Tengo una manzana roja."}', 4, 5);

INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) VALUES (1, 'Examen Final', 'exam', '{"guide": {"intro": "¡No le soples! Deja que responda solo.", "steps": []}, "questions": [{"q": "¿Cómo se dice Perro?", "options": ["Cat", "Dog", "Bird"], "answer": "Dog"}, {"q": "¿Qué significa Apple?", "options": ["Manzana", "Pera", "Plátano"], "answer": "Manzana"}]}', 10, 6);
EOF