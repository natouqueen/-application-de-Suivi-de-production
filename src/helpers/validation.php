<?php
// src/helpers/validation.php

// ✅ Vérifie si un email est valide
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ✅ Vérifie si c’est un nombre (entier > 0)
function isPositiveNumber($number) {
    return filter_var($number, FILTER_VALIDATE_INT) && $number > 0;
}

// ✅ Vérifie si une chaîne est non vide
function isNotEmpty($string) {
    return !empty(trim($string));
}

// ✅ Vérifie la longueur minimale d’un champ
function hasMinLength($string, $min) {
    return strlen(trim($string)) >= $min;
}
?>

<!-- ===== CSS pour validation ===== -->
<style>
.validation-error {
    color: #dc3545;
    font-size: 0.9rem;
    margin: 5px 0;
}
.validation-success {
    color: #28a745;
    font-size: 0.9rem;
    margin: 5px 0;
}
</style>
