# PHP RESCUE & SHIELD — Outil de Gestion & Facturation Freelance

Application web interne que j'ai développée pour piloter mon activité de freelance (maintenance, correction de bugs et sécurisation PHP/MySQL). Cet outil centralise la gestion de mes clients, l'édition de factures normalisées au format PDF et le suivi de ma trésorerie en temps réel.

---

## Ce que fait l'application

Le projet s'articule autour de deux modules principaux :

1. **Gestion des clients (`MiniGestionClient`)**
   * Suivi centralisé de la base clients.
   * Interface épurée permettant de basculer rapidement vers la création de devis ou de factures.

2. **Facturation et PDF (`FidFacture`)**
   * Génération automatique de numéros de factures séquentiels uniques (format `FAC-AAAA-XXX`).
   * Édition propre et téléchargement de factures au format PDF via la librairie **Dompdf**.
   * Gestion des statuts de paiement (**Payée** / **Impayée**) directement depuis le tableau de bord en un clic.

3. **Tableau de bord financier**
   * Calcul en direct du chiffre d'affaires total, des montants encaissés et du reste à percevoir.
   * Recherche multicritères instantanée (filtrage par nom de client, numéro de facture ou mots-clés dans la description).

---

## Choix techniques & Sécurité

J'ai conçu cette application en **PHP natif** en appliquant des règles strictes de sécurité pour éviter les vulnérabilités courantes :

* **Sécurité Base de Données** : Connexions et requêtes gérées exclusivement via **PDO** et des **requêtes préparées** (protection totale contre les injections SQL).
* **Sécurité des sorties** : Échappement systématique des données affichées à l'écran (`htmlspecialchars`) pour contrer les failles XSS.
* **Architecture** : Structure modulaire avec mutualisation des composants (fichiers `header.php` et `footer.php` partagés) pour garder un code propre et maintenable.

---

## Stack technique

* **Langage** : PHP 8
* **Base de données** : MySQL / MariaDB
* **Moteur PDF** : Dompdf
* **Environnement de test** : XAMPP (Local)

---

## Structure du code

```text
FREELANCE_PRO_OBJECTIF_LIBERTE/03_PROJETS/
├── includes/
│   ├── header.php          # En-tête de page partagé
│   └── footer.php          # Pied de page partagé
├── MiniGestionClient/
│   ├── config/
│   │   └── db.php          # Configuration de la connexion PDO
│   └── index.php           # Interface de gestion des clients
└── FidFacture/
    ├── vendor/             # Librairie Dompdf
    ├── creer_facture.php   # Formulaire de création
    ├── generer_facture.php # Script de génération PDF
    └── liste_factures.php  # Tableau de bord et recherche
