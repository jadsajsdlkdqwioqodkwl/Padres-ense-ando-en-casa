DELETE FROM lessons WHERE module_id = 2;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (2, 'Mundo 2: My School', '#00b894', 2);

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Colores del Salón', 'color_rescue', 1, 10, '
{
  "time_limit": 18,
  "rounds": [
    { "color_name": "Yellow", "color_hex": "#f1c40f", "item": "✏️", "translation": "Amarillo", "context_es": "¡Salva el lápiz amarillo!", "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Red", "hex": "#ff4757"}] },
    { "color_name": "Blue", "color_hex": "#3742fa", "item": "🖊️", "translation": "Azul", "context_es": "¡El OVNI quiere el lapicero azul! Píntalo rápido.", "distractors": [{"name": "Green", "hex": "#2ed573"}, {"name": "Yellow", "hex": "#f1c40f"}] },
    { "color_name": "Red", "color_hex": "#ff4757", "item": "📕", "translation": "Rojo", "context_es": "¡Protege el libro rojo!", "distractors": [{"name": "Black", "hex": "#2f3640"}, {"name": "Blue", "hex": "#3742fa"}] }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Defiende tu Escritorio', 'defender', 2, 10, '
{
  "time_limit": 20,
  "rounds": [
    { "word": "DESK", "translation": "Escritorio", "distractors": ["A", "P", "L"], "context_es": "¡Defiende tu ESCRITORIO del monstruo!" },
    { "word": "BOOK", "translation": "Libro", "distractors": ["M", "C", "T"], "context_es": "¡Ahora escribe LIBRO para protegerlo!" },
    { "word": "PEN", "translation": "Lapicero", "distractors": ["X", "Y", "Z"], "context_es": "¡Última defensa! Salva tu LAPICERO." }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Lluvia de Útiles', 'meteor_strike', 3, 15, '
{
  "rounds": [
    { "target_word": "RULER", "translation": "Regla", "speed": 6, "context_es": "¡Destruye el meteorito que diga Regla!", "items": [ {"id": 1, "content": "RULER", "is_correct": true}, {"id": 2, "content": "PAPER", "is_correct": false}, {"id": 3, "content": "ERASER", "is_correct": false} ] },
    { "target_word": "ERASER", "translation": "Borrador", "speed": 7, "context_es": "¡Rápido! Ahora busca el meteorito que dice Borrador.", "items": [ {"id": 1, "content": "ERASER", "is_correct": true}, {"id": 2, "content": "DESK", "is_correct": false}, {"id": 3, "content": "PEN", "is_correct": false} ] },
    { "target_word": "PAPER", "translation": "Papel", "speed": 8, "context_es": "¡Atento! Toca el meteorito que diga Papel.", "items": [ {"id": 1, "content": "PAPER", "is_correct": true}, {"id": 2, "content": "BOOK", "is_correct": false}, {"id": 3, "content": "RULER", "is_correct": false} ] }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Camino a Clases', 'sentence_survival', 4, 20, '
{
  "rounds": [
    { "sentence": ["I", "HAVE", "A", "PEN"], "translation": "Yo tengo un lapicero", "distractors": ["YOU", "BOOK"], "context_es": "¡Arma el puente diciendo: Yo tengo un lapicero!" },
    { "sentence": ["THIS", "IS", "MY", "DESK"], "translation": "Este es mi escritorio", "distractors": ["YOUR", "ERASER"], "context_es": "¡Cruza el río diciendo: Este es mi escritorio!" },
    { "sentence": ["I", "READ", "A", "BOOK"], "translation": "Yo leo un libro", "distractors": ["WRITE", "PAPER"], "context_es": "¡Último puente! Di: Yo leo un libro." }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (2, 'Examen de la Profesora (Jefe)', 'exam', 5, 40, '
{
  "time_limit": 10,
  "lives": 3,
  "questions": [
    {"q": "¿Qué significa BOOK?", "options": ["Libro", "Cuaderno", "Lápiz"], "answer": "Libro"},
    {"q": "¿Cómo se dice Escritorio?", "options": ["Desk", "Chair", "Table"], "answer": "Desk"},
    {"q": "Completa: I HAVE A ___ (Yo tengo un lapicero)", "options": ["PEN", "ERASER", "RULER"], "answer": "PEN"},
    {"q": "¿Cómo se dice Borrador?", "options": ["Eraser", "Paper", "Ruler"], "answer": "Eraser"}
  ]
}');