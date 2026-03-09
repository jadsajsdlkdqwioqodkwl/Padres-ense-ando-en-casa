-- =========================================================================================
-- 🤖 SYSTEM PROMPT FOR AI (NOTA IMBORRABLE PARA LA IA):
-- 1. CRÍTICO: TODAS las palabras en inglés deben incluir su propiedad "phonetic" adaptada.
-- 2. CRÍTICO: Archivo SQL completo y funcional para el Módulo 1 (The Farm).
-- =========================================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 1);
DELETE FROM lessons WHERE module_id = 1;
DELETE FROM modules WHERE id = 1;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (1, 'Mundo 1: The Farm', '#2B3A67', 1);

-- STAGE 1: Type Jumper (Ranas) - Reemplaza Color Rescue
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Salto de Ranita: Colores', 'jumper', 1, 15, '
{
  "rounds": [
    { "target_word": "RED", "phonetic": "red", "translation": "Rojo", "context_es": "¡Salta en la hoja de color Rojo!", "distractors": ["BLUE", "GREEN"] },
    { "target_word": "GREEN", "phonetic": "grin", "translation": "Verde", "context_es": "¡Busca el color Verde para cruzar!", "distractors": ["YELLOW", "RED"] },
    { "target_word": "YELLOW", "phonetic": "iélou", "translation": "Amarillo", "context_es": "¡Último salto! Color Amarillo.", "distractors": ["BLUE", "RED"] }
  ]
}');

-- STAGE 2: Meteor Strike (Word Ninja)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Word Ninja: Animales', 'meteor_strike', 2, 15, '
{
  "rounds": [
    { "target_word": "DOG", "phonetic": "dog", "translation": "Perro", "context_es": "¡Corta al Perro por la mitad 3 veces!", "items": [ {"content": "🐶", "is_correct": true}, {"content": "🐱", "is_correct": false}, {"content": "🐷", "is_correct": false} ] },
    { "target_word": "CAT", "phonetic": "kat", "translation": "Gato", "context_es": "¡Conviértete en ninja y corta al Gato!", "items": [ {"content": "🐱", "is_correct": true}, {"content": "🐮", "is_correct": false}, {"content": "🐶", "is_correct": false} ] },
    { "target_word": "BIRD", "phonetic": "berd", "translation": "Pájaro", "context_es": "¡Corta al Pájaro antes de que caiga!", "items": [ {"content": "🐦", "is_correct": true}, {"content": "🦆", "is_correct": false}, {"content": "🐱", "is_correct": false} ] }
  ]
}');

-- STAGE 3: Defender (Monstruo y Pastel)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Defiende el Pastel: Granja', 'defender', 3, 20, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "PIG", "phonetic": "pig", "translation": "Cerdo", "distractors": ["M", "B", "Z"], "context_es": "¡Escribe CERDO para alejar al monstruo!" },
    { "word": "COW", "phonetic": "kau", "translation": "Vaca", "distractors": ["F", "L", "P"], "context_es": "¡Ahora defiende escribiendo VACA!" },
    { "word": "DUCK", "phonetic": "dak", "translation": "Pato", "distractors": ["X", "Y", "A"], "context_es": "¡Rápido! Escribe PATO." }
  ]
}');

-- STAGE 4: Detective (Topos)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Atrapa al Topo: Acción', 'detective', 4, 20, '
{
  "rounds": [
    { "target_word": "BARK", "phonetic": "bark", "translation": "Ladrar", "context_es": "¡Golpea al topo que diga Ladrar!", "distractors": ["MEOW", "MOO"] },
    { "target_word": "SLEEP", "phonetic": "slip", "translation": "Dormir", "context_es": "¡Encuentra la acción Dormir!", "distractors": ["RUN", "FLY"] },
    { "target_word": "RUN", "phonetic": "ran", "translation": "Correr", "context_es": "¡Atrapa al topo que corre!", "distractors": ["WALK", "EAT"] }
  ]
}');

-- STAGE 5: Potion (Cohete Espacial)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Misión Espacial: Naturaleza', 'potion', 5, 25, '
{
  "rounds": [
    { "target_word": "TREE", "phonetic": "tri", "translation": "Árbol", "context_es": "¡Atrapa el Árbol para cargar combustible!", "distractors": ["FLOWER", "ROCK"] }
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;