# Oeil 360° Finance

Application web personnelle de gestion des finances en **Franc CFA (XOF)** — suivre chaque entrée et chaque sortie d'argent pour répondre en permanence à : *combien j'ai, d'où ça vient, où ça part.*

Projet d'entraînement aux standards **ASIN** (Architecture, Sécurité, Intégration, Normes) — traité avec le même sérieux qu'un projet professionnel.

---

## Fonctionnalités

### Vue 360° (Dashboard)

- Solde total et solde par compte (Espèces, Mobile Money, Banque)
- Résumé de la période : total entrées / total sorties / solde net
- Graphique camembert des dépenses par catégorie
- Courbe d'évolution du solde dans le temps
- Sélecteur de période : aujourd'hui / semaine / mois / année / dates personnalisées
- 10 dernières transactions

### Transactions

- Saisie complète : montant, sens (entrée/dépense), date, catégorie, compte, note libre
- Historique paginé avec filtres combinables (période, catégorie, compte, sens, recherche par mot-clé)
- Modification et suppression

### Catégories

- Catégories personnalisables par type : Revenu / Dépense / Les deux
- Archivage (jamais de suppression destructrice si la catégorie est utilisée)
- Restauration des catégories archivées
- Données par défaut : Alimentation, Transport, Logement, Santé, Loisirs, Imprévus, Salaire, Freelance, Autre

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

## Stack technique

| Couche | Technologie |
| --- | --- |
| Backend | Laravel 13 (PHP 8.5) |
| Base de données | MySQL (InnoDB) |
| Frontend | Bootstrap 5.3.8 + Vanilla JS ES6 |
| Graphiques | Chart.js 4.4.7 |
| Icônes | Bootstrap Icons 1.11.3 |
| Tests | PHPUnit (61 tests, 195 assertions) |

---

## Installation locale (WAMP / XAMPP)

### Prérequis

- PHP 8.2+
- MySQL
- Composer
- Serveur web local (WAMP64, XAMPP, Laragon…)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/Magloire04/oeil-360-finance.git
cd oeil-360-finance

# 2. Installer les dépendances PHP
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate
```

Éditer `.env` avec vos paramètres MySQL :

```env
DB_DATABASE=oeil360finance
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Créer la base de données (via phpMyAdmin ou MySQL CLI)
# CREATE DATABASE oeil360finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. Lancer les migrations et les seeders
php artisan migrate --seed

# 6. Démarrer le serveur
php artisan serve
```

Ouvrir [http://localhost:8000](http://localhost:8000)

---

## Données par défaut (après `db:seed`)

**9 catégories :**

- Dépenses : Alimentation, Transport, Logement, Santé, Loisirs, Imprévus
- Revenus : Salaire, Freelance
- Les deux : Autre

**3 comptes :**

- Espèces (solde initial : 0 XOF)
- MTN Mobile Money (solde initial : 0 XOF)
- Compte bancaire (solde initial : 0 XOF)

---

## Commandes utiles

```bash
# Lancer les tests
php artisan test

# Générer les transactions récurrentes dues (à planifier via cron en prod)
php artisan transactions:generate-recurring

# Réinitialiser la base avec les données par défaut
php artisan migrate:fresh --seed
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

| Ressource | Endpoint |
| --- | --- |
| Dashboard | `GET /api/dashboard` |
| Catégories | `GET/POST /api/categories` · `GET/PUT/DELETE /api/categories/{id}` · `POST /api/categories/{id}/restore` |
| Comptes | `GET/POST /api/accounts` · `GET/PUT/DELETE /api/accounts/{id}` · `POST /api/accounts/{id}/restore` |
| Transactions | `GET/POST /api/transactions` · `GET/PUT/DELETE /api/transactions/{id}` |
| Transferts | `GET/POST /api/transfers` · `GET/PUT/DELETE /api/transfers/{id}` |
| Récurrentes | `GET/POST /api/recurring-transactions` · `GET/PUT/DELETE /api/recurring-transactions/{id}` |

---

## Règles métier clés

- Devise unique : **Franc CFA (XOF)** — pas de multi-devise en V1
- Montant à zéro refusé à la saisie
- Le sens (entrée/sortie) est toujours un choix explicite — jamais déduit automatiquement
- Solde d'un compte = `solde_initial + Σ(entrées) − Σ(sorties) + Σ(transferts_entrants) − Σ(transferts_sortants)`
- Un transfert entre comptes ne compte **jamais** comme revenu ou dépense globale
- Suppression d'une catégorie / d'un compte utilisé → **archivage** (jamais de suppression destructrice)

---

## Périmètre V1 (hors scope volontaire)

- Multi-utilisateur
- Application mobile native
- Connexion bancaire automatique
- Budgets prévisionnels et alertes
- Multi-devise
- Export comptable avancé

---

## Contexte du projet

Ce projet est développé par **Élisée Atondé** dans le cadre de sa formation **ASIN (Bénin)** — l'objectif est d'appliquer rigoureusement les standards professionnels (nommage, sécurité applicative, Git/Gitflow, TDD, revue de code) sur un cas réel et personnel.
