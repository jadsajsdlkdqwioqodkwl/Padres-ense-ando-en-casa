-- 1. Creamos el Módulo My School
INSERT INTO modules (id, title, description, min_age, order_num) 
VALUES (2, 'Mundo 2: My School', 'Aprende los útiles escolares y frases de clase', 5, 2);

-- 2. Lección 1: Color Rescue (3 Rondas - Útiles y Colores)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (2, 'Los Colores de mi Clase', 'type_color_rescue', 1, 0, 10, '
{
  "time_limit": 18,
  "rounds": [
    {
      "color_name": "Yellow", "color_hex": "#f1c40f", "item": "✏️", "translation": "Amarillo",
      "context_es": "¡Salva el lápiz amarillo!",
      "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Red", "hex": "#ff4757"}]
    },
    {
      "color_name": "Blue", "color_hex": "#3742fa", "item": "🖊️", "translation": "Azul",
      "context_es": "¡El OVNI quiere el lapicero azul! Píntalo rápido.",
      "distractors": [{"name": "Green", "hex": "#2ed573"}, {"name": "Yellow", "hex": "#f1c40f"}]
    },
    {
      "color_name": "Red", "color_hex": "#ff4757", "item": "📕", "translation": "Rojo",
      "context_es": "¡Protege el libro rojo!",
      "distractors": [{"name": "Black", "hex": "#2f3640"}, {"name": "Blue", "hex": "#3742fa"}]
    }
  ]
}');

-- 3. Lección 2: Word Defender (3 Rondas - Vocabulario del Salón)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (2, 'Defiende tu Escritorio', 'type_defender', 2, 0, 10, '
{
  "time_limit": 20,
  "rounds": [
    {
      "word": "DESK", "translation": "Escritorio", "distractors": ["A", "P", "L"],
      "context_es": "¡Defiende tu ESCRITORIO del monstruo!"
    },
    {
      "word": "BOOK", "translation": "Libro", "distractors": ["M", "C", "T"],
      "context_es": "¡Ahora escribe LIBRO para protegerlo!"
    },
    {
      "word": "PEN", "translation": "Lapicero", "distractors": ["X", "Y", "Z"],
      "context_es": "¡Última defensa! Salva tu LAPICERO."
    }
  ]
}');

-- 4. Lección 3: Meteor Strike (3 Rondas - Más Útiles)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (2, 'Lluvia de Útiles', 'type_meteor_strike', 3, 0, 15, '
{
  "rounds": [
    {
      "target_word": "RULER", "translation": "Regla", "speed": 6,
      "context_es": "¡Destruye el meteorito que diga Regla!",
      "items": [
        {"id": 1, "content": "RULER", "is_correct": true},
        {"id": 2, "content": "PAPER", "is_correct": false},
        {"id": 3, "content": "ERASER", "is_correct": false}
      ]
    },
    {
      "target_word": "ERASER", "translation": "Borrador", "speed": 7,
      "context_es": "¡Rápido! Ahora busca el meteorito que dice Borrador.",
      "items": [
        {"id": 1, "content": "ERASER", "is_correct": true},
        {"id": 2, "content": "DESK", "is_correct": false},
        {"id": 3, "content": "PEN", "is_correct": false}
      ]
    },
    {
      "target_word": "PAPER", "translation": "Papel", "speed": 8,
      "context_es": "¡Atento! Toca el meteorito que diga Papel.",
      "items": [
        {"id": 1, "content": "PAPER", "is_correct": true},
        {"id": 2, "content": "BOOK", "is_correct": false},
        {"id": 3, "content": "RULER", "is_correct": false}
      ]
    }
  ]
}');

-- 5. Lección 4: Sentence Survival (3 Rondas - Oraciones Escolares)
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (2, 'Camino a Clases', 'type_sentence_survival', 4, 0, 20, '
{
  "rounds": [
    {
      "sentence": ["I", "HAVE", "A", "PEN"], "translation": "Yo tengo un lapicero", "distractors": ["YOU", "BOOK"],
      "context_es": "¡Arma el puente diciendo: Yo tengo un lapicero!"
    },
    {
      "sentence": ["THIS", "IS", "MY", "DESK"], "translation": "Este es mi escritorio", "distractors": ["YOUR", "ERASER"],
      "context_es": "¡Cruza el río diciendo: Este es mi escritorio!"
    },
    {
      "sentence": ["I", "READ", "A", "BOOK"], "translation": "Yo leo un libro", "distractors": ["WRITE", "PAPER"],
      "context_es": "¡Último puente! Di: Yo leo un libro."
    }
  ]
}');

-- 6. Lección 5: Jefe Final del Mundo 2
INSERT INTO lessons (module_id, title, lesson_type, order_num, is_boss, reward_stars, content_data) 
VALUES (2, 'Examen de la Profesora (Jefe)', 'type_exam', 5, 1, 40, '
{
  "time_limit": 10,
  "lives": 3,
  "questions": [
    {"q": "¿Qué significa BOOK?", "options": ["Libro", "Cuaderno", "Lápiz"], "answer": "Libro"},
    {"q": "¿Cómo se dice Escritorio?", "options": ["Desk", "Chair", "Table"], "answer": "Desk"},
    {"q": "Completa: I HAVE A ___ (Yo tengo un lapicero)", "options": ["PEN", "ERASER", "RULER"], "answer": "PEN"},
    {"q": "¿Cómo se dice Borrador?", "options": ["Eraser", "Paper", "Ruler"], "answer": "Eraser"},
    {"q": "¿Qué color es YELLOW?", "options": ["Amarillo", "Azul", "Rojo"], "answer": "Amarillo"}
  ]
}');