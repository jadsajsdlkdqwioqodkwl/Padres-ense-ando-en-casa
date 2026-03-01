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

DELETE FROM progress WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id = 2);
DELETE FROM lessons WHERE module_id = 2;
DELETE FROM modules WHERE id = 2;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (2, 'Mundo 2: My School', '#00b894', 2);

-- ==========================================
-- STAGE 1: Vocabulario Visual (Color Rescue)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Colores del Salón', 'color_rescue', 1, 15, '
{
  "time_limit": 18,
  "rounds": [
    { 
      "color_name": "Yellow", "phonetic": "iélou", "color_hex": "#f1c40f", "item": "✏️", "translation": "Amarillo", 
      "context_es": "¡Salva el lápiz amarillo!", "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Red", "hex": "#ff4757"}] 
    },
    { 
      "color_name": "Blue", "phonetic": "blú", "color_hex": "#3742fa", "item": "🖊️", "translation": "Azul", 
      "context_es": "¡El OVNI quiere el lapicero azul! Píntalo.", "distractors": [{"name": "Green", "hex": "#2ed573"}, {"name": "Yellow", "hex": "#f1c40f"}] 
    },
    { 
      "color_name": "Red", "phonetic": "red", "color_hex": "#ff4757", "item": "📕", "translation": "Rojo", 
      "context_es": "¡Protege el libro rojo!", "distractors": [{"name": "Black", "hex": "#2f3640"}, {"name": "Blue", "hex": "#3742fa"}] 
    },
    { 
      "color_name": "Green", "phonetic": "grin", "color_hex": "#2ed573", "item": "🍏", "translation": "Verde", 
      "context_es": "¡Salva la manzana verde del profesor!", "distractors": [{"name": "Red", "hex": "#ff4757"}, {"name": "Yellow", "hex": "#f1c40f"}] 
    }
  ]
}');

-- ==========================================
-- STAGE 2: Escucha Activa (Meteor Strike)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Lluvia de Útiles', 'meteor_strike', 2, 20, '
{
  "rounds": [
    { 
      "target_word": "BOOK", "phonetic": "buk", "translation": "Libro", "speed": 6, 
      "context_es": "¡Destruye el meteorito que diga Libro!", 
      "items": [ {"id": 1, "content": "BOOK", "is_correct": true}, {"id": 2, "content": "PEN", "is_correct": false}, {"id": 3, "content": "DESK", "is_correct": false} ] 
    },
    { 
      "target_word": "PEN", "phonetic": "pen", "translation": "Lapicero", "speed": 7, 
      "context_es": "¡Rápido! Ahora busca el Lapicero.", 
      "items": [ {"id": 1, "content": "PEN", "is_correct": true}, {"id": 2, "content": "BOOK", "is_correct": false}, {"id": 3, "content": "RULER", "is_correct": false} ] 
    },
    { 
      "target_word": "DESK", "phonetic": "desk", "translation": "Escritorio", "speed": 8, 
      "context_es": "¡Atento! Toca el Escritorio.", 
      "items": [ {"id": 1, "content": "DESK", "is_correct": true}, {"id": 2, "content": "PAPER", "is_correct": false}, {"id": 3, "content": "PEN", "is_correct": false} ] 
    },
    { 
      "target_word": "ERASER", "phonetic": "iréiser", "translation": "Borrador", "speed": 8, 
      "context_es": "¡Último! Encuentra el Borrador.", 
      "items": [ {"id": 1, "content": "ERASER", "is_correct": true}, {"id": 2, "content": "RULER", "is_correct": false}, {"id": 3, "content": "BOOK", "is_correct": false} ] 
    }
  ]
}');

-- ==========================================
-- STAGE 3: Identificar Gramática (The Detective)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Detective de Acciones', 'detective', 3, 25, '
{
  "rounds": [
    {
      "sentence": ["THE", "BOY", "READS"],
      "phonetics": ["da", "bói", "rids"],
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
    },
    {
      "sentence": ["THE", "TEACHER", "SPEAKS"],
      "phonetics": ["da", "tícher", "spiks"],
      "target_word": "SPEAKS",
      "target_type": "Verbo (Acción)",
      "translation": "El profesor habla",
      "scene_emoji": "👨‍🏫🗣️",
      "context_es": "¡Encuentra el Verbo para descubrir la escena!"
    }
  ]
}');

-- ==========================================
-- STAGE 4: Creación de Oraciones (Sentence Survival)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Puente Escolar', 'sentence_survival', 4, 30, '
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
      "word_phonetics": {"THIS":"dis", "IS":"is", "MY":"mái", "BOOK":"buk", "YOUR":"iur", "DESK":"desk"},
      "context_es": "¡Cruza el río diciendo: Este es mi libro!" 
    },
    { 
      "sentence": ["I", "READ", "A", "BOOK"], 
      "phonetic": "ai rid a buk", 
      "translation": "Yo leo un libro", 
      "distractors": ["WRITE", "PAPER"], 
      "word_phonetics": {"I":"ai", "READ":"rid", "A":"a", "BOOK":"buk", "WRITE":"rait", "PAPER":"péiper"},
      "context_es": "¡Último puente! Di: Yo leo un libro." 
    }
  ]
}');

-- ==========================================
-- STAGE 5: Boss Final (Exam)
-- ==========================================
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Examen de la Profesora (Jefe)', 'exam', 5, 50, '
{
  "time_limit": 12,
  "lives": 3,
  "questions": [
    {"q": "¿Qué significa BOOK?", "options": ["Libro", "Cuaderno", "Lápiz"], "answer": "Libro", "phonetic": "buk"},
    {"q": "¿Cómo se dice Escritorio?", "options": ["Desk", "Chair", "Table"], "answer": "Desk", "phonetic": "desk"},
    {"q": "Completa: I HAVE A RED ___ (Yo tengo un lapicero rojo)", "options": ["PEN", "ERASER", "RULER"], "answer": "PEN", "phonetic": "ai jav a red pen"},
    {"q": "¿Qué significa THE BOY READS?", "options": ["El niño lee", "La niña escribe", "El profesor habla"], "answer": "El niño lee", "phonetic": "da bói rids"},
    {"q": "¿Cómo se dice Borrador?", "options": ["Eraser", "Paper", "Ruler"], "answer": "Eraser", "phonetic": "iréiser"}
  ]
}');

SET FOREIGN_KEY_CHECKS = 1;