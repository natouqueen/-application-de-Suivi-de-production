<?php include __DIR__ . "/../../templates/header.php"; ?>

<main class="main-content">
<div class="container">
    <h1>📊 Gestion des Rapports</h1>

    <!-- Formulaire de génération -->
    <form method="post" id="rapportForm">
        <select name="type" required>
            <option value="">Sélectionner un type de rapport</option>
            <option value="stock">Stock</option>
            <option value="commandes">Commandes</option>
            <option value="production">Production</option>
            <option value="fournisseurs">Fournisseurs</option>
            <option value="clients">Clients</option>
            <option value="utilisateurs">Utilisateurs</option>
            <option value="produits">Produits</option>
        </select>
        <button type="submit" name="generer">📄 Générer Rapport</button>
    </form>
    
    <!-- Message de feedback -->
    <?php if (isset($success)): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Statistiques générales -->
    <div class="stats">
        <h2>📈 Statistiques Générales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Clients</h3>
                <p><?= $stats['totalClients'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Produits</h3>
                <p><?= $stats['totalProduits'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Commandes</h3>
                <p><?= $stats['totalCommandes'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Stock Total</h3>
                <p><?= $stats['stockTotal'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Utilisateurs</h3>
                <p><?= $stats['totalUtilisateurs'] ?></p>
            </div>
        </div>
    </div>

    <!-- Liste des rapports -->
    <div class="rapports-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rapports)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">Aucun rapport généré. Utilisez le formulaire ci-dessus.</td>
                </tr>
            <?php else: ?>
                <?php foreach($rapports as $r): ?>
                <tr>
                    <td><?= $r['id_rapport'] ?></td>
                    <td><?= htmlspecialchars($r['titre'] ?? '') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($r['date_creation'] ?? 'now')) ?></td>
                    <td class="actions">
                        <a href="?voir=<?= $r['id_rapport'] ?>" target="_blank" class="btn-action btn-voir">📄 Voir</a>
                        <a href="?telecharger=<?= $r['id_rapport'] ?>" class="btn-action btn-telecharger">⬇️ Télécharger</a>
                        <a href="?regen=<?= $r['id_rapport'] ?>" class="btn-action btn-regen" onclick="return confirm('Régénérer ce rapport ?')">🔄</a>
                        <a href="?supprimer=<?= $r['id_rapport'] ?>" class="btn-action btn-supprimer" onclick="return confirm('Supprimer ce rapport ?')">❌</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="<?= $redirect ?>" class="btn-dashboard">⬅ Retour au tableau de bord</a>
</div>
</main>

<?php include __DIR__ . "/../../templates/footer.php"; ?>

<style>
.main-content { 
    padding: 30px 20px; 
    font-family: "Segoe UI", Arial, sans-serif; 
    background: #f0f5fa; 
    min-height: 100vh; 
}

.container { max-width: 1200px; margin: 0 auto; padding: 20px; }
h1 { font-size: 1.8rem; color: #0e5d96; margin-bottom: 20px; border-left: 5px solid #0e5d96; padding-left: 15px; }

form { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e8e8e8; }
form select { padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; flex: 1; min-width: 200px; font-size: 1rem; transition: all 0.3s; }
form select:focus { border-color: #0e5d96; outline: none; box-shadow: 0 0 0 3px rgba(14,93,150,0.1); }
form button { padding: 12px 25px; background: linear-gradient(135deg, #0e5d96, #1e88e5); border: none; color: #fff; border-radius: 8px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
form button:hover { background: linear-gradient(135deg, #0a4a7a, #1976d2); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14,93,150,0.3); }

.stats { margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e8e8e8; }
.stats h2 { margin-bottom: 15px; color: #0e5d96; font-size: 1.2rem; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; }
.stat-card { text-align: center; padding: 12px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef; }
.stat-card h3 { margin: 0 0 8px 0; font-size: 0.8rem; color: #666; }
.stat-card p { margin: 0; font-size: 1.3rem; font-weight: bold; color: #0e5d96; }

.rapports-table { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 25px; }
table { width: 100%; border-collapse: collapse; }
table th, table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; }
table th { background: #0e5d96; color: #fff; text-transform: uppercase; font-size: 0.8rem; font-weight: 600; }
table tr:hover { background: #f5faff; }
table td { color: #333; font-size: 0.9rem; }

.actions { display: flex; gap: 5px; flex-wrap: wrap; }
.btn-action { padding: 6px 10px; border-radius: 5px; font-size: 0.75rem; text-decoration: none; font-weight: 600; transition: all 0.3s; }
.btn-voir { background: #28a745; color: #fff; }
.btn-voir:hover { background: #218838; }
.btn-telecharger { background: #17a2b8; color: #fff; }
.btn-telecharger:hover { background: #138496; }
.btn-regen { background: #ffc107; color: #333; }
.btn-regen:hover { background: #e0a800; }
.btn-supprimer { background: #dc3545; color: #fff; }
.btn-supprimer:hover { background: #c82333; }

a { color: #0e5d96; text-decoration: none; }
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

.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(3, 1fr); }
    .actions { flex-direction: column; }
    .btn-action { text-align: center; }
}
</style>
