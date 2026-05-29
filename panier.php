<?php
session_start();

// Si le panier n'existe pas encore dans la session, on le crée (c'est un tableau vide)
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Si on reçoit des données du formulaire de détails (ajout au panier)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nouveau_sejour = [
        'id' => $_POST['id_destination'],
        'ville' => $_POST['nom_ville'],
        'debut' => $_POST['date_debut'],
        'fin' => $_POST['date_fin'],
        'voyageurs' => $_POST['voyageurs'],
        'prix_estime' => 450 // Prix fictif pour la simulation
    ];
    
    // On ajoute ce séjour dans notre tableau de session
    $_SESSION['panier'][] = $nouveau_sejour;
    
    // On rafraîchit la page pour éviter de renvoyer le formulaire si l'utilisateur fait F5
    header('Location: panier.php');
    exit();
}

// S'il y a une action de "vider le panier"
if (isset($_GET['vider'])) {
    $_SESSION['panier'] = [];
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
</head>
<body>
    <header class="top-nav">
        <h1>VoyageVista</h1>
        <a href="index.php" class="btn-outline">Continuer d'explorer</a>
    </header>

    <main style="padding: 40px; max-width: 800px; margin: auto;">
        <h2>🛒 Panier de voyage</h2>
        
        <?php if (empty($_SESSION['panier'])): ?>
            <p>Votre panier est vide pour le moment.</p>
        <?php else: ?>
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <tr style="border-bottom: 2px solid #ccc;">
                    <th>Destination</th>
                    <th>Dates</th>
                    <th>Voyageurs</th>
                    <th>Prix</th>
                </tr>
                
                <?php 
                $total = 0;
                foreach ($_SESSION['panier'] as $item): 
                    $prix_ligne = $item['prix_estime'] * $item['voyageurs'];
                    $total += $prix_ligne;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 0;">Séjour à <?= htmlspecialchars($item['ville']) ?></td>
                        <td>Du <?= htmlspecialchars($item['debut']) ?> au <?= htmlspecialchars($item['fin']) ?></td>
                        <td><?= htmlspecialchars($item['voyageurs']) ?></td>
                        <td><?= $prix_ligne ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <h3 style="text-align: right; margin-top: 20px;">Total à payer : <?= $total ?> €</h3>
            
            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                <a href="panier.php?vider=1" style="color: red;">Vider le panier</a>
                
                <a href="paiement.php" class="btn-primary" style="padding: 15px 30px; font-size: 1.2em;">Valider et Payer</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>