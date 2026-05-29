<?php
require_once 'config.php';

// On récupère la valeur saisie dans le formulaire (name="destination")
$ville_recherchee = isset($_GET['destination']) ? $_GET['destination'] : '';

try {
    // Requête préparée pour chercher la ville (LIKE permet de trouver des résultats partiels)
    $sql = "SELECT * FROM destination WHERE ville LIKE :ville";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ville' => '%' . $ville_recherchee . '%']);
    $resultats = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Erreur lors de la recherche : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="content-layout">
        <h2>Résultats pour : "<?= htmlspecialchars($ville_recherchee) ?>"</h2>
        
        <div class="grid-results">
            <?php if(count($resultats) > 0): ?>
                <?php foreach($resultats as $dest): ?>
                    <div class="card">
                        <img src="image/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="<?= htmlspecialchars($dest['ville']) ?>">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($dest['ville']) ?></h3>
                            <p><?= htmlspecialchars($dest['description_courte']) ?></p>
                            <a href="details_offre.php?id=<?= $dest['id_destination'] ?>" class="btn-primary">Découvrir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune destination trouvée pour votre recherche.</p>
                <a href="index.php">Retour à l'accueil</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>