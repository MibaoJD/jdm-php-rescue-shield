<?php
// Protection globale XSS
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'PHP RESCUE & SHIELD'; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 30px; color: #1c1e21; }
        .container { max-width: 1050px; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin: 0 auto; }
        
        .header-title { text-transform: uppercase; letter-spacing: 1px; color: #1a252f; margin-bottom: 5px; font-size: 26px; border-bottom: 3px solid #007bff; display: inline-block; padding-bottom: 5px; }
        .subtitle { color: #606770; font-style: italic; margin-bottom: 25px; }
        
        .nav-links { margin-bottom: 25px; border-bottom: 1px solid #e4e6eb; padding-bottom: 15px; }
        .nav-links a { text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; color: #007bff; text-decoration: none; font-weight: bold; margin-right: 20px; transition: color 0.2s; }
        .nav-links a:hover { color: #0056b3; text-decoration: underline; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-transform: uppercase; font-size: 13px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-transform: uppercase; font-size: 13px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-links">
        <a href="/FREELANCE_PRO_OBJECTIF_LIBERTE/03_PROJETS/MiniGestionClient/index.php">👥 GESTION DES CLIENTS</a>
        <a href="/FREELANCE_PRO_OBJECTIF_LIBERTE/03_PROJETS/FidFacture/liste_factures.php">📊 SUIVI DES FACTURES</a>
    </div>