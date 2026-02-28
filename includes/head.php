<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . " - My English App" : "My English App"; ?></title>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
    :root {
        --primary: #2B3A67; 
        --accent: #FF7F50; 
        --bg: #F0F4F8;
        --card-bg: #FFFFFF;
        --success: #4CAF50;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg);
        margin: 0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px;
        padding-bottom: 100px;
    }

    .container {
        background: var(--card-bg);
        width: 100%;
        max-width: 800px;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        position: relative;
    }

    h1, h2, h3 { color: var(--primary); }
    
    /* Clases útiles globales */
    .text-center { text-align: center; }
</style>