            <?php
            session_start();
            require_once __DIR__ . '/../config/db.php';

            // Vérification connexion
            if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
                header("Location: login.php");
                exit;
            }

            // Données de base
            $totalClients   = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
            $totalProduits  = $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();
            $totalCommandes = $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn();
            $stockTotal     = $pdo->query("SELECT COALESCE(SUM(quantite),0) FROM stock")->fetchColumn();
            $totalUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();

            // Données Admin - total fournisseurs
            $totalFournisseurs = $pdo->query("SELECT COUNT(*) FROM fournisseur")->fetchColumn();

            // Nouvelles données pour les statistiques avancées
            $commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn();
            $produitsEnRupture = $pdo->query("SELECT COUNT(*) FROM stock WHERE quantite < 10")->fetchColumn();

            // Commandes par produit
            $commandesParProduit = $pdo->query("SELECT produit, SUM(quantite) as total FROM commande WHERE produit IS NOT NULL GROUP BY produit")->fetchAll();

            // Ventes par mois
            $ventesParMois = $pdo->query("SELECT DATE_FORMAT(date_commande, '%Y-%m') as mois, SUM(total) as total FROM commande WHERE date_commande IS NOT NULL GROUP BY mois ORDER BY mois DESC LIMIT 6")->fetchAll();
            $ventesParMois = array_reverse($ventesParMois);

            // Dernières commandes
            $dernieresCommandes = $pdo->query("SELECT c.id_commande, c.date_commande, c.total, c.produit, cl.nom as client_nom 
                FROM commande c 
                LEFT JOIN client cl ON c.id_client = cl.id_client 
                ORDER BY c.date_commande DESC LIMIT 10")->fetchAll();

            // Produits en rupture de stock (stock < 10)
            $produitsRuptureStock = $pdo->query("SELECT p.nom, s.quantite 
                FROM stock s 
                JOIN produit p ON s.id_produit = p.id_produit 
                WHERE s.quantite < 10")->fetchAll();

            $stocks = $pdo->query("SELECT p.nom, s.quantite 
                                    FROM stock s 
                                    JOIN produit p ON s.id_produit = p.id_produit")->fetchAll();

            $productions = $pdo->query("SELECT DATE_FORMAT(date_production,'%Y-%m') AS mois, 
                                                SUM(quantite_produite) AS total 
                                            FROM production 
                                            WHERE date_production IS NOT NULL
                                            GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll();

            // Si pas de données production, créer des données factices pour démonstration
            if (empty($productions)) {
                $productions = [
                    ['mois' => date('Y-m'), 'total' => rand(50, 200)],
                    ['mois' => date('Y-m', strtotime('-1 month')), 'total' => rand(50, 200)],
                    ['mois' => date('Y-m', strtotime('-2 months')), 'total' => rand(50, 200)],
                    ['mois' => date('Y-m', strtotime('-3 months')), 'total' => rand(50, 200)],
                    ['mois' => date('Y-m', strtotime('-4 months')), 'total' => rand(50, 200)],
                    ['mois' => date('Y-m', strtotime('-5 months')), 'total' => rand(50, 200)]
                ];
            }

            // Commandes par client - corrigé
            $commandes = $pdo->query("SELECT c.nom, COUNT(cmd.id_commande) as total 
                                        FROM commande cmd 
                                        LEFT JOIN client c ON cmd.id_client = c.id_client 
                                        GROUP BY c.id_client, c.nom")->fetchAll();

            // Données supplémentaires pour les graphiques - corrigé
            $commandesParMois = $pdo->query("SELECT DATE_FORMAT(date_commande,'%Y-%m') AS mois, 
                                                        COUNT(*) AS total 
                                                    FROM commande 
                                                    WHERE date_commande IS NOT NULL
                                                    GROUP BY mois ORDER BY mois DESC LIMIT 6")->fetchAll();
            $commandesParMois = array_reverse($commandesParMois);

            // Top Produits - corrigé (depuis table commande)
            $topProduits = $pdo->query("SELECT produit as nom, SUM(quantite) as total 
                                        FROM commande 
                                        WHERE produit IS NOT NULL AND quantite IS NOT NULL
                                        GROUP BY produit 
                                        ORDER BY total DESC 
                                        LIMIT 5")->fetchAll();

            include __DIR__.'/../templates/header.php';
            include __DIR__.'/../templates/nav.php';
            ?>

            <main class="main-content">
                <div class="container">
                    <div class="dashboard-header">
                        <div>
                            <h1>📊 Tableau de Bord Admin</h1>
                            <p class="welcome-text">Bienvenue <strong><?= htmlspecialchars($_SESSION['user']['nom']) ?></strong></p>
                        </div>
                        <div class="date-display">
                            <span class="date-badge">📅 <?= date('d/m/Y') ?></span>
                        </div>
                    </div>

                    <!-- Cartes de statistiques principales -->
                    <div class="stats-row">
                        <div class="stat-card stat-primary">
                            <div class="stat-icon">👥</div>
                            <div class="stat-info">
                                <span class="stat-label">Clients</span>
                                <span class="stat-value"><?= $totalClients ?></span>
                            </div>
                            <div class="stat-trend up">↑ +12%</div>
                        </div>
                        <div class="stat-card stat-success">
                            <div class="stat-icon">📦</div>
                            <div class="stat-info">
                                <span class="stat-label">Produits</span>
                                <span class="stat-value"><?= $totalProduits ?></span>
                            </div>
                            <div class="stat-trend up">↑ +5%</div>
                        </div>
                        <div class="stat-card stat-warning">
                            <div class="stat-icon">🛒</div>
                            <div class="stat-info">
                                <span class="stat-label">Commandes</span>
                                <span class="stat-value"><?= $totalCommandes ?></span>
                            </div>
                            <div class="stat-badge"><?= $commandesEnAttente ?> en attente</div>
                        </div>
                        <div class="stat-card stat-danger">
                            <div class="stat-icon">⚠️</div>
                            <div class="stat-info">
                                <span class="stat-label">Rupture</span>
                                <span class="stat-value"><?= $produitsEnRupture ?></span>
                            </div>
                            <div class="stat-badge warning">Attention</div>
                        </div>
                    </div>

                    <!-- Deuxième rangée de statistiques -->
                    <div class="stats-row">
                        <div class="stat-card stat-info">
                            <div class="stat-icon">📊</div>
                            <div class="stat-info">
                                <span class="stat-label">Stock Total</span>
                                <span class="stat-value"><?= number_format($stockTotal) ?></span>
                            </div>
                            <div class="stat-trend">Unités</div>
                        </div>
                        <div class="stat-card stat-dark">
                            <div class="stat-icon">🏭</div>
                            <div class="stat-info">
                                <span class="stat-label">Production</span>
                                <span class="stat-value"><?= $pdo->query("SELECT COALESCE(SUM(quantite_produite),0) FROM production")->fetchColumn() ?: 0 ?></span>
                            </div>
                            <div class="stat-trend">Total</div>
                        </div>
                        <div class="stat-card stat-secondary">
                            <div class="stat-icon">👤</div>
                            <div class="stat-info">
                                <span class="stat-label">Utilisateurs</span>
                                <span class="stat-value"><?= $totalUtilisateurs ?></span>
                            </div>
                            <div class="stat-trend">Actifs</div>
                        </div>
                        <div class="stat-card stat-purple">
                            <div class="stat-icon">🚚</div>
                            <div class="stat-info">
                                <span class="stat-label">Fournisseurs</span>
                                <span class="stat-value"><?= $totalFournisseurs ?></span>
                            </div>
                            <div class="stat-trend">Total</div>
                        </div>
                    </div>

                <!-- Section des graphiques -->
                    <div class="charts-section">
                        <h2 class="section-title">📈 Analyses et Statistiques</h2>
                        
                        <!-- Sélecteur de graphique -->
                        <div class="chart-selector">
                            <label for="chartSelect"> Choisir une carte : </label>
                            <select id="chartSelect" onchange="afficherGraphique(this.value)">
                                <option value="">-- Sélectionner un graphique --</option>
                                <option value="stock">📦 Produits en Stocks</option>
                                <option value="production">🏭 Production par mois</option>
                                <option value="commandes-client">🛒 Commandes par client</option>
                                <option value="commandes-mois">📅 Commandes par mois</option>
                                <option value="top-produits"> Produits les plus vendus</option>
                            </select>
                        </div>

                        <!-- Conteneurs de graphiques (cachés par défaut) -->
                        <div class="charts-grid">
                            <!-- Graphique Stocks -->
                            <div class="chart-card chart-item" id="chart-stock" style="display: none;">
                                <div class="chart-header"><h3>📦 Produits en Stocks</h3><span class="chart-badge">Temps réel</span></div>
                                <div class="chart-container"><canvas id="stockChart"></canvas></div>
                            </div>

                            <!-- Graphique Production -->
                            <div class="chart-card chart-item" id="chart-production" style="display: none;">
                                <div class="chart-header"><h3>🏭 Production par mois</h3><span class="chart-badge">12 mois</span></div>
                                <div class="chart-container"><canvas id="prodChart"></canvas></div>
                            </div>

                            <!-- Graphique Commandes par client -->
                            <div class="chart-card chart-item" id="chart-commandes-client" style="display: none;">
                                <div class="chart-header"><h3>🛒 Commandes par client</h3><span class="chart-badge">Par client</span></div>
                                <div class="chart-container"><canvas id="cmdChart"></canvas></div>
                            </div>

                            <!-- Graphique Commandes par mois -->
                            <div class="chart-card chart-item" id="chart-commandes-mois" style="display: none;">
                                <div class="chart-header"><h3>📅 Commandes par mois</h3><span class="chart-badge">6 mois</span></div>
                                <div class="chart-container"><canvas id="cmdMoisChart"></canvas></div>
                            </div>

                            <!-- Graphique Top Produits -->
                            <div class="chart-card chart-item" id="chart-top-produits" style="display: none;">
                                <div class="chart-header"><h3> Produits les plus vendus</h3><span class="chart-badge">Meilleures</span></div>
                                <div class="chart-container"><canvas id="topProduitsChart"></canvas></div>
                            </div>
                        </div>

                        <!-- Message d'invite -->
                        <div id="chart-placeholder" class="chart-placeholder">
                            <p>👆 Sélectionnez une analyse ci-dessus pour afficher le graphique correspondant</p>
                        </div>
                    </div>
                </div>
            </main>

            <?php include __DIR__.'/../templates/footer.php'; ?>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            // Données PHP vers JavaScript
            const stocks = <?= json_encode($stocks, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
            const prods = <?= json_encode($productions, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
            const cmds = <?= json_encode($commandes, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
            const cmdMois = <?= json_encode($commandesParMois, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
            const topProds = <?= json_encode($topProduits, JSON_HEX_TAG | JSON_HEX_APOS) ?>;

            // Couleurs modernes
            const colors = {
                primary: '#0e5d96',
                primaryLight: 'rgba(14, 93, 150, 0.2)',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#dc3545',
                purple: '#6f42c1',
                info: '#17a2b8',
                gradient: ['#0e5d96', '#1e88e5', '#42a5f5', '#64b5f6', '#90caf9', '#bbdefb']
            };

            // Graphique Stocks (Barres horizontales)
            if (stocks.length > 0) {
                new Chart(document.getElementById('stockChart'), {
                    type: 'bar',
                    data: {
                        labels: stocks.map(s => s.nom),
                        datasets: [{
                            label: 'Quantité en stock',
                            data: stocks.map(s => Number(s.quantite)),
                            backgroundColor: colors.primary,
                            borderColor: colors.primary,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }

            // Graphique Production (Ligne avec remplissage)
            if (prods.length > 0) {
                new Chart(document.getElementById('prodChart'), {
                    type: 'line',
                    data: {
                        labels: prods.map(p => p.mois),
                        datasets: [{
                            label: 'Production',
                            data: prods.map(p => Number(p.total)),
                            fill: true,
                            backgroundColor: colors.primaryLight,
                            borderColor: colors.primary,
                            borderWidth: 3,
                            tension: 0.4,
                            pointBackgroundColor: colors.primary,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Graphique Commandes par client (Camembert)
            if (cmds.length > 0) {
                new Chart(document.getElementById('cmdChart'), {
                    type: 'doughnut',
                    data: {
                        labels: cmds.map(c => c.nom || 'Sans nom'),
                        datasets: [{
                            data: cmds.map(c => Number(c.total) || 0),
                            backgroundColor: colors.gradient,
                            borderColor: '#fff',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }
                        }
                    }
                });
            }

            // Graphique Commandes par mois (Barres)
            if (cmdMois.length > 0) {
                new Chart(document.getElementById('cmdMoisChart'), {
                    type: 'bar',
                    data: {
                        labels: cmdMois.map(c => c.mois),
                        datasets: [{
                            label: 'Nombre de commandes',
                            data: cmdMois.map(c => Number(c.total)),
                            backgroundColor: colors.primary,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Graphique Top Produits (Barres)
            if (topProds.length > 0) {
                new Chart(document.getElementById('topProduitsChart'), {
                    type: 'bar',
                    data: {
                        labels: topProds.map(p => p.nom || 'Produit'),
                        datasets: [{
                            label: 'Quantité commandée',
                            data: topProds.map(p => Number(p.total)),
                            backgroundColor: [
                                'rgba(14, 93, 150, 0.9)',
                                'rgba(40, 167, 69, 0.9)',
                                'rgba(255, 193, 7, 0.9)',
                                'rgba(111, 66, 193, 0.9)',
                                'rgba(23, 162, 184, 0.9)'
                            ],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Message si pas de données
            document.addEventListener('DOMContentLoaded', function() {
                const chartContainers = document.querySelectorAll('.chart-container');
                chartContainers.forEach(container => {
                    if (container.innerHTML.trim() === '') {
                        container.innerHTML = '<p style="text-align:center;color:#666;padding:40px;">Aucune donnée disponible</p>';
                    }
                });
            });

            // Fonction pour afficher/masquer les graphiques
            function afficherGraphique(chartId) {
                // Masquer tous les graphiques
                const charts = document.querySelectorAll('.chart-item');
                charts.forEach(chart => {
                    chart.style.display = 'none';
                });

                // Masquer le message d'invite
                const placeholder = document.getElementById('chart-placeholder');
                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                // Afficher le graphique sélectionné
                if (chartId) {
                    const selectedChart = document.getElementById('chart-' + chartId);
                    if (selectedChart) {
                        selectedChart.style.display = 'block';
                        selectedChart.style.animation = 'fadeIn 0.5s ease';
                    }
                } else {
                    // Si aucune sélection, afficher le message d'invite
                    if (placeholder) {
                        placeholder.style.display = 'block';
                    }
                }
            }
            </script>

            <style>
            .main-content {
                padding: 20px 15px;
                font-family: "Segoe UI", Arial, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
                min-height: 100vh;
                overflow-x: hidden;
            }

            .container { max-width: 100%; margin: 0 auto; padding: 0 10px; }

            .dashboard-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .dashboard-header h1 { color: #0e5d96; font-size: 1.5rem; margin-bottom: 3px; }

            .date-badge {
                background: white; padding: 8px 15px; border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08); font-weight: 600; color: #0e5d96; font-size: 0.85rem;
            }

            .welcome-text { color: #666; font-size: 0.9rem; }

            .stats-row {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                margin-bottom: 15px;
            }

            .stat-card {
                background: white; border-radius: 12px; padding: 20px 15px;
                display: flex; align-items: center; gap: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                transition: all 0.3s ease; position: relative; overflow: hidden;
                min-height: 90px;
            }

            .stat-card::before {
                content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 100%;
            }

            .stat-card.stat-primary::before { background: #0e5d96; }
            .stat-card.stat-success::before { background: #28a745; }
            .stat-card.stat-warning::before { background: #ffc107; }
            .stat-card.stat-danger::before { background: #dc3545; }
            .stat-card.stat-info::before { background: #17a2b8; }
            .stat-card.stat-dark::before { background: #343a40; }
            .stat-card.stat-secondary::before { background: #6c757d; }
            .stat-card.stat-purple::before { background: #6f42c1; }

            .stat-card:hover { 
                transform: translateY(-3px) scale(1.02); 
                box-shadow: 0 8px 25px rgba(14, 93, 150, 0.25); 
            }

            .stat-icon { font-size: 2rem; min-width: 40px; text-align: center; }

            .stat-info { flex: 1; display: flex; flex-direction: column; }

            .stat-label { font-size: 0.85rem; color: #666; font-weight: 500; }

            .stat-value { font-size: 1.8rem; font-weight: 700; color: #333; }

            .stat-trend {
                font-size: 0.75rem; color: #28a745; font-weight: 600;
                padding: 4px 8px; border-radius: 15px; background: rgba(40, 167, 69, 0.1);
            }

            .stat-badge {
                font-size: 0.7rem; color: #ffc107; font-weight: 600;
                padding: 4px 8px; border-radius: 15px; background: rgba(255, 193, 7, 0.15);
            }

            .stat-badge.warning { color: #dc3545; background: rgba(220, 53, 69, 0.15); }

            .charts-section { margin-top: 20px; }

            .section-title {
                color: #0e5d96; font-size: 1.1rem; margin-bottom: 15px;
                padding-bottom: 8px; border-bottom: 2px solid #0e5d96; display: inline-block;
            }

            .charts-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .chart-card {
                background: white; border-radius: 12px; padding: 20px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.06);
                transition: all 0.3s ease;
            }

            .chart-card:hover { 
                box-shadow: 0 8px 30px rgba(14, 93, 150, 0.2); 
                transform: translateY(-2px);
            }

            .chart-header {
                display: flex; justify-content: space-between; align-items: center;
                margin-bottom: 15px; flex-wrap: wrap; gap: 5px;
            }

            .chart-header h3 { color: #0e5d96; font-size: 1rem; font-weight: 600; }

            .chart-badge {
                background: linear-gradient(135deg, #0e5d96, #1e88e5);
                color: white; padding: 5px 12px; border-radius: 15px;
                font-size: 0.7rem; font-weight: 600;
            }

            .chart-container { height: 250px; position: relative; }

            /* Styles pour le sélecteur de graphiques */
            .chart-selector {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
                padding: 15px 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.06);
            }

            .chart-selector label {
                color: #0e5d96;
                font-weight: 600;
                font-size: 0.95rem;
                white-space: nowrap;
            }

            .chart-selector select {
                flex: 1;
                padding: 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 0.9rem;
                color: #333;
                background: #f8f9fa;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .chart-selector select:focus {
                outline: none;
                border-color: #0e5d96;
                box-shadow: 0 0 0 3px rgba(14, 93, 150, 0.1);
            }

            .chart-selector select:hover {
                border-color: #0e5d96;
            }

            /* Message d'invite */
            .chart-placeholder {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.06);
                margin-top: 15px;
            }

            .chart-placeholder p {
                color: #666;
                font-size: 1rem;
                margin: 0;
            }

            /* Animation d'apparition */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Responsive pour le sélecteur */
            @media (max-width: 768px) {
                .chart-selector {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .chart-selector label {
                    margin-bottom: 5px;
                }
            }

            @media (max-width: 992px) {
                .charts-grid { grid-template-columns: 1fr; }
            }

            @media (max-width: 768px) {
                .dashboard-header { flex-direction: column; align-items: flex-start; }
                .stats-row { grid-template-columns: repeat(2, 1fr); }
                .dashboard-cards { grid-template-columns: repeat(2, 1fr); }
                .stat-card { padding: 8px; }
                .stat-value { font-size: 1.1rem; }
                .chart-container { height: 180px; }
            }

            @media (max-width: 480px) {
                .stats-row { grid-template-columns: repeat(2, 1fr); gap: 6px; }
                .dashboard-cards { grid-template-columns: repeat(2, 1fr); gap: 6px; }
                .nav-card { padding: 8px 5px; }
                .chart-container { height: 160px; }
                .main-content { padding: 15px 10px; }
            }
            </style>
