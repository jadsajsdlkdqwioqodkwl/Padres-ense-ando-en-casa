-- =========================================================================================
-- 🤖 SYSTEM PROMPT FOR AI (NOTA IMBORRABLE PARA LA IA):
-- 1. CRÍTICO: TODAS las palabras en inglés deben incluir su propiedad "phonetic" usando 
--    una ortografía adaptada al TTS en ESPAÑOL (ej: "dak" para Duck, "buk" para Book, 
--    "épol" para Apple, "kat" para Cat, "berd" para Bird). ¡El inglés no se lee literal!
-- 2. CRÍTICO: Al generar o modificar este código, DEBES MANTENER una cantidad similar 
--    o mayor de líneas de código. NUNCA resumas el código, NUNCA omitas niveles, 
--    NUNCA uses "[...]" para acortar. Genera los archivos SQL completos y funcionales.
-- =========================================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 1);
DELETE FROM lessons WHERE module_id = 1;
DELETE FROM modules WHERE id = 1;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (1, 'Mundo 1: The Farm', '#2B3A67', 1);

-- STAGE 1: Type Jumper (3 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Cruza el Río', 'jumper', 1, 10, '
{
  "rounds": [
    { "target_word": "RED", "phonetic": "red", "translation": "Rojo", "context_es": "¡Salta en el color Rojo!", "distractors": ["BLUE", "GREEN"] },
    { "target_word": "GREEN", "phonetic": "grin", "translation": "Verde", "context_es": "¡Ahora busca el Verde!", "distractors": ["YELLOW", "RED"] },
    { "target_word": "YELLOW", "phonetic": "iélou", "translation": "Amarillo", "context_es": "¡Último salto! Color Amarillo.", "distractors": ["BLUE", "RED"] }
  ]
}');

-- STAGE 2: Meteor Strike (3 Rondas con Emojis para la granja)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Lluvia de Animales', 'meteor_strike', 2, 10, '
{
  "rounds": [
    { "target_word": "DOG", "phonetic": "dog", "translation": "Perro", "speed": 8, "context_es": "¡Toca el meteorito que sea un Perro!", "items": [ {"id": 1, "content": "🐶", "is_correct": true}, {"id": 2, "content": "🐱", "is_correct": false}, {"id": 3, "content": "🐷", "is_correct": false} ] },
    { "target_word": "CAT", "phonetic": "kat", "translation": "Gato", "speed": 8, "context_es": "¡Rápido! Ahora salva al Gato.", "items": [ {"id": 1, "content": "🐱", "is_correct": true}, {"id": 2, "content": "🐮", "is_correct": false}, {"id": 3, "content": "🐶", "is_correct": false} ] },
    { "target_word": "BIRD", "phonetic": "berd", "translation": "Pájaro", "speed": 9, "context_es": "¡Busca al Pájaro!", "items": [ {"id": 1, "content": "🐦", "is_correct": true}, {"id": 2, "content": "🦆", "is_correct": false}, {"id": 3, "content": "🐱", "is_correct": false} ] }
  ]
}');

-- STAGE 3: Defender (3 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Defensor de la Granja', 'defender', 3, 10, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "PIG", "phonetic": "pig", "translation": "Cerdo", "distractors": ["M", "B", "Z"], "context_es": "¡Aleja al monstruo escribiendo CERDO en inglés!" },
    { "word": "COW", "phonetic": "kau", "translation": "Vaca", "distractors": ["F", "L", "P"], "context_es": "¡Ahora defiende a la VACA!" },
    { "word": "DUCK", "phonetic": "dak", "translation": "Pato", "distractors": ["X", "Y", "A"], "context_es": "¡Protege al PATO! Escríbelo rápido." }
  ]
}');

