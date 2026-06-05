<?php
session_start();
require_once 'config.php';

// SÉCURITÉ 1 : Vérifier que celui qui clique est bien un Administrateur
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Admin') {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:#dc3545;'>Accès refusé. Vous n'avez pas les droits.</h2>
            <a href='index.php' style='padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;'>Retour à l'accueil</a>
         </div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cible'])) {
    $id_cible = (int)$_POST['id_cible'];

    // SÉCURITÉ 2 : Empêcher l'administrateur de se retirer ses propres droits !
    if ($id_cible === (int)$_SESSION['id_utilisateur']) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2 style='color:#ffc107;'>Action impossible : Vous ne pouvez pas retirer vos propres droits.</h2>
                <a href='dashboard_admin.php' style='padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;'>Retour au dashboard</a>
             </div>");
    }

    try {
        // POINT GRILLE : Les permissions spéciales sont retirées correctement (On repasse le rôle à 'Client')
        $sql = "UPDATE utilisateur SET role = 'Client' WHERE id_utilisateur = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cible]);

        // Redirection vers le dashboard avec un message de succès
        header('Location: dashboard_admin.php?success=droits_retires');
        exit();

    } catch (\PDOException $e) {
        die("Erreur PDO lors du retrait des droits : " . $e->getMessage());
    }
} else {
    header('Location: dashboard_admin.php');
    exit();
}
?>