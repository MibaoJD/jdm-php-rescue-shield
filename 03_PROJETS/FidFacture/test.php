<?php
// On vérifie si le fichier autoload principal existe
$chemin1 = __DIR__ . '/vendor/dompdf/autoload.inc.php';
$chemin2 = __DIR__ . '/vendor/dompdf/src/Autoloader.php';

echo "<h3>Diagnostic de l'installation Dompdf :</h3>";

if (file_exists($chemin1)) {
    echo "<p style='color:green;'>✅ Fichier <code>autoload.inc.php</code> trouvé !</p>";
} else {
    echo "<p style='color:red;'>❌ Fichier <code>autoload.inc.php</code> INTROUVABLE.</p>";
}

if (file_exists($chemin2)) {
    echo "<p style='color:green;'>✅ Fichier <code>Autoloader.php</code> trouvé !</p>";
} else {
    echo "<p style='color:red;'>❌ Fichier <code>Autoloader.php</code> INTROUVABLE (Vérifiez si Dompdf est bien extrait dans <code>vendor/dompdf/</code>).</p>";
}
?>