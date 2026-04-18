<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $stmt = $pdo->prepare("INSERT INTO ressources (nom_salle, type_salle, capacite) VALUES (?, ?, ?)");
    $stmt->execute([cleanInput($_POST['nom_salle']), $_POST['type_salle'], $_POST['capacite']]);
    $msg = "<div class='alert alert-success alert-dismissible fade show'>Ressource ajoutée avec succès.</div>";
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM ressources WHERE id=?")->execute([$_GET['delete']]);
    header("Location: ressources.php");
    exit();
}

$ressources = $pdo->query("SELECT * FROM ressources")->fetchAll();

include '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Gestion des Salles & Ateliers</h2>
<?= $msg ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold"><i class="bi bi-door-open"></i> Ajouter une Ressource</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nom de la salle / atelier</label>
                        <input type="text" name="nom_salle" class="form-control" required placeholder="Ex: Salle 4, Labo Cisco...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type_salle" class="form-select" required>
                            <option value="Theorique">Salle Théorique</option>
                            <option value="Pratique">Atelier Pratique</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacité (Places)</label>
                        <input type="number" name="capacite" class="form-control" required min="1">
                    </div>
                    <button type="submit" name="save" class="btn btn-dark w-100 fw-bold">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Capacité</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ressources as $r): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($r['nom_salle']) ?></td>
                            <td><span class="badge bg-<?= $r['type_salle'] == 'Pratique' ? 'info text-dark' : 'secondary' ?>"><?= $r['type_salle'] ?></span></td>
                            <td><?= $r['capacite'] ?> places</td>
                            <td class="text-end">
                                <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette salle ? Cela supprimera les séances associées.');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($ressources)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Aucune ressource enregistrée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>