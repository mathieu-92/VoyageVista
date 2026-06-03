<?php
session_start();
require_once 'config.php';

// Sécurité : Vérification de la présence de l'ID de réservation
if (!isset($_GET['id_reservation']) || empty($_GET['id_reservation'])) {
    header('Location: index.php');
    exit();
}

$id_reservation = (int)$_GET['id_reservation'];

// Récupération des éléments de l'itinéraire liés à cette réservation
try {
    $stmt = $pdo->prepare("SELECT * FROM itineraire WHERE id_reservation = ? ORDER BY date_element ASC");
    $stmt->execute([$id_reservation]);
    $itineraire = $stmt->fetchAll();
    
    if (!$itineraire) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2>Aucun itinéraire trouvé pour cette réservation.</h2>
                <a href='index.php' style='color:#007BFF; text-decoration:none;'>Retour à l'accueil</a>
             </div>");
    }
} catch (\PDOException $e) {
    die("Erreur lors du chargement de l'itinéraire : " . $e->getMessage());
}

// Calcul du total
$total_itineraire = 0;
foreach ($itineraire as $item) {
    $total_itineraire += $item['prix_element'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Itinéraire - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: Arial, sans-serif; 
        }
        .itineraire-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header-title { 
            color: #333; 
            border-bottom: 2px solid #3498db; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th { 
            text-align: left; 
            background-color: #f4f7f6; 
            padding: 12px; 
            border-bottom: 1px solid #ddd; 
        }
        td { 
            padding: 12px; 
            border-bottom: 1px solid #eee; 
        }
        .total-row {
            background-color: #f1f8f5;
            font-weight: bold;
            font-size: 1.2em;
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            color: #28a745;
        }
        
        /* 
           Cette partie permet de cacher les boutons 
           lorsqu'on ouvre la fenêtre d'impression (Ctrl+P) 
        */
        @media print {
            .no-print { display: none !important; }
            .itineraire-container { box-shadow: none; margin: 0; padding: 0; }
            body { background-color: white; }
        }
    </style>
</head>
<body>

    <main class="itineraire-container">
        <h2 class="header-title">Détail de votre séjour #<?= htmlspecialchars($id_reservation) ?></h2>
        
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th style="text-align: right;">Prix</th>
                </tr>
            </thead> 
            <tbody>
                <?php foreach ($itineraire as $item): ?>
                    <tr>
                        <td><?= date('d/m', strtotime($item['date_element'])) ?></td>
                        <td><?= htmlspecialchars($item['type_element'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($item['nom_element']) ?></strong></td>
                        <td style="text-align: right;"><?= number_format($item['prix_element'], 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">
            Coût total automatique : <?= number_format($total_itineraire, 2) ?> €
        </div>

        <!-- Boutons d'action (masqués à l'impression grâce à .no-print) -->
        <div class="no-print" style="margin-top: 30px; display: flex; justify-content: center; gap: 15px;">
            <a href="index.php" style="padding: 10px 20px; background-color: white; color: #007BFF; border: 2px solid #007BFF; border-radius: 5px; text-decoration: none; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                <i class="fa-solid fa-arrow-left"></i> Retour à l'accueil
            </a>
            
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                <i class="fa-solid fa-print"></i> Imprimer l'itinéraire
            </button>
        </div>
        
    </main>

</body>
</html>