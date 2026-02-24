<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    header("Location: ../public/login.php");
    exit;
}

// Fonction pour vérifier les rôles
function requireRole($allowedRoles = []) {
    if (!in_array($_SESSION['user']['role'], $allowedRoles)) {
        header("Location: ../public/error.php"); // Redirige vers la page d’erreur
        exit;
    }
}
