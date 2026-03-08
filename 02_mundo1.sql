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

-- STAGE 1: Type Jumper (5 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Colores del Salón', 'jumper', 1, 15, '
{
  "rounds": [
    { "target_word": "YELLOW", "phonetic": "iélou", "translation": "Amarillo", "context_es": "¡Salta sobre el color Amarillo!", "distractors": ["BLUE", "RED"] },
    { "target_word": "PURPLE", "phonetic": "pérpol", "translation": "Morado", "context_es": "¡Cruza saltando en el color Morado!", "distractors": ["GREEN", "YELLOW"] },
    { "target_word": "ORANGE", "phonetic": "óranch", "translation": "Naranja", "context_es": "¡Busca el color Naranja para avanzar!", "distractors": ["BLACK", "BLUE"] },
    { "target_word": "PINK", "phonetic": "pinc", "translation": "Rosa", "context_es": "¡Salta sobre el Rosa!", "distractors": ["RED", "GREEN"] },
    { "target_word": "GREEN", "phonetic": "grin", "translation": "Verde", "context_es": "¡Último salto! Color Verde.", "distractors": ["RED", "YELLOW"] }
  ]
}');

-- STAGE 2: Meteor Strike (5 Rondas con Emojis)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Lluvia de Útiles', 'meteor_strike', 2, 20, '
{
  "rounds": [
    { "target_word": "BOOK", "phonetic": "buk", "translation": "Libro", "speed": 8, "context_es": "¡Destruye el meteorito que sea un Libro!", "items": [ {"id": 1, "content": "📖", "is_correct": true}, {"id": 2, "content": "🖊️", "is_correct": false}, {"id": 3, "content": "🪑", "is_correct": false} ] },
    { "target_word": "PEN", "phonetic": "pen", "translation": "Lapicero", "speed": 8, "context_es": "¡Rápido! Ahora busca el Lapicero.", "items": [ {"id": 1, "content": "🖊️", "is_correct": true}, {"id": 2, "content": "📖", "is_correct": false}, {"id": 3, "content": "📏", "is_correct": false} ] },
    { "target_word": "DESK", "phonetic": "desk", "translation": "Escritorio", "speed": 9, "context_es": "¡Atento! Toca el Escritorio.", "items": [ {"id": 1, "content": "🪑", "is_correct": true}, {"id": 2, "content": "📄", "is_correct": false}, {"id": 3, "content": "🖊️", "is_correct": false} ] },
    { "target_word": "ERASER", "phonetic": "iréiser", "translation": "Borrador", "speed": 9, "context_es": "¡Encuentra el Borrador!", "items": [ {"id": 1, "content": "🧽", "is_correct": true}, {"id": 2, "content": "📏", "is_correct": false}, {"id": 3, "content": "📖", "is_correct": false} ] },
    { "target_word": "RULER", "phonetic": "rúler", "translation": "Regla", "speed": 10, "context_es": "¡Último! Destruye la Regla.", "items": [ {"id": 1, "content": "📏", "is_correct": true}, {"id": 2, "content": "🧽", "is_correct": false}, {"id": 3, "content": "🪑", "is_correct": false} ] }
  ]
}');

-- STAGE 3: Defender (5 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Defiende tu Clase', 'defender', 3, 20, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "CHAIR", "phonetic": "cher", "translation": "Silla", "distractors": ["A", "P", "L"], "context_es": "¡Defiende tu SILLA del monstruo!" },
    { "word": "PAPER", "phonetic": "péiper", "translation": "Papel", "distractors": ["M", "C", "T"], "context_es": "¡Ahora escribe PAPEL para protegerlo!" },
    { "word": "CLOCK", "phonetic": "cloc", "translation": "Reloj", "distractors": ["X", "Y", "Z"], "context_es": "¡Salva tu RELOJ!" },
    { "word": "BOARD", "phonetic": "bord", "translation": "Pizarra", "distractors": ["E", "I", "U"], "context_es": "¡Protege la PIZARRA!" },
    { "word": "TRASH", "phonetic": "trash", "translation": "Basura", "distractors": ["O", "S", "D"], "context_es": "¡Última defensa! Protege la BASURA." }
  ]
}');

