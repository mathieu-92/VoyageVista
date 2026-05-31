<?php
session_start();
// On inclut config.php au cas où on en a besoin plus tard, et pour garder la cohérence
require_once 'config.php'; 

// Si le panier n'existe pas encore dans la session, on le crée
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// 1. AJOUT AU PANIER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nouveau_sejour = [
        'id' => $_POST['id_destination'],
        'ville' => $_POST['nom_ville'],
        'debut' => $_POST['date_debut'],
        'fin' => $_POST['date_fin'],
        'voyageurs' => $_POST['voyageurs'],
        'prix_estime' => $_POST['prix_estime'] ?? 450 // On récupère le vrai prix si on peut, sinon 450
    ];
    
    $_SESSION['panier'][] = $nouveau_sejour;
    header('Location: panier.php');
    exit();
}

// 2. VIDER TOUT LE PANIER
if (isset($_GET['vider'])) {
    $_SESSION['panier'] = [];
    header('Location: panier.php');
    exit();
}

// 3. NOUVEAU : SUPPRIMER UN SEUL ÉLÉMENT (Pour les 2 points de la grille !)
if (isset($_GET['supprimer'])) {
    $index_a_supprimer = $_GET['supprimer'];
    
    // On vérifie que cet élément existe bien dans le panier
    if (isset($_SESSION['panier'][$index_a_supprimer])) {
        // On le retire
        unset($_SESSION['panier'][$index_a_supprimer]);
        // On réorganise les numéros du tableau pour boucher le "trou"
        $_SESSION['panier'] = array_values($_SESSION['panier']);
    }
    
    header('Location: panier.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f8f9fa;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="user-profile-menu">
                    <div class="profile-trigger" style="color: #333;">
                        <i class="fa-solid fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline" style="color: #007BFF; border-color: #007BFF;">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main style="padding: 40px; max-width: 900px; margin: 40px auto; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 30px; color: #333;"><i class="fa-solid fa-basket-shopping"></i> Mon Panier de voyage</h2>
        
        <?php if (empty($_SESSION['panier'])): ?>
            <div style="text-align: center; padding: 40px;">
                <i class="fa-solid fa-plane-slash" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                <p style="font-size: 1.2em; color: #666;">Votre panier est vide pour le moment.</p>
                <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 15px; text-decoration: none;">Explorer les destinations</a>
            </div>
        <?php else: ?>
            <table style="width: 100%; text-align: left; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="border-bottom: 2px solid #ccc; color: #555;">
                    <th style="padding-bottom: 10px;">Destination</th>
                    <th style="padding-bottom: 10px;">Dates</th>
                    <th style="padding-bottom: 10px;">Voyageurs</th>
                    <th style="padding-bottom: 10px;">Prix</th>
                    <th style="padding-bottom: 10px; text-align: center;">Action</th>
                </tr>
                
                <?php 
                $total = 0;
                foreach ($_SESSION['panier'] as $index => $item): 
                    $prix_ligne = $item['prix_estime'] * $item['voyageurs'];
                    $total += $prix_ligne;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px 0;"><strong><?= htmlspecialchars($item['ville']) ?></strong></td>
                        <td>Du <?= htmlspecialchars($item['debut']) ?><br>au <?= htmlspecialchars($item['fin']) ?></td>
                        <td><i class="fa-solid fa-user"></i> <?= htmlspecialchars($item['voyageurs']) ?></td>
                        <td style="color: #007BFF; font-weight: bold;"><?= $prix_ligne ?> €</td>
                        
                        <td style="text-align: center;">
                            <a href="panier.php?supprimer=<?= $index ?>" style="color: #dc3545; font-size: 1.2em;" title="Retirer ce voyage" onclick="return confirm('Retirer ce voyage du panier ?');">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: right; margin-bottom: 30px;">
                <h3 style="margin: 0; color: #333;">Total à payer : <span style="color: #28a745; font-size: 1.3em;"><?= $total ?> €</span></h3>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="panier.php?vider=1" style="color: #dc3545; text-decoration: none;" onclick="return confirm('Vider tout le panier ?');"><i class="fa-solid fa-ban"></i> Vider le panier</a>
                <a href="paiement.php" class="btn-primary" style="padding: 15px 30px; font-size: 1.1em; background-color: #28a745; border: none; text-decoration: none;">
                    <i class="fa-solid fa-lock"></i> Valider et Payer
                </a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>