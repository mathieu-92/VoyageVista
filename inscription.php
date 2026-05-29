<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - VoyageVista</title>
    </head>
<body>
    <h2>Créer un compte VoyageVista</h2>
    
    <form action="traitement_inscription.php" method="POST">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mdp">Mot de passe :</label>
        <input type="password" id="mdp" name="mdp" required><br><br>

        <label for="datedenaissance">Date de naissance :</label>
        <input type="date" id="datedenaissance" name="datedenaissance" required><br><br>

        <label for="role">Je suis un :</label>
        <select id="role" name="role">
            <option value="Voyageur">Voyageur (pour réserver)</option>
            <option value="Prestataire">Prestataire / Admin (pour proposer des offres)</option>
        </select><br><br>

        <input type="checkbox" id="consentement" name="consentement" required>
        <label for="consentement">J'accepte les conditions d'utilisation</label><br><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html> 