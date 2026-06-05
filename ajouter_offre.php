<?php
session_start();
// Redirection si l'utilisateur n'est pas connecté ou n'est pas un prestataire
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'Prestataire') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une offre - VoyageVista</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f7f6; font-family: Arial, sans-serif; margin: 0;">
    
    <header class="top-nav" style="background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: left;">
        <a href="index.php" style="text-decoration: none; color: #007BFF; font-weight: bold;"><i class="fa-solid fa-arrow-left"></i> Retour à l'accueil</a>
    </header>

    <main style="max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #333; margin-bottom: 25px;">Publier une nouvelle destination</h2>
        
        <form action="traitement_ajout_offre.php" method="POST" enctype="multipart/form-data">
            
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Ville *</label>
                    <input type="text" name="ville" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Pays *</label>
                    <input type="text" name="pays" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Code Postal *</label>
                    <input type="number" name="codepostal" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Code Aéroport (ex: FCO) *</label>
                    <input type="text" name="code_aeroport" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Description courte</label>
                <textarea name="description_courte" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: bold; color: #555; display:block; margin-bottom:5px;">Image d'illustration *</label>
                <div id="drop-zone" style="width: 100%; height: 150px; padding: 25px; display: flex; align-items: center; justify-content: center; text-align: center; cursor: pointer; color: #007BFF; border: 2px dashed #007BFF; border-radius: 10px; background-color: #f8f9fa; transition: all 0.3s; box-sizing: border-box;">
                    <span id="drop-zone-prompt">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2em; display: block; margin-bottom: 10px;"></i> 
                        Glissez l'image ici ou cliquez pour choisir
                    </span>
                    <input type="file" name="image_upload" id="file-input" accept="image/jpeg, image/png, image/webp" required style="display: none;">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em; background-color: #007BFF; border: none; border-radius: 5px; color: white; cursor: pointer; font-weight: bold; transition: 0.3s;">
                Mettre en ligne
            </button>
        </form>
    </main>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const promptText = document.getElementById('drop-zone-prompt');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.backgroundColor = '#e8f0fe';
            dropZone.style.borderStyle = 'solid';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.backgroundColor = '#f8f9fa';
            dropZone.style.borderStyle = 'dashed';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateThumbnail(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                updateThumbnail(fileInput.files[0]);
            }
        });

        function updateThumbnail(file) {
            dropZone.style.backgroundColor = '#f8f9fa';
            dropZone.style.borderStyle = 'solid';
            dropZone.style.borderColor = '#28a745';
            promptText.innerHTML = `<i class="fa-solid fa-check-circle" style="color: #28a745; font-size: 2em; display: block; margin-bottom: 10px;"></i> Fichier sélectionné : <br><strong style="color: #333;">${file.name}</strong>`;
        }
    </script>
</body>
</html>