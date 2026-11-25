/**
 * validate.js - validation des formulaires côté navigateur (confort utilisateur).
 * Rappel : la validation SERVEUR (UserController, contrôleurs) reste obligatoire : le JavaScript peut être contourné.
 *
 * Utilisation : <form data-validate novalidate> ... <input data-rules="required|min:3|email"> <div class="invalid-feedback"></div>
 * Règles : required, min:N, max:N, email, phone, password, decimal, date, intrange:A-B,
 *          match:#idAutreChamp, afterdate:#idAutreChamp, reqif:#idChamp=valeur
 */
(function (root) {
    'use strict';

    var RULES = {
        required: function (v) { return v.trim() !== '' ? null : 'Ce champ est obligatoire.'; },
        min: function (v, a) { var n = parseInt(a, 10); return v.trim().length >= n ? null : 'Minimum ' + n + ' caractères.'; },
        max: function (v, a) { var n = parseInt(a, 10); return v.trim().length <= n ? null : 'Maximum ' + n + ' caractères.'; },
        email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? null : 'Email invalide.'; },
        phone: function (v) { return /^[0-9 +]{8,15}$/.test(v.trim()) ? null : 'Téléphone invalide (8 à 15 chiffres).'; },
        password: function (v) {
            return (v.length >= 8 && /[A-Za-z]/.test(v) && /[0-9]/.test(v))
                ? null : 'Mot de passe : 8 caractères minimum, avec au moins une lettre et un chiffre.';
        },
        decimal: function (v) {
            var s = v.trim().replace(',', '.');
            return (/^\d{1,6}(\.\d{1,2})?$/.test(s) && parseFloat(s) > 0)
                ? null : 'Nombre positif attendu (2 décimales maximum).';
        },
        date: function (v) { return isDate(v.trim()) ? null : 'Date invalide.'; },
        intrange: function (v, a) {
            var b = a.split('-');
            var lo = parseInt(b[0], 10), hi = parseInt(b[1], 10);
            var s = v.trim();
            return (/^\d{1,4}$/.test(s) && parseInt(s, 10) >= lo && parseInt(s, 10) <= hi)
                ? null : 'Entier entre ' + lo + ' et ' + hi + ' attendu.';
        }
    };

    /** Vrai si s est une date réelle au format AAAA-MM-JJ (ex. 2026-02-30 est refusée). */
    function isDate(s) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
        if (!m) { return false; }
        var d = new Date(Date.UTC(+m[1], +m[2] - 1, +m[3]));
        return d.getUTCFullYear() === +m[1] && d.getUTCMonth() === +m[2] - 1 && d.getUTCDate() === +m[3];
    }

    /**
     * Valide une valeur selon une chaîne de règles. Retourne le message d'erreur ou null.
     * getOther(selector) : fonction qui retourne la valeur d'un autre champ (pour match / reqif).
     * Un champ vide n'est contrôlé que par "required" / "match" / "reqif" (les autres règles ne s'appliquent qu'aux champs remplis).
     */
    function validateValue(value, rulesStr, getOther) {
        var rules = (rulesStr || '').split('|').filter(Boolean);
        var isRequired = false;
        for (var i = 0; i < rules.length; i++) {
            var parts = rules[i].split(':');
            var name = parts[0];
            var arg = parts.slice(1).join(':');
            var msg = null;
            if (name === 'match') {
                var other = getOther ? getOther(arg) : '';
                msg = value === other ? null : 'Les mots de passe ne correspondent pas.';
            } else if (name === 'afterdate') {
                var from = getOther ? getOther(arg) : '';
                if (value.trim() !== '' && from !== '' && value < from) { msg = 'La date doit être postérieure ou égale à la date de départ.'; }
            } else if (name === 'reqif') {
                var cond = arg.split('=');
                var cur = getOther ? getOther(cond[0]) : '';
                if (cur === cond[1]) { isRequired = true; msg = RULES.required(value); }
            } else if (name === 'required') {
                isRequired = true;
                msg = RULES.required(value);
            } else if (RULES[name]) {
                if (value.trim() === '') { continue; }   // champ optionnel vide : OK
                msg = RULES[name](value, arg);
            }
            if (msg) { return msg; }
        }
        return null;
    }

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = { validateValue: validateValue };
    }
    if (typeof document === 'undefined') { return; }

    function feedbackOf(input) {
        var next = input.nextElementSibling;
        while (next && !next.classList.contains('invalid-feedback')) { next = next.nextElementSibling; }
        if (!next) {
            next = document.createElement('div');
            next.className = 'invalid-feedback';
            input.parentNode.insertBefore(next, input.nextSibling);
        }
        return next;
    }

    function getOther(sel) {
        var el = document.querySelector(sel);
        return el ? el.value : '';
    }

    function checkField(input) {
        var msg = validateValue(input.value, input.getAttribute('data-rules'), getOther);
        input.classList.toggle('is-invalid', !!msg);
        feedbackOf(input).textContent = msg || '';
        return !msg;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var forms = document.querySelectorAll('form[data-validate]');
        Array.prototype.forEach.call(forms, function (form) {
            var fields = form.querySelectorAll('[data-rules]');
            form.addEventListener('submit', function (ev) {
                var firstBad = null;
                Array.prototype.forEach.call(fields, function (f) {
                    if (!checkField(f) && !firstBad) { firstBad = f; }
                });
                if (firstBad) { ev.preventDefault(); firstBad.focus(); }
            });
            Array.prototype.forEach.call(fields, function (f) {
                f.addEventListener('blur', function () { if (f.value !== '' || f.classList.contains('is-invalid')) { checkField(f); } });
                f.addEventListener('input', function () { if (f.classList.contains('is-invalid')) { checkField(f); } });
                f.addEventListener('change', function () { checkField(f); });
            });
        });
    });
})(typeof window !== 'undefined' ? window : this);
