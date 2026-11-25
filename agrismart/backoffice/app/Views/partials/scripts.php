<!-- partials/scripts.php : scripts communs (workshop BO étape 7) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- TODO template : ajouter ici les scripts du template (Chart.js, main.js...) copiés dans app/Views/assets/js/ -->
<script src="app/Views/assets/js/validate.js"></script>
<script src="app/Views/assets/js/main.js"></script>

<?php if (!empty($extraScripts)): ?>
    <?= $extraScripts ?>
<?php endif; ?>
</body>
</html>
