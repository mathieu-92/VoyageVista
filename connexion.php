<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Connexion à VoyageVista</h2>
    
    <form action="traitement_connexion.php" method="POST">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mdp">Mot de passe :</label>
        <input type="password" id="mdp" name="mdp" required><br><br>

        <button type="submit">Se connecter</button>
    </form>
    
    <p>Pas encore de compte ? <a href="inscription.php">S'inscrire ici</a></p>
</body>
</html>