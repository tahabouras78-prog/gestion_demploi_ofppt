<?php
$host = 'localhost';
$dbname = 'gestion_emplois_db';
$username = 'root'; // Modifie si nécessaire
$password = '';     // Modifie si nécessaire

try {
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; font-family:sans-serif;'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>");
}

// Fonction utilitaire pour nettoyer les entrées
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>