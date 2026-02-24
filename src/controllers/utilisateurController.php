<?php
require_once __DIR__ . "/../models/Utilisateur.php";

$utilisateurModel = new Utilisateur();

// Ajouter un utilisateur
function ajouterUtilisateur($nom, $email, $mot_de_passe) {
    global $utilisateurModel;
    return $utilisateurModel->ajouter($nom, $email, $mot_de_passe);
}

// Lister tous les utilisateurs
function listerUtilisateurs() {
    global $utilisateurModel;
    return $utilisateurModel->lister();
}

// Supprimer un utilisateur
function supprimerUtilisateur($id) {
    global $utilisateurModel;
    return $utilisateurModel->supprimer($id);
}

// Trouver un utilisateur par ID
function trouverUtilisateurParId($id) {
    global $utilisateurModel;
    return $utilisateurModel->trouverParId($id);
}

// Modifier un utilisateur
function modifierUtilisateur($id, $nom, $email, $mot_de_passe = null) {
    global $utilisateurModel;
    return $utilisateurModel->modifier($id, $nom, $email, $mot_de_passe);
}

// Logique principale pour la page utilisateur
session_start();
require_once __DIR__ . "/../../config/auth.php";
requireRole([1]);

$redirect = "dashboard.php";

// Ajouter utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterUtilisateur($_POST['nom'], $_POST['email'], $_POST['mot_de_passe']);
    header("Location: utilisateur.php");
    exit;
}

// Modifier utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierUtilisateur($_POST['id_utilisateur'], $_POST['nom'], $_POST['email'], $_POST['mot_de_passe'] ?? null);
    header("Location: utilisateur.php");
    exit;
}

// Supprimer utilisateur
if (isset($_GET['supprimer'])) {
    supprimerUtilisateur($_GET['supprimer']);
    header("Location: utilisateur.php");
    exit;
}

// Charger les données
$utilisateurs = listerUtilisateurs();
$utilisateurEdit = isset($_GET['modifier']) ? trouverUtilisateurParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/utilisateurs.php";
