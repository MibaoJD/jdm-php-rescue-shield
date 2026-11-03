<?php
require_once 'config/db.php';

$page_title = "GESTION DES CLIENTS — PHP RESCUE & SHIELD";
$message = '';
$erreur = '';

// --- 1. TRAITEMENT DU FORMULAIRE D'AJOUT CLIENT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_client'])) {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    if (!empty($nom) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO clients (nom, email, telephone) VALUES (:nom, :email, :telephone)");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone
            ]);
            $message = "CLIENT ENREGISTRÉ AVEC SUCCÈS.";
        } catch (PDOException $e) {
            $erreur = "ERREUR LORS DE L'ENREGISTREMENT DU CLIENT.";
        }
    } else {
        $erreur = "VEUILLEZ REMPLIR TOUS LES CHAMPS OBLIGATOIRES (NOM ET E-MAIL).";
    }
}

// --- 2. TRAITEMENT DE LA SUPPRESSION D'UN CLIENT ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $client_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $client_id]);
        $message = "CLIENT SUPPRIMÉ AVEC SUCCÈS.";
    } catch (PDOException $e) {
        $erreur = "IMPOSSIBLE DE SUPPRIMER CE CLIENT (DES FACTURES Y SONT PEUT-ÊTRE LIÉES).";
    }
}

// --- 3. RÉCUPÉRATION DE LA LISTE DES CLIENTS ---
$stmt = $pdo->query("SELECT * FROM clients ORDER BY id ASC");
$clients = $stmt->fetchAll();

// --- INCLUSION DU HEADER COMMUN ---
require_once '../includes/header.php';
?>

<h1 class="header-title">GESTION DES CLIENTS</h1>
<p class="subtitle">Base de données clients et création de prestations</p>

<?php if ($message): ?>
    <div class="alert-success"><?= e($message); ?></div>
<?php endif; ?>

<?php if ($erreur): ?>
    <div class="alert-danger"><?= e($erreur); ?></div>
<?php endif; ?>

<!-- FORMULAIRE D'AJOUT D'UN CLIENT -->
<div style="background: #fafafa; border: 1px solid #e9ecef; border-left: 5px solid #007bff; padding: 20px; border-radius: 6px; margin-bottom: 30px;">
    <h2 style="text-transform: uppercase; font-size: 15px; color: #1a252f; margin-top: 0; margin-bottom: 15px;">➕ AJOUTER UN NOUVEAU CLIENT</h2>
    <form method="POST" action="index.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto; gap: 12px; align-items: end;">
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #495057; margin-bottom: 5px;">NOM DU CLIENT *</label>
            <input type="text" name="nom" required placeholder="Ex: Jean Bernard" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 5px; font-size: 13px; box-sizing: border-box;">
        </div>
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #495057; margin-bottom: 5px;">ADRESSE E-MAIL *</label>
            <input type="email" name="email" required placeholder="Ex: client@email.com" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 5px; font-size: 13px; box-sizing: border-box;">
        </div>
        <div>
            <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #495057; margin-bottom: 5px;">TÉLÉPHONE</label>
            <input type="text" name="telephone" placeholder="Ex: 034 00 000 00" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 5px; font-size: 13px; box-sizing: border-box;">
        </div>
        <button type="submit" name="ajouter_client" style="padding: 11px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-weight: bold; text-transform: uppercase; cursor: pointer; font-size: 12px; letter-spacing: 0.5px;">
            ENREGISTRER
        </button>
    </form>
</div>

<!-- TABLEAU DES CLIENTS -->
<h2 style="text-transform: uppercase; font-size: 16px; color: #333; margin-bottom: 15px;">LISTE DES CLIENTS ENREGISTRÉS</h2>
<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px;">
    <thead>
        <tr style="background-color: #1a252f; color: #ffffff; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">
            <th style="padding: 12px; text-align: center; width: 50px;">ID</th>
            <th style="padding: 12px; text-align: left;">NOM</th>
            <th style="padding: 12px; text-align: left;">E-MAIL</th>
            <th style="padding: 12px; text-align: left;">TÉLÉPHONE</th>
            <th style="padding: 12px; text-align: left;">DATE D'AJOUT</th>
            <th style="padding: 12px; text-align: left;">ACTIONS</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($clients) > 0): ?>
            <?php foreach ($clients as $c): ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; text-align: center;">
                        <span style="background: #e7f1ff; color: #007bff; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-weight: bold; font-size: 12px;">#<?= e($c['id']); ?></span>
                    </td>
                    <td style="padding: 12px;"><strong><?= e($c['nom']); ?></strong></td>
                    <td style="padding: 12px; color: #495057;"><?= e($c['email']); ?></td>
                    <td style="padding: 12px; color: #495057;"><?= e($c['telephone'] ?: '—'); ?></td>
                    <td style="padding: 12px; color: #6c757d; font-size: 13px;"><?= date('d/m/Y H:i', strtotime($c['created_at'])); ?></td>
                    <td style="padding: 12px;">
                        <a href="../FidFacture/creer_facture.php?client_id=<?= e($c['id']); ?>" style="color: #28a745; text-decoration: none; font-weight: bold; margin-right: 15px; text-transform: uppercase; font-size: 12px;">
                            📄 NOUVELLE FACTURE
                        </a>
                        <a href="index.php?action=delete&id=<?= e($c['id']); ?>" style="color: #dc3545; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                            🗑️ SUPPRIMER
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 20px;">AUCUN CLIENT ENREGISTRÉ POUR LE MOMENT.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// --- INCLUSION DU FOOTER COMMUN ---
require_once '../includes/footer.php';
?>