<?php
require_once '../includes/db.php';

// Fetch quick statistics
$stats = [
    'formateurs' => $pdo->query("SELECT COUNT(*) FROM formateurs")->fetchColumn(),
    'groupes' => $pdo->query("SELECT COUNT(*) FROM groupes")->fetchColumn(),
    'modules' => $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn(),
    'ressources' => $pdo->query("SELECT COUNT(*) FROM ressources")->fetchColumn(),
    'sessions' => $pdo->query("SELECT COUNT(*) FROM emplois")->fetchColumn()
];

// Detect conflicts dynamically
$queryConflits = "
    SELECT COUNT(*) FROM emplois e1
    JOIN emplois e2 ON e1.id < e2.id 
    AND e1.jour = e2.jour 
    AND e1.heure_debut < e2.heure_fin 
    AND e1.heure_fin > e2.heure_debut 
    AND (e1.id_formateur = e2.id_formateur OR e1.id_ressource = e2.id_ressource)
";
$total_conflits = $pdo->query($queryConflits)->fetchColumn();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-gray-800">Tableau de Bord</h2>
    <a href="emplois.php" class="btn btn-primary shadow-sm"><i class="bi bi-plus-circle me-1"></i> Gérer les séances</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Formateurs</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['formateurs'] ?></div>
                    </div>
                    <div class="col-auto"><i class="bi bi-person-badge fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Groupes</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['groupes'] ?></div>
                    </div>
                    <div class="col-auto"><i class="bi bi-people fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Salles & Ateliers</div>
                        <div class="h5 mb-0 fw-bold text-gray-800"><?= $stats['ressources'] ?></div>
                    </div>
                    <div class="col-auto"><i class="bi bi-door-open fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-warning h-100 py-2 bg-<?= $total_conflits > 0 ? 'danger' : 'success' ?> text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-uppercase mb-1">État des Conflits</div>
                        <div class="h5 mb-0 fw-bold"><?= $total_conflits ?> Détecté(s)</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-exclamation-triangle fs-2 text-white"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">Vue d'ensemble du système</h6>
    </div>
    <div class="card-body">
        <p>Bienvenue dans l'interface d'administration. Vous avez actuellement <strong><?= $stats['sessions'] ?> séances</strong> programmées.</p>
        <p>Utilisez le menu latéral pour naviguer entre les différentes sections et gérer vos ressources pédagogiques efficacement.</p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>