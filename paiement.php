<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// 1. Sécurité : Si le panier est vide et qu'on n'a pas de message de succès, retour à l'accueil
if (empty($_SESSION['panier']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Calcul du total pour l'affichage
$total = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $total += $item['prix_estime'] * $item['voyageurs'];
    }
}

// 2. Traitement du formulaire de paiement quand il est soumis
$message_succes = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carte_nom'])) {
    
    // On vérifie une dernière fois que l'utilisateur est bien connecté avant d'insérer
    if (isset($_SESSION['id_utilisateur'])) {
        
        try {
            // On commence une transaction PDO pour être sûr que toutes les insertions fonctionnent ensemble
            $pdo->beginTransaction();

            // A. Enregistrement des réservations
            foreach ($_SESSION['panier'] as $item) {
                $sql = "INSERT INTO Reservation (
                            date_commande, prix_total_calcule, statut_paiement, notification, 
                            nb_bebe, nb_jeunes, nb_etudiant, nb_adultes, nb_seniors, 
                            date_debut_sejour, date_fin_sejour, id_utilisateur
                        ) VALUES (
                            NOW(), :prix_total, 'Payé', 1, 
                            0, 0, 0, :voyageurs, 0, 
                            :date_debut, :date_fin, :id_utilisateur
                        )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':prix_total' => $item['prix_estime'] * $item['voyageurs'],
                    ':voyageurs' => $item['voyageurs'],
                    ':date_debut' => $item['debut'],
                    ':date_fin' => $item['fin'],
                    ':id_utilisateur' => $_SESSION['id_utilisateur']
                ]);
            }

            // B. Création de la notification officielle pour l'utilisateur
            $msg = "Félicitations ! Votre paiement de " . $total . " € a été accepté. Votre séjour est confirmé.";
            // Remarque : adapte le nom de la table "notification" selon ce qui est dans ta base de données
            $sql_notif = "INSERT INTO notification (id_utilisateur, message, date_creation, lue) VALUES (:id_user, :msg, NOW(), 0)";
            $stmt_notif = $pdo->prepare($sql_notif);
            $stmt_notif->execute([
                ':id_user' => $_SESSION['id_utilisateur'],
                ':msg' => $msg
            ]);

            // Si tout est bon, on valide définitivement dans la base de données
            $pdo->commit();

            // On vide le panier puisque la commande est passée !
            $_SESSION['panier'] = [];
            $message_succes = "Votre paiement a été accepté ! Votre voyage est réservé. 🎉";

        } catch (\PDOException $e) {
            // En cas de bug, on annule tout pour ne pas avoir de données corrompues
            $pdo->rollBack();
            die("Erreur lors de l'enregistrement de la réservation : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-icons { font-size: 2.5em; color: #555; text-align: center; margin-bottom: 20px; }
        .card-icons i { margin: 0 10px; }
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
            <?php endif; ?>
            <a href="panier.php" class="btn-outline" style="color: #007BFF; border-color: #007BFF;">Retour au panier</a>
        </div>
    </header>

    <main class="payment-container">
        
        <?php if (!empty($message_succes)): ?>
            <div style="text-align: center;">
                <i class="fa-solid fa-circle-check" style="font-size: 5em; color: #28a745; margin-bottom: 20px;"></i>
                <h2 style="color: #28a745;"><?= $message_succes ?></h2>
                <p style="color: #666; font-size: 1.1em; margin-bottom: 30px;">Un reçu vous a été envoyé et une alerte a été ajoutée à vos notifications.</p>
                <a href="index.php" class="btn-primary" style="text-decoration: none; padding: 12px 25px;">Retourner à l'accueil</a>
            </div>

        <?php elseif (!isset($_SESSION['id_utilisateur'])): ?>
            <div style="text-align: center;">
                <i class="fa-solid fa-lock" style="font-size: 4em; color: #ffc107; margin-bottom: 20px;"></i>
                <h2 style="color: #333;">Connexion obligatoire</h2>
                <p style="color: #666; margin-bottom: 30px;">Pour finaliser votre réservation et procéder au paiement de <strong><?= $total ?> €</strong>, vous devez posséder un compte VoyageVista.</p>
                <div style="display: flex; justify-content: center; gap: 15px;">
                    <a href="connexion.php" class="btn-primary" style="text-decoration: none;">Se connecter</a>
                    <a href="inscription.php" class="btn-outline" style="text-decoration: none;">Créer un compte</a>
                </div>
            </div>

        <?php else: ?>
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #333; margin-bottom: 10px;"><i class="fa-solid fa-shield-halved" style="color: #28a745;"></i> Paiement Sécurisé</h2>
                <div style="background: #f8f9fa; border: 2px dashed #007BFF; padding: 15px; border-radius: 8px; display: inline-block;">
                    <span style="font-size: 1.2em; color: #555;">Montant à régler :</span>
                    <strong style="font-size: 1.5em; color: #333; margin-left: 10px;"><?= $total ?> €</strong>
                </div>
            </div>

            <div class="card-icons">
                <i class="fa-brands fa-cc-visa" style="color: #1a1f71;"></i>
                <i class="fa-brands fa-cc-mastercard" style="color: #eb001b;"></i>
                <i class="fa-brands fa-cc-amex" style="color: #002663;"></i>
            </div>

            <form action="paiement.php" method="POST" style="text-align: left;">
                <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">Nom sur la carte</label>
                <input type="text" name="carte_nom" value="<?= htmlspecialchars($_SESSION['prenom'] ?? '') ?>" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">

                <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">Numéro de carte</label>
                <input type="text" placeholder="4532 71XX XXXX XXXX" maxlength="19" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">

                <div style="display: flex; gap: 20px; margin-bottom: 25px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">Expiration</label>
                        <input type="text" placeholder="MM/AA" maxlength="5" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">CVV</label>
                        <input type="password" placeholder="123" maxlength="3" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.2em; background-color: #28a745; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fa-solid fa-lock"></i> Confirmer et Payer
                </button>
            </form>
        <?php endif; ?>

    </main>
</body>
</html>