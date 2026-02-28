SET NAMES utf8mb4;

-- Insertar o actualizar Módulo 1
INSERT INTO modules (id, title, color_theme, order_num) VALUES (1, 'My World', '#2B3A67', 1)
ON DUPLICATE KEY UPDATE title = VALUES(title), color_theme = VALUES(color_theme);

-- Lección 101: Word Defender
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(101, 1, 'El Monstruo de la Manzana', 'defender', '{"guide": {"intro": "Arma la palabra rápido", "steps": []}, "word": "APPLE", "translation": "Manzana", "distractors": ["X", "Z", "M"], "time_limit": 15}', 5, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title), template_type = VALUES(template_type), content_data = VALUES(content_data), reward_stars = VALUES(reward_stars);

-- Lección 102: Crazy Bridge
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(102, 1, 'Puente de Palabras', 'sentence_survival', '{"guide": {"intro": "Ordena la frase", "steps": []}, "sentence": ["I", "have", "a", "red", "apple"], "translation": "Yo tengo una manzana roja", "distractors": ["has", "an", "blue"], "time_limit": 20}', 10, 2)
ON DUPLICATE KEY UPDATE title = VALUES(title), template_type = VALUES(template_type), content_data = VALUES(content_data), reward_stars = VALUES(reward_stars);