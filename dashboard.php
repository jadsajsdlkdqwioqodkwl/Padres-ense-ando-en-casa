<?php
// dashboard.php
session_start();
require_once 'includes/config.php';

// Seguridad: prevenir acceso sin login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener módulos
$stmtModules = $pdo->query("SELECT * FROM modules ORDER BY order_num ASC");
$modules = $stmtModules->fetchAll();

$page_title = "Mis Módulos";
?>

<!DOCTYPE html>
<html lang="es">

<head>

<?php include 'includes/head.php'; ?>

<!-- Twemoji para estandarizar emojis -->
<script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>

<style>

.module-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:30px;
margin-top:40px;
}

.module-card{
background:white;
border-radius:18px;
padding:40px 30px;
text-align:center;
box-shadow:0 15px 35px rgba(28,61,106,0.05);
text-decoration:none;
color:inherit;
border:1px solid #E2E8F0;
transition:0.3s;
display:block;
position:relative;
overflow:hidden;
}

.module-card:hover{
transform:translateY(-6px);
box-shadow:0 20px 45px rgba(28,61,106,0.1);
}

.module-icon{
font-size:70px;
margin-bottom:20px;
transition:0.3s;
}

.module-card:hover .module-icon{
transform:scale(1.1);
}

.btn-enter{
margin-top:25px;
padding:12px 20px;
background:var(--bg-light);
border-radius:50px;
font-weight:700;
color:var(--brand-blue);
border:2px solid #E2E8F0;
transition:0.3s;
display:inline-block;
}

.module-card:hover .btn-enter{
background:#E2E8F0;
}

/* Modal padre */

.modal-overlay{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
display:flex;
justify-content:center;
align-items:center;
opacity:0;
pointer-events:none;
transition:0.3s;
z-index:9999;
}

.modal-overlay.active{
opacity:1;
pointer-events:auto;
}

.modal-content{
background:white;
padding:40px;
border-radius:18px;
max-width:520px;
text-align:center;
box-shadow:0 25px 60px rgba(0,0,0,0.25);
}

.modal-title{
font-size:2rem;
color:var(--brand-blue);
margin-bottom:15px;
}

.modal-text{
color:#475569;
font-size:1.1rem;
line-height:1.6;
}

</style>

</head>

<body>

<div class="container text-center">

<?php include 'includes/navbar.php'; ?>

<h1 style="color:var(--brand-blue);font-size:2.5rem;margin-bottom:10px;">
Explora tu Mundo 🌍
</h1>

<p style="color:#64748B;font-size:1.1rem;">
Selecciona una semana para empezar a jugar.
</p>

<div class="module-grid">

<?php foreach ($modules as $mod): 

$icon = ($mod['order_num'] == 1) ? '🏡' : '🌳';

?>

<a href="course.php?module=<?php echo $mod['id']; ?>" 
class="module-card"
style="border-bottom:6px solid <?php echo $mod['color_theme']; ?>;">

<div class="module-icon">
<?php echo $icon; ?>
</div>

<h2 style="color:<?php echo $mod['color_theme']; ?>;margin-bottom:5px;">
<?php echo htmlspecialchars($mod['title']); ?>
</h2>

<div class="btn-enter">
Entrar a la Semana ➡️
</div>

</a>

<?php endforeach; ?>

</div>

</div>

<!-- Modal Bienvenida Padres -->

<div id="welcomeParentModal" class="modal-overlay">

<div class="modal-content">

<h2 class="modal-title">
¡Bienvenido, Papá / Mamá! 👨‍👩‍👧‍👦
</h2>

<p class="modal-text">

Esta plataforma está diseñada para que <strong>tú</strong> enseñes inglés a tu hijo,
aunque no hables el idioma.

<br><br>

✔ Lee la pronunciación en español  
<br>
✔ Jueguen juntos  
<br>
✔ Tu voz es la guía del aprendizaje

<br><br>

¡Disfruten esta aventura! 🚀✨

</p>

<button id="closeParentModalBtn" class="btn-enter" style="margin-top:20px;">
¡Entendido! 👍
</button>

</div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    /* Twemoji */
    twemoji.parse(document.body,{
        folder:'svg',
        ext:'.svg'
    });

    /* Modal bienvenida */

    const modal = document.getElementById('welcomeParentModal');
    const closeBtn = document.getElementById('closeParentModalBtn');

    if(!localStorage.getItem('parentWelcomeShown')){

        setTimeout(()=>{
            modal.classList.add('active');
        },600);

    }

    closeBtn.addEventListener('click',()=>{

        modal.classList.remove('active');

        localStorage.setItem('parentWelcomeShown','true');

    });

});

</script>

<script src="assets/js/engine.js"></script>

</body>
</html>