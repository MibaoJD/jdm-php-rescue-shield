<?php
require_once '../MiniGestionClient/config/db.php';

$page_title = "SUIVI DES FACTURES — PHP RESCUE & SHIELD";
$message = '';
$erreur = '';

// --- 1. CHANGEMENT DE STATUT (PAYÉE / IMPAYÉE) ---
if (isset($_GET['action']) && $_GET['action'] === 'toggle_statut' && !empty($_GET['id'])) {
    $facture_id = (int)$_GET['id'];
    $current_statut = $_GET['current'] ?? 'Impayée';
    $nouveau_statut = ($current_statut === 'Payée') ? 'Impayée' : 'Payée';

    try {
        $stmt = $pdo->prepare("UPDATE factures SET statut = :statut WHERE id = :id");
        $stmt->execute([':statut' => $nouveau_statut, ':id' => $facture_id]);
        $message = "STATUT DE LA FACTURE MIS À JOUR (" . strtoupper($nouveau_statut) . ").";
    } catch (PDOException $e) {
        $erreur = "ERREUR LORS DE LA MISE À JOUR DU STATUT.";
    }
}

// --- 2. TRAITEMENT DE LA SUPPRESSION D'UNE FACTURE ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $facture_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM factures WHERE id = :id");
        $stmt->execute([':id' => $facture_id]);
        $message = "FACTURE SUPPRIMÉE AVEC SUCCÈS.";
    } catch (PDOException $e) {
        $erreur = "ERREUR LORS DE LA SUPPRESSION DE LA FACTURE.";
    }
}

// --- 2. GESTION DE LA RECHERCHE ---
$recherche = trim($_GET['q'] ?? '');

