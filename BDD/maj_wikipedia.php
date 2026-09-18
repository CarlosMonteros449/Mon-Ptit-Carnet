<?php
require_once './bdd/env.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // On sélectionne les espèces pour lesquelles on n'a pas encore de description longue
    $stmt = $pdo->query("SELECT ID_ESPECE, NOM_COM, NOM_SCIEN FROM ESPECE");
    $especes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h1>Mise à jour encyclopédique via Wikipedia</h1>";
    echo "<ul>";

    foreach ($especes as $espece) {
        // L'API MediaWiki demande le titre de la page. Le nom scientifique est souvent le plus fiable pour éviter les pages d'homonymie.
        $titre_recherche = urlencode($espece['NOM_SCIEN']); 
        
        // URL de l'API Wikipedia en français (prop=extracts récupère le texte pur sans HTML)
        $url_api = "https://fr.wikipedia.org/w/api.php?action=query&prop=extracts&explaintext=1&titles={$titre_recherche}&format=json";
        
        // On récupère les données
        // On définit l'identité de ton script
        $options = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: MonCarnetDePecheBot/1.0 (alex@test.fr)\r\n"
            ]
        ];
        $contexte = stream_context_create($options);

        // On récupère les données avec l'autorisation de Wikipedia
        $json_response = file_get_contents($url_api, false, $contexte);
        $data = json_decode($json_response, true);
        
        // On fouille dans le JSON pour trouver la page
        $pages = $data['query']['pages'];
        $page = reset($pages); // Prend le premier élément du tableau

        if (isset($page['extract'])) {
            $texte_complet = $page['extract'];
            
            // L'API renvoie tout le texte. On va extraire la description générale (le début)
            // On coupe le texte au premier grand titre de section (qui commence par "==" sur Wikipedia)
            $parties = explode("==", $texte_complet);
            $description_courte = trim($parties[0]); 
            
            // Mise à jour dans la base
            $update = $pdo->prepare("UPDATE ESPECE SET DESCRIPTION = :desc WHERE ID_ESPECE = :id");
            $update->execute([
                'desc' => $description_courte,
                'id' => $espece['ID_ESPECE']
            ]);
            
            echo "<li>✅ <strong>" . htmlspecialchars($espece['NOM_COM']) . "</strong> : Données récupérées et sauvegardées.</li>";
        } else {
            // Si le nom scientifique ne marche pas, il faudra tester avec le nom commun à la main
            echo "<li>❌ <strong>" . htmlspecialchars($espece['NOM_COM']) . "</strong> : Page Wikipedia introuvable avec le nom scientifique.</li>";
        }
    }
    
    echo "</ul>";
    echo "<p>Mise à jour terminée ! <a href='codex.php'>Retour au Codex</a></p>";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>