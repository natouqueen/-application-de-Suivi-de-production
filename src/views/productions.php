<?php include __DIR__ . "/../../templates/header.php"; ?>

<main class="main-content">
<div class="container">
    <h1>🏭 Gestion de la Production</h1>

    <!-- Formulaire pour ajouter/modifier une production -->
    <form method="post">
        <input type="hidden" name="id_production" value="<?= $productionEdit['id_production'] ?? '' ?>">
        
        <select name="id_produit" required>
            <option value="">Sélectionner un produit</option>
            <?php foreach($produits as $p): ?>
                <option value="<?= $p['id_produit'] ?>" <?= isset($productionEdit['id_produit']) && $productionEdit['id_produit']==$p['id_produit'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="number" name="quantite" placeholder="Quantité produite" value="<?= $productionEdit['quantite_produite'] ?? '' ?>" required>
        <input type="date" name="date_production" value="<?= $productionEdit['date_production'] ?? '' ?>" required>

        <?php if ($productionEdit): ?>
            <button type="submit" name="modifier">✏️ Modifier</button>
            <a href="production.php">❌ Annuler</a>
        <?php else: ?>
            <button type="submit" name="ajouter">➕ Ajouter</button>
        <?php endif; ?>
    </form>

    <!-- Tableau des productions -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($productions as $prod): ?>
            <tr>
                <td><?= $prod['id_production'] ?></td>
                <td><?= htmlspecialchars($prod['produit_nom'] ?? $prod['nom'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($prod['quantite_produite']) ?></td>
                <td><?= htmlspecialchars($prod['date_production']) ?></td>
                <td>
                    <a href="?modifier=<?= $prod['id_production'] ?>">✏️ Modifier</a> |
                    <a href="?supprimer=<?= $prod['id_production'] ?>" onclick="return confirm('Supprimer cette production ?')">❌ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="<?= $redirect ?>" class="btn-dashboard">⬅ Retour au tableau de bord</a>
</div>
</main>

<?php include __DIR__ . "/../../templates/footer.php"; ?>

<style>
.main-content { padding: 30px 20px; font-family: "Segoe UI", Arial, sans-serif; background: #f0f5fa; min-height: 100vh; }
.container { max-width: 1100px; margin: 0 auto; padding: 20px; }
h1 { font-size: 1.8rem; color: #0e5d96; margin-bottom: 20px; border-left: 5px solid #0e5d96; padding-left: 15px; }

form { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e8e8e8; }
form input, form select { padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; flex: 1; min-width: 150px; font-size: 1rem; transition: all 0.3s; }
form input:focus, form select:focus { border-color: #0e5d96; outline: none; box-shadow: 0 0 0 3px rgba(14,93,150,0.1); }
form button { padding: 12px 25px; background: linear-gradient(135deg, #0e5d96, #1e88e5); border: none; color: #fff; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
form button:hover { background: linear-gradient(135deg, #0a4a7a, #1976d2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14,93,150,0.3); }
form a { padding: 12px 25px; background: #6c757d; border: none; color: #fff; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; text-decoration: none; display: inline-block; }
form a:hover { background: #5a6268; transform: translateY(-2px); }

table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 25px; border: 1px solid #e8e8e8; }
table th, table td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; }
table th { background: #0e5d96; color: #fff; text-transform: uppercase; font-size: 0.85rem; font-weight: 600; }
table tr:hover { background: #f5faff; }
table td { color: #333; font-size: 0.95rem; }

a { color: #0e5d96; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }

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
