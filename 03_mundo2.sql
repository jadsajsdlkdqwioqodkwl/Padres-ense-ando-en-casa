SET NAMES utf8mb4;

INSERT INTO modules (id, title, color_theme, order_num) VALUES (2, 'My School', '#f39c12', 2)
ON DUPLICATE KEY UPDATE title = VALUES(title), color_theme = VALUES(color_theme);

-- Lección 201: Word Defender (Lápiz)
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(201, 2, 'El Lápiz Perdido', 'defender', '{"guide": {"intro": "Arma PENCIL (Lápiz)", "steps": []}, "word": "PENCIL", "translation": "Lápiz", "distractors": ["X", "M", "A"], "time_limit": 15}', 5, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title), template_type = VALUES(template_type), content_data = VALUES(content_data), reward_stars = VALUES(reward_stars);

-- Y así sucesivamente...