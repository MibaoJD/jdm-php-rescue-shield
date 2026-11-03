<?php
require_once '../MiniGestionClient/config/db.php';

// Fonction de protection XSS
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$erreur = '';
$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

// 1. Vérifier si le client existe
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->execute([':id' => $client_id]);
$client = $stmt->fetch();

if (!$client) {
    die("❌ Client introuvable.");
}

// 2. Génération automatique du numéro séquentiel (ex: FAC-2026-001)
$annee = date('Y');
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM factures WHERE numero_facture LIKE :annee");
$stmtCount->execute([':annee' => "FAC-$annee-%"]);
$nb_factures = $stmtCount->fetchColumn();

// Prochain numéro incrémenté de 1 (formaté sur 3 chiffres ex: 001, 002)
$prochain_numero = $nb_factures + 1;
$numero_facture_auto = "FAC-" . $annee . "-" . str_pad($prochain_numero, 3, '0', STR_PAD_LEFT);

// 3. Traitement du formulaire d'envoi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_facture = trim($_POST['numero_facture'] ?? '');
    $date_facture = trim($_POST['date_facture'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $montant = trim($_POST['montant'] ?? '');

    if (!empty($numero_facture) && !empty($date_facture) && !empty($description) && is_numeric($montant) && $montant > 0) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO factures (client_id, numero_facture, description, montant, created_at) VALUES (:client_id, :numero_facture, :description, :montant, :created_at)");
            $stmtInsert->execute([
                ':client_id' => $client_id,
                ':numero_facture' => $numero_facture,
                ':description' => $description,
                ':montant' => $montant,
                ':created_at' => $date_facture . ' ' . date('H:i:s')
            ]);

            // Récupérer l'ID de la facture
            $facture_id = $pdo->lastInsertId();

            // Redirection vers la génération PDF
            header("Location: generer_facture.php?id=" . $facture_id);
            exit();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erreur = "Ce numéro de facture existe déjà. Veuillez utiliser un numéro unique.";
            } else {
                $erreur = "Erreur lors de l'enregistrement de la facture.";
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une facture - FidFacture</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 30px; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin: 0 auto; }
        h2 { color: #333; }
        .client-info { background: #f8f9fa; padding: 12px; border-left: 4px solid #007bff; margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        form { display: grid; gap: 12px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        input, textarea { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; width: 100%; box-sizing: border-box; }
        textarea { height: 90px; resize: vertical; }
        button { padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #218838; }
        .back-link { display: inline-block; margin-top: 15px; color: #007bff; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>📄 Créer une nouvelle facture</h2>
    
    <div class="client-info">
        <strong>Client :</strong> <?= e($client['nom']); ?><br>
        <strong>E-mail :</strong> <?= e($client['email']); ?>
    </div>

    <?php if ($erreur): ?>
        <div class="alert-danger"><?= e($erreur); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="numero_facture"><strong>Numéro de facture (Séquentiel auto) :</strong></label>
            <input type="text" name="numero_facture" id="numero_facture" value="<?= e($numero_facture_auto); ?>" required>
        </div>

        <div class="form-group">
            <label for="date_facture"><strong>Date de la facture :</strong></label>
            <input type="date" name="date_facture" id="date_facture" value="<?= date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
            <label for="description"><strong>Description de la prestation :</strong></label>
            <textarea name="description" id="description" placeholder="Ex: Audit de sécurité, correction des injections SQL et mise en place PDO..." required></textarea>
        </div>

        <div class="form-group">
            <label for="montant"><strong>Montant H.T. (€) :</strong></label>
            <input type="number" step="0.01" name="montant" id="montant" placeholder="Ex: 250.00" required>
        </div>

        <button type="submit">💾 Enregistrer et Générer le PDF</button>
    </form>

    <a href="../MiniGestionClient/index.php" class="back-link">⬅️ Retour à la liste des clients</a>
</div>

</body>
</html>