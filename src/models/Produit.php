<?php
require_once __DIR__ . "/../../config/db.php";

class Produit {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($nom, $prix, $id_fournisseur) {
        $sql = "INSERT INTO Produit (nom, prix, id_fournisseur) VALUES (:nom, :prix, :id_fournisseur)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':prix' => $prix,
            ':id_fournisseur' => $id_fournisseur
        ]);
    }

    public function lister() {
        $sql = "SELECT p.*, f.nom AS fournisseur_nom
                FROM Produit p
                LEFT JOIN Fournisseur f ON p.id_fournisseur = f.id_fournisseur";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Produit WHERE id_produit=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function trouverParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Produit WHERE id_produit=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function modifier($id, $nom, $prix, $id_fournisseur) {
        $sql = "UPDATE Produit SET nom=:nom, prix=:prix, id_fournisseur=:id_fournisseur WHERE id_produit=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prix' => $prix,
            ':id_fournisseur' => $id_fournisseur
        ]);
    }
}
