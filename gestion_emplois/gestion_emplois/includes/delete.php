<?php
require_once 'db.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];

    // Map the 'type' to the actual database table names
    $allowed_tables = [
        'formateur' => 'formateurs',
        'ressource' => 'ressources',
        'module' => 'modules',
        'groupe' => 'groupes'
    ];

    if (array_key_exists($type, $allowed_tables)) {
        $table = $allowed_tables[$type];
        try {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirect back to the page you came from
            header("Location: ../pages/" . $type . "s.php?status=deleted");
            exit();
        } catch (PDOException $e) {
            die("Erreur de suppression : " . $e->getMessage());
        }
    }
}
header("Location: ../pages/dashboard.php");
?>