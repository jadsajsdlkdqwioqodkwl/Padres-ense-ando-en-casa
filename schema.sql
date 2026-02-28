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
    template_type VARCHAR(50) NOT NULL, -- Define qué PHP de /templates/ cargar
    content_data JSON NOT NULL,         -- Variables del juego (palabras, imágenes)
    reward_stars INT DEFAULT 3,         -- Dopamina
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

-- Creamos al niño (ID 1)
INSERT INTO users (child_name, parent_phone, total_stars) 
VALUES ('Explorador', '+51928529656', 12);

-- Creamos el Módulo 1
INSERT INTO modules (title, order_num) 
VALUES ('My World', 1);

-- Creamos la Lección 1 (My Face) inyectando el JSON
INSERT INTO lessons (module_id, title, template_type, content_data, reward_stars, order_num) 
VALUES (
    1, 
    'My Face', 
    'drag_drop', 
    '{"words": ["EYES", "NOSE", "MOUTH"], "match_ids": ["drop-eyes", "drop-nose", "drop-mouth"]}', 
    3, 
    1
);