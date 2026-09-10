<?php
$host = 'localhost';
$dbname = 'MLR1'; 
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// On pré-remplit la date et l'heure avec l'instant présent
$date_actuelle = date('Y-m-d\TH:i');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Session - Mon Carnet de Pêche</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    
    <!-- Ton fichier CSS externe -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <!-- En-tête avec bouton retour -->
    <header class="custom-header text-white text-center py-4 shadow-sm mb-4 position-relative">
        <a href="index.php" class="text-white position-absolute start-0 translate-middle-y ms-3 text-decoration-none" style="top: 50%;">
            <span class="material-symbols-rounded">arrow_back_ios_new</span>
        </a>
        <h1 class="h4 mb-0 fw-semibold">Nouvelle Session</h1>
    </header>

    <!-- Contenu principal : Formulaire -->
    <main class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
            <form action="" method="POST">
                
                <!-- Date et Heure -->
                <div class="mb-4">
                    <label for="date_debut" class="form-label fw-medium text-secondary">
                        <span class="material-symbols-rounded align-middle fs-5 me-1">calendar_month</span> Date et heure
                    </label>
                    <input type="datetime-local" class="form-control form-control-lg bg-light border-0" id="date_debut" name="date_debut" value="<?php echo $date_actuelle; ?>" required>
                </div>

                <!-- Type de Session -->
                <div class="mb-4">
                    <label for="type_session" class="form-label fw-medium text-secondary">
                        <span class="material-symbols-rounded align-middle fs-5 me-1">water</span> Milieu
                    </label>
                    <select class="form-select form-select-lg bg-light border-0" id="type_session" name="type_session" required>
                        <option value="" selected disabled>Choisir un type...</option>
                        <option value="1">Rivière</option>
                        <option value="2">Lac / Étang</option>
                        <option value="3">Mer</option>
                        <option value="4">Canal</option>
                    </select>
                </div>

                <!-- Bouton de validation -->
                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold shadow-sm custom-btn-submit">
                        Démarrer la session
                    </button>
                </div>

            </form>
        </div>
    </main>

    <!-- Navigation fixée (identique à l'index avec le bug de l'icône corrigé) -->
    <nav class="navbar fixed-bottom bg-white custom-navbar border-0">
        <div class="container-fluid d-flex justify-content-around align-items-end px-2">
            
            <a href="index.php" class="nav-item d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">home</span>
                <span class="menu-text">Accueil</span>
            </a>
            
            <!-- Correction du bouton : retrait de text-muted et mb-3, taille ajustée -->
            <a href="nouvelle_session.php" class="btn-add-catch active">
                <span class="material-symbols-rounded" style="font-size: 36px;">phishing</span>
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