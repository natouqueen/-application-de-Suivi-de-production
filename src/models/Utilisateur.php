<?php
require_once __DIR__ . "/../../config/db.php";

class Utilisateur {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouter($nom, $email, $mot_de_passe) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Utilisateur (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':mot_de_passe' => $hash
        ]);
    }

    public function lister() {
        return $this->pdo->query("SELECT id_utilisateur, nom, email FROM Utilisateur")->fetchAll();
    }

    public function supprimer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Utilisateur WHERE id_utilisateur=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function trouverParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function modifier($id, $nom, $email, $mot_de_passe = null) {
        if ($mot_de_passe) {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $sql = "UPDATE Utilisateur SET nom=:nom, email=:email, mot_de_passe=:mot_de_passe WHERE id_utilisateur=:id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nom' => $nom,
                ':email' => $email,
                ':mot_de_passe' => $hash
            ]);
        } else {
            $sql = "UPDATE Utilisateur SET nom=:nom, email=:email WHERE id_utilisateur=:id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nom' => $nom,
                ':email' => $email
            ]);
        }
    }

    public function verifierConnexion($email, $mot_de_passe) {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
}
