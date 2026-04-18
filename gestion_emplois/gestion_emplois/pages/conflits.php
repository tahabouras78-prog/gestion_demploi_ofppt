<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Advanced query to detect exact overlaps for a teacher or a room
$query = "
    SELECT 
        e1.id as id1, e2.id as id2, 
        e1.jour, e1.heure_debut, e1.heure_fin, 
        f.nom as prof, f.prenom, r.nom_salle, 
        IF(e1.id_formateur = e2.id_formateur, 'Double affectation Prof', 'Double réservation Salle') as cause
    FROM emplois e1
    JOIN emplois e2 ON e1.id < e2.id 
        AND e1.jour = e2.jour 
        AND e1.heure_debut < e2.heure_fin 
        AND e1.heure_fin > e2.heure_debut 
        AND (e1.id_formateur = e2.id_formateur OR e1.id_ressource = e2.id_ressource)
    LEFT JOIN formateurs f ON e1.id_formateur = f.id
    LEFT JOIN ressources r ON e1.id_ressource = r.id
";

$conflits = $pdo->query($query)->fetchAll();

if (isset($_GET['resolve'])) {
    $pdo->prepare("DELETE FROM emplois WHERE id=?")->execute([$_GET['resolve']]);
    header("Location: conflits.php");
    exit();
}

include '../includes/header.php';
?>

<div class="mb-4">
    <h2 class="fw-bold text-danger"><i class="bi bi-exclamation-octagon"></i> Analyse des Conflits</h2>
    <p class="text-muted">Le système détecte automatiquement si un formateur ou une salle est assigné(e) à deux groupes en même temps.</p>
</div>

<?php if (count($conflits) == 0): ?>
    <div class="alert alert-success p-5 shadow-sm text-center border-0 rounded-3">
        <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
        <h4 class="alert-heading mt-3 fw-bold">Excellent !</h4>
        <p class="mb-0 fs-5">Aucun conflit d'emploi du temps n'a été détecté dans le système.</p>
    </div>
<?php else: ?>
    <div class="card border-danger shadow-sm border-0">
        <div class="card-header bg-danger text-white fw-bold">
            <i class="bi bi-shield-exclamation"></i> Conflits nécessitant votre attention (<?= count($conflits) ?>)
        </div>
        <div class="card-body table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type d'erreur</th>
                        <th>Jour & Heure</th>
                        <th>Entité concernée</th>
                        <th>Résolution Rapide</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conflits as $c): ?>
                    <tr>
                        <td><span class="badge bg-danger fs-6"><?= $c['cause'] ?></span></td>
                        <td class="fw-bold"><?= $c['jour'] ?> <br> <small class="text-muted">(<?= substr($c['heure_debut'],0,5) ?> - <?= substr($c['heure_fin'],0,5) ?>)</small></td>
                        <td class="fw-bold fs-6">
                            <?= $c['cause'] == 'Double affectation Prof' ? "<i class='bi bi-person-x text-danger'></i> Prof : ".$c['prof']." ".$c['prenom'] : "<i class='bi bi-door-closed text-danger'></i> Salle : ".$c['nom_salle'] ?>
                        </td>
                        <td>
                            <a href="?resolve=<?= $c['id1'] ?>" class="btn btn-sm btn-outline-dark" title="Supprimer la 1ère séance">Supprimer Séance 1</a>
                            <a href="?resolve=<?= $c['id2'] ?>" class="btn btn-sm btn-outline-dark" title="Supprimer la 2ème séance">Supprimer Séance 2</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>