-- STAGE 4: Detective (5 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Detective Escolar', 'detective', 4, 25, '
{
  "rounds": [
    { "sentence": ["THE", "BOY", "READS"], "phonetics": ["da", "bói", "rids"], "target_word": "READS", "target_type": "Verbo (Acción)", "translation": "El niño lee", "scene_emoji": "👦📖", "context_es": "¡Encuentra la ACCIÓN para encender la luz!" },
    { "sentence": ["THE", "GIRL", "WRITES"], "phonetics": ["da", "guerl", "raits"], "target_word": "GIRL", "target_type": "Sujeto (Quién lo hace)", "translation": "La niña escribe", "scene_emoji": "👧✍️", "context_es": "¡Encuentra el SUJETO para encender la luz!" },
    { "sentence": ["THE", "TEACHER", "SPEAKS"], "phonetics": ["da", "tícher", "spiks"], "target_word": "SPEAKS", "target_type": "Verbo (Acción)", "translation": "El profesor habla", "scene_emoji": "👨‍🏫🗣️", "context_es": "¡Encuentra la ACCIÓN para descubrir la escena!" },
    { "sentence": ["THE", "STUDENT", "LISTENS"], "phonetics": ["da", "stiúdent", "lísens"], "target_word": "STUDENT", "target_type": "Sujeto (Quién lo hace)", "translation": "El estudiante escucha", "scene_emoji": "🧑‍🎓👂", "context_es": "¡Encuentra el SUJETO para encender la luz!" },
    { "sentence": ["THE", "CLASS", "STARTS"], "phonetics": ["da", "clas", "starts"], "target_word": "STARTS", "target_type": "Verbo (Acción)", "translation": "La clase empieza", "scene_emoji": "🏫🔔", "context_es": "¡Encuentra la ACCIÓN para encender la luz!" }
  ]
}');

-- STAGE 5: Grammar Train (5 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Tren Escolar', 'grammar_train', 5, 25, '
{
  "rounds": [
    { "sentence": ["I", "HAVE", "A", "BOOK"], "translations": ["Yo", "Tengo", "Un", "Libro"], "phonetics": ["ai", "jav", "a", "buk"], "sentence_phonetic": "ai jav a buk", "distractors": ["PEN", "HAS"], "distractors_phonetics": ["pen", "jas"], "context_es": "¡Carga los vagones uniendo el inglés con su significado!" },
    { "sentence": ["YOU", "NEED", "A", "PEN"], "translations": ["Tú", "Necesitas", "Un", "Lapicero"], "phonetics": ["iu", "nid", "a", "pen"], "sentence_phonetic": "iu nid a pen", "distractors": ["HE", "DESK"], "distractors_phonetics": ["ji", "desk"], "context_es": "¡Arma el tren diciendo Tú necesitas un lapicero!" },
    { "sentence": ["WE", "LEARN", "ENGLISH"], "translations": ["Nosotros", "Aprendemos", "Inglés"], "phonetics": ["ui", "lern", "ínglish"], "sentence_phonetic": "ui lern ínglish", "distractors": ["THEY", "SPEAK"], "distractors_phonetics": ["dei", "spik"], "context_es": "¡Conecta: Nosotros aprendemos inglés!" },
    { "sentence": ["SHE", "DRAWS", "A", "CAT"], "translations": ["Ella", "Dibuja", "Un", "Gato"], "phonetics": ["shi", "droos", "a", "kat"], "sentence_phonetic": "shi droos a kat", "distractors": ["HE", "DOG"], "distractors_phonetics": ["ji", "dog"], "context_es": "¡Arma el tren: Ella dibuja un gato!" },
    { "sentence": ["HE", "OPENS", "THE", "DOOR"], "translations": ["Él", "Abre", "La", "Puerta"], "phonetics": ["ji", "ópens", "da", "dor"], "sentence_phonetic": "ji ópens da dor", "distractors": ["SHE", "CLOSES"], "distractors_phonetics": ["shi", "clóuses"], "context_es": "¡Último tren! Él abre la puerta." }
  ]
}');

-- STAGE 6: Sentence Survival (5 Rondas)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'El Puente de Frases', 'sentence_survival', 6, 30, '
{
  "rounds": [
    { "sentence": ["I", "HAVE", "A", "RED", "PEN"], "phonetic": "ai jav a red pen", "translation": "Yo tengo un lapicero rojo", "distractors": ["HAS", "BLUE"], "word_phonetics": {"I":"ai", "HAVE":"jav", "A":"a", "RED":"red", "PEN":"pen", "HAS":"jas", "BLUE":"blú"}, "context_es": "¡Arma el puente diciendo: Yo tengo un lapicero rojo!" },
    { "sentence": ["THIS", "IS", "MY", "BOOK"], "phonetic": "dis is mai buk", "translation": "Este es mi libro", "distractors": ["YOUR", "DESK"], "word_phonetics": {"THIS":"dis", "IS":"is", "MY":"mái", "BOOK":"buk", "YOUR":"iur", "DESK":"desk"}, "context_es": "¡Cruza el río diciendo: Este es mi libro!" },
    { "sentence": ["I", "READ", "A", "STORY"], "phonetic": "ai rid a stóri", "translation": "Yo leo un cuento", "distractors": ["WRITE", "PAPER"], "word_phonetics": {"I":"ai", "READ":"rid", "A":"a", "STORY":"stóri", "WRITE":"rait", "PAPER":"péiper"}, "context_es": "¡Arma el puente: Yo leo un cuento!" },
    { "sentence": ["THE", "DESK", "IS", "BIG"], "phonetic": "da desk is big", "translation": "El escritorio es grande", "distractors": ["SMALL", "CHAIR"], "word_phonetics": {"THE":"da", "DESK":"desk", "IS":"is", "BIG":"big", "SMALL":"smol", "CHAIR":"cher"}, "context_es": "¡Cruza el río: El escritorio es grande!" },
    { "sentence": ["YOU", "WRITE", "A", "WORD"], "phonetic": "iu rait a uord", "translation": "Tú escribes una palabra", "distractors": ["READ", "BOOK"], "word_phonetics": {"YOU":"iu", "WRITE":"rait", "A":"a", "WORD":"uord", "READ":"rid", "BOOK":"buk"}, "context_es": "¡Último puente! Di: Tú escribes una palabra." }
  ]
}');

-- STAGE 7: Exam (Boss Final)
INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Examen de la Profesora (Jefe)', 'exam', 7, 50, '
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