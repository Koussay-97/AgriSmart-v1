/* AgriSmart Back Office : preloader + sidebar mobile. Remplaçable par le main.js du template. */
(function () {
    window.addEventListener('load', function () {
        var p = document.getElementById('preloader');
        if (p) { p.style.opacity = '0'; setTimeout(function () { p.style.display = 'none'; }, 300); }
    });
    var toggle = document.getElementById('menu-toggle');
    var overlay = document.querySelector('.overlay');
    function toggleSidebar() { document.body.classList.toggle('sidebar-open'); }
    if (toggle) toggle.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
})();

// Modale de confirmation de suppression (liste des utilisateurs) - pas de JS inline : la CSP l'interdit.
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('deleteModal');
    if (!modal) { return; }
    modal.addEventListener('show.bs.modal', function (ev) {
        var b = ev.relatedTarget;
        document.getElementById('deleteId').value = b.getAttribute('data-id');
        document.getElementById('deleteName').textContent = b.getAttribute('data-nom');
    });
});

// <select data-autosubmit> : envoie le formulaire dès que la valeur change (changement de rôle dans la liste).
document.addEventListener('change', function (e) {
    var el = e.target;
    if (el && el.hasAttribute && el.hasAttribute('data-autosubmit') && el.form) {
        if (window.confirm('Modifier le rôle de cet utilisateur ?')) { el.form.submit(); } else { el.form.reset(); }
    }
});

// Confirmation avant action destructive : <form class="js-confirm" data-confirm="Message ?"> (pas de JS inline : CSP)
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.classList && form.classList.contains('js-confirm') && !e.defaultPrevented) {
        if (!window.confirm(form.getAttribute('data-confirm') || 'Confirmer ?')) { e.preventDefault(); }
    }
});
