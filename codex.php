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

    // Récupération de toutes les espèces classées par ordre alphabétique
    $stmt = $pdo->query("SELECT NOM_COM, NOM_SCIEN, TAILLE_MAX, POIDS_MAX, ICONE_CHEMIN FROM ESPECE ORDER BY NOM_COM ASC");
    $especes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codex - Mon Carnet de Pêche</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <header class="custom-header text-white text-center py-4 shadow-sm mb-4 position-relative">
        <a href="accueil.php" class="text-white position-absolute start-0 translate-middle-y ms-3 text-decoration-none" style="top: 50%;">
            <span class="material-symbols-rounded">arrow_back_ios_new</span>
        </a>
        <h1 class="h4 mb-0 fw-semibold">Codex des Espèces</h1>
    </header>

    <main class="container mb-5 pb-4">
        <p class="text-muted text-center small mb-4">Découvrez les <?= count($especes) ?> espèces répertoriées dans les eaux françaises.</p>
        
        <!-- Grille Bootstrap : 2 colonnes sur mobile (col-6), 3 sur tablette (col-md-4), 4 sur PC (col-lg-3) -->
        <div class="row g-3">
            <?php foreach ($especes as $espece): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 d-flex flex-column align-items-center justify-content-between transition-hover">
                        
                        <!-- Affichage de l'icône vectorielle -->
                        <div class="mb-2 d-flex align-items-center justify-content-center" style="height: 80px; width: 100%;">
                            <?php if (!empty($espece['ICONE_CHEMIN'])): ?>
                                <!-- On utilise object-fit: contain pour ne pas déformer l'image -->
                                <img src="<?= htmlspecialchars($espece['ICONE_CHEMIN']) ?>" alt="<?= htmlspecialchars($espece['NOM_COM']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            <?php else: ?>
                                <span class="material-symbols-rounded text-secondary" style="font-size: 40px;">set_meal</span>
                            <?php endif; ?>
                        </div>

                        <!-- Textes -->
                        <div>
                            <h3 class="h6 fw-bold text-dark mb-0"><?= htmlspecialchars($espece['NOM_COM']) ?></h3>
                            <small class="text-muted fst-italic" style="font-size: 0.65rem;"><?= htmlspecialchars($espece['NOM_SCIEN']) ?></small>
                        </div>
                        
                        <!-- Petites infos de taille/poids max si on veut cliquer plus tard -->
                        <?php if(!empty($espece['TAILLE_MAX']) || !empty($espece['POIDS_MAX'])): ?>
                            <div class="mt-2 pt-2 border-top w-100">
                                <small class="text-primary fw-medium" style="font-size: 0.7rem;">
                                    Max: <?= htmlspecialchars($espece['TAILLE_MAX']) ?>cm / <?= htmlspecialchars($espece['POIDS_MAX']) ?>kg
                                </small>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- La Navbar -->
    <nav class="navbar fixed-bottom bg-white custom-navbar border-0">
        <div class="container-fluid d-flex justify-content-around align-items-end px-2">
            <a href="accueil.php" class="nav-item d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">home</span>
                <span class="menu-text">Accueil</span>
            </a>
            <a href="nouvelle_session.php" class="btn-add-catch">
                <span class="material-symbols-rounded text-white" style="font-size: 36px;">phishing</span>
            </a>
            <a href="profil.php" class="nav-item d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">person</span>
                <span class="menu-text">Profil</span>
            </a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>