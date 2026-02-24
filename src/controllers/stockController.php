<?php
require_once __DIR__ . "/../models/Stock.php";

$stockModel = new Stock();

// Lister le stock (produits + quantités)
function listerStock() {
    global $stockModel;
    return $stockModel->lister();
}

// Mettre à jour la quantité du stock
function majStock($id_produit, $quantite) {
    global $stockModel;
    return $stockModel->majQuantite($id_produit, $quantite);
}

// Logique principale pour la page stock
session_start();
require_once __DIR__ . "/../../config/auth.php";
requireRole([1]);

$role = $_SESSION['user']['role'] ?? 3; // par défaut utilisateur simple
$redirect = "dashboard.php"; // par défaut

// Mettre à jour une quantité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maj'])) {
    majStock($_POST['id_produit'], $_POST['quantite']);
    header("Location: stock.php");
    exit;
}

// Charger le stock
$stocks = listerStock();

// Inclure la vue
include __DIR__ . "/../views/stocks.php";
