# ⚡ RÈGLES DE TRAVAIL : DISCIPLINE & EXCELLENCE FREELANCE

**Règle d'or :** *Pas d'excuses, pas de procrastination. Chaque jour compte pour se rapprocher de l'indépendance financière.*

---

## 🕒 1. Organisation du Temps (Blocs Quotidiens)

Chaque jour de travail est divisé en blocs stricts pour lier apprentissage technique et action commerciale :

* **Bloc 1 : Code, Correction & Développement (2h à 3h)**
  * Travail sur les projets vitrines (`FidFacture`, `MiniGestionClient`) ou résolution de bugs réels.
  * Application directe des règles de sécurité (PDO, requêtes préparées, XSS, CSRF).
* **Bloc 2 : Offres & Visibilité (1h)**
  * Travail sur le dossier `09_OFFRES_FREELANCE` (optimisation des fiches ComeUp/Fiverr, création de visuels portfolio, prospection).
* **Bloc 3 : Capitalisation & Preuve (30 min)**
  * Remplissage obligatoire du `Journal_de_preuves.md` (noter chaque bug résolu, chaque concept maîtrisé).

---

## 🛡️ 2. Standards de Code & Sécurité (PHP / MySQL)

Aucun code ne quitte votre machine locale XAMPP sans respecter ces critères :

1. **Connexion & Requêtes :** Connexion exclusivement via **PDO** et utilisation systématique de **requêtes préparées** (zéro injection SQL tolérée).
2. **Nettoyage des entrées/sorties :** 
   * Échappement des affichages HTML avec `htmlspecialchars()` pour contrer les failles XSS.
   * Utilisation de tokens anti-CSRF pour les formulaires sensibles.
3. **Gestion des erreurs :** Pas de `die()` brut en production ; gestion propre des exceptions try/catch et journalisation des erreurs.
4. **Modularité :** Code propre, indenté, commenté et réutilisable pour de futures interventions de maintenance.

---

## 📊 3. Gestion des Projets & Suivi

1. **Test local d'abord :** Tout développement est testé, validé et validé sous XAMPP avant d'être archivé ou présenté.
2. **Traçabilité :** Dès qu'un problème complexe est résolu, la solution est immédiatement consignée dans le `Journal_de_preuves.md` avec un extrait de code "Avant / Après".
3. **Orientation Client :** Penser chaque script ou module sous l'angle de la valeur pour le client : *Est-ce que cela résout un problème critique ? Est-ce rapide à déployer ?*

---

## 🚫 4. Ce qui est INTERDIT

* ❌ Attendre d'être "parfait" pour publier une offre ou postuler.
* ❌ Laisser un projet sans mesures de sécurité de base (PDO / Échappement).
* ❌ Passer une journée entière sans ligne de code ni action commerciale.