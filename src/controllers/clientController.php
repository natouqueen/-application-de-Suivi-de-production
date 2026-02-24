<?php
require_once __DIR__ . "/../models/Client.php";

$clientModel = new Client();

// Ajouter un client
function ajouterClient($nom, $adresse, $telephone) {
    global $clientModel;
    return $clientModel->ajouter($nom, $adresse, $telephone);
}

// Modifier un client
function modifierClient($id, $nom, $adresse, $telephone) {
    global $clientModel;
    return $clientModel->modifier($id, $nom, $adresse, $telephone);
}

// Supprimer un client
function supprimerClient($id) {
    global $clientModel;
    return $clientModel->supprimer($id);
}

// Lister les clients
function listerClients() {
    global $clientModel;
    return $clientModel->lister();
}

// Trouver un client par ID
function trouverClientParId($id) {
    global $clientModel;
    return $clientModel->trouverParId($id);
}

// Logique principale pour la page client
session_start();
require_once __DIR__ . "/../../config/auth.php";
requireRole([1, 2, 3]);

// Déterminer le tableau de bord selon le rôle
$role = $_SESSION['user']['role'] ?? 3; // par défaut utilisateur simple
$redirect = "dashboard.php"; // par défaut
if ($role == 2) $redirect = "dashboard_operateur.php"; // Opérateur
elseif ($role == 3) $redirect = "dashboard_user.php"; // Utilisateur simple

// Ajouter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    ajouterClient($_POST['nom'], $_POST['adresse'], $_POST['telephone']);
    header("Location: client.php");
    exit;
}

// Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    modifierClient($_POST['id_client'], $_POST['nom'], $_POST['adresse'], $_POST['telephone']);
    header("Location: client.php");
    exit;
}

// Supprimer
if (isset($_GET['supprimer'])) {
    supprimerClient($_GET['supprimer']);
    header("Location: client.php");
    exit;
}

// Charger les données
$clients = listerClients();
$clientEdit = isset($_GET['modifier']) ? trouverClientParId($_GET['modifier']) : null;

// Inclure la vue
include __DIR__ . "/../views/clients.php";
