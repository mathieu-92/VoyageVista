<?php
// --- PARTIE BACKEND (Logique et requêtes) ---
session_start();
require_once 'config.php';

// Récupération des notifications non lues si l'utilisateur est connecté
$notif_count = 0;
if (isset($_SESSION['id_utilisateur'])) {
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE id_utilisateur = ? AND lue = 0");
    $stmt_count->execute([$_SESSION['id_utilisateur']]);
    $notif_count = $stmt_count->fetchColumn();
}

// Requête optimisée : on récupère les destinations AVEC un prix de départ dynamique
// On utilise un LEFT JOIN sur la table 'vol' pour récupérer le prix minimum
$sql = "
    SELECT d.*, MIN(v.prix) as prix_depart 
    FROM destination d
    LEFT JOIN vol v ON d.id_destination = v.id_destination 
    GROUP BY d.id_destination
    LIMIT 6
";
$stmt = $pdo->query($sql);
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- PARTIE FRONTEND (Affichage HTML) ---
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVista - Planifiez. Explorez. Vivez.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* =========================================
           STYLE GLOBAL ET RÉINITIALISATION (À adapter selon ton style.css de base)
        ========================================= */
        /* --- CSS POUR LE MENU PROFIL DÉROULANT --- */

.user-profile-menu {
    position: relative; /* Indispensable pour positionner le menu en dessous */
    display: inline-block;
}

.profile-trigger {
    display: flex;
    align-items: center;
    gap: 8px; /* Espace entre l'icône, le prénom et la flèche */
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 20px;
    transition: background 0.3s;
}

.profile-trigger:hover {
    background-color: #f8f9fa;
}

.profile-trigger i {
    font-size: 1.3em;
    color: #007BFF;
}

.profile-trigger span {
    font-weight: 500;
    color: #333;
}

.profile-trigger .arrow {
    font-size: 0.8em;
    color: #888;
}

/* Le contenu du menu (caché par défaut) */
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background-color: #ffffff;
    min-width: 180px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    border-radius: 8px;
    padding: 8px 0;
    z-index: 1000;
    margin-top: 5px;
}

<<<<<<< HEAD
/* Affichage au survol */
.user-profile-menu:hover .dropdown-content {
    display: block;
}
=======
    /* 2. Le bas des cartes (Séparation du prix et du bouton) */
    .card-content {
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Permet au contenu de remplir la carte */
    }
    .desc {
        flex-grow: 1; /* Pousse le footer tout en bas */
        margin-bottom: 15px;
    }
    /* --- CORRECTION DESIGN : ESPACEMENT DU TITRE PRINCIPAL --- */
    
    .hero-text {
        text-align: center;
        margin-bottom: 35px !important; /* Décolle tout le bloc de texte des boutons (Vols, Hôtels...) */
    }

    .hero-text h2 {
        margin-bottom: 15px !important; /* Décolle le grand titre "Planifiez..." du petit sous-titre */
        color: #fff; /* Tu peux mettre #fff (blanc) si ton image de fond est trop sombre */
        text-shadow: 0px 2px 5px rgba(255,255,255,0.3); /* Légère lueur pour améliorer la lecture */
    }

    .hero-text p {
        margin: 0;
        font-size: 1.1em;
        color: #555;
    }
    .card-footer {
        display: flex;
        justify-content: space-between; /* Éloigne le prix (à gauche) du bouton (à droite) */
        align-items: center; /* Centre verticalement */
        border-top: 1px solid #eee; /* Petite ligne de séparation propre */
        padding-top: 15px;
        margin-top: auto; /* Force le footer à rester collé en bas de la carte */
    }
>>>>>>> 67d014e86d2e645da4578480909b97d11a5835ad

