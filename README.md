# Oeil 360° Finance

Application web personnelle de gestion des finances en **Franc CFA (XOF)** — suivre chaque entrée et chaque sortie d'argent pour répondre en permanence à : *combien j'ai, d'où ça vient, où ça part.*

Projet d'entraînement aux standards **ASIN** (Architecture, Sécurité, Intégration, Normes) — traité avec le même sérieux qu'un projet professionnel.

---

## Fonctionnalités

### Vue 360° (Dashboard V2)

- **4 KPI tiles** : solde total, nombre de transactions sur la période, dépense moyenne journalière, catégorie la plus dépensière
- Solde par compte avec mini barre de progression (% du total)
- Résumé de la période : total entrées / total sorties / solde net
- Graphique camembert des dépenses par catégorie
- **Graphique barres groupées** : Revenus vs Dépenses sur 12 mois glissants
- Sélecteur de période : aujourd'hui / semaine / mois / année / dates personnalisées
- 10 dernières transactions avec lien "Voir toutes →"

### Transactions

- Saisie complète : montant, sens (entrée/dépense), date, catégorie, compte, note libre
- Historique paginé avec filtres combinables (période, catégorie, compte, sens, recherche par mot-clé)
- Modification et suppression

### Catégories

- Catégories personnalisables par type : Revenu / Dépense / Les deux
- Archivage (jamais de suppression destructrice si la catégorie est utilisée)
- Restauration des catégories archivées

### Comptes

- Gestion multi-comptes : Espèces, Mobile Money, Compte bancaire
- Solde calculé en temps réel (solde initial + transactions + transferts)
- Archivage / restauration

### Transferts entre comptes

- Déplacement d'argent entre deux comptes différents
- Non comptabilisé comme revenu ou dépense dans les totaux globaux

### Transactions récurrentes

- Définition d'une transaction qui se répète automatiquement
- Fréquences : quotidienne, hebdomadaire, mensuelle, annuelle
- Activation / désactivation
- Génération via commande Artisan : `php artisan transactions:generate-recurring`
- Chaque occurrence générée reste modifiable individuellement

---

## Stack technique V2

| Couche | Technologie |
| --- | --- |
| Backend | Laravel 13 (PHP 8.4.15 via WAMP64) |
| Authentification | Auth0 (`auth0/login` SDK v7) |
| Base de données | MySQL (InnoDB) |
| Frontend | Bootstrap 5.3.8 + Vanilla JS ES6 |
| Graphiques | Chart.js 4.4.7 |
| Icônes | Bootstrap Icons 1.11.3 |
| Tests | PHPUnit (66 tests, 214 assertions) |

---

## Installation locale (WAMP64)

### Prérequis

- WAMP64 avec **PHP 8.4.15** activé (Apache + MySQL)
- Composer
- Un compte Auth0 (gratuit)

### 1. Virtual Host WAMP

Dans WAMP → Apache → `httpd-vhosts.conf`, ajouter :

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

Dans `C:\Windows\System32\drivers\etc\hosts`, ajouter :

```text
127.0.0.1 oeil360.test
```

Redémarrer WAMP.

### 2. Installation PHP

```bash
# Toujours utiliser le PHP WAMP (pas le PHP système)
C:\wamp64\bin\php\php8.4.15\php.exe composer install

cp .env.example .env
C:\wamp64\bin\php\php8.4.15\php.exe artisan key:generate
```

### 3. Base de données

Créer la base dans phpMyAdmin ou MySQL CLI :

```sql
CREATE DATABASE oeil360finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configurer `.env` :

```env
DB_DATABASE=oeil360finance
DB_USERNAME=root
DB_PASSWORD=
```

```bash
C:\wamp64\bin\php\php8.4.15\php.exe artisan migrate
```

### 4. Auth0 — Configuration

1. Créer une application **Regular Web Application** sur [auth0.com](https://auth0.com)
2. Dans les settings de l'app Auth0 :
   - **Allowed Callback URLs** : `http://oeil360.test/auth/callback`
   - **Allowed Logout URLs** : `http://oeil360.test`