if (!empty($recherche)) {
    $stmt = $pdo->prepare("
        SELECT f.*, c.nom AS client_nom 
        FROM factures f 
        JOIN clients c ON f.client_id = c.id 
        WHERE f.numero_facture LIKE :q1 
           OR c.nom LIKE :q2 
           OR f.description LIKE :q3
        ORDER BY f.created_at ASC
    ");
    $stmt->execute([
        ':q1' => '%' . $recherche . '%',
        ':q2' => '%' . $recherche . '%',
        ':q3' => '%' . $recherche . '%'
    ]);
} else {
    $stmt = $pdo->query("
        SELECT f.*, c.nom AS client_nom 
        FROM factures f 
        JOIN clients c ON f.client_id = c.id 
        ORDER BY f.created_at ASC
    ");
}

$factures = $stmt->fetchAll();

// --- 4. CALCUL DES STATISTIQUES ---
$total_ca = 0;
$total_paye = 0;
$total_impaye = 0;

foreach ($factures as $f) {
    $total_ca += $f['montant'];
    if (($f['statut'] ?? 'Impayée') === 'Payée') {
        $total_paye += $f['montant'];
    } else {
        $total_impaye += $f['montant'];
    }
}

// --- INCLUSION DU HEADER COMMUN ---
require_once '../includes/header.php';
?>

<h1 class="header-title">TABLEAU DE BORD DES FACTURES</h1>
<p class="subtitle">Gestion globale, recherche et suivi du chiffre d'affaires</p>

<?php if ($message): ?>
    <div class="alert-success"><?= e($message); ?></div>
<?php endif; ?>

<?php if ($erreur): ?>
    <div class="alert-danger"><?= e($erreur); ?></div>
<?php endif; ?>

<!-- CARTES DE STATISTIQUES -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px;">
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-left: 5px solid #007bff; padding: 15px; border-radius: 6px;">
        <div style="text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #495057; font-weight: bold;">TOTAL FACTURÉ</div>
        <div style="font-size: 20px; font-weight: bold; color: #007bff; margin-top: 5px;"><?= number_format($total_ca, 2, ',', ' '); ?> €</div>
    </div>
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-left: 5px solid #28a745; padding: 15px; border-radius: 6px;">
        <div style="text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #495057; font-weight: bold;">ENCAISSÉ (PAYÉ)</div>
        <div style="font-size: 20px; font-weight: bold; color: #28a745; margin-top: 5px;"><?= number_format($total_paye, 2, ',', ' '); ?> €</div>
    </div>
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-left: 5px solid #dc3545; padding: 15px; border-radius: 6px;">
        <div style="text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #495057; font-weight: bold;">EN ATTENTE (IMPAYÉ)</div>
        <div style="font-size: 20px; font-weight: bold; color: #dc3545; margin-top: 5px;"><?= number_format($total_impaye, 2, ',', ' '); ?> €</div>
    </div>
</div>

<!-- BARRE DE RECHERCHE DYNAMIQUE -->
<form method="GET" action="liste_factures.php" style="display: flex; gap: 10px; margin-bottom: 25px; background: #fafafa; padding: 15px; border-radius: 6px; border: 1px solid #eee;">
    <input type="text" name="q" placeholder="RECHERCHER PAR N° FACTURE, NOM DE CLIENT OU DESCRIPTION..." value="<?= e($recherche); ?>" style="flex: 1; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 5px; font-size: 14px;">
    <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-weight: bold; text-transform: uppercase; cursor: pointer; font-size: 12px; letter-spacing: 0.5px;">🔍 RECHERCHER</button>
    <?php if (!empty($recherche)): ?>
        <a href="liste_factures.php" style="padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; text-transform: uppercase; font-size: 12px; display: inline-flex; align-items: center;">✖ RÉINITIALISER</a>
    <?php endif; ?>
</form>

<h2 style="text-transform: uppercase; font-size: 16px; color: #333;">HISTORIQUE DES FACTURES ÉMISES</h2>
<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px;">
    <thead>
        <tr style="background-color: #1a252f; color: #ffffff; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">
            <th style="padding: 12px; text-align: left;">N° FACTURE</th>
            <th style="padding: 12px; text-align: left;">CLIENT</th>
            <th style="padding: 12px; text-align: left;">DESCRIPTION</th>
            <th style="padding: 12px; text-align: left;">MONTANT H.T.</th>
            <th style="padding: 12px; text-align: center;">STATUT</th>
            <th style="padding: 12px; text-align: left;">DATE</th>
            <th style="padding: 12px; text-align: left;">ACTIONS</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($factures) > 0): ?>
            <?php foreach ($factures as $f): ?>
                <?php $statut = $f['statut'] ?? 'Impayée'; ?>
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px;"><span style="background: #e7f1ff; color: #007bff; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-weight: bold;"><?= e($f['numero_facture']); ?></span></td>
                    <td style="padding: 12px;"><strong><?= e($f['client_nom']); ?></strong></td>
                    <td style="padding: 12px;"><?= e(substr($f['description'], 0, 35)) . (strlen($f['description']) > 35 ? '...' : ''); ?></td>
                    <td style="padding: 12px;"><strong style="color: #28a745;"><?= number_format($f['montant'], 2, ',', ' '); ?> €</strong></td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="liste_factures.php?action=toggle_statut&id=<?= e($f['id']); ?>&current=<?= e($statut); ?>" 
                           title="Cliquer pour changer le statut"
                           style="text-decoration: none; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: white; background-color: <?= $statut === 'Payée' ? '#28a745' : '#dc3545'; ?>;">
                            <?= e($statut); ?> 🔄
                        </a>
                    </td>
                    <td style="padding: 12px;"><?= date('d/m/Y', strtotime($f['created_at'])); ?></td>
                    <td style="padding: 12px;">
                        <a href="generer_facture.php?id=<?= e($f['id']); ?>" target="_blank" style="color: #28a745; text-decoration: none; font-weight: bold; margin-right: 12px; text-transform: uppercase; font-size: 12px;">📄 PDF</a>
                        <a href="liste_factures.php?action=delete&id=<?= e($f['id']); ?>" style="color: #dc3545; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">🗑️ SUPPRIMER</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #888; padding: 20px;">
                    <?= !empty($recherche) ? 'AUCUNE FACTURE NE CORRESPOND À VOTRE RECHERCHE.' : 'AUCUNE FACTURE ÉMISE POUR LE MOMENT.'; ?>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// --- INCLUSION DU FOOTER COMMUN ---
require_once '../includes/footer.php';
?>