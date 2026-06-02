<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-header">
            <img src="image/logo.png" alt="VoyageVista" style="height: 60px;">
            <h2>Créer un compte</h2>
            <p>Rejoignez l'aventure VoyageVista</p>
        </div> 

        <form action="traitement_inscription.php" method="POST" class="auth-form">
            <div class="input-row">
                <div class="input-group">
                    <label><i class="fa-solid fa-user"></i> Nom</label>
                    <input type="text" name="nom" placeholder="Nom" required>
                </div>
                <div class="input-group">
                    <label><i class="fa-solid fa-user"></i> Prénom</label>
                    <input type="text" name="prenom" placeholder="Prénom" required>
                </div>
            </div>
            
            <div class="input-group">
                <label><i class="fa-solid fa-calendar"></i> Date de naissance</label>
                <input type="date" name="datedenaissance" required>
            </div>
            
            <div class="input-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="mail" placeholder="votre@email.com" required>
            </div>

            <div class="input-group">
                <label><i class="fa-solid fa-lock"></i> Mot de passe</label>
                <input type="password" name="mdp" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <p style="margin-bottom: 8px; color: #555;"><strong>Je souhaite créer un compte :</strong></p>
                
                <label style="margin-right: 15px; cursor: pointer;">
                    <input type="radio" name="role" value="Client" checked> Voyageur
                </label>
                
                <label style="cursor: pointer;">
                    <input type="radio" name="role" value="Prestataire"> Partenaire Pro
                </label>
            </div>
            <button type="submit" class="btn-primary full-width">S'inscrire</button>
        </form>

        <div class="auth-footer">
            <p>Déjà membre ? <a href="connexion.php">Connectez-vous ici</a></p>
        </div>
    </div>

</body>
</html>