3. Renseigner `.env` :

```env
AUTH0_DOMAIN=your-tenant.auth0.com
AUTH0_CLIENT_ID=your_client_id
AUTH0_CLIENT_SECRET=your_client_secret
AUTH0_COOKIE_SECRET=une-chaine-aleatoire-de-32-caracteres-minimum
```

### 5. Lancement

Ouvrir [http://oeil360.test](http://oeil360.test) → redirige vers Auth0 si non connecté.

À la **première connexion**, les 9 catégories et 3 comptes par défaut sont créés automatiquement pour le nouvel utilisateur.

---

## Données par défaut (créées automatiquement à la première connexion Auth0)

**9 catégories :**

- Revenus : Salaire, Freelance, Autres revenus
- Dépenses : Alimentation, Transport, Logement, Santé, Loisirs, Autres dépenses

**3 comptes :**

- Caisse (espèces)
- Mobile Money
- Banque

---

## Commandes utiles

```bash
# Lancer les tests
C:\wamp64\bin\php\php8.4.15\php.exe artisan test

# Générer les transactions récurrentes dues (à planifier via cron en prod)
C:\wamp64\bin\php\php8.4.15\php.exe artisan transactions:generate-recurring

# Réinitialiser la base
C:\wamp64\bin\php\php8.4.15\php.exe artisan migrate:fresh
```

---

## Architecture API

Toutes les réponses suivent l'enveloppe :

```json
{
  "data": { ... },
  "meta": { ... },
  "error": null
}
```

Toutes les routes API requièrent une session Auth0 active (`middleware('auth')`).

| Ressource | Endpoint |
| --- | --- |
| Dashboard | `GET /api/dashboard` |
| Dashboard mensuel | `GET /api/dashboard/monthly` |
| Catégories | `GET/POST /api/categories` · `GET/PUT/DELETE /api/categories/{id}` · `POST /api/categories/{id}/restore` |
| Comptes | `GET/POST /api/accounts` · `GET/PUT/DELETE /api/accounts/{id}` · `POST /api/accounts/{id}/restore` |
| Transactions | `GET/POST /api/transactions` · `GET/PUT/DELETE /api/transactions/{id}` |
| Transferts | `GET/POST /api/transfers` · `GET/PUT/DELETE /api/transfers/{id}` |
| Récurrentes | `GET/POST /api/recurring-transactions` · `GET/PUT/DELETE /api/recurring-transactions/{id}` |

---

## Multi-utilisateurs

Chaque utilisateur Auth0 a ses propres données (catégories, comptes, transactions, transferts). L'isolation est garantie au niveau de chaque requête API : toutes les queries sont filtrées par `user_id = auth()->id()`. Un utilisateur ne peut jamais accéder aux données d'un autre (réponse 404 si tentative).

---

## Règles métier clés

- Devise unique : **Franc CFA (XOF)** — pas de multi-devise
- Montant à zéro refusé à la saisie
- Le sens (entrée/sortie) est toujours un choix explicite — jamais déduit automatiquement
- Solde d'un compte = `solde_initial + Σ(entrées) − Σ(sorties) + Σ(transferts_entrants) − Σ(transferts_sortants)`
- Un transfert entre comptes ne compte **jamais** comme revenu ou dépense globale
- Suppression d'une catégorie / d'un compte utilisé → **archivage** (jamais de suppression destructrice)

---

## Contexte du projet

Ce projet est développé par **Élisée Atondé** dans le cadre de sa formation **ASIN (Bénin)** — l'objectif est d'appliquer rigoureusement les standards professionnels (nommage, sécurité applicative, Auth0, Git/Gitflow, TDD, revue de code) sur un cas réel et personnel.
