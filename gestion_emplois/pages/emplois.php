<?php
require_once '../includes/db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_session'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO emplois (id_formateur, id_groupe, id_module, id_ressource, jour, heure_debut, heure_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_formateur'], 
            $_POST['id_groupe'], 
            $_POST['id_module'], 
            $_POST['id_ressource'], 
            $_POST['jour'], 
            $_POST['heure_debut'], 
            $_POST['heure_fin']
        ]);
        $msg = "<div class='alert alert-success alert-dismissible fade show'>Séance programmée avec succès.</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show'>Erreur : Impossible de planifier la séance. Vérifiez vos données.</div>";
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM emplois WHERE id=?")->execute([$_GET['delete']]);
    header("Location: emplois.php");
    exit();
}

// Fetch schedule 
$sessions = $pdo->query("
    SELECT e.id, f.nom as prof, f.prenom, g.nom_groupe, m.nom_module, m.type_module, r.nom_salle, e.jour, e.heure_debut, e.heure_fin 
    FROM emplois e
    JOIN formateurs f ON e.id_formateur = f.id
    JOIN groupes g ON e.id_groupe = g.id
    JOIN modules m ON e.id_module = m.id
    JOIN ressources r ON e.id_ressource = r.id
    ORDER BY FIELD(e.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'), e.heure_debut
")->fetchAll();

include '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Générateur d'Emplois du Temps</h2>
<?= $msg ?>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white fw-bold"><i class="bi bi-calendar-plus"></i> Programmer une nouvelle séance</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-2">
                <select name="jour" class="form-select" required>
                    <option value="">Jour...</option>
                    <option value="Lundi">Lundi</option>
                    <option value="Mardi">Mardi</option>
                    <option value="Mercredi">Mercredi</option>
                    <option value="Jeudi">Jeudi</option>
                    <option value="Vendredi">Vendredi</option>
                    <option value="Samedi">Samedi</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="time" name="heure_debut" class="form-control" required title="Heure de début">
            </div>
            <div class="col-md-2">
                <input type="time" name="heure_fin" class="form-control" required title="Heure de fin">
            </div>
            <div class="col-md-3">
                <select name="id_groupe" class="form-select" required>
                    <option value="">Groupe...</option>
                    <?php foreach($pdo->query("SELECT * FROM groupes") as $g) echo "<option value='{$g['id']}'>{$g['nom_groupe']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="id_module" class="form-select" required>
                    <option value="">Module...</option>
                    <?php foreach($pdo->query("SELECT * FROM modules") as $m) echo "<option value='{$m['id']}'>{$m['nom_module']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="id_formateur" class="form-select" required>
                    <option value="">Formateur...</option>
                    <?php foreach($pdo->query("SELECT * FROM formateurs") as $f) echo "<option value='{$f['id']}'>{$f['nom']} {$f['prenom']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="id_ressource" class="form-select" required>
                    <option value="">Salle/Atelier...</option>
                    <?php foreach($pdo->query("SELECT * FROM ressources") as $r) echo "<option value='{$r['id']}'>{$r['nom_salle']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" name="add_session" class="btn btn-primary w-100 fw-bold"><i class="bi bi-calendar-check"></i> Ajouter au planning</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jour & Heure</th>
                    <th>Groupe</th>
                    <th>Module</th>
                    <th>Formateur</th>
                    <th>Salle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): 
                    $bg_class = $s['type_module'] == 'Pratique' ? 'bg-pratique' : 'bg-theorique';
                ?>
                <tr>
                    <td class="fw-bold"><?= $s['jour'] ?><br>
                        <small class="text-muted"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?></small>
                    </td>
                    <td class="fw-bold"><?= htmlspecialchars($s['nom_groupe']) ?></td>
                    <td class="<?= $bg_class ?>"><strong><?= htmlspecialchars($s['nom_module']) ?></strong><br><small><?= $s['type_module'] ?></small></td>
                    <td><?= htmlspecialchars($s['prof'] . ' ' . $s['prenom']) ?></td>
                    <td><span class="badge bg-secondary fs-6"><?= htmlspecialchars($s['nom_salle']) ?></span></td>
                    <td><a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($sessions)): ?>
                    <tr><td colspan="6" class="text-center text-muted">Aucune séance planifiée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>