<?php
session_start();
// Si déjà connecté, on redirige vers l'accueil
if(isset($_SESSION['id_utilisateur'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - VoyageVista</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .auth-page { background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: Arial, sans-serif; }
        .auth-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #444; }
        .input-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn-primary { width: 100%; padding: 12px; background-color: #007BFF; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; font-weight: bold; }
        .btn-primary:hover { background-color: #0056b3; }
        .auth-footer { text-align: center; margin-top: 20px; color: #777; }
        .auth-footer a { color: #007BFF; text-decoration: none; }
    </style>
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-header">
            <img src="image/logo.png" alt="VoyageVista" style="height: 60px;">
            <h2>Connexion</h2>
            <p>Heureux de vous revoir !</p>
        </div>

        <?php if(isset($_GET['succes'])): ?>
            <p style="color: #2ecc71; text-align:center; margin-bottom:15px; font-weight:bold;">Compte créé avec succès ! Connectez-vous.</p>
        <?php endif; ?>

        <form action="traitement_connexion.php" method="POST" class="auth-form">
            <div class="input-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="mail" placeholder="votre@email.com" required>
            </div>

            <div class="input-group">
                <label><i class="fa-solid fa-lock"></i> Mot de passe</label>
                <input type="password" name="mdp" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary">Se connecter</button>
        </form>

        <div class="auth-footer">
            <p>Nouveau ici ? <a href="inscription.php">Créer un compte</a></p>
        </div>
    </div>

</body>
</html>