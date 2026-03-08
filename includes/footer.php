<?php
// includes/footer.php
?>
    <style>
        .site-footer {
            width: 100%;
            max-width: 1000px;
            text-align: center;
            padding: 30px 0;
            color: #94A3B8;
            font-size: 14px;
            margin-top: 40px;
            border-top: 1px solid #E2E8F0;
        }

        .footer-mascot-msg {
            display: block;
            margin-bottom: 15px;
            font-weight: 700;
            color: var(--brand-blue);
            font-size: 16px;
        }

        @media print {
            .site-footer { display: none; }
        }
    </style>

    <footer class="site-footer">
        <span class="footer-mascot-msg">🐶 "¡Sigue practicando, lo estás haciendo genial!"</span>
        <p>&copy; <?php echo date('Y'); ?> <strong>My World</strong> - Plataforma de Aprendizaje Interactivo</p>
        <p>Lección: <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'App Interactiva'; ?></p>
    </footer>

    <script src="assets/js/engine.js"></script>
    <script src="assets/js/global.js"></script>
</body>
</html>