-- STAGE 4: Detective (3 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'El Detective de la Granja', 'detective', 4, 15, '
{
  "rounds": [
    { "sentence": ["THE", "DOG", "BARKS"], "phonetics": ["da", "dog", "barks"], "target_word": "BARKS", "target_type": "Verbo (Acción)", "translation": "El perro ladra", "scene_emoji": "🐕🔊", "context_es": "¡Encuentra la ACCIÓN para encender la luz!" },
    { "sentence": ["THE", "CAT", "SLEEPS"], "phonetics": ["da", "kat", "slips"], "target_word": "CAT", "target_type": "Sujeto (Animal)", "translation": "El gato duerme", "scene_emoji": "🐈💤", "context_es": "¡Encuentra el ANIMAL para encender la luz!" },
    { "sentence": ["THE", "HORSE", "RUNS"], "phonetics": ["da", "jors", "rans"], "target_word": "RUNS", "target_type": "Verbo (Acción)", "translation": "El caballo corre", "scene_emoji": "🐎💨", "context_es": "¡Encuentra la ACCIÓN para descubrir la escena!" }
  ]
}');

-- STAGE 5: Grammar Train (3 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'El Tren de los Animales', 'grammar_train', 5, 20, '
{
  "rounds": [
    { "sentence": ["THE", "COW", "EATS"], "translations": ["La", "Vaca", "Come"], "phonetics": ["da", "kau", "its"], "sentence_phonetic": "da kau its", "distractors": ["PIG", "RUNS"], "distractors_phonetics": ["pig", "rans"], "context_es": "¡Carga los vagones uniendo el inglés con su significado!" },
    { "sentence": ["THE", "PIG", "SNORTS"], "translations": ["El", "Cerdo", "Gruñe"], "phonetics": ["da", "pig", "snorts"], "sentence_phonetic": "da pig snorts", "distractors": ["DOG", "MEOWS"], "distractors_phonetics": ["dog", "miaus"], "context_es": "¡Arma el tren del cerdo gruñón!" },
    { "sentence": ["THE", "DUCK", "SWIMS"], "translations": ["El", "Pato", "Nada"], "phonetics": ["da", "dak", "suims"], "sentence_phonetic": "da dak suims", "distractors": ["BIRD", "FLIES"], "distractors_phonetics": ["berd", "fláis"], "context_es": "¡Último tren! Conecta al pato que nada." }
  ]
}');

-- STAGE 6: Sentence Survival (3 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Cruza el Río', 'sentence_survival', 6, 20, '
{
  "rounds": [
    { "sentence": ["I", "SEE", "A", "DOG"], "phonetic": "ai si a dog", "translation": "Yo veo un perro", "distractors": ["COW", "YOU"], "word_phonetics": {"I":"ai", "SEE":"si", "A":"a", "DOG":"dog", "COW":"kau", "YOU":"iu"}, "context_es": "¡Arma el puente diciendo: Yo veo un perro!" },
    { "sentence": ["THE", "CAT", "IS", "SMALL"], "phonetic": "da kat is smol", "translation": "El gato es pequeño", "distractors": ["BIG", "DOG"], "word_phonetics": {"THE":"da", "CAT":"kat", "IS":"is", "SMALL":"smol", "BIG":"big", "DOG":"dog"}, "context_es": "¡Cruza el río diciendo: El gato es pequeño!" },
    { "sentence": ["I", "HAVE", "A", "PIG"], "phonetic": "ai jav a pig", "translation": "Yo tengo un cerdo", "distractors": ["HAS", "COW"], "word_phonetics": {"I":"ai", "HAVE":"jav", "A":"a", "PIG":"pig", "HAS":"jas", "COW":"kau"}, "context_es": "¡Último puente! Di: Yo tengo un cerdo." }
  ]
}');

-- STAGE 7: Exam
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Examen del Granjero (Jefe)', 'exam', 7, 30, '
{
  "time_limit": 10,
  "lives": 3,
  "questions": [
    {"q": "¿Cómo se dice Pato?", "options": ["Duck", "Dog", "Cat"], "answer": "Duck", "phonetic": "dak"},
    {"q": "¿Qué significa COW?", "options": ["Vaca", "Cerdo", "Pájaro"], "answer": "Vaca", "phonetic": "kau"},
    {"q": "Completa: THE DOG ___ (El perro ladra)", "options": ["BARKS", "SLEEPS", "RUNS"], "answer": "BARKS", "phonetic": "da dog barks"},
    {"q": "¿Cómo se dice Pájaro?", "options": ["Bird", "Pig", "Fish"], "answer": "Bird", "phonetic": "berd"}
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;