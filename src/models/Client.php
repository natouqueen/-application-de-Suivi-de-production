<?php
require_once __DIR__ . "/../../config/db.php";

class Client {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($nom, $adresse, $telephone) {
        $stmt = $this->pdo->prepare("INSERT INTO client (nom, adresse, telephone) VALUES (?, ?, ?)");
        return $stmt->execute([$nom, $adresse, $telephone]);
    }

    public function modifier($id, $nom, $adresse, $telephone) {
        $stmt = $this->pdo->prepare("UPDATE client SET nom = ?, adresse = ?, telephone = ? WHERE id_client = ?");
        return $stmt->execute([$nom, $adresse, $telephone, $id]);
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM client WHERE id_client = ?");
        return $stmt->execute([$id]);
    }

    public function lister() {
        $stmt = $this->pdo->query("SELECT * FROM client ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM client WHERE id_client = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
