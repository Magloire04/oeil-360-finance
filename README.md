# Oeil 360° Finance

[![CI](https://github.com/Magloire04/oeil-360-finance/actions/workflows/ci.yml/badge.svg)](https://github.com/Magloire04/oeil-360-finance/actions/workflows/ci.yml)

Application web de gestion des finances personnelles en **Franc CFA (XOF)** : suivre chaque entrée et chaque sortie d'argent pour répondre en permanence à *combien j'ai, d'où ça vient, où ça part.*

- **Statut** : v1 publique en production
- **En ligne** : [oeil360finance.bytechnum.com](https://oeil360finance.bytechnum.com)
- **Contact** : [oeil360finance@bytechnum.com](mailto:oeil360finance@bytechnum.com)

---

## Aperçu

Oeil 360° Finance est un gestionnaire de budget personnel multi-utilisateurs. Chaque personne gère ses comptes, ses catégories, ses transactions, ses transferts et ses opérations récurrentes, et retrouve une vue synthétique de sa situation sur la période de son choix. L'authentification passe par Auth0 (dont la connexion Google), l'application est installable comme une PWA, et le traitement des données respecte la loi n°2017-20 du Bénin (APDP).

---

## Fonctionnalités

### Vue 360° (Dashboard)

- 4 indicateurs clés : solde total, nombre de transactions sur la période, dépense moyenne journalière, catégorie la plus dépensière
- Solde par compte avec mini barre de progression (part du total)
- Résumé de la période : total entrées, total sorties, solde net
- Graphique camembert des dépenses par catégorie
- Graphique barres groupées : Revenus vs Dépenses sur 12 mois glissants
- Sélecteur de période : aujourd'hui, semaine, mois, année, dates personnalisées
- 10 dernières transactions avec lien vers l'historique complet

### Transactions

- Saisie complète : montant, sens (entrée ou dépense), date, catégorie, compte, note libre
- Historique paginé avec filtres combinables (période, catégorie, compte, sens, recherche par mot-clé)
- Modification et suppression

### Catégories

- Catégories personnalisables par type : Revenu, Dépense ou Les deux
- Archivage (jamais de suppression destructrice si la catégorie est utilisée) et restauration

### Comptes

- Gestion multi-comptes : Espèces, Mobile Money, Compte bancaire
- Solde calculé en temps réel (solde initial + transactions + transferts)
- Archivage et restauration

### Transferts entre comptes

- Déplacement d'argent entre deux comptes différents
- Jamais comptabilisé comme revenu ou dépense dans les totaux globaux

### Transactions récurrentes

- Définition d'une transaction qui se répète automatiquement
- Fréquences : quotidienne, hebdomadaire, mensuelle, annuelle
- Activation et désactivation ; chaque occurrence générée reste modifiable individuellement
- Génération via `php artisan transactions:generate-recurring` (planifiable en cron)

---

## Application installable (PWA)

L'application est une Progressive Web App : elle peut être installée sur mobile et bureau depuis le navigateur.

- Manifeste web (`public/manifest.webmanifest`), thème et icônes (dont icône maskable et `apple-touch-icon`)
- Service worker (`public/sw.js`) et page hors-ligne (`public/offline.html`)
- Métadonnées regroupées dans `resources/views/partials/pwa.blade.php` et incluses dans chaque page

```

---

## Confidentialité et conformité APDP (loi n°2017-20)

Le projet applique les principes de la loi béninoise sur la protection des données personnelles :

- **Consentement versionné** : à chaque évolution de la politique, l'utilisateur repasse par l'écran de consentement (`/consent`) avant d'accéder à l'application
- **Politique de confidentialité publique** et versionnée : [`/politique-confidentialite`](https://oeil360finance.bytechnum.com/politique-confidentialite)
- **Minimisation des données d'usage** : les statistiques sont pseudonymes (ni IP nominative, ni contenu des opérations) et purgées automatiquement au-delà de la rétention (`php artisan oeil360:prune-activity-events`, planifié quotidiennement)
- **Droit d'accès** : export complet des données depuis l'espace « Mon compte »
- **Droit à l'effacement** : suppression du compte (`DELETE /api/profile`) qui anonymise les données et bloque toute recréation silencieuse via le SSO (écran `/compte-supprime` et reconnexion fédérée via `/auth/relogin`)
- **Conservation limitée** : purge des comptes inactifs au-delà de `DATA_RETENTION_YEARS` (`php artisan oeil360:purge-inactive`, dry-run par défaut, `--execute` pour la suppression réelle)

---

## Stack technique

| Couche | Technologie |
| --- | --- |
| Backend | Laravel 13 (PHP 8.4) |
| Authentification | Auth0 (`auth0/login` SDK v7), connexion sociale Google |
| Base de données | MySQL (InnoDB) en production, SQLite en mémoire pour les tests |
| Frontend | Bootstrap 5.3.8 + JavaScript Vanilla ES6 |
| Graphiques | Chart.js 4.4.7 |
| Icônes | Bootstrap Icons 1.11.3 |
| Tests | PHPUnit (119 tests, 343 assertions) |
| Qualité | Laravel Pint (style) + PHPStan / Larastan (analyse statique) |

---

## Architecture API

Toutes les réponses suivent la même enveloppe :

```json
{
  "data": { },
  "meta": { },
  "error": null
}
```

Les routes API sont protégées par le guard de session Auth0 (`auth:web`). Le pipeline web ajoute la journalisation d'usage pseudonyme et le blocage des identités supprimées ; les pages protégées passent en plus par `consent` et `activity`, et l'espace admin par `admin`.

| Ressource | Endpoints |
| --- | --- |
| Dashboard | `GET /api/dashboard` ; `GET /api/dashboard/monthly` |
| Catégories | `GET/POST /api/categories` ; `GET/PUT/DELETE /api/categories/{id}` ; `POST /api/categories/{id}/restore` |
| Comptes | `GET/POST /api/accounts` ; `GET/PUT/DELETE /api/accounts/{id}` ; `POST /api/accounts/{id}/restore` |
| Transactions | `GET/POST /api/transactions` ; `GET/PUT/DELETE /api/transactions/{id}` |
| Transferts | `GET/POST /api/transfers` ; `GET/PUT/DELETE /api/transfers/{id}` |
| Récurrentes | `GET/POST /api/recurring-transactions` ; `GET/PUT/DELETE /api/recurring-transactions/{id}` |
| Profil | `DELETE /api/profile` (suppression définitive du compte) |
| Observabilité admin | `GET /api/admin/metrics/{overview, user-growth, active-users, operations, top-features, traffic, performance}` (middleware `admin`) |

---

## Installation locale (WAMP64)

### Prérequis

- WAMP64 avec **PHP 8.4** activé (Apache + MySQL)
- Composer
- Un compte Auth0 (gratuit)

### 1. Virtual Host

Dans WAMP, `httpd-vhosts.conf` :

```apache
<VirtualHost *:80>
    ServerName oeil360.test
    DocumentRoot "C:/wamp64/www/oeil_360_finance/public"
    <Directory "C:/wamp64/www/oeil_360_finance/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Dans `C:\Windows\System32\drivers\etc\hosts` :

```text
127.0.0.1 oeil360.test
```

Redémarrer WAMP.

### 2. Dépendances et environnement

```bash
# Toujours utiliser le PHP de WAMP (pas le PHP système)
C:\wamp64\bin\php\php8.4.15\php.exe composer install

cp .env.example .env
C:\wamp64\bin\php\php8.4.15\php.exe artisan key:generate
```

### 3. Base de données

```sql
CREATE DATABASE oeil360finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```env
DB_DATABASE=oeil360finance
DB_USERNAME=root
DB_PASSWORD=
```

```bash
C:\wamp64\bin\php\php8.4.15\php.exe artisan migrate
```

### 4. Lancement

Ouvrir [http://oeil360.test](http://oeil360.test) : redirection vers Auth0 si non connecté. À la première connexion, les 9 catégories et 3 comptes par défaut sont créés automatiquement.

---

## Configuration

Les clés se trouvent dans `.env.example` (versionné ; le `.env` réel ne l'est jamais).

### Auth0

Créer une application **Regular Web Application** sur [auth0.com](https://auth0.com), puis renseigner :

```env
AUTH0_DOMAIN=votre-tenant.auth0.com
AUTH0_CLIENT_ID=...
AUTH0_CLIENT_SECRET=...
AUTH0_COOKIE_SECRET=chaine-aleatoire-de-32-caracteres-minimum
AUTH0_REDIRECT_URI=http://oeil360.test/auth/callback
---

## Données par défaut (créées à la première connexion)

**9 catégories**

- Revenus : Salaire, Freelance, Autres revenus
- Dépenses : Alimentation, Transport, Logement, Santé, Loisirs, Autres dépenses

**3 comptes**

- Caisse (espèces), Mobile Money, Banque

---

## Tests et qualité

```bash
# Suite de tests (PHPUnit)
php artisan test

# Style de code
./vendor/bin/pint --test

# Analyse statique
./vendor/bin/phpstan analyse
```

La CI GitHub Actions (`.github/workflows/ci.yml`) exécute les tests (SQLite en mémoire), Pint et PHPStan sur PHP 8.4 à chaque push et pull request vers `main` et la branche d'intégration `feature/OEIL360FINANCE-v2`.

---

## Commandes Artisan utiles

```bash
# Générer les transactions récurrentes dues (planifiable en cron)
php artisan transactions:generate-recurring

# Purger les événements d'usage au-delà de la rétention (minimisation APDP)
php artisan oeil360:prune-activity-events

# Purger/anonymiser les comptes inactifs (dry-run par défaut ; --execute pour agir)
php artisan oeil360:purge-inactive --execute

# Promouvoir (ou révoquer avec --revoke) un compte administrateur
php artisan oeil360:make-admin utilisateur@example.com

# Réinitialiser la base
php artisan migrate:fresh
```

---

## Déploiement

Flux de livraison :

1. Branche `feature/OEIL360FINANCE-{desc}` créée depuis la branche d'intégration
2. Pull Request vers `feature/OEIL360FINANCE-v2` (intégration)
3. Pull Request de release de `feature/OEIL360FINANCE-v2` vers `main` (production)
4. Déploiement sur le serveur : `bash deploy.sh`

`deploy.sh` passe l'application en maintenance, récupère `main`, installe les dépendances de production, applique les migrations, reconstruit les caches (config et vues) puis lève la maintenance. Le workflow `release.yml` publie une Release GitHub avec changelog lorsqu'un tag `v*` est poussé.

---

## Règles métier clés

- Devise unique : **Franc CFA (XOF)**, pas de multi-devise
- Montant à zéro refusé à la saisie
- Le sens (entrée ou sortie) est toujours un choix explicite, jamais déduit automatiquement
- Solde d'un compte = `solde_initial + Σ(entrées) − Σ(sorties) + Σ(transferts_entrants) − Σ(transferts_sortants)`
- Un transfert entre comptes ne compte jamais comme revenu ou dépense globale
- Suppression d'une catégorie ou d'un compte utilisé : archivage, jamais de suppression destructrice

---

## Hors périmètre (v1)

- Multi-devise
- Application mobile native (la PWA en tient lieu)
- Partage de comptes entre plusieurs utilisateurs

---

**Licence** : propriétaire. Tous droits réservés. Voir [LICENSE](LICENSE).
