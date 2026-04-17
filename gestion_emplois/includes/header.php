<?php
// Get current page name for dynamic active states
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartSchedule - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar py-4">
            <div class="text-center mb-4 px-3">
                 <a href="dashboard.php" class="d-block text-decoration-none">
                 <img src="../assets/img/logo.png" alt="Logo ofppt" class="img-fluid" style="max-height: 65px; object-fit: contain;">
                 </a>
            </div>
            <ul class="nav flex-column px-2">
                <li class="nav-item"><a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page == 'formateurs.php' ? 'active' : '' ?>" href="formateurs.php"><i class="bi bi-person-badge me-2"></i> Formateurs</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page == 'groupes_modules.php' ? 'active' : '' ?>" href="groupes_modules.php"><i class="bi bi-collection me-2"></i> Groupes & Modules</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page == 'ressources.php' ? 'active' : '' ?>" href="ressources.php"><i class="bi bi-door-open me-2"></i> Ressources</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page == 'emplois.php' ? 'active' : '' ?>" href="emplois.php"><i class="bi bi-grid-3x3-gap me-2"></i> Emplois du temps</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_page == 'conflits.php' ? 'active' : '' ?>" href="conflits.php"><i class="bi bi-exclamation-triangle me-2"></i> Conflits</a></li>
            </ul>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="content-area">