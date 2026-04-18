<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
$msg = '';

// Create or Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    $nom = cleanInput($_POST['nom']);
    $prenom = cleanInput($_POST['prenom']);
    $specialite = cleanInput($_POST['specialite']);
    $email = cleanInput($_POST['email']);

    try {
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE formateurs SET nom=?, prenom=?, specialite=?, email=? WHERE id=?");
            $stmt->execute([$nom, $prenom, $specialite, $email, $id]);
            $msg = "<div class='alert alert-success alert-dismissible fade show'>Formateur mis à jour.</div>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO formateurs (nom, prenom, specialite, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $specialite, $email]);
            $msg = "<div class='alert alert-success alert-dismissible fade show'>Formateur ajouté.</div>";
        }
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger alert-dismissible fade show'>Erreur : Cet email existe peut-être déjà.</div>";
    }
}

// Delete logic
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM formateurs WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: formateurs.php");
    exit();
}

// Get data for Editing
$edit_f = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM formateurs WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_f = $stmt->fetch();
}

// Fetch all
$formateurs = $pdo->query("SELECT * FROM formateurs ORDER BY id DESC")->fetchAll();

include '../includes/header.php';
?>

<h2 class="fw-bold mb-4">Gestion des Formateurs</h2>
<?= $msg ?>

<div class="row g-4">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header <?= $edit_f ? 'bg-warning text-dark' : 'bg-primary text-white' ?> fw-bold">
                <?= $edit_f ? '<i class="bi bi-pencil-square"></i> Modifier' : '<i class="bi bi-person-plus"></i> Ajouter' ?> un Formateur
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit_f['id'] ?? '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= $edit_f['nom'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= $edit_f['prenom'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialite" class="form-control" value="<?= $edit_f['specialite'] ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $edit_f['email'] ?? '' ?>" required>
                    </div>
                    <button type="submit" name="save" class="btn btn-<?= $edit_f ? 'warning' : 'primary' ?> w-100 fw-bold">
                        <?= $edit_f ? 'Mettre à jour' : 'Ajouter' ?>
                    </button>
                    <?php if($edit_f): ?>
                        <a href="formateurs.php" class="btn btn-secondary w-100 mt-2">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom & Prénom</th>
                            <th>Spécialité</th>
                            <th>Email</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formateurs as $f): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($f['nom'] . ' ' . $f['prenom']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($f['specialite']) ?></span></td>
                            <td><?= htmlspecialchars($f['email']) ?></td>
                            <td class="text-end">
                                <a href="?edit=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirmer la suppression ? Cela supprimera aussi les séances associées.');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($formateurs)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Aucun formateur enregistré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>