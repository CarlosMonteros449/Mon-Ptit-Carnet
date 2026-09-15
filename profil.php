<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

require_once './bdd/env.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT PSEUDO, MAIL, DATE_CREATION, DESCRIPTION, PDP_CHEMIN, BANNIERE_CHEMIN FROM UTILISATEUR WHERE ID_UTILISATEUR = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Mon Carnet de Pêche</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    
    <link href="css/style.css" rel="stylesheet">
    <link href="css/profil.css" rel="stylesheet">

</head>
<body>

    <!-- Si BANNIERE_CHEMIN existe, on remplace le fond par l'image -->
    <header class="profile-banner" <?php if(!empty($profil['BANNIERE_CHEMIN'])) echo 'style="background-image: url(\'' . htmlspecialchars($profil['BANNIERE_CHEMIN']) . '\');"'; ?>>
        
        <div class="profile-avatar-wrapper">
            <?php if(!empty($profil['PDP_CHEMIN'])): ?>
                <img src="<?= htmlspecialchars($profil['PDP_CHEMIN']) ?>" alt="Photo de profil" class="profile-avatar">
            <?php else: ?>
                <div class="profile-avatar">
                    <span class="material-symbols-rounded">person</span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
        <div class="profile-info text-center mb-4">
            <h1 class="h3 fw-bold text-dark mb-1"><?= htmlspecialchars($profil['PSEUDO']) ?></h1>
            <p class="text-muted small mb-3"><?= htmlspecialchars($profil['MAIL']) ?></p>
            
            <?php if(!empty($profil['DESCRIPTION'])): ?>
                <p class="text-secondary bg-white p-3 rounded-4 shadow-sm mx-auto" style="max-width: 400px;">
                    "<?= htmlspecialchars($profil['DESCRIPTION']) ?>"
                </p>
            <?php else: ?>
                <p class="text-secondary fst-italic small">Aucune description renseignée.</p>
            <?php endif; ?>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-3 mb-5 mx-auto" style="max-width: 400px;">
            <a href="modifier_profil.php" class="btn btn-light d-flex align-items-center justify-content-between p-3 mb-2 rounded-3 text-decoration-none">
                <div class="d-flex align-items-center">
                    <span class="material-symbols-rounded text-primary me-3">edit</span>
                    <span class="text-dark fw-medium">Modifier mon profil</span>
                </div>
                <span class="material-symbols-rounded text-muted">chevron_right</span>
            </a>
            
            <a href="#" class="btn btn-light d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 text-decoration-none">
                <div class="d-flex align-items-center">
                    <span class="material-symbols-rounded text-primary me-3">analytics</span>
                    <span class="text-dark fw-medium">Mes statistiques</span>
                </div>
                <span class="material-symbols-rounded text-muted">chevron_right</span>
            </a>

            <a href="deconnexion.php" class="btn btn-outline-danger d-flex align-items-center justify-content-center p-3 rounded-3 fw-bold">
                <span class="material-symbols-rounded me-2">logout</span>
                Se déconnecter
            </a>
        </div>
    </main>

    <nav class="navbar fixed-bottom bg-white custom-navbar border-0">
        <div class="container-fluid d-flex justify-content-around align-items-end px-2">
            
            <a href="accueil.php" class="nav-item d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">home</span>
                <span class="menu-text">Accueil</span>
            </a>
            
            <a href="nouvelle_session.php" class="btn-add-catch">
                <span class="material-symbols-rounded text-white" style="font-size: 36px;">phishing</span>
            </a>

            <a href="profil.php" class="nav-item active d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">person</span>
                <span class="menu-text">Profil</span>
            </a>
            
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>