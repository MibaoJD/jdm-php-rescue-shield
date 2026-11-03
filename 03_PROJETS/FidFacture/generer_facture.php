<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données MySQL
require_once '../MiniGestionClient/config/db.php';

// Chargement de l'autoloader Dompdf
$autoloader = __DIR__ . '/vendor/dompdf/autoload.inc.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    $autoloader_alt = __DIR__ . '/vendor/dompdf/dompdf/autoload.inc.php';
    if (file_exists($autoloader_alt)) {
        require_once $autoloader_alt;
    } else {
        die("❌ Erreur critique : Le fichier d'autoloader Dompdf est introuvable.");
    }
}

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Récupération de l'ID de la facture depuis l'URL (ex: generer_facture.php?id=3)
$facture_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($facture_id <= 0) {
    die("❌ ID de facture invalide.");
}

// 2. Requête SQL avec jointure (INNER JOIN) pour récupérer la facture ET le client associé
$stmt = $pdo->prepare("
    SELECT f.*, c.nom AS client_nom, c.email AS client_email, c.telephone AS client_telephone 
    FROM factures f 
    JOIN clients c ON f.client_id = c.id 
    WHERE f.id = :facture_id
");
$stmt->execute([':facture_id' => $facture_id]);
$facture = $stmt->fetch();

if (!$facture) {
    die("❌ Facture introuvable en base de données.");
}

// 3. Configuration des options de Dompdf
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// 4. Modèle HTML / CSS de la facture
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; color: #333; line-height: 1.5; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; text-align: left; vertical-align: top; }
        .header-right { display: table-cell; text-align: right; vertical-align: top; }
        .title { font-size: 22px; font-weight: bold; color: #007bff; }
        .client-card { background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; color: #28a745; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #6c757d; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="header-left">
                <div class="title">PHP RESCUE & SHIELD</div>
                <div>Maintenance, Sécurité & Développement Web</div>
            </div>
            <div class="header-right">
                <strong>Facture N° :</strong> ' . htmlspecialchars($facture['numero_facture']) . '<br>
                <strong>Date :</strong> ' . date('d/m/Y', strtotime($facture['created_at'])) . '
            </div>
        </div>

        <div class="client-card">
            <strong>Facturé à :</strong><br>
            <strong>' . htmlspecialchars($facture['client_nom']) . '</strong><br>
            E-mail : ' . htmlspecialchars($facture['client_email']) . '<br>
            Tél : ' . htmlspecialchars($facture['client_telephone']) . '
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description de la prestation</th>
                    <th style="width: 25%; text-align: right;">Montant H.T.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . nl2br(htmlspecialchars($facture['description'])) . '</td>
                    <td style="text-align: right;">' . number_format($facture['montant'], 2, ',', ' ') . ' €</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Total à payer : ' . number_format($facture['montant'], 2, ',', ' ') . ' €
        </div>

        <div class="footer">
            Document généré automatiquement via FidFacture — Base de données MySQL & Dompdf
        </div>
    </div>
</body>
</html>';

// 5. Génération et affichage du PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Facture_" . $facture['numero_facture'] . ".pdf", ["Attachment" => false]);
exit();
?>