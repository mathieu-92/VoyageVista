<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Sécurité
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_user = $_SESSION['id_utilisateur'];

// Traitement annulation
if (isset($_GET['annuler'])) {
    $id_resa = (int)$_GET['annuler'];
    $stmt = $pdo->prepare("DELETE FROM Reservation WHERE id_reservation = ? AND id_utilisateur = ?");
    $stmt->execute([$id_resa, $id_user]);
    header('Location: gestion_reservations.php?msg=annule');
    exit();
}

// Récupération
$stmt = $pdo->prepare("SELECT * FROM Reservation WHERE id_utilisateur = ? ORDER BY date_commande DESC");
$stmt->execute([$id_user]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f8f9fa;">
    
    <header class="top-nav" style="background: white; padding: 15px; display: flex; justify-content: space-between;">
        <div class="logo"><a href="index.php">VoyageVista</a></div>
        <a href="panier.php" class="btn-primary">Retour au panier</a>
    </header>

    <main style="max-width: 800px; margin: 40px auto; padding: 20px;">
        <h2>Mes voyages réservés</h2>
        
        <?php foreach ($reservations as $resa): ?>
            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div>
                    <p><strong>Date :</strong> <?= htmlspecialchars($resa['date_debut_sejour']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($resa['prix_total_calcule']) ?> €</p>
                </div>
                <a href="gestion_reservations.php?annuler=<?= $resa['id_reservation'] ?>" class="btn-primary" style="background: red; border: none;">Annuler</a>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>