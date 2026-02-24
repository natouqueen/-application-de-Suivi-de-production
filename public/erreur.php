<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nom = !empty($_SESSION['user']['nom']) ? htmlspecialchars($_SESSION['user']['nom']) : "Invité";
$role = $_SESSION['user']['role'] ?? 0;

$redirect = "login.php";
if ($role == 1) {
    $redirect = "dashboard.php";
} elseif ($role == 2) {
    $redirect = "dashboard_operateur.php";
} elseif ($role == 3) {
    $redirect = "dashboard_user.php";
}

include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/nav.php';
?>

<main class="main-content">
    <div class="container error-page">
        <h1>🚫 Accès refusé</h1>
        <p>
            Désolé <strong><?= $nom ?></strong>,<br>
            vous n’avez pas les privilèges nécessaires pour accéder à cette page.
        </p>
        <a href="<?= $redirect ?>" class="btn">⬅ Retour au tableau de bord</a>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<style>
.error-page {
    text-align: center;
    padding: 60px 20px;
}
.error-page h1 {
    font-size: 2.2rem;
    color: #dc3545;
    margin-bottom: 20px;
    text-shadow: 1px 1px 2px #fff;
}
.error-page p {
    margin-bottom: 30px;
    color: #444;
    font-size: 1.2rem;
    line-height: 1.5;
}
.btn {
    padding: 12px 24px;
    background: linear-gradient(90deg, #007bff, #00c6ff);
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}
.btn:hover {
    background: linear-gradient(90deg, #0056b3, #0094cc);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>
</body>
</html>
