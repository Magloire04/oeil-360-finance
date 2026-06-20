# SPEC — Oeil_360_finance (Gestion personnelle des dépenses)

> Nom de travail. À renommer librement.
> Ce document dit **QUOI** construire — pas comment. La conception technique (architecture, modèle de données, stack détaillée) vient après, en Plan Mode avec Claude Code, une fois ce document validé.

---

## 1. Objectif

Avoir une vue à 360° sur l'ensemble de mes finances personnelles : chaque entrée (revenu) et chaque sortie (dépense) d'argent, **même la plus petite**, doit pouvoir être enregistrée et retrouvée. L'application doit répondre en permanence à : *combien j'ai, d'où ça vient, où ça part.*

## 2. Utilisateur

Application **mono-utilisateur**, usage strictement personnel. Pas de notion de compte partagé, de rôles, ou d'équipe. Accès via navigateur web, **mobile-first** (utilisation principale prévue depuis un téléphone).

## 3. Comportement attendu

### 3.1 Saisie d'une transaction

Chaque transaction enregistrée comporte :
- un **montant** (en Francs CFA, décimales autorisées)
- un **sens** : entrée (revenu) ou sortie (dépense) — choisi explicitement, pas déduit du signe du montant
- une **date** (par défaut aujourd'hui, modifiable)
- une **catégorie** (une seule par transaction)
- un **compte / moyen de paiement** associé (une seule par transaction)
- une **note** libre, optionnelle

Aucune transaction n'est "trop petite" pour être enregistrée — pas de montant minimum imposé.

### 3.2 Catégories

- Liste de catégories **personnalisable** par l'utilisateur (créer, renommer, archiver)
- Exemples de départ : Alimentation, Transport, Logement, Santé, Loisirs, Salaire, Freelance, Imprévus
- Une catégorie peut être marquée "revenu" ou "dépense" pour limiter les erreurs de saisie, mais ce n'est qu'une aide, pas une contrainte technique bloquante

### 3.3 Comptes / moyens de paiement

- Plusieurs comptes possibles : ex. Espèces, Mobile Money (MTN, Moov), Compte bancaire
- Chaque compte a un **solde courant**, calculé automatiquement
- **Transfert entre comptes** possible (ex. retrait espèces depuis le compte bancaire) — un transfert n'est ni un revenu ni une dépense au sens global, mais déplace de l'argent d'un compte à l'autre

### 3.4 Vue 360° (tableau de bord)

- Solde total (tous comptes confondus) et solde par compte
- Total des entrées et des sorties sur une période choisie (jour / semaine / mois / année / personnalisée)
- Répartition des dépenses par catégorie sur la période (vue visuelle : ex. camembert ou barres)
- Évolution du solde dans le temps (courbe)
- Liste des dernières transactions

### 3.5 Historique, recherche & filtres

- Historique complet de toutes les transactions, trié par date (plus récent en premier)
- Chaque transaction est **modifiable** et **supprimable**
- Filtres combinables : par période, par catégorie, par compte, par sens (entrée/sortie)
- Recherche par mot-clé dans la note

### 3.6 Transactions récurrentes

- Possibilité de définir une transaction qui se répète automatiquement (ex. salaire mensuel, abonnement)
- Fréquence : quotidienne, hebdomadaire, mensuelle, annuelle
- Chaque occurrence générée reste modifiable/supprimable individuellement sans affecter les autres occurrences

## 4. Règles métier

- Une transaction appartient à **exactement une** catégorie et **un** compte
- Le solde d'un compte = somme des entrées − somme des sorties sur ce compte, **transferts inclus**
- Toute création, modification ou suppression d'une transaction **recalcule immédiatement** les soldes et le tableau de bord affichés — jamais de valeur en cache obsolète visible
- Devise unique : **Franc CFA (XOF)** — pas de gestion multi-devise en V1
- Le sens (entrée/sortie) est un choix explicite de l'utilisateur, jamais déduit automatiquement d'un signe + ou −

## 5. Cas limites à gérer explicitement

- **Montant à zéro** : refusé à la saisie (aucun sens métier)
- **Montant avec décimales** (centimes) : doit être supporté correctement, sans erreur d'arrondi visible sur les totaux
- **Suppression d'une catégorie déjà utilisée** : les transactions existantes ne doivent jamais être supprimées ou orphelines — la catégorie doit être archivée (conservée en lecture seule) plutôt que supprimée définitivement si elle est utilisée
- **Suppression d'un compte avec des transactions existantes** : même logique — archivage plutôt que suppression destructrice
- **Aucune transaction sur la période sélectionnée** : le tableau de bord doit afficher un état vide explicite ("Aucune transaction sur cette période"), jamais une erreur ou un écran cassé
- **Transfert entre comptes** : ne doit jamais être compté comme un revenu ou une dépense dans les totaux globaux d'entrées/sorties, uniquement comme un mouvement interne

## 6. Hors périmètre pour cette V1 (explicite)

Ces points sont **volontairement exclus** de cette première version. Toute envie de les ajouter "en cours de route" doit être documentée comme un écart de scope, pas glissée silencieusement dans le code (cf. standards ASIN — "tout écart est une décision, pas un oubli") :

- Multi-utilisateur ou partage de compte avec une autre personne
- Application mobile native (le web doit être responsive/mobile-first, mais pas d'app iOS/Android séparée)
- Connexion bancaire automatique ou import automatique de relevés
- Budgets prévisionnels et alertes de dépassement de budget
- Gestion multi-devise
- Export comptable/fiscal avancé (un export simple CSV des transactions reste envisageable, mais pas un module de reporting fiscal)

## 7. Prochaine étape

Ce document doit être **relu et validé** (ou corrigé) avant de passer à la suite. Une fois validé, l'étape suivante est de demander à Claude Code de générer le **PLAN** (fichier de tâches numérotées avec fichiers concernés et définition de "Done"), en **Plan Mode** (`Shift+Tab`), à partir de ce SPEC — pas de code avant cette étape.
