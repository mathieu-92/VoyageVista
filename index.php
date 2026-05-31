<?php
// Démarrage de la session
session_start();

// Inclusion de la connexion à la base
require_once 'config.php';

// Requête pour récupérer 6 destinations
$sql = "SELECT * FROM destination LIMIT 6";
$stmt = $pdo->query($sql);
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVista - Planifiez. Explorez. Vivez.</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<a href="deconnexion.php"></a>
    <header class="top-nav">
        <div class="logo">
            <img src="image/logo.png" alt="VoyageVista Logo" style="height: 70px;">
        </div>
        
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="notif-bell">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge"></span>
                </div>
                
                <div class="user-profile-menu">
                    <div class="profile-trigger">
                        <i class="fa-solid fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                        <i class="fa-solid fa-chevron-down arrow"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="profil.php">Mon Profil</a>
                        <a href="mes_reservations.php">Mes Réservations</a>
                        <div class="divider"></div>
                        <a href="deconnexion.php" class="logout">Déconnexion</a>
                    </div>
                </div>
                
                <a href="panier.php" class="btn-primary panier-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <?php if(!empty($_SESSION['panier'])): ?>
                        <span class="cart-badge"><?= count($_SESSION['panier']) ?></span>
                    <?php endif; ?>
                </a>

            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
                <a href="inscription.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </header>
<style>
    /* Correction de l'alignement pour la barre de recherche de l'accueil */
    .search-bar .input-group {
        display: flex !important;
        align-items: center !important;
        gap: 8px; /* Laisse un petit espace propre entre l'icône et le champ */
    }

    .search-bar .input-group i {
        font-size: 1.2em;
        color: #555;
        margin: 0; /* Supprime les marges parasites qui décalent l'icône vers le haut */
    }
</style>
    <section class="hero-search">
        <div class="hero-text">
            <h2>Planifiez. Explorez. Vivez.</h2>
            <p>Le voyage de vos rêves commence ici.</p>
        </div>
        
        <div class="search-container">
            <div class="search-tabs">
                <button type="button" class="active" onclick="changeTab('vols', this)"><i class="fa-solid fa-plane"></i> Vols</button>
                <button type="button" onclick="changeTab('hotels', this)"><i class="fa-solid fa-hotel"></i> Hôtels</button>
                <button type="button" onclick="changeTab('sejours', this)"><i class="fa-solid fa-umbrella-beach"></i> Séjours</button>
                <button type="button" onclick="changeTab('activites', this)"><i class="fa-solid fa-person-running"></i> Activités</button>
            </div>
            <form action="traitement_recherche.php" method="GET" class="search-bar">
                
                <input type="hidden" name="type_recherche" id="type_recherche" value="vols">

                <div class="input-group">
                    <i class="fa-solid fa-map-marker-alt"></i>
                    <input type="text" name="destination" id="input-dest" placeholder="Où voulez-vous aller ?" required>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-calendar-alt"></i>
                    <input type="date" name="depart" required>
                </div>
                <div class="input-group" id="container-retour">
                    <i class="fa-solid fa-calendar-alt"></i>
                    <input type="date" name="retour">
                </div>
                <div class="input-group" id="container-voyageurs" style="display: none; max-width: 100px;">
                    <i class="fa-solid fa-user-group"></i>
                    <input type="number" name="voyageurs" placeholder="Pers." min="1" value="2" style="padding-left: 5px;">
                </div>

                <button type="submit" class="btn-search">Rechercher</button>
            </form>
        </div>
    </section>

    <main class="content-layout">

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Prestataire'): ?>
            <div style="text-align: center; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 8px; border: 1px dashed #ccc;">
                <p style="margin-bottom: 10px; color: #555;"><strong>Espace Pro :</strong> Vous pouvez ajouter une nouvelle offre directement d'ici.</p>
                <a href="ajouter_offre.php" class="btn-primary" style="background-color: #28a745; border: none; margin-right: 10px;">
                    <i class="fa-solid fa-plus-circle"></i> Publier une annonce
                </a>
                <a href="dashboard_prestataire.php" class="btn-primary" style="background-color: #6c757d; border: none;">
                    <i class="fa-solid fa-toolbox"></i> Mon Dashboard
                </a>
            </div>
        <?php endif; ?>
        <section class="results-section">
            <div class="section-header">
                <h2>Explorez nos destinations populaires</h2>
                <a href="hebergements.php" class="view-all">Voir tout <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            
            <div class="grid-results">
                
                <?php if(count($destinations) > 0): ?>
                    <?php foreach($destinations as $dest): ?>
                        <div class="card">
                          <img src="image/<?= htmlspecialchars($dest['image_illustration']) ?>" alt="<?= htmlspecialchars($dest['ville']) ?>">  
                            <div class="card-content">
                                <h3><?= htmlspecialchars($dest['ville']) ?></h3>
                                <p class="country"><?= htmlspecialchars($dest['pays']) ?></p>
                                <p class="desc"><?= htmlspecialchars($dest['description_courte']) ?></p>
                                <div class="card-footer">
                                    <span class="price">À partir de <strong>299€</strong>/pers</span>
                                    <a href="details_offre.php?id=<?= $dest['id_destination'] ?>" class="btn-primary">Découvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-results">Le catalogue est en cours de mise à jour.</p>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <script>
        function changeTab(type, element) {
            let tabs = document.querySelectorAll('.search-tabs button');
            tabs.forEach(tab => tab.classList.remove('active'));
            element.classList.add('active');

            document.getElementById('type_recherche').value = type;

            let destInput = document.getElementById('input-dest');
            let retourContainer = document.getElementById('container-retour');
            let voyageursContainer = document.getElementById('container-voyageurs');

            if (type === 'vols') {
                destInput.placeholder = "Où voulez-vous aller ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "none";
            } else if (type === 'hotels') {
                destInput.placeholder = "Dans quelle ville ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "flex";
            } else if (type === 'sejours') {
                destInput.placeholder = "Destination du séjour ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "flex";
            } else if (type === 'activites') {
                destInput.placeholder = "Où cherchez-vous une activité ?";
                retourContainer.style.display = "none"; 
                voyageursContainer.style.display = "flex";
            }
        }
    </script>
</body>
</html>