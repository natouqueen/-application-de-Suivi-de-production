<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['mot_de_passe'] ?? '';

    // Vérifie que les champs ne sont pas vides
    if (empty($email) || empty($pass)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $stored = $user['mot_de_passe'];
            $ok = password_get_info($stored)['algo']
                  ? password_verify($pass, $stored)
                  : hash_equals($stored, $pass);

            if ($ok) {
                // Stockage des informations utilisateur dans la session
                $_SESSION['user'] = [
                    'id'    => $user['id_utilisateur'],
                    'nom'   => $user['nom'],
                    'email' => $user['email'],
                    'role'  => $user['id_role']
                ];

                // Redirection selon le rôle
                switch ($user['id_role']) {
                    case 1:
                        header("Location: dashboard.php");
                        break;
                    case 2:
                        header("Location: dashboard_operateur.php");
                        break;
                    case 3:
                        header("Location: dashboard_user.php");
                        break;
                    default:
                        header("Location: erreur.php");
                        break;
                }
                exit;
            }
        }

        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
body { height: 100vh; display: flex; justify-content: center; align-items: center;
       background: linear-gradient(135deg, #78c3e1 0%, #8bace6 100%); }
.container { display: flex; justify-content: center; align-items: center; width: 100%; }

.card { 
    background: #ffffff; padding: 45px 40px; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; 
    max-width: 480px; width: 90%; 
}
.card .logo-img { 
    width: 100px; height: 100px; margin-bottom: 15px; object-fit: cover;
}
.card h2 { 
    margin-bottom: 8px; font-size: 1.8rem; color: #333; 
    font-weight: 600;
}
.card .subtitle {
    color: #666; font-size: 0.9rem; margin-bottom: 25px;
}
p { margin-bottom: 15px; font-weight: 600; color: #e74c3c; }

.form-row div { margin-bottom: 20px; text-align: left; position: relative; }
label { font-weight: 600; display: block; margin-bottom: 8px; color: #444; font-size: 0.9rem; }

.input-icon-wrapper { position: relative; }
.input-icon-wrapper i {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: #888; font-size: 1rem;
}

input[type="email"], input[type="password"] {
    width: 100%; padding: 13px 14px 13px 42px; border: 2px solid #e0e0e0; 
    border-radius: 10px; font-size: 1rem; transition: all 0.3s;
}
input[type="email"]:focus, input[type="password"]:focus {
    border-color: #1e3c72; box-shadow: 0 0 0 4px rgba(30,60,114,0.12); outline: none;
}

.button-wrapper {
    display: flex; justify-content: center; margin-top: 15px;
}

button.btn-primary {
    background: linear-gradient(135deg, #6394ed 0%, #377df6 100%);
    color: #fff; border: none; padding: 14px 45px; border-radius: 10px;
    font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s;
}
button.btn-primary:hover {
    transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30,60,114,0.35);
}
button.btn-primary:active { transform: translateY(0); }

@media (max-width: 480px) { 
    .card { padding: 30px 25px; } 
    .card h2 { font-size: 1.6rem; } 
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="card">
        <img src="../templates/b.jfif" alt = "AZUR " class="logo-img" style="border-radius: 50%;">
        <h2>Connexion</h2>
        <p class="subtitle">Accédez à votre espace</p>
        <?php if($error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" class="form-row">
            <div class="input-icon-wrapper">
                <i class="fas fa-envelope"></i>
                <label> </label>
                <input type="email" name="email" placeholder="Entrez votre email" required>
            </div>
            <div class="input-icon-wrapper">
                <i class="fas fa-lock"></i>
                <label> </label>
                <input type="password" name="mot_de_passe" placeholder="Entrez votre mot de passe" required>
            </div>
            <div class="button-wrapper">
                <button class="btn-primary" type="submit">Se connecter</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
