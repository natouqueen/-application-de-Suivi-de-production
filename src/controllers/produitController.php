<?php
require_once __DIR__ . "/../models/Produit.php";
require_once __DIR__ . "/../models/Fournisseur.php"; // Modèle uniquement, pas le contrôleur

$produitModel = new Produit();
$fournisseurModel = new Fournisseur();

// Action : Lister
if (isset($_GET['action']) && $_GET['action'] === 'list') {
    $produits = $produitModel->lister();
    include __DIR__ . '/../views/produits/list.php';
    exit;
}

// Ajouter un produit
function ajouterProduit($nom, $prix, $id_fournisseur) {
    global $produitModel;
    return $produitModel->ajouter($nom, $prix, $id_fournisseur);
}

// Lister tous les produits
function listerProduits() {
    global $produitModel;
    return $produitModel->lister();
}

// Supprimer un produit
function supprimerProduit($id) {
    global $produitModel;
    return $produitModel->supprimer($id);
}

// Trouver un produit par ID
function trouverProduitParId($id) {
    global $produitModel;
    return $produitModel->trouverParId($id);
}

// Modifier un produit
function modifierProduit($id, $nom, $prix, $id_fournisseur) {
    global $produitModel;
    return $produitModel->modifier($id, $nom, $prix, $id_fournisseur);
}

// Logique principale pour la page produit

require_once __DIR__ . "/../../config/auth.php";
requireRole([1, 2, 3]);

// Déterminer le tableau de bord selon le rôle
$role = $_SESSION['user']['role'] ?? 3; // par défaut utilisateur simple
$redirect = "dashboard.php"; // par défaut
if ($role == 2) $redirect = "dashboard_operateur.php"; // Opérateur
elseif ($role == 3) $redirect = "dashboard_user.php"; // Utilisateur simple

// Récupérer les fournisseurs pour le <select>
$fournisseurs = $fournisseurModel->lister();

// Ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterProduit($_POST['nom'], $_POST['prix'], $_POST['id_fournisseur']);
    header("Location: produit.php");
    exit;
}

// Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierProduit($_POST['id_produit'], $_POST['nom'], $_POST['prix'], $_POST['id_fournisseur']);
    header("Location: produit.php");
    exit;
}

// Supprimer
if (isset($_GET['supprimer'])) {
    supprimerProduit($_GET['supprimer']);
    header("Location: produit.php");
    exit;
}

// Charger les données
$produits = listerProduits();
$produitEdit = isset($_GET['modifier']) ? trouverProduitParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/produits.php";
