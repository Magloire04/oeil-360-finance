# CLAUDE.md - Oeil_360_finance

## Contexte projet

Application web personnelle de gestion des dépenses (revenus & dépenses, vue 360°). Usage mono-utilisateur. **Projet d'entraînement** pour appliquer rigoureusement les standards ASIN appris en formation - traiter ce projet perso avec le même sérieux qu'un projet ASIN réel, c'est tout l'objectif de l'exercice.

- **Stack** : Laravel (API backend) + Bootstrap 5 + HTML5 + JavaScript Vanilla (frontend)
- **Devise** : Franc CFA (XOF) uniquement
- **Hébergement** : développement local pour l'instant, pas encore déployé
- **Spécification complète** : voir `SPEC.md` à la racine - ne pas commencer à coder sans l'avoir lu

## Skill à utiliser systématiquement

Ce projet doit respecter le skill **`asin-dev-standards`** (nommage, sécurité applicative, qualité de code, Git/Pull Requests, méthodologie Claude Code). Le skill prime sur toute habitude personnelle ou convention par défaut d'un framework.

## Méthodologie à suivre

1. **SPEC** → déjà rédigée (`SPEC.md`), validée par Élisée
2. **PLAN** → à générer en **Plan Mode** (`Shift+Tab`) à partir du SPEC, avant tout code. Revue humaine obligatoire avant de passer à l'étape suivante.
3. **CODE** → tâche par tâche, en cochant le PLAN, TDD strict (tests d'abord)

Ne jamais sauter directement de SPEC à CODE.

## Conventions critiques (rappel - détail complet dans le skill)

- Nommage : PascalCase (composants), camelCase (variables/fonctions), kebab-case (fichiers de config)
- API : enveloppe `{ data, meta, error }` systématique, codes HTTP sémantiques, routes REST en pluriel
- Git : `feature/OEIL360FINANCE-{desc}` même en solo - pratique délibérée du Gitflow ASIN
- Sécurité : aucun secret en dur, `.env.example` versionné (jamais le `.env` réel), requêtes paramétrées uniquement

## Interdit

- Pas de code avant qu'un PLAN existe et soit validé
- Pas de montant ou solde calculé sans repasser par les règles métier définies dans `SPEC.md` (section 4)
- Pas de suppression destructrice de catégorie/compte déjà utilisé (voir cas limites du SPEC, section 5)
