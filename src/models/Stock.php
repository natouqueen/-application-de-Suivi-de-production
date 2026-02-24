<?php
require_once __DIR__ . "/../../config/db.php";

class Stock {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function lister() {
        $sql = "SELECT p.id_produit, p.nom, p.prix, COALESCE(s.quantite,0) AS quantite
                FROM Produit p
                LEFT JOIN Stock s ON p.id_produit = s.id_produit";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function majQuantite($id_produit, $quantite) {
        // Vérifier si le produit existe déjà dans le stock
        $stmt = $this->pdo->prepare("SELECT * FROM Stock WHERE id_produit=:id");
        $stmt->execute([':id'=>$id_produit]);
        $existe = $stmt->fetch();

        if ($existe) {
            // Update
            $sql = "UPDATE Stock SET quantite=:quantite WHERE id_produit=:id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':quantite'=>$quantite, ':id'=>$id_produit]);
        } else {
            // Insert
            $sql = "INSERT INTO Stock (id_produit, quantite) VALUES (:id, :quantite)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id'=>$id_produit, ':quantite'=>$quantite]);
        }
    }

    public function totalQuantite() {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(quantite),0) FROM stock");
        return $stmt->fetchColumn();
    }
}
