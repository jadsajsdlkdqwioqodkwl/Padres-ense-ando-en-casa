DELETE FROM lessons WHERE module_id = 1;

INSERT IGNORE INTO modules (id, title, color_theme, order_num) 
VALUES (1, 'Mundo 1: Primeros Pasos', '#2B3A67', 1);

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Colores Mágicos', 'color_rescue', 1, 10, '
{
  "time_limit": 20,
  "rounds": [
    { "color_name": "Red", "color_hex": "#ff4757", "item": "🍎", "translation": "Rojo", "context_es": "¡Atento! Encuentra el color de la manzana.", "distractors": [{"name": "Blue", "hex": "#3742fa"}, {"name": "Green", "hex": "#2ed573"}] },
    { "color_name": "Green", "color_hex": "#2ed573", "item": "🌳", "translation": "Verde", "context_es": "¡Ahora busca el color de las hojas del árbol!", "distractors": [{"name": "Yellow", "hex": "#f1c40f"}, {"name": "Red", "hex": "#ff4757"}] }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Mascotas al Rescate', 'defender', 2, 10, '
{
  "time_limit": 25,
  "rounds": [
    { "word": "CAT", "translation": "Gato", "distractors": ["M", "B", "Z"], "context_es": "¡Aleja al monstruo escribiendo GATO en inglés!" },
    { "word": "DOG", "translation": "Perro", "distractors": ["F", "L", "P"], "context_es": "¡Ahora defiende al PERRO!" }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'El Cielo Cae', 'meteor_strike', 3, 10, '
{
  "rounds": [
    { "target_word": "SUN", "translation": "Sol", "speed": 6, "context_es": "¡Destruye el meteorito que diga Sol!", "items": [ {"id": 1, "content": "SUN", "is_correct": true}, {"id": 2, "content": "MOON", "is_correct": false}, {"id": 3, "content": "STAR", "is_correct": false} ] },
    { "target_word": "MOON", "translation": "Luna", "speed": 7, "context_es": "¡Rápido! Ahora busca el meteorito que dice Luna.", "items": [ {"id": 1, "content": "MOON", "is_correct": true}, {"id": 2, "content": "SUN", "is_correct": false}, {"id": 3, "content": "CLOUD", "is_correct": false} ] }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Puente de Emociones', 'sentence_survival', 4, 15, '
{
  "rounds": [
    { "sentence": ["I", "AM", "HAPPY"], "translation": "Yo soy feliz", "distractors": ["SAD", "YOU"], "context_es": "¡Construye el puente diciendo: Yo soy feliz!" },
    { "sentence": ["YOU", "ARE", "TALL"], "translation": "Tú eres alto", "distractors": ["I", "SHORT"], "context_es": "¡Cruza el río diciendo: Tú eres alto!" }
  ]
}');

INSERT INTO lessons (module_id, title, template_type, order_num, reward_stars, content_data) 
VALUES (1, 'Examen Inicial (Jefe)', 'exam', 5, 30, '
{
  "time_limit": 10,
  "lives": 3,
  "questions": [
    {"q": "¿Cómo se dice Perro?", "options": ["Dog", "Cat", "Sun"], "answer": "Dog"},
    {"q": "¿Qué significa SUN?", "options": ["Sol", "Luna", "Estrella"], "answer": "Sol"},
    {"q": "Completa: I AM ___ (Yo soy feliz)", "options": ["HAPPY", "TALL", "SAD"], "answer": "HAPPY"},
    {"q": "¿Cómo se dice Rojo?", "options": ["Red", "Blue", "Green"], "answer": "Red"}
  ]
}');