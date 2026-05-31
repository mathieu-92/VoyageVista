<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Récupération des notifications
$stmt = $pdo->prepare("SELECT * FROM notification WHERE id_utilisateur = ? ORDER BY date_creation DESC");
$stmt->execute([$_SESSION['id_utilisateur']]);
$notifs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Notifications - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f8f9fa;">
    <main style="max-width: 600px; margin: 40px auto; background: white; padding: 20px; border-radius: 10px;">
        <h2><i class="fa-solid fa-bell"></i> Vos notifications</h2>
        <?php foreach ($notifs as $n): ?>
            <div style="padding: 15px; border-bottom: 1px solid #eee; <?= !$n['lue'] ? 'background: #eef6ff;' : '' ?>">
                <p style="margin: 0; font-size: 0.9em; color: #555;"><?= htmlspecialchars($n['date_creation']) ?></p>
                <p style="margin: 5px 0 0 0;"><?= htmlspecialchars($n['message']) ?></p>
            </div>
        <?php endforeach; ?>
        <a href="index.php" style="display: block; margin-top: 20px;">Retour à l'accueil</a>
    </main>
</body>
</html>