<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: pages/dashboard.php");
    exit();
}

require_once 'includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: pages/dashboard.php");
        exit();
    } else {
        $error = "<div class='alert alert-danger text-center'>Identifiants incorrects.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SmartSchedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fc;
        }
        
        /* Partie gauche : L'image de l'école */
        .bg-image {
            background-image: url('assets/img/ecole.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            position: relative;
        }
        
        /* Un filtre coloré par-dessus l'image pour la rendre plus stylée */
        .bg-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            /* On passe de 0.85 à 0.4 pour laisser passer la photo */
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.4) 0%, rgba(34, 74, 190, 0.6) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 2rem;
            /* Optionnel : ajoute un léger flou sur l'image pour un effet premium */
            backdrop-filter: blur(2px); 
        }

        /* Ajoute ceci pour faire ressortir le texte si la photo est claire */
        .bg-overlay h1, .bg-overlay p, .bg-overlay i {
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }

        /* Partie droite : Le formulaire */
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        @keyframes slowZoom {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }

        .bg-image {
            overflow: hidden;
            animation: slowZoom 20s infinite alternate;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-lg-6 d-none d-lg-block bg-image">
            <div class="bg-overlay">
                <i class="bi bi-calendar-check" style="font-size: 5rem; margin-bottom: 20px;"></i>
                <h1 class="fw-bold">SmartSchedule</h1>
                <p class="fs-5 mt-3">Système intelligent de gestion des emplois du temps pour l'ISTAG BAB TIZIMI Meknès.</p>
            </div>
        </div>

        <div class="col-lg-6 login-container">
            <div class="login-form-wrapper">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-primary">Bon retour !</h2>
                    <p class="text-muted">Veuillez vous connecter à votre espace admin</p>
                </div>
                
                <?= $error ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-bold">Nom d'utilisateur</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0" required placeholder="username">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label text-secondary fw-bold">Mot de passe</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">Se connecter</button>
                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>