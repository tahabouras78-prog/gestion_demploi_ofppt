<?php
$current_page = 'profil.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

$success_msg = '';
$error_msg = '';

// Récupérer les informations de l'admin actuel
$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$_SESSION['admin_username']]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = cleanInput($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Vérifier si le mot de passe actuel est correct (Sécurité)
    if (password_verify($current_password, $admin['password'])) {
        
        $update_password = false;
        
        // 2. Si l'utilisateur veut aussi changer son mot de passe
        if (!empty($new_password)) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password = true;
            } else {
                $error_msg = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }

        // 3. Mise à jour dans la base de données
        if (empty($error_msg)) {
            try {
                if ($update_password) {
                    $update_stmt = $pdo->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
                    $update_stmt->execute([$new_username, $hashed_password, $admin['id']]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
                    $update_stmt->execute([$new_username, $admin['id']]);
                }

                // Mettre à jour la session avec le nouveau nom d'utilisateur
                $_SESSION['admin_username'] = $new_username;
                $success_msg = "Votre profil a été mis à jour avec succès !";
                
                // Recharger les données pour l'affichage
                $admin['username'] = $new_username; 

            } catch (PDOException $e) {
                // Gestion de l'erreur si le nouveau nom d'utilisateur existe déjà
                if ($e->getCode() == 23000) { 
                    $error_msg = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    $error_msg = "Erreur lors de la mise à jour.";
                }
            }
        }
    } else {
        $error_msg = "Le mot de passe actuel est incorrect.";
    }
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Paramètres du Profil</h2>
            <p class="text-muted">Gérez vos identifiants de connexion administratifs.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    
                    <?php if ($success_msg): ?>
                        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= $success_msg ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $error_msg ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <h5 class="mb-3 text-secondary border-bottom pb-2">Informations Générales</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom d'utilisateur</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                            </div>
                        </div>

                        <h5 class="mb-3 mt-4 text-secondary border-bottom pb-2">Changer le mot de passe</h5>
                        <p class="text-muted small">Laissez les champs "Nouveau mot de passe" vides si vous souhaitez conserver l'actuel.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••">
                            </div>
                        </div>

                        <hr>
                        
                        <div class="mb-4 bg-light p-3 rounded border border-warning border-opacity-50">
                            <label class="form-label fw-bold text-warning-emphasis"><i class="bi bi-shield-lock me-1"></i> Vérification de sécurité</label>
                            <p class="small text-muted mb-2">Veuillez entrer votre mot de passe <strong>actuel</strong> pour valider les modifications.</p>
                            <input type="password" name="current_password" class="form-control" required placeholder="Mot de passe actuel">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-save me-2"></i>Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 d-none d-lg-block">
            <div class="card bg-primary text-white shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-5 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-shield-check" style="font-size: 6rem; opacity: 0.5;"></i>
                    <h3 class="mt-4 fw-bold">Sécurité du Compte</h3>
                    <p class="lead" style="opacity: 0.8;">Gardez vos identifiants en sécurité. Utilisez un mot de passe fort combinant lettres, chiffres et caractères spéciaux pour protéger les emplois du temps de l'établissement.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>