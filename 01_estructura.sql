SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    child_name VARCHAR(50) NOT NULL, 
    parent_phone VARCHAR(20) UNIQUE, -- El UNIQUE es vital para no duplicar papás
    total_stars INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS modules (
    id INT PRIMARY KEY, -- Quitamos el AUTO_INCREMENT para controlar nosotros el ID
    title VARCHAR(100) NOT NULL, 
    color_theme VARCHAR(20) DEFAULT '#2B3A67', 
    order_num INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lessons (
    id INT PRIMARY KEY, -- Controlamos el ID para poder actualizar sin borrar
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