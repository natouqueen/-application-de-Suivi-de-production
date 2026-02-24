<?php
// Démarrer la session en premier
session_start();

// Vérifier que l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Vérifier le rôle (1=admin, 2=manager, 3=operateur)
$userRole = $_SESSION['user']['role'] ?? 3;
$allowedRoles = [1, 2, 3];
if (!in_array($userRole, $allowedRoles)) {
    header("Location: dashboard.php?error=access_denied");
    exit;
}

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../models/Rapport.php";

$rapportModel = new Rapport();
$role = $userRole;
$redirect = "dashboard.php";

// Génération automatique du contenu selon le type de rapport
function genererContenuRapport($type) {
    global $rapportModel;
    $contenu = "";

    switch (strtolower($type)) {
        case 'stock':
            $rows = $rapportModel->stocksParProduit();
            $contenu .= "📦 État du stock :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['nom']} : {$r['quantite']} unités\n";
            }
            break;

        case 'commandes':
            $rows = $rapportModel->commandesParClient();
            $contenu .= "🛒 Résumé des commandes :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['nom']} : {$r['total']} FCFA\n";
            }
            break;

        case 'production':
            $rows = $rapportModel->productionsParMois();
            $contenu .= "🏭 Rapport Production :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['mois']} : {$r['total']} produits fabriqués\n";
            }
            break;

        case 'fournisseurs':
            global $pdo;
            $sql = "SELECT nom, contact, email FROM fournisseur";
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $contenu .= "🏢 Liste des fournisseurs :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['nom']} | Contact: {$r['contact']} | Email: {$r['email']}\n";
            }
            break;

        case 'clients':
            global $pdo;
            $sql = "SELECT nom, telephone, email FROM client";
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $contenu .= "👥 Liste des clients :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['nom']} | Téléphone: {$r['telephone']} | Email: {$r['email']}\n";
            }
            break;

        case 'utilisateurs':
            global $pdo;
            $sql = "SELECT nom, id_role, email FROM utilisateur";
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $contenu .= "👤 Liste des utilisateurs :\n";
            foreach ($rows as $r) {
                $roleName = $r['id_role'] == 1 ? 'Administrateur' : ($r['id_role'] == 2 ? 'Opérateur' : 'Utilisateur');
                $contenu .= "- {$r['nom']} | Rôle: {$roleName} | Email: {$r['email']}\n";
            }
            break;

        case 'produits':
            global $pdo;
            $sql = "SELECT nom, prix FROM produit";
            $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $contenu .= "🛍 Liste des produits :\n";
            foreach ($rows as $r) {
                $contenu .= "- {$r['nom']} | Prix: {$r['prix']} FCFA\n";
            }
            break;

        default:
            $contenu .= "ℹ️ Aucun contenu disponible pour ce type de rapport.\n";
            break;
    }

    return $contenu;
}

// Ajouter rapport (auto-généré)
function ajouterRapport($type, $id_utilisateur) {
    global $pdo;
    $titre = ucfirst($type) . " - " . date('d/m/Y H:i');
    $contenu = genererContenuRapport($type);

    try {
        $stmt = $pdo->prepare("INSERT INTO rapport (titre, contenu, id_utilisateur, date_creation) VALUES (?,?,?, NOW())");
        $result = $stmt->execute([$titre, $contenu, $id_utilisateur]);
        return $result;
    } catch (Exception $e) {
        echo "Erreur SQL: " . $e->getMessage();
        return false;
    }
}

// Modifier rapport (régénération auto)
function modifierRapport($id, $type, $id_utilisateur) {
    global $pdo;
    $titre = ucfirst($type) . " - " . date('d/m/Y H:i');
    $contenu = genererContenuRapport($type);

    $stmt = $pdo->prepare("UPDATE rapport SET titre=?, contenu=?, id_utilisateur=?, date_creation=NOW() WHERE id_rapport=?");
    return $stmt->execute([$titre, $contenu, $id_utilisateur, $id]);
}

// Supprimer rapport
function supprimerRapport($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM rapport WHERE id_rapport=?");
    return $stmt->execute([$id]);
}

