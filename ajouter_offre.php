<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// 1. SÉCURITÉ : Vérification du rôle
// Si l'utilisateur n'est pas connecté OU n'est pas un Prestataire, on le vire !
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Prestataire') {
    header('Location: index.php');
    exit();
}

$message = "";

// 2. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ville = trim($_POST['ville']);
    $pays = trim($_POST['pays']);
    $description = trim($_POST['description_courte']);
    $image = trim($_POST['image_illustration']);
    
    // Si l'image est vide, on met une image par défaut
    if (empty($image)) {
        $image = 'default.jpg'; 
    }

    // Vérification que les champs requis ne sont pas vides
    if (!empty($ville) && !empty($pays)) {
        try {
            $sql = "INSERT INTO destination (ville, pays, description_courte, image_illustration) 
                    VALUES (:ville, :pays, :desc, :img)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ville' => $ville,
                ':pays' => $pays,
                ':desc' => $description,
                ':img' => $image
            ]);
            
            $message = "<div class='alert-success'><i class='fa-solid fa-circle-check'></i> Nouvelle destination ajoutée au catalogue avec succès !</div>";
        } catch (\PDOException $e) {
            $message = "<div class='alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Erreur lors de l'ajout : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Veuillez remplir la ville et le pays.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Offre - Espace Pro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-top: 5px solid #007BFF;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #444;
            margin-bottom: 8px;
        }
        .form-group input[type="text"], 
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus, 
        .form-group textarea:focus {
            border-color: #007BFF;
            outline: none;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border-left: 5px solid #dc3545;
        }
        .pro-badge {
            background: #007BFF;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.7em;
            vertical-align: middle;
            margin-left: 10px;
        }
    </style>
</head>
<body style="background-color: #f4f7f6;">

    <header class="top-nav" style="background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="VoyageVista Logo" style="height: 50px;"></a>
        </div>
        <div class="user-actions">
            <span style="color: #555; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['prenom']) ?> <span class="pro-badge">PRO</span>
            </span>
            <a href="index.php" class="btn-outline" style="margin-left: 15px;">Retour au site</a>
        </div>
    </header>

    <main class="admin-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fa-solid fa-map-location-dot" style="font-size: 3em; color: #007BFF; margin-bottom: 15px;"></i>
            <h2 style="color: #333; margin: 0;">Publier une nouvelle destination</h2>
            <p style="color: #777; margin-top: 5px;">Enrichissez le catalogue VoyageVista.</p>
        </div>

        <?= $message ?>

        <form action="ajouter_offre.php" method="POST">
            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label for="ville">Ville <span style="color: red;">*</span></label>
                    <input type="text" id="ville" name="ville" placeholder="Ex: Tokyo" required>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label for="pays">Pays <span style="color: red;">*</span></label>
                    <input type="text" id="pays" name="pays" placeholder="Ex: Japon" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description_courte">Description courte</label>
                <textarea id="description_courte" name="description_courte" rows="3" placeholder="Une brève description pour donner envie aux voyageurs..."></textarea>
            </div>

            <div class="form-group">
                <label for="image_illustration">Nom de l'image (optionnel)</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-regular fa-image" style="color: #888; font-size: 1.5em;"></i>
                    <input type="text" id="image_illustration" name="image_illustration" placeholder="Ex: tokyo.jpg">
                </div>
                <small style="color: #888;">L'image doit être placée dans le dossier 'image/'. Par défaut : default.jpg</small>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em; background-color: #007BFF; border: none; margin-top: 10px;">
                <i class="fa-solid fa-paper-plane"></i> Mettre en ligne l'offre
            </button>
        </form>
    </main>

</body>
</html>