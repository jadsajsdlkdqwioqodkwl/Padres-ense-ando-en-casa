-- 1. Tabla de Usuarios (Niños y Padres)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(50) NOT NULL,
    parent_phone VARCHAR(20),
    total_stars INT DEFAULT 0
);

-- 2. Tabla de Módulos (Ej: My World)
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    color_theme VARCHAR(20) DEFAULT '#2B3A67',
    order_num INT NOT NULL
);

-- 3. Tabla de Lecciones (El núcleo dinámico)
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT,
    title VARCHAR(100) NOT NULL,
    template_type VARCHAR(50) NOT NULL,
    content_data JSON NOT NULL,
    reward_stars INT DEFAULT 3,
    order_num INT NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id)
);

-- 4. Tabla de Progreso (Para saber qué niveles desbloquear en el mapa)
CREATE TABLE IF NOT EXISTS progress (
    user_id INT,
    lesson_id INT,
    is_completed BOOLEAN DEFAULT FALSE,
    stars_earned INT DEFAULT 0,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)
);

-- ==========================================
-- 🛠️ DATOS DE PRUEBA (Para que arranque tu app)
-- ==========================================

INSERT INTO users (child_name, parent_phone, total_stars) VALUES ('Explorador', '+51928529656', 0);
INSERT INTO modules (title, order_num) VALUES ('My World', 1);

-- Lección 1: Flashcards (Tarjetas de vocabulario)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (1, 'Mi Familia', 'flashcards', '{"flashcards": [{"en": "Mom", "es": "Mamá", "ph": "[mam]"}, {"en": "Dad", "es": "Papá", "ph": "[dad]"}]}', 2, 1);

-- Lección 2: Drag & Drop (Arrastrar a la cara)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (1, 'Mi Cara', 'drag_drop', '{"items": [{"word": "EYES", "icon": "👀"}, {"word": "NOSE", "icon": "👃"}, {"word": "MOUTH", "icon": "👄"}]}', 3, 2);

-- Lección 3: Matching (Unir parejas de animales)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (1, 'Animales', 'matching', '{"pairs": [{"left": "Dog", "right": "🐶", "id": 1}, {"left": "Cat", "right": "🐱", "id": 2}]}', 3, 3);

-- Lección 4: Coloring (Pintar)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (1, 'Pintar', 'coloring', '{"colors": ["#FF7F50", "#4CAF50", "#FFD700", "#2B3A67"]}', 3, 4);

-- Lección 5: Writing (Escribir la palabra)
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (1, 'Escribir', 'writing', '{"word": "APPLE", "hint": "🍎"}', 4, 5);