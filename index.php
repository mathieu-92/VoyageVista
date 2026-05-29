<?php
// Démarrage de la session pour gérer le statut de connexion plus tard
session_start();

// Inclusion de la connexion à la base de données
require_once 'config.php';

// Requête pour récupérer toutes les destinations de la base de données
$sql = "SELECT * FROM destination LIMIT 6";
$stmt = $pdo->query($sql);
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVista - Planifiez. Explorez. Vivez.</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="top-nav">
        <div class="logo">
            <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 10 L90 80 L10 80 Z" fill="#2ECC71"/>
                <path d="M50 30 L80 80 L20 80 Z" fill="#3498DB"/>
            </svg>
            <h1>VoyageVista</h1>
        </div>
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <span>Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?> !</span>
                <a href="panier.php" class="btn-outline">Mon Panier</a>
                <a href="deconnexion.php" class="btn-primary">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
                <a href="inscription.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero-search">
        <div class="search-tabs">
            <button class="active">✈️ Vols</button>
            <button>🏨 Hôtels</button>
            <button>🏝️ Séjours</button>
            <button>🏄‍♂️ Activités</button>
        </div>
        <form action="vols.php" method="GET" class="search-bar">
            <input type="text" name="destination" placeholder="Où voulez-vous aller ?" required>
            <input type="date" name="depart" required>
            <input type="date" name="retour">
            <button type="submit" class="btn-search">Rechercher</button>
        </form>
    </section>

    <main class="content-layout">
        <section class="results-section">
            <h2>Destinations populaires</h2>
            <div class="grid-results">
                
                <?php if(count($destinations) > 0): ?>
                    <?php foreach($destinations as $dest): ?>
                        <div class="card">
                            <img src="images/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="<?= htmlspecialchars($dest['ville']) ?>" style="width:100%; height:150px; object-fit:cover; border-radius:12px;">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($dest['ville']) ?>, <?= htmlspecialchars($dest['pays']) ?></h3>
                                <p><?= htmlspecialchars($dest['description_courte']) ?></p>
                                <a href="details_offre.php?id=<?= $dest['id_destination'] ?>" class="btn-primary" style="width:100%; text-align:center; display:block; margin-top:10px;">Voir les offres</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune destination disponible pour le moment. Le catalogue est en cours de mise à jour.</p>
                <?php endif; ?>

            </div>
        </section>
    </main>

</body>
</html>