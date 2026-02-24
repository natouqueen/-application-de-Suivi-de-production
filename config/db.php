<?php
$host = "localhost";
$dbname = "suivi";
$username = "root";
$password = "";

define("BASE_URL", "/gestion_projet");


try {
    $pdo = new PDO("mysql:host=localhost;dbname=suivi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>