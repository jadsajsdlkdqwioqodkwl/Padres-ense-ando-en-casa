<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . " - My World" : "My World"; ?></title>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #4834d4;
        --accent: #ff9f43;
        --success: #2ed573;
        --dark: #2f3542;
        --light: #f1f2f6;
        --bg: #f8faff;
        --card-bg: #FFFFFF;
        --border-color: #eee;
        --text-muted: #57606f;
    }

    body {
        font-family: 'Outfit', sans-serif;
        background: linear-gradient(135deg, #f8faff 0%, #e0e7ff 100%);
        color: var(--dark);
        margin: 0; padding: 20px;
        display: flex; flex-direction: column; align-items: center;
        gap: 30px; padding-bottom: 100px;
        min-height: 100vh;
    }

    h1, h2, h3 { font-family: 'Fredoka', sans-serif; color: var(--primary); }
    
    .container {
        background: var(--card-bg); width: 100%; max-width: 800px;
        padding: 40px; border-radius: 25px;
        box-shadow: 0 10px 30px rgba(72, 52, 212, 0.08);
        position: relative; border: 1px solid var(--border-color);
    }
    .text-center { text-align: center; }
</style>