<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// --- NOUVEAU : Traitement du clic pour marquer UNE notification comme lue ---
if (isset($_GET['action']) && $_GET['action'] === 'read' && isset($_GET['id'])) {
    $id_notif = (int)$_GET['id'];
    
    // On met à jour uniquement la notification cliquée
    $stmt_update = $pdo->prepare("UPDATE notification SET lue = 1 WHERE id_notification = ? AND id_utilisateur = ?");
    $stmt_update->execute([$id_notif, $_SESSION['id_utilisateur']]);
    
    // Redirection propre pour nettoyer l'URL après le clic
    header('Location: notifications.php');
    exit();
}

// 1. Récupération des notifications pour l'affichage
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
    <style>
        /* Petit effet au survol pour montrer que c'est cliquable */
        .notif-cliquable:hover {
            background-color: #dbeafe !important;
            cursor: pointer;
            transform: scale(1.01);
        }
        .notif-cliquable {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    <main style="max-width: 600px; margin: 40px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #333;"><i class="fa-solid fa-bell" style="color: #007BFF;"></i> Vos notifications</h2>
            <a href="index.php" class="btn-outline" style="text-decoration: none; padding: 8px 15px; color: #007BFF; border: 1px solid #007BFF; border-radius: 5px;">Retour à l'accueil</a>
        </div>
        
        <?php if (count($notifs) > 0): ?>
            <?php foreach ($notifs as $n): ?>
                
                <?php if (!$n['lue']): ?>
                    <!-- Notification NON lue : Cliquable -->
                    <a href="notifications.php?action=read&id=<?= $n['id_notification'] ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="notif-cliquable" style="padding: 15px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #eee; background: #eef6ff; border-left: 4px solid #007BFF;">
                            <p style="margin: 0; font-size: 0.85em; color: #888;">
                                <i class="fa-regular fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($n['date_creation'])) ?>
                                <span style="float: right; color: #007BFF; font-weight: bold; font-size: 0.9em;"><i class="fa-solid fa-check-double"></i> Marquer lu</span>
                            </p>
                            <p style="margin: 8px 0 0 0; color: #444; font-size: 1.05em;"><?= htmlspecialchars($n['message']) ?></p>
                        </div>
                    </a>
                <?php else: ?>
                    <!-- Notification DEJA lue : Non cliquable -->
                    <div style="padding: 15px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #eee; background: white;">
                        <p style="margin: 0; font-size: 0.85em; color: #888;">
                            <i class="fa-regular fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($n['date_creation'])) ?>
                        </p>
                        <p style="margin: 8px 0 0 0; color: #444; font-size: 1.05em;"><?= htmlspecialchars($n['message']) ?></p>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #777;">
                <i class="fa-regular fa-bell-slash" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                <p>Vous n'avez aucune notification pour le moment.</p>
            </div>
        <?php endif; ?>
        
    </main>
</body>
</html>