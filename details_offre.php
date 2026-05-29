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
$sql = "SELECT * FROM destination WHERE id_destination = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$destination = $stmt->fetch();

// Si l'ID n'existe pas dans la base, on le renvoie à l'accueil
if (!$destination) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($destination['ville']) ?> - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <h1>VoyageVista</h1>
        </div>
        <div class="user-actions">
            <a href="index.php" class="btn-outline">Retour au catalogue</a>
            <a href="panier.php" class="btn-primary">Mon Panier</a>
        </div>
    </header>

    <main style="padding: 40px; max-width: 800px; margin: auto;">
        <img src="images/<?= htmlspecialchars($destination['image_illustration']) ?>" alt="<?= htmlspecialchars($destination['ville']) ?>" style="width:100%; border-radius:12px;">
        
        <h2>Découvrez <?= htmlspecialchars($destination['ville']) ?>, <?= htmlspecialchars($destination['pays']) ?></h2>
        <p><?= htmlspecialchars($destination['description_courte']) ?></p>
        
        <hr>
        
        <h3>Organiser mon séjour</h3>
        <form action="panier.php" method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="id_destination" value="<?= $destination['id_destination'] ?>">
            <input type="hidden" name="nom_ville" value="<?= $destination['ville'] ?>">
            
            <label for="date_debut">Date de départ :</label>
            <input type="date" id="date_debut" name="date_debut" required><br><br>
            
            <label for="date_fin">Date de retour :</label>
            <input type="date" id="date_fin" name="date_fin" required><br><br>
            
            <label for="voyageurs">Nombre de voyageurs :</label>
            <input type="number" id="voyageurs" name="voyageurs" value="2" min="1" required><br><br>
            
            <button type="submit" class="btn-primary">Ajouter au panier</button>
        </form>
    </main>
</body>
</html>