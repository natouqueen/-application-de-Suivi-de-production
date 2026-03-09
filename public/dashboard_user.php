        <?php
        session_start();
        require_once __DIR__.'/../config/db.php';

        // Vérification de la connexion et du rôle
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] != 3) {
            header("Location: login.php");
            exit;
        }

        // Données de base
        $totalClients   = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
        $totalProduits  = $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();
        $totalCommandes = $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn();
        $stockTotal     = $pdo->query("SELECT COALESCE(SUM(quantite),0) FROM stock")->fetchColumn();

        // Données pour les statistiques
        $commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commande")->fetchColumn();
        $produitsEnRupture = $pdo->query("SELECT COUNT(*) FROM stock WHERE quantite < 10")->fetchColumn();
        $chiffreAffaires = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commande")->fetchColumn();

        // Stocks par produit
        $stocks = $pdo->query("SELECT p.nom, s.quantite 
                                FROM stock s 
                                JOIN produit p ON s.id_produit = p.id_produit")->fetchAll();

        // Production par mois
        $productions = $pdo->query("SELECT DATE_FORMAT(date_production,'%Y-%m') AS mois, 
                                            SUM(quantite_produite) AS total 
                                        FROM production 
                                        GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll();

        // Commandes par client
        $commandes = $pdo->query("SELECT c.nom, SUM(cmd.total) AS total 
                                    FROM commande cmd 
                                    JOIN client c ON cmd.id_client = c.id_client 
                                    GROUP BY c.nom")->fetchAll();

        // Commandes par mois
        $commandesParMois = $pdo->query("SELECT DATE_FORMAT(date_commande,'%Y-%m') AS mois, 
                                                COUNT(*) AS total 
                                            FROM commande 
                                            GROUP BY mois ORDER BY mois DESC LIMIT 6")->fetchAll();
        $commandesParMois = array_reverse($commandesParMois);

        // Top 5 Produits les plus commandés
        $topProduits = $pdo->query("SELECT p.nom, SUM(cp.quantite) as total 
                                    FROM commande_produit cp 
                                    JOIN produit p ON cp.id_produit = p.id_produit 
                                    GROUP BY p.id_produit, p.nom 
                                    ORDER BY total DESC 
                                    LIMIT 5")->fetchAll();

        include __DIR__.'/../templates/header.php';
        include __DIR__.'/../templates/nav.php';
        ?>

        <main class="main-content">
            <div class="container">
                <div class="dashboard-header">
                    <div>
                        <h1>📊 Tableau de Bord</h1>
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
                    </div>
                    <div class="stat-card stat-success">
                        <div class="stat-icon">📦</div>
                        <div class="stat-info">
                            <span class="stat-label">Produits</span>
                            <span class="stat-value"><?= $totalProduits ?></span>
                        </div>
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
                    </div>
                </div>

                <!-- Cartes de navigation rapides -->
                <div class="dashboard-cards">
                    <a href="produit.php" class="card-link">
                        <div class="card nav-card">
                            <div class="card-icon">📦</div>
                            <h2>Produits</h2>
                            <p>Total: <?= $totalProduits ?></p>
                            <span class="card-link-text">Voir →</span>
                        </div>
                    </a>
                    <a href="production.php" class="card-link">
                        <div class="card nav-card">
                            <div class="card-icon">🏭</div>
                            <h2>Production</h2>
                            <p>Suivi</p>
                            <span class="card-link-text">Voir →</span>
                        </div>
                    </a>
                    <a href="rapport.php" class="card-link">
                        <div class="card nav-card">
                            <div class="card-icon">📈</div>
                            <h2>Rapports</h2>
                            <p>Statistiques</p>
                            <span class="card-link-text">Voir →</span>
                        </div>
                    </a>
                </div>

                <!-- Sélecteur d'analyse -->
                <div class="analysis-selector">
                    <label for="analysisSelect">📊 Choisir une analyse:</label>
                    <select id="analysisSelect" onchange="showAnalysis(this.value)">
                        <option value="">-- Sélectionner --</option>
                        <option value="prod">🏭 Production par mois</option>
                        <option value="top">🏆 Produits les plus commandés</option>
                        <option value="stock">📦 Stocks par produit</option>
                        <option value="cmdMois">📅 Commandes par mois</option>
                        <option value="cmdClient">👥 Commandes par client</option>
                    </select>
                </div>

                <!-- Section des graphiques -->
                <div class="charts-section">
                    <h2 class="section-title">📈 Analyses et Statistiques</h2>
                    
                    <div class="charts-grid">
                        <!-- Analyse: Production par mois -->
                        <div class="chart-card analysis-card" id="card-prod" style="display: none;">
                            <div class="chart-header">
                                <h3>🏭 Production par mois</h3>
                                <span class="chart-badge">12 mois</span>
                            </div>
                            <div class="chart-container">
                                <canvas id="prodChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Analyse: Top Produits -->
                        <div class="chart-card analysis-card" id="card-top" style="display: none;">
                            <div class="chart-header">
                                <h3>🏆 Produits les plus commandés</h3>
                                <span class="chart-badge">Top 5</span>
                            </div>
                            <div class="chart-container">
                                <canvas id="topProduitsChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Analyse: Stocks -->
                        <div class="chart-card analysis-card" id="card-stock" style="display: none;">
                            <div class="chart-header">
                                <h3>📦 Stocks par produit</h3>
                                <span class="chart-badge">Quantités</span>
                            </div>
                            <div class="chart-container">
                                <canvas id="stockChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Analyse: Commandes par mois -->
                        <div class="chart-card analysis-card" id="card-cmdMois" style="display: none;">
                            <div class="chart-header">
                                <h3>📅 Commandes par mois</h3>
                                <span class="chart-badge">6 mois</span>
                            </div>
                            <div class="chart-container">
                                <canvas id="cmdMoisChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Analyse: Commandes par client -->
                        <div class="chart-card analysis-card" id="card-cmdClient" style="display: none;">
                            <div class="chart-header">
                                <h3>👥 Commandes par client</h3>
                                <span class="chart-badge">Répartition</span>
                            </div>
                            <div class="chart-container">
                                <canvas id="cmdChart"></canvas>
                            </div>
                        </div>
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

        // Stocks (Barres horizontales)
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
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(14, 93, 150, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y: { grid: { display: false } }
                }
            }
        });

        // Production (Ligne avec remplissage)
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
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(14, 93, 150, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Commandes par client (Camembert)
        new Chart(document.getElementById('cmdChart'), {
            type: 'doughnut',
            data: {
                labels: cmds.map(c => c.nom),
                datasets: [{
                    data: cmds.map(c => Number(c.total)),
                    backgroundColor: colors.gradient,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 15, usePointStyle: true }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(14, 93, 150, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' €';
                            }
                        }
                    }
                }
            }
        });

        // Commandes par mois (Barres)
        new Chart(document.getElementById('cmdMoisChart'), {
            type: 'bar',
            data: {
                labels: cmdMois.map(c => c.mois),
                datasets: [{
                    label: 'Nombre de commandes',
                    data: cmdMois.map(c => Number(c.total)),
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(14, 93, 150, 0.3)');
                        gradient.addColorStop(1, 'rgba(14, 93, 150, 1)');
                        return gradient;
                    },
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(14, 93, 150, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Top Produits (Barres)
        new Chart(document.getElementById('topProduitsChart'), {
            type: 'bar',
            data: {
                labels: topProds.map(p => p.nom),
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
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(14, 93, 150, 0.9)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .chart-card, .nav-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Fonction pour afficher l'analyse sélectionnée
        function showAnalysis(value) {
            // Masquer toutes les cartes
            document.querySelectorAll('.analysis-card').forEach(card => {
                card.style.display = 'none';
            });
            // Afficher la carte sélectionnée
            document.getElementById('card-' + value).style.display = 'block';
        }
        </script>

        <style>
        .main-content {
            padding: 30px 20px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 15px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dashboard-header h1 {
            color: #0e5d96;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .date-badge {
            background: white;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-weight: 600;
            color: #0e5d96;
        }

        .welcome-text {
            color: #666;
            font-size: 1rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.stat-primary::before { background: #0e5d96; }
        .stat-card.stat-success::before { background: #28a745; }
        .stat-card.stat-warning::before { background: #ffc107; }
        .stat-card.stat-danger::before { background: #dc3545; }
        .stat-card.stat-info::before { background: #17a2b8; }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(14, 93, 150, 0.2);
        }

        .stat-icon {
            font-size: 1.8rem;
            min-width: 35px;
            text-align: center;
        }

        .stat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #666;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }

        .stat-badge {
            font-size: 0.65rem;
            color: #ffc107;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 20px;
            background: rgba(255, 193, 7, 0.15);
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 25px;
        }

        .card-link {
            text-decoration: none;
        }

        .nav-card {
            background: white;
            border-radius: 14px;
            padding: 15px 12px;
            text-align: center;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(14, 93, 150, 0.15);
            border-color: #0e5d96;
        }

        .nav-card .card-icon {
            font-size: 1.6rem;
            margin-bottom: 6px;
        }

        .nav-card h2 {
            font-size: 0.85rem;
            color: #0e5d96;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .nav-card p {
            color: #666;
            font-size: 0.75rem;
            margin-bottom: 5px;
        }

        .card-link-text {
            color: #0e5d96;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Sélecteur d'analyse */
        .analysis-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .analysis-selector label {
            font-weight: 600;
            color: #0e5d96;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .analysis-selector select {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #333;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .analysis-selector select:focus {
            outline: none;
            border-color: #0e5d96;
            box-shadow: 0 0 0 3px rgba(14,93,150,0.1);
        }

        .charts-section {
            margin-top: 25px;
        }

        .section-title {
            color: #0e5d96;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #0e5d96;
            display: inline-block;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: 0 8px 30px rgba(14, 93, 150, 0.15);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chart-header h3 {
            color: #0e5d96;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .chart-badge {
            background: linear-gradient(135deg, #0e5d96, #1e88e5);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .chart-container {
            height: 280px;
            position: relative;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-card {
                padding: 12px;
            }
            .stat-value {
                font-size: 1.2rem;
            }
            .chart-container {
                height: 220px;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr;
            }
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .nav-card {
                padding: 12px 8px;
            }
            .chart-container {
                height: 200px;
            }
        }
        </style>
