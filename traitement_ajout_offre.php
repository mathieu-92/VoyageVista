<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// 1. SÉCURITÉ : On vérifie que c'est bien un Prestataire qui essaie d'ajouter une offre
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Prestataire') {
    header('Location: index.php');
    exit();
}

// 2. RÉCUPÉRATION DES DONNÉES DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On nettoie les espaces inutiles avec trim()
    $ville = trim($_POST['ville']);
    $pays = trim($_POST['pays']);
    $description = trim($_POST['description_courte']);
    $image = trim($_POST['image_illustration']);
    
    // Si le prestataire n'a pas mis d'image, on force l'image par défaut
    if (empty($image)) {
        $image = 'default.jpg'; 
    }

    // 3. VÉRIFICATION ET INSERTION
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
            
            // Si ça marche, on le renvoie sur le dashboard avec un message de succès
            header('Location: dashboard_prestataire.php?msg=ajout_succes');
            exit();

        } catch (\PDOException $e) {
            // S'il y a un bug SQL, on le renvoie sur le formulaire avec une erreur
            header('Location: ajouter_offre.php?erreur=sql');
            exit();
        }
    } else {
        // Si les champs obligatoires sont vides
        header('Location: ajouter_offre.php?erreur=vide');
        exit();
    }
} else {
    // Si quelqu'un accède à la page sans valider le formulaire
    header('Location: ajouter_offre.php');
    exit();
}
?>