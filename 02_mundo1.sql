-- =========================================================================================
-- 🤖 SYSTEM PROMPT FOR AI (NOTA IMBORRABLE PARA LA IA)
-- =========================================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 2);
DELETE FROM lessons WHERE module_id = 2;
DELETE FROM modules WHERE id = 2;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (2, 'Mundo 2: My School', '#00b894', 2);

-- STAGE 1: Type Jumper (Ranas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Salto de Ranita: Salón', 'jumper', 1, 15, '
{
  "rounds": [
    { "target_word": "PURPLE", "phonetic": "pérpol", "translation": "Morado", "context_es": "¡Salta sobre el color Morado!", "distractors": ["GREEN", "YELLOW"] },
    { "target_word": "ORANGE", "phonetic": "óranch", "translation": "Naranja", "context_es": "¡Busca el color Naranja para avanzar!", "distractors": ["BLACK", "BLUE"] },
    { "target_word": "PINK", "phonetic": "pinc", "translation": "Rosa", "context_es": "¡Último salto! Color Rosa.", "distractors": ["RED", "GREEN"] }
  ]
}');

-- STAGE 2: Meteor Strike (Word Ninja)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Word Ninja: Útiles', 'meteor_strike', 2, 20, '
{
  "rounds": [
    { "target_word": "BOOK", "phonetic": "buk", "translation": "Libro", "context_es": "¡Corta el Libro por la mitad!", "items": [ {"content": "📖", "is_correct": true}, {"content": "🖊️", "is_correct": false}, {"content": "🪑", "is_correct": false} ] },
    { "target_word": "PEN", "phonetic": "pen", "translation": "Lapicero", "context_es": "¡Conviértete en ninja y corta el Lapicero!", "items": [ {"content": "🖊️", "is_correct": true}, {"content": "📖", "is_correct": false}, {"content": "📏", "is_correct": false} ] },
    { "target_word": "DESK", "phonetic": "desk", "translation": "Escritorio", "context_es": "¡Rápido! Corta el Escritorio.", "items": [ {"content": "🪑", "is_correct": true}, {"content": "📄", "is_correct": false}, {"content": "🖊️", "is_correct": false} ] }
  ]
}');

-- STAGE 3: Defender (Monstruo y Pastel)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Defiende el Pastel: Clase', 'defender', 3, 20, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "CHAIR", "phonetic": "cher", "translation": "Silla", "distractors": ["A", "P", "L"], "context_es": "¡Escribe SILLA para proteger el pastel!" },
    { "word": "PAPER", "phonetic": "péiper", "translation": "Papel", "distractors": ["M", "C", "T"], "context_es": "¡Ahora defiende escribiendo PAPEL!" },
    { "word": "CLOCK", "phonetic": "cloc", "translation": "Reloj", "distractors": ["X", "Y", "Z"], "context_es": "¡Salva todo escribiendo RELOJ!" }
  ]
}');

-- STAGE 4: Detective (Topos)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Atrapa al Topo: Verbos', 'detective', 4, 25, '
{
  "rounds": [
    { "target_word": "READ", "phonetic": "rid", "translation": "Leer", "context_es": "¡Golpea al topo que diga Leer!", "distractors": ["WRITE", "PLAY"] },
    { "target_word": "WRITE", "phonetic": "rait", "translation": "Escribir", "context_es": "¡Encuentra la acción Escribir!", "distractors": ["SPEAK", "LISTEN"] },
    { "target_word": "LISTEN", "phonetic": "lisen", "translation": "Escuchar", "context_es": "¡Atrapa al topo que escucha!", "distractors": ["LOOK", "RUN"] }
  ]
}');

-- STAGE 5: Potion (Cohete Espacial)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Misión Espacial: Escuela', 'potion', 5, 25, '
{
  "rounds": [
    { "target_word": "BOARD", "phonetic": "bord", "translation": "Pizarra", "context_es": "¡Atrapa la Pizarra para el cohete!", "distractors": ["TRASH", "DOOR"] }
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;