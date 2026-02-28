SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 2);
DELETE FROM lessons WHERE module_id = 2;
DELETE FROM modules WHERE id = 2;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (2, 'Mundo 2: My School', '#00b894', 2);

-- STAGE 1: Vocabulario Adjetivos (Color Rescue)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Colores del Salón', 'color_rescue', 1, 10, '
{
  "time_limit": 18,
  "rounds": [
    { "color_name": "Yellow", "phonetic": "iélou", "color_hex": "#f1c40f", "item": "✏️", "translation": "Amarillo", "context_es": "¡Salva el lápiz amarillo!", "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Red", "hex": "#ff4757"}] },
    { "color_name": "Purple", "phonetic": "pérpol", "color_hex": "#9b59b6", "item": "🎒", "translation": "Morado", "context_es": "¡El OVNI quiere la mochila morada! Píntala rápido.", "distractors": [{"name": "Green", "hex": "#2ed573"}, {"name": "Yellow", "hex": "#f1c40f"}] },
    { "color_name": "Orange", "phonetic": "óranch", "color_hex": "#e67e22", "item": "📙", "translation": "Naranja", "context_es": "¡Protege el libro naranja!", "distractors": [{"name": "Black", "hex": "#2f3640"}, {"name": "Blue", "hex": "#3742fa"}] }
  ]
}');

-- STAGE 2: Ortografía de Sustantivos (Defender)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Defiende tu Escritorio', 'defender', 2, 10, '
{
  "time_limit": 20,
  "rounds": [
    { "word": "DESK", "phonetic": "desc", "translation": "Escritorio", "distractors": ["A", "P", "L"], "context_es": "¡Defiende tu ESCRITORIO del monstruo!" },
    { "word": "BOOK", "phonetic": "buk", "translation": "Libro", "distractors": ["M", "C", "T"], "context_es": "¡Ahora escribe LIBRO para protegerlo!" },
    { "word": "PEN", "phonetic": "pen", "translation": "Lapicero", "distractors": ["X", "Y", "Z"], "context_es": "¡Última defensa! Salva tu LAPICERO." }
  ]
}');

-- STAGE 3: Identificar Estructuras (The Detective)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Buscando la Acción', 'detective', 3, 20, '
{
  "rounds": [
    {
      "sentence": ["THE", "BOY", "READS"],
      "phonetics": ["da", "boi", "rids"],
      "target_word": "READS",
      "target_type": "Verbo (Acción)",
      "translation": "El niño lee",
      "scene_emoji": "👦📖",
      "context_es": "¡Encuentra el Verbo para encender la luz!"
    },
    {
      "sentence": ["THE", "GIRL", "WRITES"],
      "phonetics": ["da", "guerl", "raits"],
      "target_word": "GIRL",
      "target_type": "Sujeto (Quién lo hace)",
      "translation": "La niña escribe",
      "scene_emoji": "👧✍️",
      "context_es": "¡Encuentra el Sujeto para encender la luz!"
    }
  ]
}');

-- STAGE 4: Creación de Oraciones (Sentence Survival)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Puente Escolar', 'sentence_survival', 4, 20, '
{
  "rounds": [
    { 
      "sentence": ["I", "HAVE", "A", "RED", "PEN"], 
      "phonetic": "ai jav a red pen", 
      "translation": "Yo tengo un lapicero rojo", 
      "distractors": ["HAS", "BLUE"], 
      "word_phonetics": {"I":"ai", "HAVE":"jav", "A":"a", "RED":"red", "PEN":"pen", "HAS":"jas", "BLUE":"blú"},
      "context_es": "¡Arma el puente diciendo: Yo tengo un lapicero rojo!" 
    },
    { 
      "sentence": ["THIS", "IS", "MY", "BOOK"], 
      "phonetic": "dis is mai buk", 
      "translation": "Este es mi libro", 
      "distractors": ["YOUR", "DESK"], 
      "word_phonetics": {"THIS":"dis", "IS":"is", "MY":"mai", "BOOK":"buk", "YOUR":"iur", "DESK":"desc"},
      "context_es": "¡Cruza el río diciendo: Este es mi libro!" 
    }
  ]
}');

-- STAGE 5: Boss Final (Exam)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Examen de la Profesora (Jefe)', 'exam', 5, 40, '
{
  "time_limit": 12,
  "lives": 3,
  "questions": [
    {"q": "¿Qué significa BOOK?", "options": ["Libro", "Cuaderno", "Lápiz"], "answer": "Libro", "phonetic": "buk"},
    {"q": "¿Cómo se dice Escritorio?", "options": ["Desk", "Chair", "Table"], "answer": "Desk", "phonetic": "desc"},
    {"q": "Completa: I HAVE A RED ___ (Yo tengo un lapicero rojo)", "options": ["PEN", "ERASER", "RULER"], "answer": "PEN", "phonetic": "ai jav a red pen"},
    {"q": "¿Qué significa THE DOG RUNS?", "options": ["El perro corre", "El gato duerme", "El pájaro vuela"], "answer": "El perro corre", "phonetic": "da dog rans"}
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;