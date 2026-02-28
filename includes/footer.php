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
        <span class="footer-mascot-msg">🐶 "Keep practicing, you are doing great!"</span>
        <p>&copy; <?php echo date('Y'); ?> <strong>English 15</strong> - Interactive Learning Platform</p>
        <p>Lesson: <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Interactive App'; ?></p>
    </footer>

    <script>
        // Si hay una mascota, podemos hacer que se despida al final de la página
        console.log("English 15 Engine: Lesson Loaded Successfully.");
    </script>

</body>
</html>