.dropdown-content a {
    color: #333;
    padding: 10px 16px;
    text-decoration: none;
    display: block;
    font-size: 0.95em;
    transition: background 0.2s;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.dropdown-content .logout {
    color: #dc3545; /* Couleur rouge pour la déconnexion */
}

.dropdown-content .divider {
    height: 1px;
    background-color: #eee;
    margin: 5px 0;
}
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }

        /* =========================================
           MENU PRINCIPAL (NOUVEAU)
        ========================================= */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .main-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .main-menu a:hover {
            color: #007BFF;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* =========================================
           STYLE DE LA BARRE DE RECHERCHE (PILULE)
        ========================================= */
        .hero-search {
            text-align: center;
            padding: 50px 20px;
            background: linear-gradient(to right, #007BFF, #00b4db);
            color: white;
        }

        .search-container {
            text-align: center;
            margin-top: 20px;
        }

        .search-tabs {
            margin-bottom: 10px;
        }

        .search-tabs button {
            background: transparent;
            border: none;
            color: white;
            font-size: 1em;
            padding: 10px 15px;
            cursor: pointer;
            opacity: 0.7;
        }

        .search-tabs button.active {
            opacity: 1;
            font-weight: bold;
            border-bottom: 2px solid white;
        }

        .search-bar {
            display: inline-flex !important;
            align-items: center !important;
            background-color: white !important;
            padding: 8px 10px !important;
            border-radius: 50px !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
            margin: 0 auto !important;
        }

        .search-bar .input-group {
            display: flex;
            align-items: center !important;
            background-color: transparent !important;
            padding: 0 15px !important;
            height: 40px !important;
            border-right: 1px solid #ddd;
        }

        .search-bar .input-group:nth-last-child(2) {
            border-right: none !important;
        }

        .search-bar .input-group i {
            color: #007BFF !important;
            font-size: 1.2em !important;
            margin-right: 10px !important;
            margin-top: 0 !important;
        }

        .search-bar input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-family: inherit !important;
            color: #333 !important;
            font-size: 0.95em !important;
        }

        .search-bar input[type="date"] {
            text-transform: uppercase;
            font-size: 0.85em !important;
            color: #555 !important;
            cursor: pointer;
        }

        .btn-search {
            height: 45px !important;
            padding: 0 25px !important;
            border: none !important;
            background-color: #28a745 !important;
            color: white !important;
            font-weight: bold !important;
            border-radius: 30px !important;
            cursor: pointer !important;
            transition: background 0.3s;
            margin-left: 5px;
        }

        .btn-search:hover {
            background-color: #218838 !important;
        }

        /* =========================================
           CARTES DE DESTINATIONS
        ========================================= */
        .content-layout {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .view-all {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .view-all:hover {
            color: #0056b3;
        }

        .view-all i {
            transition: transform 0.3s ease;
        }

        .view-all:hover i {
            transform: translateX(6px);
        }

        .grid-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-content h3 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .country {
            color: #777;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .desc {
            flex-grow: 1;
            margin-bottom: 15px;
            color: #555;
            font-size: 0.95em;
            line-height: 1.4;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: auto;
        }

        .price strong {
            font-size: 1.2em;
            color: #28a745;
        }

        .card-footer .btn-primary {
            background-color: #007BFF !important;
            color: white;
            border-radius: 20px !important;
            padding: 8px 18px !important;
            font-size: 0.9em !important;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }

        .card-footer .btn-primary:hover {
            background-color: #0056b3 !important;
            transform: scale(1.05);
        }
        
        .btn-primary {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
        }
        
        .btn-outline {
            display: inline-block;
            text-decoration: none;
            color: #007BFF;
            border: 1px solid #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">
            <a href="index.php">
                <img src="image/logo.png" alt="VoyageVista Logo" style="height: 70px;">
            </a>
        </div>

        
        <div class="user-actions">
            <?php if(isset($_SESSION['id_utilisateur'])): ?>
                <div class="notif-bell">
                    <a href="notifications.php" style="position: relative; color: #333; text-decoration: none;">
                        <i class="fa-regular fa-bell" style="font-size: 1.2em;"></i>
                        <?php if($notif_count > 0): ?>
                            <span style="position: absolute; top: -5px; right: -8px; background: #dc3545; color: white; border-radius: 50%; padding: 1px 5px; font-size: 0.6em; font-weight: bold;">
                                <?= htmlspecialchars($notif_count) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
                
        
                 <div class="user-profile-menu">
                    <div class="profile-trigger">
                        <i class="fa-solid fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['prenom']) ?></span>
                        <i class="fa-solid fa-chevron-down arrow"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="profil.php">Mon Profil</a>
                        <a href="gestion_reservations.php">Mes Réservations</a>
                        <div class="divider"></div>
                        <a href="deconnexion.php" class="logout">Déconnexion</a>
                    </div>
                </div>
                <a href="panier.php" class="btn-primary panier-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <?php if(!empty($_SESSION['panier'])): ?>
                        <span class="cart-badge" style="background:#dc3545; border-radius:50%; padding:2px 6px; font-size:0.8em;"><?= count($_SESSION['panier']) ?></span>
                    <?php endif; ?>
                </a>

            <?php else: ?>
                <a href="connexion.php" class="btn-outline">Se connecter</a>
                <a href="inscription.php" class="btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero-search">
        <div class="hero-text">
            <h2>Planifiez. Explorez. Vivez.</h2>
          
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

                <div class="input-group" id="container-origine">
                    <i class="fa-solid fa-plane-departure"></i>
                    <input type="text" name="origine" id="input-origine" placeholder="De quelle ville ?">
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-map-marker-alt"></i>
                    <input type="text" name="destination" id="input-dest" placeholder="Où voulez-vous aller ?" required>
                </div>
                <div class="input-group">
                    <input type="date" name="depart" required>
                </div>
                <div class="input-group" id="container-retour">
                    <input type="date" name="retour">
                </div>
                <div class="input-group" id="container-voyageurs" style="display: none; max-width: 100px;">
                    <i class="fa-solid fa-user-group"></i>
                    <input type="number" name="voyageurs" placeholder="Pers." min="1" value="2" style="width: 50px !important;">
                </div>

                <button type="submit" class="btn-search">Rechercher</button>
            </form>
        </div>
    </section>

    <main class="content-layout">

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Prestataire'): ?>
            <div class="espace-pro-banner" style="text-align: center; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 8px; border: 1px dashed #ccc;">
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
                <?php if(!empty($destinations)): ?>
                    <?php foreach($destinations as $dest): ?>
                        <div class="card">
                          <img src="image/<?= htmlspecialchars($dest['image_illustration'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($dest['ville']) ?>">  
                            <div class="card-content">
                                <h3><?= htmlspecialchars($dest['ville']) ?></h3>
                                <p class="country"><?= htmlspecialchars($dest['pays']) ?></p>
                                <p class="desc"><?= htmlspecialchars($dest['description_courte']) ?></p>
                                <div class="card-footer">
                                    <span class="price">
                                        À partir de 
                                        <strong>
                                            <?= !empty($dest['prix_depart']) ? htmlspecialchars($dest['prix_depart']) . '€' : 'N/A' ?>
                                        </strong>/pers
                                    </span>
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

            let origineContainer = document.getElementById('container-origine');
            let destInput = document.getElementById('input-dest');
            let retourContainer = document.getElementById('container-retour');
            let voyageursContainer = document.getElementById('container-voyageurs');

            if (type === 'vols') {
                origineContainer.style.display = "flex";
                destInput.placeholder = "Où voulez-vous aller ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "none";
            } else if (type === 'hotels') {
                origineContainer.style.display = "none";
                destInput.placeholder = "Dans quelle ville ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "flex";
            } else if (type === 'sejours') {
                origineContainer.style.display = "none";
                destInput.placeholder = "Destination du séjour ?";
                retourContainer.style.display = "flex";
                voyageursContainer.style.display = "flex";
            } else if (type === 'activites') {
                origineContainer.style.display = "none";
                destInput.placeholder = "Où cherchez-vous une activité ?";
                retourContainer.style.display = "none"; 
                voyageursContainer.style.display = "flex";
            }
        }
    </script>
</body>
</html>