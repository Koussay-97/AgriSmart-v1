<?php /* includes/scripts.php : scripts JS communs (workshop étape 7).
         Tous hébergés localement : la CSP n'autorise que 'self' et interdit tout JavaScript inline. */ ?>
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/bootstrap/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/validate.js"></script>
<script src="js/template.js"></script>
<script src="js/main.js"></script>
<?php if (!empty($extraScripts)): ?>
    <?= $extraScripts ?>
<?php endif; ?>
