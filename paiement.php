<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// 1. Sécurité : Si le panier est vide, rien à faire ici, on retourne à l'accueil
if (empty($_SESSION['panier'])) {
    header('Location: index.php');
    exit();
}

// Calcul du total pour l'affichage
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix_estime'] * $item['voyageurs'];
}

// 2. Traitement du formulaire de paiement quand il est soumis
$message_succes = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carte_nom'])) {
    
    // On vérifie une dernière fois que l'utilisateur est bien connecté avant d'insérer
    if (isset($_SESSION['id_utilisateur'])) {
        
        try {
            // On commence une transaction PDO pour être sûr que toutes les insertions fonctionnent ensemble
            $pdo->beginTransaction();

            // Pour chaque voyage dans le panier, on va créer une ligne dans la table Reservation
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
                
                // Optionnel : Si tu voulais lier à un hôtel ou un vol via tes tables Contenir_... 
                // Tu récupérerais l'ID de la réservation ici avec $id_reservation = $pdo->lastInsertId();
            }

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
    <title>Paiement - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-nav">
        <h1>VoyageVista</h1>
        <a href="index.php" class="btn-outline">Retour à l'accueil</a>
    </header>

    <main style="padding: 40px; max-width: 600px; margin: auto;">
        
        <?php if (!empty($message_succes)): ?>
            <div style="background: #D4EDDA; color: #155724; padding: 20px; border-radius: 8px; text-align: center;">
                <h2><?= $message_succes ?></h2>
                <p>Retrouvez vos alertes dans vos notifications.</p>
                <a href="index.php" class="btn-primary" style="display:inline-block; margin-top:15px;">Retourner à l'accueil</a>
            </div>

        <?php elseif (!isset($_SESSION['id_utilisateur'])): ?>
            <div style="background: #FFF3CD; color: #856404; padding: 25px; border-radius: 8px; text-align: center; border: 1px solid #ffeeba;">
                <h2>🔒 Connexion obligatoire</h2>
                <p>Pour finaliser votre réservation et procéder au paiement de vos <strong><?= $total ?> €</strong> de voyage, vous devez posséder un compte VoyageVista.</p>
                <br>
                <div style="display: flex; justify-content: space-around;">
                    <a href="connexion.php" class="btn-primary" style="padding: 10px 20px;">Se connecter</a>
                    <a href="inscription.php" class="btn-outline" style="padding: 10px 20px;">Créer un compte</a>
                </div>
            </div>

        <?php else: ?>
            <h2>💳 Sécurisation du paiement</h2>
            <p>Utilisateur connecté : <strong><?= htmlspecialchars($_SESSION['prenom']) ?> <?= htmlspecialchars($_SESSION['nom']) ?></strong></p>
            
            <div style="background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3>Récapitulatif du montant : <span style="color: #2ECC71;"><?= $total ?> €</span></h3>
            </div>

            <form action="paiement.php" method="POST" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <label for="carte_nom">Nom sur la carte :</label><br>
                <input type="text" id="carte_nom" name="carte_nom" value="<?= htmlspecialchars($_SESSION['nom']) ?> <?= htmlspecialchars($_SESSION['prenom']) ?>" required style="width:100%; padding:8px; margin-top:5px;"><br><br>

                <label for="carte_num">Numéro de carte :</label><br>
                <input type="text" id="carte_num" placeholder="4532 71XX XXXX XXXX" maxlength="16" required style="width:100%; padding:8px; margin-top:5px;"><br><br>

                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label for="carte_date">Date d'expiration :</label>
                        <input type="text" id="carte_date" placeholder="MM/AA" maxlength="5" required style="width:100%; padding:8px; margin-top:5px;">
                    </div>
                    <div style="flex: 1;">
                        <label for="carte_cvv">CVV :</label>
                        <input type="password" id="carte_cvv" placeholder="123" maxlength="3" required style="width:100%; padding:8px; margin-top:5px;">
                    </div>
                </div>
                <br><br>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em;">
                    Confirmer et Payer <?= $total ?> €
                </button>
            </form>
        <?php endif; ?>

    </main>
</body>
</html>