<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-header">
            <img src="image/logo.png" alt="VoyageVista" style="height: 60px;">
            <h2>Connexion</h2>
            <p>Heureux de vous revoir !</p>
        </div>

        <?php if(isset($_GET['succes'])): ?>
            <p style="color: #2ecc71; text-align:center; margin-bottom:15px;">Compte créé avec succès ! Connectez-vous.</p>
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

            <button type="submit" class="btn-primary full-width">Se connecter</button>
        </form>

        <div class="auth-footer">
            <p>Nouveau ici ? <a href="inscription.php">Créer un compte</a></p>
        </div>
    </div>

</body>
</html>