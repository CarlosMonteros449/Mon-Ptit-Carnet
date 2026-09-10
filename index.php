<?php
$host = 'localhost';
$dbname = 'MLR1'; 
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $status_db = "Connexion à la base MLR1 réussie";
} catch (PDOException $e) {
    $status_db = "Erreur de connexion : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Carnet de Pêche</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts : Poppins (Typographie) & Material Symbols Rounded (Icônes) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=phishing" />
    
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <header class="custom-header text-white text-center py-4 shadow-sm mb-4">
        <h1 class="h4 mb-0 fw-semibold">Mon Carnet</h1>
    </header>

    <main class="container">
        <h2 class="h5 fw-bold text-dark mb-3">Dernières prises</h2>
        
        <!-- Remplacement du texte simple par une "carte" Bootstrap moderne -->
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 mt-4">
            <span class="material-symbols-rounded text-muted mb-3" style="font-size: 48px;">phishing</span>
            <p class="text-muted mb-0">Aucune prise enregistrée pour le moment.<br>Préparez votre matériel !</p>
        </div>
    </main>

    <nav class="navbar fixed-bottom bg-white custom-navbar border-0">
        <div class="container-fluid d-flex justify-content-around align-items-end px-2">
            
            <a href="index.php" class="nav-item active d-flex flex-column align-items-center">
                <span class="material-symbols-rounded">home</span>
                <span class="menu-text">Accueil</span>
            </a>
            
            <a href="nouvelle_session.php" class="btn-add-catch">
                <span class="material-symbols-rounded" style="font-size: 45px;">phishing</span>
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