<?php
require_once __DIR__ . "/../../config/db.php";

class Production {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($id_produit, $quantite, $date_production) {
        $sql = "INSERT INTO Production (id_produit, quantite_produite, date_production) VALUES (:id_produit, :quantite_produite, :date_production)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_produit' => $id_produit,
            ':quantite_produite' => $quantite,
            ':date_production' => $date_production
        ]);
    }

    public function lister() {
        $sql = "SELECT pr.*, p.nom AS produit_nom
                FROM Production pr
                LEFT JOIN Produit p ON pr.id_produit = p.id_produit";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Production WHERE id_production=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function trouverParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Production WHERE id_production=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function modifier($id, $id_produit, $quantite, $date_production) {
        $sql = "UPDATE Production SET id_produit=:id_produit, quantite_produite=:quantite_produite, date_production=:date_production WHERE id_production=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':id_produit' => $id_produit,
            ':quantite_produite' => $quantite,
            ':date_production' => $date_production
        ]);
    }

    public function productionsParMois() {
        $stmt = $this->pdo->query("SELECT DATE_FORMAT(date_production,'%Y-%m') AS mois,
                                    SUM(quantite_produite) AS total
                                FROM production
                                GROUP BY mois ORDER BY mois");
        return $stmt->fetchAll();
    }
}
