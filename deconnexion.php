<?php
session_start();
session_destroy(); // Détruit la session
header('Location: connexion.php'); // Renvoie au login
exit();
?>