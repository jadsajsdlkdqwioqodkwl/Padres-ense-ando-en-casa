-- =========================================================
-- PLANTILLA PARA CREAR UN MUNDO NUEVO SIN TOCAR CÓDIGO PHP
-- =========================================================

-- 1. Creamos el Módulo (El Mundo)
INSERT INTO modules (title, description, min_age, order_num) 
VALUES ('Mundo 3: Los Animales', 'Aprende sobre los animales de la granja', 4, 3);

-- (Supongamos que el ID de este nuevo módulo es 3)

-- 2. Lección 1: Word Defender (Escalable a 2 rondas)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (3, 'Defiende el Granero', 'type_defender', 1, 0, 10, '
{
  "time_limit": 25,
  "rounds": [
    {
      "word": "CAT",
      "translation": "Gato",
      "distractors": ["M", "B", "Z"],
      "context_es": "¡Aleja al lobo escribiendo GATO en inglés!"
    },
    {
      "word": "DOG",
      "translation": "Perro",
      "distractors": ["F", "L", "P"],
      "context_es": "¡Ahora defiende al PERRO!"
    }
  ]
}');

-- 3. Lección 2: Meteor Strike (Escalable a 2 rondas)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (3, 'Lluvia en la Granja', 'type_meteor_strike', 2, 0, 10, '
{
  "rounds": [
    {
      "target_word": "COW",
      "translation": "Vaca",
      "speed": 6,
      "context_es": "¡Destruye el meteorito que tenga la palabra Vaca!",
      "items": [
        {"id": 1, "content": "COW", "is_correct": true},
        {"id": 2, "content": "PIG", "is_correct": false},
        {"id": 3, "content": "BIRD", "is_correct": false}
      ]
    },
    {
      "target_word": "PIG",
      "translation": "Cerdo",
      "speed": 7,
      "context_es": "¡Rápido! Ahora busca el meteorito que dice Cerdo.",
      "items": [
        {"id": 1, "content": "PIG", "is_correct": true},
        {"id": 2, "content": "COW", "is_correct": false},
        {"id": 3, "content": "CAT", "is_correct": false}
      ]
    }
  ]
}');

-- 4. Lección 3: Sentence Survival (Puente de palabras)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (3, 'Cruza el Río', 'type_sentence_survival', 3, 0, 15, '
{
  "rounds": [
    {
      "sentence": ["THE", "CAT", "RUNS"],
      "translation": "El gato corre",
      "distractors": ["DOG", "JUMPS"],
      "context_es": "¡Arma el puente diciendo: El gato corre!"
    },
    {
      "sentence": ["I", "SEE", "A", "DOG"],
      "translation": "Yo veo un perro",
      "distractors": ["YOU", "COW"],
      "context_es": "¡Cruza el río diciendo: Yo veo un perro!"
    }
  ]
}');

-- 5. Lección 4: JEFE FINAL (Multifase)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (3, 'Examen del Granjero (Jefe)', 'type_exam', 4, 1, 30, '
{
  "time_limit": 10,
  "lives": 3,
  "questions": [
    {"q": "¿Cómo se dice Perro?", "options": ["Dog", "Cat", "Cow"], "answer": "Dog"},
    {"q": "¿Qué significa PIG?", "options": ["Cerdo", "Vaca", "Oveja"], "answer": "Cerdo"},
    {"q": "Completa: THE ___ RUNS (El gato corre)", "options": ["CAT", "DOG", "BIRD"], "answer": "CAT"},
    {"q": "¿Cómo se dice Vaca?", "options": ["Cow", "Pig", "Cat"], "answer": "Cow"}
  ]
}');