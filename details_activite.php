<?php
session_start();
require_once 'config.php';

// 1. Vérification de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_activite = (int)$_GET['id'];

// 2. Récupération des détails de l'activité et de la ville associée
try {
    $sql = "SELECT a.*, d.ville, d.pays 
            FROM activite a 
            JOIN destination d ON a.id_destination = d.id_destination 
            WHERE a.id_activite = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_activite]);
    $activite = $stmt->fetch();

    if (!$activite) {
        die("Activité introuvable.");
    }
} catch (\PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Pour la cloche de notification
$notif_count = 0;
if(isset($_SESSION['id_utilisateur'])) {
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE id_utilisateur = ? AND lue = 0");
    $stmt_count->execute([$_SESSION['id_utilisateur']]);
    $notif_count = $stmt_count->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($activite['nom']) ?> - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .details-container { max-width: 1000px; margin: 40px auto; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden; display: flex; flex-wrap: wrap; }
        .details-img { flex: 1; min-width: 300px; background: #eee; }
        .details-img img { width: 100%; height: 100%; object-fit: cover; }
        .details-content { flex: 1; padding: 40px; min-width: 300px; display: flex; flex-direction: column; }
        .tag { display: inline-block; background: #eef6ff; color: #007BFF; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9em; margin-bottom: 15px; }
        .title { margin: 0 0 10px 0; font-size: 2.2em; color: #333; }
        .location { color: #666; font-size: 1.1em; margin-bottom: 20px; }
        .desc { color: #555; line-height: 1.6; margin-bottom: 30px; font-size: 1.05em; }
        .features { display: flex; gap: 20px; margin-bottom: 30px; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 15px 0; }
        .feature-item { color: #555; display: flex; align-items: center; gap: 8px; }
        
        .booking-box { background: #f8f9fa; padding: 25px; border-radius: 10px; border: 1px solid #eee; }
        .price { font-size: 2em; color: #28a745; font-weight: bold; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
    </style>
</head>
<body style="background-color: #f4f7f6;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <a href="panier.php" class="btn-primary panier-icon"><i class="fa-solid fa-shopping-cart"></i></a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="content-layout">
        <a href="javascript:history.back()" style="color: #007BFF; text-decoration: none; display: inline-block; margin-bottom: 20px;">
            <i class="fa-solid fa-arrow-left"></i> Retour aux résultats
        </a>

        <div class="details-container">
            <div class="details-img">
                <img src="image/<?= htmlspecialchars($activite['image_illustration']) ?>" onerror="this.src='image/default.jpg'" alt="<?= htmlspecialchars($activite['nom']) ?>">
            </div>
            
            <div class="details-content">
                <div>
                    <span class="tag"><i class="fa-solid fa-person-running"></i> <?= htmlspecialchars($activite['type'] ?? 'Activité') ?></span>
                </div>
                
                <h1 class="title"><?= htmlspecialchars($activite['nom']) ?></h1>
                
                <div class="location">
                    <i class="fa-solid fa-location-dot" style="color: #dc3545;"></i> <?= htmlspecialchars($activite['ville']) ?>, <?= htmlspecialchars($activite['pays']) ?>
                </div>
                
                <p class="desc"><?= nl2br(htmlspecialchars($activite['description'])) ?></p>
                
                <div class="features">
                    <div class="feature-item">
                        <i class="fa-regular fa-clock" style="color: #007BFF; font-size: 1.2em;"></i> 
                        Durée : <?= htmlspecialchars($activite['duree'] ?? 'Non spécifiée') ?>
                    </div>
                </div>
                
                <div class="booking-box">
                    <div class="price"><?= htmlspecialchars($activite['prix']) ?> € <span style="font-size: 0.4em; color: #888; font-weight: normal;">/ pers.</span></div>
                    
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="action" value="ajouter">
                        <input type="hidden" name="id_destination" value="ACT-<?= $activite['id_activite'] ?>">
                        <input type="hidden" name="nom_ville" value="<?= htmlspecialchars($activite['nom']) ?> (<?= htmlspecialchars($activite['ville']) ?>)">
                        <input type="hidden" name="prix_estime" value="<?= htmlspecialchars($activite['prix']) ?>">
                        
                        <div style="display: flex; gap: 15px;">
                            <div class="form-group" style="flex: 2;">
                                <label>Date prévue :</label>
                                <input type="date" name="date_debut" id="date_debut" required>
                                <input type="hidden" name="date_fin" id="date_fin">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Personnes :</label>
                                <input type="number" name="voyageurs" min="1" value="1" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em; border: none; margin-top: 10px;">
                            <i class="fa-solid fa-cart-plus"></i> Ajouter au panier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Petit script pour que la "date de fin" invisible soit la même que la "date de début" (pour que ton panier fonctionne sans erreur)
        document.getElementById('date_debut').addEventListener('change', function() {
            document.getElementById('date_fin').value = this.value;
        });
    </script>
</body>
</html>