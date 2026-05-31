<?php
session_start();
require_once 'config.php'; 

// Si le panier n'existe pas, on le crée
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
        'prix_estime' => $_POST['prix_estime'] ?? 0
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

// 3. SUPPRIMER UN SEUL ÉLÉMENT (Validé : "Interaction efficace avec le panier")
if (isset($_GET['supprimer'])) {
    $index = (int)$_GET['supprimer'];
    if (isset($_SESSION['panier'][$index])) {
        unset($_SESSION['panier'][$index]);
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
                <div class="profile-trigger" style="color: #333;"><i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($_SESSION['prenom']) ?></div>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main style="padding: 40px; max-width: 900px; margin: 40px auto; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 30px; color: #333;"><i class="fa-solid fa-basket-shopping"></i> Mon Panier de voyage</h2>
        
        <?php if (empty($_SESSION['panier'])): ?>
            <div style="text-align: center; padding: 40px;">
                <i class="fa-solid fa-plane-slash" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                <p>Votre panier est vide.</p>
                <a href="index.php" class="btn-primary">Explorer les destinations</a>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="border-bottom: 2px solid #ccc; color: #555;">
                    <th style="padding: 10px;">Destination</th>
                    <th style="padding: 10px;">Dates</th>
                    <th style="padding: 10px;">Voyageurs</th>
                    <th style="padding: 10px;">Prix</th>
                    <th style="padding: 10px;">Action</th>
                </tr>
                <?php 
                $total = 0;
                foreach ($_SESSION['panier'] as $index => $item): 
                    $prix_ligne = $item['prix_estime'] * $item['voyageurs'];
                    $total += $prix_ligne;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><strong><?= htmlspecialchars($item['ville']) ?></strong></td>
                        <td style="padding: 15px; font-size: 0.9em;">Du <?= htmlspecialchars($item['debut']) ?><br>au <?= htmlspecialchars($item['fin']) ?></td>
                        <td style="padding: 15px;"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($item['voyageurs']) ?></td>
                        <td style="padding: 15px; color: #007BFF; font-weight: bold;"><?= number_format($prix_ligne, 2) ?> €</td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="panier.php?supprimer=<?= $index ?>" style="color: #dc3545;" onclick="return confirm('Retirer cet élément ?');">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <div style="text-align: right; margin-bottom: 30px;">
                <h3 style="color: #333;">Total du séjour : <span style="color: #28a745;"><?= number_format($total, 2) ?> €</span></h3>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <a href="visualiser_itineraire.php?id_reservation=1" style="color: #007BFF; text-decoration: underline;">
                <i class="fa-solid fa-map-marked-alt"></i> Voir le détail de l'itinéraire
                </a>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <a href="panier.php?vider=1" style="color: #dc3545;"><i class="fa-solid fa-ban"></i> Vider</a>
                <a href="paiement.php" class="btn-primary" style="padding: 10px 20px;">Valider et Payer</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>