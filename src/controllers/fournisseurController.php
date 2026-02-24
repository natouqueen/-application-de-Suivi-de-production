<?php
require_once __DIR__ . "/../models/Fournisseur.php";

$fournisseurModel = new Fournisseur();

// Ajouter un fournisseur
function ajouterFournisseur($nom, $email, $telephone, $adresse) {
    global $fournisseurModel;
    return $fournisseurModel->ajouter($nom, $email, $telephone, $adresse);
}

// Lister tous les fournisseurs
function listerFournisseurs() {
    global $fournisseurModel;
    return $fournisseurModel->lister();
}

// Supprimer un fournisseur
function supprimerFournisseur($id) {
    global $fournisseurModel;
    return $fournisseurModel->supprimer($id);
}

// Trouver un fournisseur par ID
function trouverFournisseurParId($id) {
    global $fournisseurModel;
    return $fournisseurModel->trouverParId($id);
}

// Modifier un fournisseur
function modifierFournisseur($id, $nom, $email, $telephone, $adresse) {
    global $fournisseurModel;
    return $fournisseurModel->modifier($id, $nom, $email, $telephone, $adresse);
}

// Logique principale pour la page fournisseur

require_once __DIR__ . "/../../config/auth.php";
requireRole([1]);

$redirect = "dashboard.php";

// Ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterFournisseur($_POST['nom'], $_POST['email'], $_POST['telephone'], $_POST['adresse']);
    header("Location: fournisseur.php?success=1");
    exit;
}

// Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierFournisseur($_POST['id_fournisseur'], $_POST['nom'], $_POST['email'], $_POST['telephone'], $_POST['adresse']);
    header("Location: fournisseur.php?modif=1");
    exit;
}

// Supprimer
if (isset($_GET['supprimer'])) {
    supprimerFournisseur($_GET['supprimer']);
    header("Location: fournisseur.php?delete=1");
    exit;
}

// Charger les données
$fournisseurs = listerFournisseurs();
$fournisseurEdit = isset($_GET['modifier']) ? trouverFournisseurParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/fournisseurs.php";
