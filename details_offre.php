<?php
session_start();
require_once 'config.php';

// On vérifie si un ID de destination a bien été envoyé dans l'URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}

// On récupère les infos de la destination précise
try {
    $sql = "SELECT * FROM destination WHERE id_destination = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $destination = $stmt->fetch();

    // Si l'ID n'existe pas dans la base, on le renvoie à l'accueil
    if (!$destination) {
        header('Location: index.php');
        exit();
    }
} catch (\PDOException $e) {
    die("Erreur de chargement : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($destination['ville']) ?> - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .offre-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        @media (max-width: 768px) {
            .offre-layout { grid-template-columns: 1fr; }
        }
        .offre-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .offre-content { margin-top: 20px; }
        .booking-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
            border: 1px solid #eee;
            height: fit-content;
        }
        .booking-card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .booking-card label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .booking-card input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
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
                <a href="panier.php" class="btn-primary panier-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <?php if(!empty($_SESSION['panier'])): ?>
                        <span class="cart-badge"><?= count($_SESSION['panier']) ?></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline" style="color: #007BFF; border-color: #007BFF;">Se connecter</a>
                <a href="inscription.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="offre-layout">
        <div>
            <img src="image/<?= htmlspecialchars($destination['image_illustration']) ?>" alt="<?= htmlspecialchars($destination['ville']) ?>" class="offre-img">
            
            <div class="offre-content">
                <h1 style="color: #333; font-size: 2.5em; margin-bottom: 5px;"><?= htmlspecialchars($destination['ville']) ?></h1>
                <p style="color: #666; font-size: 1.2em; margin-top: 0;">
                    <i class="fa-solid fa-location-dot" style="color: #007BFF;"></i> <?= htmlspecialchars($destination['pays']) ?>
                </p>
                
                <h3 style="margin-top: 30px;">À propos de cette destination</h3>
                <p style="line-height: 1.6; color: #555;">
                    <?= nl2br(htmlspecialchars($destination['description_courte'] ?? 'Une destination magnifique à découvrir.')) ?>
                </p>
            </div>
        </div>

        <div>
            <div class="booking-card">
                <h3><span style="font-size: 1.3em; color: #007BFF;">299 €</span> <span style="font-size: 0.6em; color: #666; font-weight: normal;">/ voyageur</span></h3>
                
                <form action="panier.php" method="POST">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="id_destination" value="<?= $destination['id_destination'] ?>">
                    <input type="hidden" name="nom_ville" value="<?= htmlspecialchars($destination['ville']) ?>">
                    <input type="hidden" name="prix_estime" value="299">
                    
                    <label for="date_debut"><i class="fa-regular fa-calendar-check"></i> Arrivée :</label>
                    <input type="date" id="date_debut" name="date_debut" required>
                    
                    <label for="date_fin"><i class="fa-regular fa-calendar-xmark"></i> Départ :</label>
                    <input type="date" id="date_fin" name="date_fin" required>
                    
                    <label for="voyageurs"><i class="fa-solid fa-user-group"></i> Voyageurs :</label>
                    <input type="number" id="voyageurs" name="voyageurs" value="2" min="1" required>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1em; padding: 12px; margin-top: 10px; background-color: #28a745; border: none;">
                        Ajouter au panier
                    </button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>