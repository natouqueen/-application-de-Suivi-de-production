<?php
require_once __DIR__ . "/../models/Commande.php";
require_once __DIR__ . "/../models/Client.php"; // Modèle uniquement, pas le contrôleur

$commandeModel = new Commande();
$clientModel = new Client();

// Ajouter une commande
function ajouterCommande($id_client, $produit, $quantite, $date_commande) {
    global $commandeModel;
    return $commandeModel->ajouter($id_client, $produit, $quantite, $date_commande);
}

// Modifier une commande
function modifierCommande($id_commande, $id_client, $produit, $quantite, $date_commande) {
    global $commandeModel;
    return $commandeModel->modifier($id_commande, $id_client, $produit, $quantite, $date_commande);
}

// Supprimer une commande
function supprimerCommande($id_commande) {
    global $commandeModel;
    return $commandeModel->supprimer($id_commande);
}

// Lister toutes les commandes avec le nom du client
function listerCommandes() {
    global $commandeModel;
    return $commandeModel->lister();
}

// Trouver une commande par ID
function trouverCommandeParId($id_commande) {
    global $commandeModel;
    return $commandeModel->trouverParId($id_commande);
}

// Logique principale pour la page commande

require_once __DIR__ . "/../../config/auth.php";
requireRole([1]);

$redirect = "dashboard.php"; // Admin only

// Ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterCommande($_POST['id_client'], $_POST['produit'], $_POST['quantite'], $_POST['date_commande']);
    header("Location: commande.php");
    exit;
}

// Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierCommande($_POST['id_commande'], $_POST['id_client'], $_POST['produit'], $_POST['quantite'], $_POST['date_commande']);
    header("Location: commande.php");
    exit;
}

// Supprimer
if (isset($_GET['supprimer'])) {
    supprimerCommande($_GET['supprimer']);
    header("Location: commande.php");
    exit;
}

// Charger les données
$commandes = listerCommandes();
$clients = $clientModel->lister(); // récupère tous les clients
$commandeEdit = isset($_GET['modifier']) ? trouverCommandeParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/commandes.php";
