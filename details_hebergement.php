<?php
session_start();
require_once 'config.php';

// Vérification de l'ID passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_hebergement = (int)$_GET['id'];

// Récupération des détails de l'hébergement avec la ville et le pays associés
try {
    $sql = "SELECT h.*, d.ville, d.pays FROM hebergement h 
            JOIN destination d ON h.id_destination = d.id_destination 
            WHERE h.id_hebergement = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_hebergement]);
    $hotel = $stmt->fetch();

    if (!$hotel) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2>Hébergement introuvable.</h2>
                <a href='index.php'>Retour à l'accueil</a>
             </div>");
    }
} catch (\PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hotel['nom_hebergement']) ?> - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .details-container { max-width: 1000px; margin: 40px auto; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-wrap: wrap; }
        .details-image { flex: 1 1 500px; background: #eee; }
        .details-image img { width: 100%; height: 100%; object-fit: cover; min-height: 400px; }
        .details-content { flex: 1 1 400px; padding: 40px; display: flex; flex-direction: column; }
        .hotel-title { margin: 0 0 10px 0; color: #333; font-size: 2em; }
        .hotel-location { color: #666; font-size: 1.1em; margin-bottom: 20px; }
        .hotel-desc { color: #555; line-height: 1.6; margin-bottom: 30px; flex-grow: 1; }
        .hotel-price { font-size: 1.8em; color: #28a745; font-weight: bold; margin-bottom: 20px; }
        
        .booking-form { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #eee; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body style="background-color: #f4f7f6;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions">
            <a href="panier.php" class="btn-primary panier-icon"><i class="fa-solid fa-shopping-cart"></i> Mon Panier</a>
        </div>
    </header>

    <main class="details-container">
        <div class="details-image">
            <img src="image/<?= htmlspecialchars($hotel['image_illustration'] ?? 'default.jpg') ?>" onerror="this.src='image/default.jpg'" alt="<?= htmlspecialchars($hotel['nom_hebergement']) ?>">
        </div>
        
        <div class="details-content">
            <h1 class="hotel-title"><?= htmlspecialchars($hotel['nom_hebergement']) ?></h1>
            <div class="hotel-location">
                <i class="fa-solid fa-map-pin" style="color: #007BFF;"></i> <?= htmlspecialchars($hotel['ville']) ?>, <?= htmlspecialchars($hotel['pays']) ?>
                <span style="margin-left: 15px; color: #f1c40f;"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($hotel['nombre_etoiles'] ?? '4') ?> Étoiles</span>
            </div>
            
            <p class="hotel-desc">
                <?= nl2br(htmlspecialchars($hotel['description'] ?? 'Profitez d\'un séjour inoubliable dans cet établissement d\'exception.')) ?>
            </p>
            
            <div class="hotel-price">
                <?= htmlspecialchars($hotel['prix_par_nuit']) ?> € <span style="font-size: 0.5em; color: #777; font-weight: normal;">/ nuit / pers.</span>
            </div>

            <div class="booking-form">
                <form action="panier.php" method="POST">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="id_destination" value="HEB-<?= $hotel['id_hebergement'] ?>">
                    <input type="hidden" name="nom_ville" value="Hôtel <?= htmlspecialchars($hotel['nom_hebergement']) ?> à <?= htmlspecialchars($hotel['ville']) ?>">
                    <input type="hidden" name="prix_estime" value="<?= htmlspecialchars($hotel['prix_par_nuit']) ?>">

                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Arrivée</label>
                            <input type="date" name="date_debut" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Départ</label>
                            <input type="date" name="date_fin" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre de voyageurs</label>
                        <input type="number" name="voyageurs" min="1" value="2" required>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1em; padding: 12px; background-color: #007BFF; border: none; border-radius: 5px; cursor: pointer; color: white;">
                        <i class="fa-solid fa-bed"></i> Ajouter au séjour
                    </button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>