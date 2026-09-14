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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_user = $_SESSION['user_id'];
        
        // 1. Mise à jour des textes
        $stmt = $pdo->prepare("UPDATE UTILISATEUR SET PSEUDO = :pseudo, DESCRIPTION = :desc WHERE ID_UTILISATEUR = :id");
        $stmt->execute([
            'pseudo' => $_POST['pseudo'],
            'desc' => $_POST['description'],
            'id' => $id_user
        ]);

        // Création du sous-dossier unique par utilisateur
        $dossier_upload = 'uploads/user_' . $id_user . '/';
        if (!is_dir($dossier_upload)) {
            mkdir($dossier_upload, 0777, true);
        }

        // 2. Fonction simplifiée pour décoder et sauvegarder le Base64 fourni par Cropper.js
        function sauvegarderBase64($base64_string, $dossier, $prefixe, $id_user, $pdo, $colonne_bdd) {
            $image_parts = explode(";base64,", $base64_string);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                // On force le format JPG et on garantit un nom unique
                $nom_unique = uniqid() . '_' . $prefixe . '_' . $id_user . '.jpg';
                $chemin_final = $dossier . $nom_unique;
                
                if (file_put_contents($chemin_final, $image_base64)) {
                    $stmtImg = $pdo->prepare("UPDATE UTILISATEUR SET $colonne_bdd = :chemin WHERE ID_UTILISATEUR = :id");
                    $stmtImg->execute(['chemin' => $chemin_final, 'id' => $id_user]);
                }
            }
        }

        // 3. Sauvegarde de la Photo de profil si recadrée
        if (!empty($_POST['pdp_cropped'])) {
            sauvegarderBase64($_POST['pdp_cropped'], $dossier_upload, 'pdp', $id_user, $pdo, 'PDP_CHEMIN');
        }

        // 4. Sauvegarde de la Bannière si recadrée
        if (!empty($_POST['banniere_cropped'])) {
            sauvegarderBase64($_POST['banniere_cropped'], $dossier_upload, 'ban', $id_user, $pdo, 'BANNIERE_CHEMIN');
        }

        header('Location: profil.php');
        exit();
    }

    // Récupération des données pour pré-remplir le formulaire
    $stmt = $pdo->prepare("SELECT PSEUDO, DESCRIPTION, PDP_CHEMIN, BANNIERE_CHEMIN FROM UTILISATEUR WHERE ID_UTILISATEUR = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil - Mon Carnet de Pêche</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
    <!-- CSS de Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <header class="custom-header text-white text-center py-4 shadow-sm mb-4 position-relative">
        <a href="profil.php" class="text-white position-absolute start-0 translate-middle-y ms-3 text-decoration-none" style="top: 50%;">
            <span class="material-symbols-rounded">arrow_back_ios_new</span>
        </a>
        <h1 class="h4 mb-0 fw-semibold">Modifier Profil</h1>
    </header>

    <main class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 mx-auto" style="max-width: 500px;">
            
            <form id="form-profil" action="modifier_profil.php" method="POST">
                
                <h5 class="fw-bold mb-3 text-dark">Informations</h5>
                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium">Pseudo</label>
                    <input type="text" class="form-control bg-light border-0" name="pseudo" value="<?= htmlspecialchars($profil['PSEUDO']) ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium">Description de profil</label>
                    <textarea class="form-control bg-light border-0" name="description" rows="3" placeholder="Parlez un peu de votre passion..."><?= htmlspecialchars($profil['DESCRIPTION'] ?? '') ?></textarea>
                </div>

                <hr class="my-4">
                
                <h5 class="fw-bold mb-3 text-dark">Personnalisation</h5>
                
                <div class="mb-5">
                    <label class="form-label text-secondary small fw-medium d-block">Photo de profil</label>
                    
                    <?php if(!empty($profil['PDP_CHEMIN'])): ?>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= htmlspecialchars($profil['PDP_CHEMIN']) ?>" alt="Actuelle" class="rounded-circle shadow-sm me-3" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #f4f7f6;">
                            <span class="text-muted small">Image actuelle</span>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" class="form-control bg-light border-0 mb-2" id="pdp-input" accept="image/*">
                    <input type="hidden" name="pdp_cropped" id="pdp_cropped">
                    
                    <div id="pdp-cropper-container" style="display:none; max-height: 400px;">
                        <img id="pdp-image-to-crop" style="max-width: 100%; display: block;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium d-block">Image de bannière</label>
                    
                    <?php if(!empty($profil['BANNIERE_CHEMIN'])): ?>
                        <div class="mb-3">
                            <img src="<?= htmlspecialchars($profil['BANNIERE_CHEMIN']) ?>" alt="Actuelle" class="rounded-3 shadow-sm w-100" style="aspect-ratio: 21/9; object-fit: cover;">
                            <span class="text-muted small d-block mt-1">Bannière actuelle</span>
                        </div>
                    <?php endif; ?>

                    <input type="file" class="form-control bg-light border-0 mb-2" id="banniere-input" accept="image/*">
                    <input type="hidden" name="banniere_cropped" id="banniere_cropped">
                    
                    <!-- Conteneur Cropper Bannière -->
                    <div id="banniere-cropper-container" style="display:none; max-height: 400px;">
                        <img id="banniere-image-to-crop" style="max-width: 100%; display: block;">
                    </div>
                </div>

                <div class="d-grid mt-5">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold shadow-sm custom-btn-submit">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>

        </div>
    </main>

    <!-- Script Cropper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropperBan, cropperPdp;

        // --- GESTION DE LA PHOTO DE PROFIL ---
        document.getElementById('pdp-input').addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const imgToCrop = document.getElementById('pdp-image-to-crop');
                    imgToCrop.src = event.target.result;
                    document.getElementById('pdp-cropper-container').style.display = 'block';
                    
                    if (cropperPdp) cropperPdp.destroy();
                    
                    // Ratio 1:1 pour un rond parfait de photo de profil
                    cropperPdp = new Cropper(imgToCrop, {
                        aspectRatio: 1 / 1,
                        viewMode: 1,
                        autoCropArea: 1
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        // --- GESTION DE LA BANNIÈRE ---
        document.getElementById('banniere-input').addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const imgToCrop = document.getElementById('banniere-image-to-crop');
                    imgToCrop.src = event.target.result;
                    document.getElementById('banniere-cropper-container').style.display = 'block';
                    
                    if (cropperBan) cropperBan.destroy();
                    
                    // Ratio 2.22:1 pour correspondre exactement à ta bannière CSS (ex: 400px de large pour 180px de haut)
                    cropperBan = new Cropper(imgToCrop, {
                        aspectRatio: 21 / 9, // Le nouveau ratio parfait
                        viewMode: 1,
                        autoCropArea: 1
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        // --- INTERCEPTION DU BOUTON ENREGISTRER ---
        document.getElementById('form-profil').addEventListener('submit', function(e) {
            // Si l'utilisateur a recadré une bannière
            if (cropperBan) {
                const canvasBan = cropperBan.getCroppedCanvas({ width: 1050, height: 450 });
                document.getElementById('banniere_cropped').value = canvasBan.toDataURL('image/jpeg', 0.8);
            }
            
            // Si l'utilisateur a recadré une photo de profil
            if (cropperPdp) {
                const canvasPdp = cropperPdp.getCroppedCanvas({ width: 400, height: 400 });
                document.getElementById('pdp_cropped').value = canvasPdp.toDataURL('image/jpeg', 0.8);
            }
        });
    </script>
</body>
</html>