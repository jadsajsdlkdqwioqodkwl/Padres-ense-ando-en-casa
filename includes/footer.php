<?php
// includes/footer.php
?>
    <style>
        .site-footer {
            width: 100%;
            max-width: 800px;
            text-align: center;
            padding: 30px 0;
            color: #888;
            font-size: 14px;
            margin-top: 40px;
            border-top: 1px solid #eee;
        }

        .footer-mascot-msg {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: var(--primary);
        }

        @media print {
            .site-footer { display: none; }
        }
    </style>

    <footer class="site-footer">
        <span class="footer-mascot-msg">🐶 "¡Sigue practicando, lo estás haciendo genial!"</span>
        <p>&copy; <?php echo date('Y'); ?> <strong>English 15</strong> - Plataforma de Aprendizaje Interactivo</p>
        <p>Lección: <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'App Interactiva'; ?></p>
    </footer>

    <script src="assets/js/engine.js"></script>
    <script src="assets/js/global.js"></script>
</body>
</html>