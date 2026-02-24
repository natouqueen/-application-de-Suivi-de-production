<?php
// src/helpers/session.php

// ✅ Fonction pour démarrer la session (si pas déjà active)
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// ✅ Vérifie si l'utilisateur est connecté
function isLoggedIn() {
    startSession();
    return !empty($_SESSION['user']);
}

// ✅ Récupère l'utilisateur connecté
function getUser() {
    startSession();
    return $_SESSION['user'] ?? null;
}

// ✅ Vérifie si l'utilisateur a un rôle donné
function hasRole($role) {
    startSession();
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == $role;
}

// ✅ Déconnexion
function logout() {
    startSession();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!-- ===== CSS pour messages de session ===== -->
<style>
.session-message {
    background: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 0.95rem;
    text-align: center;
}
.session-error {
    background: #dc3545;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 0.95rem;
    text-align: center;
}
</style>
