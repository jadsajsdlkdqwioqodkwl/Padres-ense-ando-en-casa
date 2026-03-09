<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . " - My World" : "My World"; ?></title>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/twemoji.min.js"></script>
<script>
    // Parsea toda la página para convertir emojis nativos a imágenes HD de Twemoji
    document.addEventListener("DOMContentLoaded", function() {
        twemoji.parse(document.body, { folder: 'svg', ext: '.svg' });
    });
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">

<style>
    :root {
        /* Paleta Oficial de Landing Page */
        --brand-blue: #1C3D6A;   
        --brand-green: #68A93E;  
        --brand-orange: #F29C38; 
        --brand-lblue: #5CB2E4;  
        --bg-light: #F8FAFC;     
        --text-main: #333333;
        --text-muted: #64748B;
        --white: #FFFFFF;

        /* Retrocompatibilidad Segura (NO BORRAR) */
        --primary: var(--brand-blue);
        --accent: var(--brand-orange);
        --success: var(--brand-green);
        --dark: var(--text-main);
        --light: var(--bg-light);
        --bg: var(--bg-light);
        --card-bg: var(--white);
        --border-color: #E2E8F0;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: var(--bg-light);
        color: var(--text-main);
        margin: 0; padding: 20px;
        display: flex; flex-direction: column; align-items: center;
        gap: 30px; padding-bottom: 100px;
        min-height: 100vh;
        line-height: 1.6;
    }

    h1, h2, h3 { 
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
        color: var(--brand-blue); 
        letter-spacing: -0.5px;
        font-weight: 800;
    }
    
    .container {
        background: var(--card-bg); 
        width: 100%; 
        max-width: 1000px;
        padding: 40px; 
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(28, 61, 106, 0.05);
        position: relative; 
        border-top: 5px solid var(--brand-green);
    }
    
    .text-center { text-align: center; }

    /* FASE 5: Regla CSS global para que los Twemoji no rompan la alineación del texto */
    img.emoji { 
        height: 1.2em; 
        width: 1.2em; 
        margin: 0 .05em 0 .1em; 
        vertical-align: -0.1em; 
        display: inline-block; 
        pointer-events: none; 
    }
</style>