// Lister tous les rapports
function listerRapports() {
    global $pdo;
    $sql = "SELECT r.*, u.nom AS auteur
            FROM rapport r
            LEFT JOIN utilisateur u ON r.id_utilisateur=u.id_utilisateur
            ORDER BY r.date_creation DESC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Trouver un rapport par ID
function trouverRapportParId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM rapport WHERE id_rapport=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer l'ID utilisateur en toute sécurité
$id_utilisateur = $_SESSION['user']['id_utilisateur'] ?? $_SESSION['user']['id'] ?? $_SESSION['user']['id_user'] ?? null;

// Générer un rapport - TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generer'])) {
    $type = $_POST['type'] ?? '';
    
    if (empty($type)) {
        $_SESSION['error'] = "Veuillez sélectionner un type de rapport";
    } elseif (empty($id_utilisateur)) {
        $_SESSION['error'] = "Utilisateur non identifié";
    } else {
        try {
            $result = ajouterRapport($type, $id_utilisateur);
            if ($result) {
                $_SESSION['success'] = "Rapport généré avec succès!";
            } else {
                $_SESSION['error'] = "Erreur lors de la génération du rapport";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur: " . $e->getMessage();
        }
    }
    // Rediriger pour éviter la resoumission du formulaire
    header("Location: rapport.php");
    exit;
}

// Récupérer les messages de session
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Régénérer un rapport
if (isset($_GET['regen']) && is_numeric($_GET['regen'])) {
    $rapport = trouverRapportParId($_GET['regen']);
    if ($rapport && $id_utilisateur) {
        $type = strtolower(explode(' - ', $rapport['titre'])[0]);
        modifierRapport($rapport['id_rapport'], $type, $id_utilisateur);
        $_SESSION['success'] = "Rapport régénéré avec succès!";
    }
    header("Location: rapport.php");
    exit;
}

// Supprimer
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    supprimerRapport($_GET['supprimer']);
    $_SESSION['success'] = "Rapport supprimé";
    header("Location: rapport.php");
    exit;
}

// Voir/Rafficher un rapport
if (isset($_GET['voir']) && is_numeric($_GET['voir'])) {
    $rapport = trouverRapportParId($_GET['voir']);
    if ($rapport) {
        // Afficher le rapport dans une nouvelle page
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title><?= htmlspecialchars($rapport['titre']) ?></title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; background: #f5f5f5; }
                .rapport { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
                h1 { color: #0e5d96; border-bottom: 2px solid #0e5d96; padding-bottom: 10px; }
                .meta { color: #666; font-size: 0.9rem; margin-bottom: 20px; }
                .contenu { white-space: pre-wrap; line-height: 1.8; background: #f9f9f9; padding: 20px; border-radius: 8px; }
                .btn { display: inline-block; padding: 10px 20px; background: #0e5d96; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="rapport">
                <h1><?= htmlspecialchars($rapport['titre']) ?></h1>
                <div class="meta">Créé le: <?= htmlspecialchars($rapport['date_creation']) ?></div>
                <div class="contenu"><?= htmlspecialchars($rapport['contenu']) ?></div>
                <a href="rapport.php" class="btn">← Retour</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Télécharger un rapport en fichier texte
if (isset($_GET['telecharger']) && is_numeric($_GET['telecharger'])) {
    $rapport = trouverRapportParId($_GET['telecharger']);
    if ($rapport) {
        $titre = $rapport['titre'] ?? 'Rapport';
        $contenu = $rapport['contenu'] ?? '';
        $date = date('d-m-Y_H-i', strtotime($rapport['date_creation'] ?? 'now'));
        
        // Nettoyer le titre pour le nom de fichier
        $nomFichier = preg_replace('/[^a-zA-Z0-9_-]/', '_', $titre) . '_' . $date . '.txt';
        
        // Forcer le téléchargement
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');
        
        echo $contenu;
        exit;
    }
}

// Charger les données
$rapports = listerRapports();
$stats = $rapportModel->statistiquesGenerales();

// Inclure la vue
include __DIR__ . "/../views/rapports.php";
?>
