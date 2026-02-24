<?php
require_once __DIR__ . "/../../config/db.php";

class Commande {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($id_client, $produit, $quantite, $date_commande) {
        $stmt = $this->pdo->prepare("INSERT INTO commande (id_client, produit, quantite, date_commande) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$id_client, $produit, $quantite, $date_commande]);
    }

    public function modifier($id_commande, $id_client, $produit, $quantite, $date_commande) {
        $stmt = $this->pdo->prepare("UPDATE commande
                           SET id_client = ?, produit = ?, quantite = ?, date_commande = ?
                           WHERE id_commande = ?");
        return $stmt->execute([$id_client, $produit, $quantite, $date_commande, $id_commande]);
    }

    public function supprimer($id_commande) {
        $stmt = $this->pdo->prepare("DELETE FROM commande WHERE id_commande = ?");
        return $stmt->execute([$id_commande]);
    }

    public function lister() {
        $sql = "SELECT c.id_commande, c.produit, c.quantite, c.date_commande,
                   cl.nom AS client_nom
            FROM commande c
            INNER JOIN client cl ON c.id_client = cl.id_client
            ORDER BY c.date_commande DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id_commande) {
        $sql = "SELECT c.id_commande, c.id_client, c.produit, c.quantite, c.date_commande,
                   cl.nom AS client_nom
            FROM commande c
            INNER JOIN client cl ON c.id_client = cl.id_client
            WHERE c.id_commande = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_commande]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
