## Description
<!-- Décris le changement et pourquoi il est nécessaire -->

## Type de changement
- [ ] Bug fix
- [ ] Nouvelle feature
- [ ] Conformité / sécurité (APDP, Auth0…)
- [ ] CI / qualité de code
- [ ] Documentation

## Checklist

### Tests & qualité
- [ ] `php artisan test` → tous les tests passent
- [ ] `./vendor/bin/pint --test` → aucune violation de style
- [ ] `./vendor/bin/phpstan analyse` → aucune erreur PHPStan (niveau 5)

### Sécurité
- [ ] Aucun secret commité (`.env` dans `.gitignore`, pas de clé en dur)
- [ ] Les nouvelles routes API sont protégées par `auth:web`
- [ ] Les nouvelles routes web sont protégées par `auth` + `consent`

### APDP
- [ ] Si données personnelles impliquées → ajout de colonnes nullable uniquement
- [ ] Migrations additives uniquement (pas de suppression de colonne)
- [ ] Aucune donnée utilisateur accessible par un autre utilisateur (vérifier le `user_id` dans les queries)

### Frontend
- [ ] Aucun `id="..."` ou `data-bs-*` modifié (ciblés par le JS)
- [ ] Aucun appel `window.api` dans les scripts de démo/aide (données fictives locales uniquement)
