<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$msg = '';

// Add Group
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_groupe'])) {
    $stmt = $pdo->prepare("INSERT INTO groupes (nom_groupe, filiere) VALUES (?, ?)");
    $stmt->execute([cleanInput($_POST['nom_groupe']), cleanInput($_POST['filiere'])]);
    $msg = "<div class='alert alert-success alert-dismissible fade show'>Groupe ajouté avec succès.</div>";
}

// Delete Group
if (isset($_GET['del_groupe'])) {
    $pdo->prepare("DELETE FROM groupes WHERE id=?")->execute([$_GET['del_groupe']]);
    header("Location: groupes_modules.php");
    exit();
}

// Add Module
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_module'])) {
    $stmt = $pdo->prepare("INSERT INTO modules (nom_module, type_module, masse_horaire) VALUES (?, ?, ?)");
    $stmt->execute([cleanInput($_POST['nom_module']), $_POST['type_module'], $_POST['masse_horaire']]);
    $msg = "<div class='alert alert-success alert-dismissible fade show'>Module ajouté avec succès.</div>";
}

// Delete Module
if (isset($_GET['del_module'])) {
    $pdo->prepare("DELETE FROM modules WHERE id=?")->execute([$_GET['del_module']]);
    header("Location: groupes_modules.php");
    exit();
}

$groupes = $pdo->query("SELECT * FROM groupes")->fetchAll();
$modules = $pdo->query("SELECT * FROM modules")->fetchAll();

include '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Groupes & Modules</h2>
<?= $msg ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-success text-white fw-bold"><i class="bi bi-people"></i> Gestion des Groupes</div>
            <div class="card-body">
                <form method="POST" class="row g-2 mb-4">
                    <div class="col-md-5">
                        <input type="text" name="nom_groupe" class="form-control" placeholder="Nom (ex: DEV101)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="filiere" class="form-control" placeholder="Filière" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_groupe" class="btn btn-success w-100"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
                
                <ul class="list-group list-group-flush border">
                    <?php foreach ($groupes as $g): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-success"><?= htmlspecialchars($g['nom_groupe']) ?></strong> 
                            <small class="text-muted">(<?= htmlspecialchars($g['filiere']) ?>)</small>
                        </div>
                        <a href="?del_groupe=<?= $g['id'] ?>" class="text-danger" onclick="return confirm('Supprimer ce groupe ?');"><i class="bi bi-trash"></i></a>
                    </li>
                    <?php endforeach; ?>
                    <?php if(empty($groupes)): ?>
                        <li class="list-group-item text-center text-muted">Aucun groupe</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-info text-white fw-bold"><i class="bi bi-book"></i> Gestion des Modules</div>
            <div class="card-body">
                <form method="POST" class="row g-2 mb-4">
                    <div class="col-12">
                        <input type="text" name="nom_module" class="form-control" placeholder="Nom du module" required>
                    </div>
                    <div class="col-md-5">
                        <select name="type_module" class="form-select" required>
                            <option value="Theorique">Théorique</option>
                            <option value="Pratique">Pratique</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="masse_horaire" class="form-control" placeholder="Heures" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="add_module" class="btn btn-info text-white w-100"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
                
                <ul class="list-group list-group-flush border">
                    <?php foreach ($modules as $m): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?= htmlspecialchars($m['nom_module']) ?>
                            <span class="badge bg-<?= $m['type_module']=='Pratique' ? 'success' : 'primary' ?> ms-2"><?= $m['type_module'] ?></span>
                        </div>
                        <div>
                            <span class="badge bg-secondary rounded-pill me-2"><?= $m['masse_horaire'] ?>h</span>
                            <a href="?del_module=<?= $m['id'] ?>" class="text-danger" onclick="return confirm('Supprimer ce module ?');"><i class="bi bi-trash"></i></a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php if(empty($modules)): ?>
                        <li class="list-group-item text-center text-muted">Aucun module</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>