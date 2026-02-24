<?php include __DIR__ . "/../../templates/header.php"; ?>

<main class="main-content">
<div class="container">
    <div class="page-header">
        <h1>📦 Gestion des Commandes</h1>
        <a href="<?= $redirect ?>" class="btn-back">⬅ Retour</a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card stat-primary">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <span class="stat-label">Total Commandes</span>
                <span class="stat-value"><?= count($commandes) ?></span>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <span class="stat-label">Clients</span>
                <span class="stat-value"><?= count($clients) ?></span>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <span class="stat-label">En attente</span>
                <span class="stat-value"><?= count($commandes) ?></span>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="post" class="commande-form">
        <input type="hidden" name="id_commande" value="<?= $commandeEdit['id_commande'] ?? '' ?>">

        <div class="form-group">
            <label>Client</label>
            <select name="id_client" required>
                <option value="">-- Sélectionner un client --</option>
                <?php foreach($clients as $cl): ?>
                    <option value="<?= $cl['id_client'] ?>"
                        <?= isset($commandeEdit['id_client']) && $commandeEdit['id_client'] == $cl['id_client'] ? 'selected' : '' ?> >
                        <?= htmlspecialchars($cl['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Produit</label>
            <input type="text" name="produit" placeholder="Nom du produit" value="<?= $commandeEdit['produit'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Quantité</label>
            <input type="number" name="quantite" placeholder="Quantité" value="<?= $commandeEdit['quantite'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date_commande" value="<?= $commandeEdit['date_commande'] ?? date('Y-m-d') ?>" required>
        </div>

        <div class="form-actions">
            <?php if ($commandeEdit): ?>
                <button type="submit" name="modifier" class="btn-modifier">✏️ Modifier</button>
                <a href="commande.php" class="btn-annuler">❌ Annuler</a>
            <?php else: ?>
                <button type="submit" name="ajouter" class="btn-ajouter">➕ Ajouter Commande</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tableau -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($commandes as $c): ?>
                <tr>
                    <td><span class="id-badge">#<?= $c['id_commande'] ?></span></td>
                    <td><strong><?= htmlspecialchars($c['client_nom'] ?? '') ?></strong></td>
                    <td><?= htmlspecialchars($c['produit'] ?? '') ?></td>
                    <td><span class="qty-badge"><?= htmlspecialchars($c['quantite'] ?? '') ?></span></td>
                    <td><?= htmlspecialchars($c['date_commande'] ?? '') ?></td>
                    <td class="actions-cell">
                        <div class="action-buttons">
                            <a href="?modifier=<?= $c['id_commande'] ?>" class="btn-edit" title="Modifier">✏️</a>
                            <a href="?supprimer=<?= $c['id_commande'] ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette commande ?')">❌</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include __DIR__ . "/../../templates/footer.php"; ?>


<style>
.main-content {
    padding: 20px 15px;
    font-family: "Segoe UI", Arial, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    min-height: 100vh;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 10px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

h1 {
    font-size: 1.5rem;
    color: #0e5d96;
    margin: 0;
}

.btn-back {
    padding: 10px 20px;
    background: linear-gradient(135deg, #0e5d96, #1e88e5);
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: linear-gradient(135deg, #0a4a7a, #1976d2);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(14,93,150,0.3);
}

/* Stats Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    cursor: pointer;
}

.stat-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 5px 20px rgba(14, 93, 150, 0.2);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    border-radius: 10px 0 0 10px;
}

.stat-card.stat-primary::before { background: #0e5d96; }
.stat-card.stat-success::before { background: #28a745; }
.stat-card.stat-warning::before { background: #ffc107; }

.stat-icon { font-size: 1.8rem; }

.stat-info { display: flex; flex-direction: column; }

.stat-label { font-size: 0.7rem; color: #666; font-weight: 500; }

.stat-value { font-size: 1.4rem; font-weight: 700; color: #333; }

/* Form */
.commande-form {
    display: grid;
    grid-template-columns: repeat(4, 1fr) auto;
    gap: 12px;
    margin-bottom: 20px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    align-items: end;
}

.form-group { display: flex; flex-direction: column; gap: 5px; }

.form-group label {
    font-size: 0.75rem;
    color: #666;
    font-weight: 600;
}

.commande-form input,
.commande-form select {
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.commande-form input:focus,
.commande-form select:focus {
    border-color: #0e5d96;
    outline: none;
    box-shadow: 0 0 0 3px rgba(14,93,150,0.1);
}

.form-actions {
    display: flex;
    gap: 8px;
}

.btn-ajouter, .btn-modifier {
    padding: 10px 20px;
    background: linear-gradient(135deg, #28a745, #34ce57);
    border: none;
    color: #fff;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    white-space: nowrap;
}

.btn-ajouter:hover, .btn-modifier:hover {
    background: linear-gradient(135deg, #218838, #2eb846);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-annuler {
    padding: 10px 20px;
    background: #6c757d;
    border: none;
    color: #fff;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-annuler:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Table */
.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 14px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

table th {
    background: #0e5d96;
    color: #fff;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 600;
}

table tr:hover {
    background: #f5faff;
}

table td {
    color: #333;
    font-size: 0.9rem;
}

.id-badge {
    background: #e3f2fd;
    color: #0e5d96;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.8rem;
}

.qty-badge {
    background: #fff3e0;
    color: #e65100;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-delete {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.btn-edit {
    background: #e3f2fd;
    color: #0e5d96;
}

.btn-edit:hover {
    background: #0e5d96;
    color: #fff;
    transform: scale(1.1);
}

.btn-delete {
    background: #ffebee;
    color: #c62828;
}

.btn-delete:hover {
    background: #c62828;
    color: #fff;
    transform: scale(1.1);
}

.actions-cell {
    white-space: nowrap;
    width: 100px;
}

/* Responsive */
@media (max-width: 768px) {
    .commande-form {
        grid-template-columns: 1fr 1fr;
    }
    
    .form-actions {
        grid-column: span 2;
        justify-content: center;
    }
    
    .stats-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .commande-form {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        grid-column: span 1;
    }
    
    table th, table td {
        padding: 8px;
        font-size: 0.8rem;
    }
}
</style>
