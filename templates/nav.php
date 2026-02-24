<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="main-nav">
    <div class="nav-links">
        <a href="/gestion_projet/public/produit.php"> Produits </a>
        <a href="/gestion_projet/public/fournisseur.php"> Fournisseurs </a>
        <a href="/gestion_projet/public/stock.php"> Stock </a>
        <a href="/gestion_projet/public/production.php"> Production </a>
        <a href="/gestion_projet/public/client.php"> Clients </a>
        <a href="/gestion_projet/public/commande.php"> Commandes</a>
        <a href="/gestion_projet/public/rapport.php"> Rapports </a>
        <a href="/gestion_projet/public/utilisateur.php"> Utilisateurs </a>
    </div>
</nav>

<style>
.main-nav {
    background: #f0f4ff;
    border-radius: 12px;
    padding: 10px 20px;
    margin: 10px 0 20px 0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.nav-links {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}
.nav-links a {
    text-decoration: none;
    color: #007bff;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.nav-links a:hover {
    background: #007bff;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
@media (max-width: 768px) {
    .nav-links {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
