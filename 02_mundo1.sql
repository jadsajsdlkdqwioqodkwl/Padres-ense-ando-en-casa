SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Limpieza segura
DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 1);
DELETE FROM lessons WHERE module_id = 1;
DELETE FROM modules WHERE id = 1;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (1, 'Mundo 1: Primeros Pasos', '#2B3A67', 1);

-- STAGE 1: Vocabulario Visual y Auditivo (Meteor Strike)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Lluvia de Mascotas', 'meteor_strike', 1, 10, '
{
  "rounds": [
    { "target_word": "DOG", "phonetic": "dog", "translation": "Perro", "speed": 5, "context_es": "¡Toca el meteorito que diga Perro!", "items": [ {"id": 1, "content": "DOG", "is_correct": true}, {"id": 2, "content": "CAT", "is_correct": false}, {"id": 3, "content": "BIRD", "is_correct": false} ] },
    { "target_word": "CAT", "phonetic": "cat", "translation": "Gato", "speed": 6, "context_es": "¡Rápido! Ahora salva al Gato.", "items": [ {"id": 1, "content": "CAT", "is_correct": true}, {"id": 2, "content": "FISH", "is_correct": false}, {"id": 3, "content": "DOG", "is_correct": false} ] },
    { "target_word": "BIRD", "phonetic": "berd", "translation": "Pájaro", "speed": 7, "context_es": "¡Último! Encuentra al Pájaro.", "items": [ {"id": 1, "content": "BIRD", "is_correct": true}, {"id": 2, "content": "DUCK", "is_correct": false}, {"id": 3, "content": "CAT", "is_correct": false} ] }
  ]
}');

-- STAGE 2: Ortografía y Memoria (Word Defender)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Defensor de la Granja', 'defender', 2, 15, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "DUCK", "phonetic": "dac", "translation": "Pato", "distractors": ["M", "B", "Z"], "context_es": "¡Aleja al monstruo escribiendo PATO en inglés!" },
    { "word": "COW", "phonetic": "cau", "translation": "Vaca", "distractors": ["F", "L", "P"], "context_es": "¡Ahora defiende a la VACA!" },
    { "word": "PIG", "phonetic": "pig", "translation": "Cerdo", "distractors": ["X", "Y", "A"], "context_es": "¡Protege al CERDO!" }
  ]
}');

-- STAGE 3: Gramática Básica - Significados (Grammar Train)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'El Tren de los Animales', 'grammar_train', 3, 20, '
{
  "rounds": [
    {
      "sentence": ["THE", "DOG", "BARKS"],
      "translations": ["El", "Perro", "Ladra"],
      "phonetics": ["da", "dog", "barcs"],
      "sentence_phonetic": "da dog barcs",
      "distractors": ["CAT", "MEOWS"],
      "distractors_phonetics": ["cat", "miaus"],
      "context_es": "¡Carga los vagones uniendo el inglés con su significado!"
    },
    {
      "sentence": ["THE", "CAT", "SLEEPS"],
      "translations": ["El", "Gato", "Duerme"],
      "phonetics": ["da", "cat", "slips"],
      "sentence_phonetic": "da cat slips",
      "distractors": ["DOG", "RUNS"],
      "distractors_phonetics": ["dog", "rans"],
      "context_es": "¡Arma el tren del gato dormilón!"
    }
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;