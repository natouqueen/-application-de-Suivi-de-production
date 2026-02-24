<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php"; // <-- ajout de la config
?>
<header class="header">
    <div class="header-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/public/dashboard.php">
            <img src = "<?= BASE_URL ?>/templates/logo.png" alt="🌐Azur S.A" class="logo-img">
        </a>
        <!-- Infos utilisateur -->
        <div class="user-info">
            <span class="wave"></span>
            <span><?= htmlspecialchars($_SESSION['user']['nom'] ?? '') ?></span>
            <a href="/gestion_projet/public/login.php" class="btn-logout"> Déconnexion</a>
        </div>
    </div>
</header>
<style>
/*  HEADER  */
.header {
    background: linear-gradient(90deg, rgba(14, 93, 150, 1), rgba(14, 93, 150, 1)); 
    padding: 15px 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Logo */
.logo-img {
    width: 120px;
    height: auto;
    max-height: 60px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    object-fit: contain;
    background: rgba(255,255,255,0.1);
    padding: 5px 10px;
}
.logo-img:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}

/* Infos utilisateur */
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff;
    font-weight: 500;
    font-size: 1rem;
}

/* Bouton déconnexion */
.btn-logout {
    padding: 8px 16px;
    background: #fff;
    color: #063f79ff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}
.btn-logout:hover {
    background: #023d79ff;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
}


</style>
