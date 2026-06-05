<?php
session_start();
require_once 'config.php';

// Sécurité : On vérifie que c'est bien un Prestataire
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Prestataire') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupération de toutes les données texte
    $ville = trim($_POST['ville']);
    $pays = trim($_POST['pays']);
    $description_courte = trim($_POST['description_courte']);
    $codepostal = isset($_POST['codepostal']) ? (int)$_POST['codepostal'] : 0;
    $code_aeroport = isset($_POST['code_aeroport']) ? trim($_POST['code_aeroport']) : '';
    
    $nom_image_bdd = 'default.jpg'; 

    // 2. GESTION DE L'UPLOAD DE L'IMAGE
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        
        $tmp_name = $_FILES['image_upload']['tmp_name'];
        $name = basename($_FILES['image_upload']['name']);
        
        // On remplace juste les espaces et caractères bizarres pour éviter les bugs, 
        // mais on garde le nom intact (ex: Rome.jpg reste Rome.jpg)
        $nom_final = preg_replace("/[^a-zA-Z0-9.-]/", "_", $name);
        
        $dossier_cible = 'image/';
        $chemin_final = $dossier_cible . $nom_final;
        
        if (move_uploaded_file($tmp_name, $chemin_final)) {
            $nom_image_bdd = $nom_final; 
        }
    }

    // 3. ENREGISTREMENT DANS LA BASE DE DONNÉES
    try {
        // On insère dynamiquement les variables $codepostal et $code_aeroport
        $sql = "INSERT INTO destination (ville, pays, description_courte, image_illustration, codepostal, code_aeroport) 
                VALUES (:ville, :pays, :desc, :img, :cp, :aeroport)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ville' => $ville,
            ':pays' => $pays,
            ':desc' => $description_courte,
            ':img' => $nom_image_bdd,
            ':cp' => $codepostal,
            ':aeroport' => $code_aeroport
        ]);
        
        header('Location: index.php');
        exit();

    } catch (\PDOException $e) {
        die("Erreur PDO lors de l'ajout de l'offre : " . $e->getMessage());
    }
    
} else {
    header('Location: ajouter_offre.php');
    exit();
}
?>