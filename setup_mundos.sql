SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE progress;
DELETE FROM lessons;
DELETE FROM modules;

-- Crear las Semanas (Módulos)
INSERT INTO modules (id, title, color_theme, order_num) VALUES 
(1, 'Semana 1: The Farm', '#68A93E', 1),
(2, 'Semana 2: My School', '#F29C38', 2);

-- Días de la Semana 1
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(1, 1, 'Entrenamiento Granja', 'dynamic', '{}', 10, 1),
(2, 1, 'Misión Animales', 'dynamic', '{}', 15, 2),
(3, 1, 'Rescate del Corral', 'dynamic', '{}', 20, 3),
(4, 1, 'Aventura en el Campo', 'dynamic', '{}', 25, 4),
(5, 1, 'Reto del Granjero', 'dynamic', '{}', 50, 5);

-- Días de la Semana 2
INSERT INTO lessons (id, module_id, title, template_type, content_data, reward_stars, order_num) VALUES 
(6, 2, 'Primer Día de Clases', 'dynamic', '{}', 10, 1),
(7, 2, 'Misión Salón', 'dynamic', '{}', 15, 2),
(8, 2, 'Rescate de Útiles', 'dynamic', '{}', 20, 3),
(9, 2, 'Aventura Escolar', 'dynamic', '{}', 25, 4),
(10, 2, 'Examen Final', 'dynamic', '{}', 50, 5);

SET FOREIGN_KEY_CHECKS = 1;