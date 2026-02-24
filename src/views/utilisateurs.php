<?php include __DIR__ . "/../../templates/header.php"; ?>

<main class="main-content">
<div class="container">
    <h1>👤 Gestion des Utilisateurs</h1>

    <!-- Formulaire -->
    <form method="post">
        <input type="hidden" name="id_utilisateur" value="<?= $utilisateurEdit['id_utilisateur'] ?? '' ?>">
        <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($utilisateurEdit['nom'] ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($utilisateurEdit['email'] ?? '') ?>" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" <?= $utilisateurEdit ? '' : 'required' ?>>

        <?php if ($utilisateurEdit): ?>
            <button type="submit" name="modifier">✏️ Modifier</button>
            <a href="utilisateur.php">❌ Annuler</a>
        <?php else: ?>
            <button type="submit" name="ajouter">➕ Ajouter</button>
        <?php endif; ?>
    </form>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($utilisateurs as $u):
            // Déterminer le dashboard selon le rôle
            $role = $u['role'] ?? 3;
            $dashboard = "dashboard_user.php";
            if ($role == 1) $dashboard = "dashboard.php";
            elseif ($role == 2) $dashboard = "dashboard_operateur.php";
        ?>
            <tr onclick="window.location.href='<?= $dashboard ?>?id=<?= $u['id_utilisateur'] ?>'" style="cursor:pointer;">
                <td><?= $u['id_utilisateur'] ?? '' ?></td>
                <td><?= htmlspecialchars($u['nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                <td>
                    <a href="?modifier=<?= $u['id_utilisateur'] ?>">✏️ Modifier</a> |
                    <a href="?supprimer=<?= $u['id_utilisateur'] ?>" class="delete-btn" onclick="return confirm('Supprimer cet utilisateur ?')">❌ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top:20px;">
        <a href="dashboard.php" class="btn-dashboard">⬅ Retour au tableau de bord</a>
    </div>
</div>
</main>

<?php include __DIR__ . "/../../templates/footer.php"; ?>

<style>
.main-content { padding: 30px 20px; font-family: "Segoe UI", Arial, sans-serif; background: #f0f5fa; min-height: 100vh; }
.container { max-width: 1100px; margin: 0 auto; padding: 20px; }
h1 { font-size: 1.8rem; color: #0e5d96; margin-bottom: 20px; border-left: 5px solid #0e5d96; padding-left: 15px; }

form { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e8e8e8; }
form input { padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; flex: 1; min-width: 150px; font-size: 1rem; transition: all 0.3s; }
form input:focus { border-color: #0e5d96; outline: none; box-shadow: 0 0 0 3px rgba(14,93,150,0.1); }
form button { padding: 12px 25px; background: linear-gradient(135deg, #0e5d96, #1e88e5); border: none; color: #fff; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
form button:hover { background: linear-gradient(135deg, #0a4a7a, #1976d2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14,93,150,0.3); }
form a { padding: 12px 25px; background: #6c757d; border: none; color: #fff; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; text-decoration: none; display: inline-block; }
form a:hover { background: #5a6268; transform: translateY(-2px); }

table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 25px; border: 1px solid #e8e8e8; }
table th, table td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; }
table th { background: #0e5d96; color: #fff; text-transform: uppercase; font-size: 0.85rem; font-weight: 600; }
table tr:hover { background: #f5faff; cursor: pointer; }
table td { color: #333; font-size: 0.95rem; }

a { color: #0e5d96; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }
a.delete-btn { color: #dc3545; }

.btn-dashboard {
    padding: 14px 28px;
    font-size: 1rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #0e5d96, #1e88e5);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-dashboard:hover {
    background: linear-gradient(135deg, #0a4a7a, #1976d2);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14,93,150,0.35);
}
</style>
