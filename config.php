<?php
// Configuration des paramètres de la base de données
$host = 'localhost';
$db   = 'voyagevista'; // Remplace par le nom exact de ta base sur phpMyAdmin
$user = 'root';        // Par défaut sur WAMP/XAMPP
$pass = '';            // Laisse vide sur Windows, ou met 'root' si tu es sur Mac (MAMP)
$charset = 'utf8mb4';

// Configuration des options de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Active la gestion des erreurs et des exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupère les données sous forme de tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive l'émulation pour utiliser les vraies requêtes préparées
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Création de l'instance PDO (la connexion est établie ici)
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Décommente la ligne ci-dessous si tu veux tester la connexion dans ton navigateur, puis supprime-la
    // echo "Connexion réussie à VoyageVista !"; 
    
} catch (\PDOException $e) {
    // Si la connexion échoue, on arrête le script et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}