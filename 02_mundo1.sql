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

-- Limpieza segura antes de actualizar
DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 1);
DELETE FROM lessons WHERE module_id = 1;
DELETE FROM modules WHERE id = 1;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (1, 'Mundo 1: The Farm', '#2B3A67', 1);

-- ==========================================
-- STAGE 1: Vocabulario (Meteor Strike)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Lluvia de Animales', 'meteor_strike', 1, 15, '
{
  "rounds": [
    { 
      "target_word": "DOG", "phonetic": "dog", "translation": "Perro", "speed": 6, 
      "context_es": "¡Toca el meteorito que diga Perro!", 
      "items": [ {"id": 1, "content": "DOG", "is_correct": true}, {"id": 2, "content": "CAT", "is_correct": false}, {"id": 3, "content": "PIG", "is_correct": false} ] 
    },
    { 
      "target_word": "CAT", "phonetic": "kat", "translation": "Gato", "speed": 6, 
      "context_es": "¡Rápido! Ahora salva al Gato.", 
      "items": [ {"id": 1, "content": "CAT", "is_correct": true}, {"id": 2, "content": "COW", "is_correct": false}, {"id": 3, "content": "DOG", "is_correct": false} ] 
    },
    { 
      "target_word": "BIRD", "phonetic": "berd", "translation": "Pájaro", "speed": 7, 
      "context_es": "¡Busca al Pájaro!", 
      "items": [ {"id": 1, "content": "BIRD", "is_correct": true}, {"id": 2, "content": "DUCK", "is_correct": false}, {"id": 3, "content": "CAT", "is_correct": false} ] 
    },
    { 
      "target_word": "DUCK", "phonetic": "dak", "translation": "Pato", "speed": 7, 
      "context_es": "¡Cuidado! Encuentra al Pato.", 
      "items": [ {"id": 1, "content": "DUCK", "is_correct": true}, {"id": 2, "content": "BIRD", "is_correct": false}, {"id": 3, "content": "FISH", "is_correct": false} ] 
    },
    { 
      "target_word": "COW", "phonetic": "kau", "translation": "Vaca", "speed": 8, 
      "context_es": "¡Último esfuerzo! Salva a la Vaca.", 
      "items": [ {"id": 1, "content": "COW", "is_correct": true}, {"id": 2, "content": "PIG", "is_correct": false}, {"id": 3, "content": "DOG", "is_correct": false} ] 
    }
  ]
}');

-- ==========================================
-- STAGE 2: Ortografía (Word Defender)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Defensor de la Granja', 'defender', 2, 20, '
{
  "time_limit": 25,
  "rounds": [
    { 
      "word": "PIG", "phonetic": "pig", "translation": "Cerdo", 
      "distractors": ["M", "B", "Z"], 
      "context_es": "¡Aleja al monstruo escribiendo CERDO en inglés!" 
    },
    { 
      "word": "FISH", "phonetic": "fish", "translation": "Pez", 
      "distractors": ["F", "L", "P"], 
      "context_es": "¡Ahora defiende al PEZ!" 
    },
    { 
      "word": "DUCK", "phonetic": "dak", "translation": "Pato", 
      "distractors": ["X", "Y", "A"], 
      "context_es": "¡Protege al PATO! Escríbelo rápido." 
    }
  ]
}');

-- ==========================================
-- STAGE 3: Gramática Básica (Grammar Train)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'El Tren de los Animales', 'grammar_train', 3, 25, '
{
  "rounds": [
    {
      "sentence": ["THE", "DOG", "BARKS"],
      "translations": ["El", "Perro", "Ladra"],
      "phonetics": ["da", "dog", "barks"],
      "sentence_phonetic": "da dog barks",
      "distractors": ["CAT", "MEOWS"],
      "distractors_phonetics": ["kat", "miaus"],
      "context_es": "¡Carga los vagones uniendo el inglés con su significado!"
    },
    {
      "sentence": ["THE", "CAT", "SLEEPS"],
      "translations": ["El", "Gato", "Duerme"],
      "phonetics": ["da", "kat", "slips"],
      "sentence_phonetic": "da kat slips",
      "distractors": ["DOG", "RUNS"],
      "distractors_phonetics": ["dog", "rans"],
      "context_es": "¡Arma el tren del gato dormilón!"
    },
    {
      "sentence": ["THE", "DUCK", "SWIMS"],
      "translations": ["El", "Pato", "Nada"],
      "phonetics": ["da", "dak", "suims"],
      "sentence_phonetic": "da dak suims",
      "distractors": ["BIRD", "FLIES"],
      "distractors_phonetics": ["berd", "fláis"],
      "context_es": "¡Último tren! Conecta al pato que nada."
    }
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;