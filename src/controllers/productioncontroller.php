<?php
require_once __DIR__ . "/../models/Production.php";

$productionModel = new Production();

// Ajouter une production
function ajouterProduction($id_produit, $quantite, $date_production) {
    global $productionModel;
    return $productionModel->ajouter($id_produit, $quantite, $date_production);
}

// Lister toutes les productions
function listerProductions() {
    global $productionModel;
    return $productionModel->lister();
}

// Supprimer une production
function supprimerProduction($id) {
    global $productionModel;
    return $productionModel->supprimer($id);
}

// Trouver une production par ID
function trouverProductionParId($id) {
    global $productionModel;
    return $productionModel->trouverParId($id);
}

// Modifier une production
function modifierProduction($id, $id_produit, $quantite, $date_production) {
    global $productionModel;
    return $productionModel->modifier($id, $id_produit, $quantite, $date_production);
}

// Logique principale pour la page production
session_start();
require_once __DIR__ . "/../../config/auth.php";
requireRole([1, 2, 3]);

$role = $_SESSION['user']['role'] ?? 3;
$redirect = "dashboard.php";
if ($role == 2) $redirect = "dashboard_operateur.php";

// Ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterProduction($_POST['id_produit'], $_POST['quantite'], $_POST['date_production']);
    header("Location: production.php");
    exit;
}

// Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierProduction($_POST['id_production'], $_POST['id_produit'], $_POST['quantite'], $_POST['date_production']);
    header("Location: production.php");
    exit;
}

// Supprimer
if (isset($_GET['supprimer'])) {
    supprimerProduction($_GET['supprimer']);
    header("Location: production.php");
    exit;
}

// Charger les données
$productions = listerProductions();
require_once __DIR__ . "/produitController.php";
$produits = listerProduits();
$productionEdit = isset($_GET['modifier']) ? trouverProductionParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/productions.php";


