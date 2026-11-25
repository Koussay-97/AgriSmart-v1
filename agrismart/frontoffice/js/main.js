/* AgriSmart Front Office : comportements propres au projet.
   Le spinner, le bouton "retour en haut", la navbar collante, WOW et les carrousels sont gérés par js/template.js (template eLEARNING). */

// Confirmation avant action destructive : <form class="js-confirm" data-confirm="Message ?">
// (pas de JavaScript inline dans les pages : la Content-Security-Policy l'interdit)
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.classList && form.classList.contains('js-confirm') && !e.defaultPrevented) {
        if (!window.confirm(form.getAttribute('data-confirm') || 'Confirmer ?')) { e.preventDefault(); }
    }
});
