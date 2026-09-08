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
    <!-- La balise viewport est indispensable pour un affichage propre sur smartphone -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Carnet de Pêche</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Mon Carnet de Pêche</h1>
    </header>

    <main>
        <div class="test-connexion">
            <p><strong>Statut du serveur :</strong> <?php echo $status_db; ?></p>
        </div>
        
        <h2>Mes dernières prises</h2>
        <div class="liste-prises">
            <p>Aucune prise enregistrée pour le moment. Il va falloir aller au bord de l'eau !</p>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> - Projet Carnet de Pêche</p>
    </footer>
</body>
</html>