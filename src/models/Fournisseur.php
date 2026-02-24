<?php
require_once __DIR__ . "/../../config/db.php";

class Fournisseur {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($nom, $email, $telephone, $adresse) {
        $sql = "INSERT INTO Fournisseur (nom,email,telephone,adresse) VALUES (:nom,:email,:tel,:adr)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':tel' => $telephone,
            ':adr' => $adresse
        ]);
    }

    public function lister() {
        return $this->pdo->query("SELECT * FROM Fournisseur")->fetchAll();
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Fournisseur WHERE id_fournisseur=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function trouverParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Fournisseur WHERE id_fournisseur=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function modifier($id, $nom, $email, $telephone, $adresse) {
        $sql = "UPDATE Fournisseur SET nom=:nom, email=:email, telephone=:tel, adresse=:adr WHERE id_fournisseur=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':email' => $email,
            ':tel' => $telephone,
            ':adr' => $adresse
        ]);
    }
}
