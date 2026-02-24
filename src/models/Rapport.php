A<?php
require_once __DIR__ . "/../../config/db.php";

class Rapport {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function statistiquesGenerales() {
        $stats = [];

        $stats['totalClients'] = $this->pdo->query("SELECT COUNT(*) FROM CLIENT")->fetchColumn();
        $stats['totalProduits'] = $this->pdo->query("SELECT COUNT(*) FROM PRODUIT")->fetchColumn();
        $stats['totalCommandes'] = $this->pdo->query("SELECT COUNT(*) FROM COMMANDE")->fetchColumn();
        $stats['stockTotal'] = $this->pdo->query("SELECT COALESCE(SUM(quantite),0) FROM STOCK")->fetchColumn();
        $stats['totalUtilisateurs'] = $this->pdo->query("SELECT COUNT(*) FROM UTILISATEUR")->fetchColumn();

        return $stats;
    }

    public function stocksParProduit() {
        return $this->pdo->query("SELECT p.nom, s.quantite
                        FROM STOCK s
                        JOIN PRODUIT p ON s.id_produit = p.id_produit")->fetchAll();
    }

    public function productionsParMois() {
        return $this->pdo->query("SELECT DATE_FORMAT(date_production,'%Y-%m') AS mois,
                                    SUM(quantite_produite) AS total
                                FROM PRODUCTION
                                GROUP BY mois ORDER BY mois")->fetchAll();
    }

    public function commandesParClient() {
        return $this->pdo->query("SELECT c.nom, SUM(cmd.total) AS total
                            FROM COMMANDE cmd
                            JOIN CLIENT c ON cmd.id_client = c.id_client
                            GROUP BY c.nom")->fetchAll();
    }
}
