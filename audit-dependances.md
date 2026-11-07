# Audit des dépendances Composer : Oeil 360 Finance

| | |
|---|---|
| Date de l'audit | 7 novembre 2026 |
| Périmètre | Dépendances PHP verrouillées dans `composer.lock` (version en production : commit `a23d051`, identique à la branche `feature/OEIL360FINANCE-v2`) |
| Outil | `composer audit --locked` (base d'avis Packagist et GitHub Security Advisories) |
| Résultat | 11 avis de sécurité sur 5 paquets, tous en dépendances de production. Aucun paquet abandonné. |
| Conclusion | Aucune faille exploitable dans l'usage actuel de l'application, une exposition faible sur un point. Mise à jour recommandée cette semaine : l'étape `composer audit` de la CI bloque toute nouvelle Pull Request tant que ces avis sont ouverts. |

## 1. Dépendances concernées

| Paquet | Version installée | Version corrigée | Avis | Gravité la plus haute | Exposition dans l'application |
|---|---|---|---|---|---|
| `phpoffice/phpspreadsheet` | 5.9.0 | 5.10.0 | 4 | Haute | Aucune |
| `symfony/http-foundation` | 8.1.9 | 8.1.11 | 4 | Non renseignée | Faible (1 avis), aucune pour les 3 autres |
| `symfony/http-kernel` | 8.1.9 | 8.1.11 | 1 | Non renseignée | Aucune |
| `symfony/mime` | 8.1.7 | 8.1.10 | 1 | Moyenne | Aucune à ce jour |
| `symfony/routing` | 8.1.9 | 8.1.11 | 1 | Non renseignée | Aucune |

Aucune de ces dépendances n'est déclarée directement dans `composer.json`. PhpSpreadsheet est tiré par `maatwebsite/excel` (contrainte `^5.8`, export Excel du relevé), les composants Symfony par `laravel/framework`.

## 2. Vulnérabilités identifiées

### phpoffice/phpspreadsheet 5.9.0

L'application ne fait qu'écrire des fichiers Excel (export du relevé financier). Elle n'importe ni ne lit aucun fichier tableur fourni par un utilisateur.

| Identifiant | Gravité | Description | Exposition |
|---|---|---|---|
| CVE-2026-108360 ([GHSA-2phh-27fw-q4gc](https://github.com/advisories/GHSA-2phh-27fw-q4gc)) | Moyenne | Contournement de la liste blanche anti-SSRF de `Drawing::setPath()` : une redirection HTTP n'est pas revérifiée lors du chargement d'une image externe. | Aucune. Le seul appel charge un fichier local fixe, `public/images/oeil360-icon-white-bg.png` (`app/Exports/Sheets/SummarySheet.php:87`). |
| CVE-2026-108359 ([GHSA-j2w7-387v-m2rq](https://github.com/advisories/GHSA-j2w7-387v-m2rq)) | Haute | Lecteur ODS : l'attribut `number-rows-repeated` n'est pas borné, ce qui permet d'épuiser le processeur et la mémoire (déni de service). | Aucune. Aucun lecteur de fichier n'est utilisé. |
| CVE-2026-108358 ([GHSA-3x94-m842-937c](https://github.com/advisories/GHSA-3x94-m842-937c)) | Haute | Contournement du filtre `XmlScanner` par des séquences d'échappement ISO-2022 : injection d'entité externe XML (XXE) et déni de service à la lecture d'un fichier. | Aucune, pour la même raison. |
| CVE-2026-108357 ([GHSA-rxj4-h86g-25gc](https://github.com/advisories/GHSA-rxj4-h86g-25gc)) | Haute | Contournement du même filtre par double encodage (condition de concurrence dans `unentity()`) : XXE et déni de service à la lecture d'un fichier. | Aucune, pour la même raison. |

### symfony/http-foundation 8.1.9

| Identifiant | Gravité | Description | Exposition |
|---|---|---|---|
| [GHSA-5mx8-mwrp-5693](https://github.com/symfony/symfony/security/advisories/GHSA-5mx8-mwrp-5693) | Non renseignée | Les URL générées par l'application peuvent pointer vers un hôte choisi par le client. | Faible. L'adresse de retour Auth0 est fixe, lue dans la configuration (`config/auth0.php:54`). En revanche, la canonical, `og:url` et les liens de la page d'accueil sont générés à partir de la requête (`resources/views/layout-public.blade.php:13` et `:21`). Les pages HTML ne sont pas mises en cache (`Cache-Control: no-cache, private`), ce qui limite le risque d'empoisonnement de cache. |
| CVE-2026-118514 ([avis Symfony](https://symfony.com/cve-2026-118514)) | Non renseignée | Les requêtes `HEAD` contournent l'option `methods` des règles `access_control` et des pare-feux du composant Symfony Security (correctif incomplet de CVE-2026-45075). | Aucune. Le composant Symfony Security n'est pas installé ; l'accès est contrôlé par les middlewares Laravel (`auth`, `consent`, `admin`). |
| [GHSA-3h27-hjv7-fqjx](https://github.com/symfony/symfony/security/advisories/GHSA-3h27-hjv7-fqjx) | Non renseignée | Des règles d'accès par chemin appliquées par le serveur web, devant PHP, peuvent être contournées en ajoutant le chemin après le nom du script (`/index.php/chemin`). | Aucune. `public/.htaccess` ne contient aucune règle d'accès par chemin ; les routes protégées sont filtrées dans Laravel. |
| [GHSA-xg2q-4rrq-657q](https://github.com/symfony/symfony/security/advisories/GHSA-xg2q-4rrq-657q) | Non renseignée | Le raccourci `private_ranges` des proxys de confiance accepte comme proxy un client qui présente une adresse IPv4 mappée, 6to4, Teredo ou NAT64. | Aucune. Aucun proxy de confiance n'est configuré (pas d'appel à `trustProxies()` dans `bootstrap/app.php`). |

### symfony/http-kernel 8.1.9

| Identifiant | Gravité | Description | Exposition |
|---|---|---|---|
| [GHSA-wg5q-m2ww-f3c9](https://github.com/symfony/symfony/security/advisories/GHSA-wg5q-m2ww-f3c9) | Non renseignée | Les fragments rendus en ligne laissent des en-têtes `Forwarded` non fiables fixer l'hôte, le schéma et le port des URL générées pour la requête principale. | Aucune. Laravel n'utilise pas le rendu de fragments de HttpKernel. |

### symfony/mime 8.1.7

| Identifiant | Gravité | Description | Exposition |
|---|---|---|---|
| CVE-2026-45070 ([avis Symfony](https://symfony.com/cve-2026-45070)) | Moyenne | Injection d'en-têtes d'e-mail par des caractères non autorisés dans les noms de paramètres MIME. | Aucune à ce jour : aucun code n'envoie d'e-mail (pas de `Mailable` ni de notification par e-mail). À réévaluer si l'envoi d'e-mails est ajouté. |

### symfony/routing 8.1.9

| Identifiant | Gravité | Description | Exposition |
|---|---|---|---|
| CVE-2026-118516 ([avis Symfony](https://symfony.com/cve-2026-118516)) | Non renseignée | Dans les correspondances d'URL compilées, un paramètre d'hôte peut correspondre au début du chemin et contourner un contrôle d'accès basé sur le chemin. | Aucune. Aucune route n'est liée à un hôte (`Route::domain`) et le cache de routes n'est pas utilisé (`deploy.sh:35`). |

## 3. Corrections recommandées

### R1. Mettre à jour les cinq paquets (priorité haute)

Sur une branche `feature/OEIL360FINANCE-...` partie de `feature/OEIL360FINANCE-v2` :

```bash
composer update phpoffice/phpspreadsheet symfony/http-foundation symfony/http-kernel symfony/routing symfony/mime --with-dependencies
```

Résultat attendu, vérifié par une simulation (`--dry-run`) sur le verrou de production le 7 novembre 2026 :

| Paquet | Avant | Après |
|---|---|---|
| `phpoffice/phpspreadsheet` | 5.9.0 | 5.10.0 |
| `symfony/http-foundation`, `symfony/http-kernel`, `symfony/routing` | 8.1.9 | 8.1.11 |
| `symfony/mime` | 8.1.7 | 8.1.10 |

La commande met aussi à jour quelques dépendances voisines (`symfony/console`, `symfony/error-handler`, `symfony/string`, les contrats Symfony en 3.8). Aucune version majeure ne change et la contrainte `^5.8` de `maatwebsite/excel` reste respectée.

Vérifications avant de livrer :

- `composer audit --locked` ne signale plus aucun avis ;
- `php artisan test`, `./vendor/bin/pint --test` et `./vendor/bin/phpstan analyse` passent ;
- le relevé se télécharge en Excel et en PDF depuis « Mon compte » (PhpSpreadsheet change de version) ;
- une connexion puis une déconnexion Auth0 fonctionnent.

Livraison selon le circuit habituel : Pull Request vers v2, release v2 vers main, puis `deploy.sh`.

### R2. Restreindre les hôtes acceptés (défense en profondeur)

Pour ne plus dépendre de Symfony sur la famille de failles « hôte choisi par le client » (GHSA-5mx8-mwrp-5693), activer le middleware des hôtes de confiance de Laravel dans `bootstrap/app.php` :

```php
$middleware->trustHosts(
    at: function (): array {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return is_string($host) && $host !== '' ? ['^'.preg_quote($host).'$'] : [];
    },
    subdomains: false,
);
```

Une requête portant un autre en-tête `Host` est alors rejetée (réponse 400) avant toute génération d'URL.

Trois précautions, toutes nécessaires :

- **Ancrer et échapper le motif.** Symfony traite chaque hôte de confiance comme une expression régulière non ancrée : `at: ['oeil360finance.bytechnum.com']` accepterait aussi `oeil360finance.bytechnum.com.attaquant.example` et `oeil360financeXbytechnum.com` (le point remplace n'importe quel caractère). C'était l'erreur de la première version de cette recommandation.
- **Refuser les sous-domaines** avec `subdomains: false`, sinon Laravel ajoute tous les sous-domaines d'`APP_URL`.
- **Vérifier `APP_URL` en production** avant de déployer : c'est lui qui définit l'hôte accepté. Une valeur erronée ferait rejeter toutes les requêtes. Si `APP_URL` n'a pas d'hôte, aucune restriction n'est appliquée plutôt que de bloquer le site.

Le middleware ne s'applique ni dans l'environnement `local` ni pendant les tests (`TrustHosts::shouldSpecifyTrustedHosts()`) : le test Feature qui le couvre force l'environnement à `production`, vérifie que l'hôte officiel est accepté et que les hôtes inconnus, les sous-domaines et les hôtes contenant l'hôte officiel sont refusés, puis remet à zéro la liste statique de Symfony.

### R3. Garder le contrôle d'accès dans Laravel

L'avis GHSA-3h27-hjv7-fqjx rappelle qu'une règle d'accès écrite dans la configuration du serveur web (`.htaccess`) peut être contournée. Le projet n'en contient aucune et doit continuer ainsi : toute route protégée passe par les middlewares `auth`, `consent` ou `admin`.

### R4. Ne pas contourner l'étape `composer audit` de la CI

Depuis la PR #37, la CI échoue dès qu'une dépendance verrouillée fait l'objet d'un avis. C'est le comportement voulu : la corriger par R1, et non par `--ignore-severity` ou une liste d'avis ignorés.

### R5. Suivi

- Relancer `composer audit --locked` avant chaque mise en production : c'est fait par la CI sur la Pull Request de release vers main.
- Le relancer aussi à intervalle régulier, pour les avis publiés entre deux livraisons : workflow planifié chaque semaine sur main (`.github/workflows/security-audit.yml`). Une semaine plutôt qu'un mois, pour le même coût.
- Réévaluer CVE-2026-45070 (`symfony/mime`) si l'application se met à envoyer des e-mails.
- Réévaluer les quatre avis PhpSpreadsheet si une fonction d'import de fichier tableur est ajoutée : la lecture de fichiers fournis par les utilisateurs est précisément ce qu'ils visent.

## Remarque sur le poste de développement

`composer audit` échoue en local avec l'erreur `curl error 60` (certificat non vérifiable) : l'analyse HTTPS de l'antivirus Avast intercepte les connexions vers Packagist. Ajouter une exception pour `php.exe` dans l'analyse HTTPS d'Avast, ou installer son certificat racine dans le magasin utilisé par PHP. Ne pas désactiver la vérification SSL de Composer (`secure-http`, `disable-tls`).

Solution ponctuelle utilisée pour cet audit : un bundle temporaire réunissant `C:\php\cacert.pem` et le certificat racine public « Avast Web/Mail Shield Root » (déjà approuvé par Windows sur ce poste), passé à Composer pour la seule commande avec la variable `COMPOSER_CAFILE`. La vérification TLS reste active et la configuration du poste n'est pas modifiée.

## 4. État des corrections

| Recommandation | État au 7 novembre 2026 | Référence |
|---|---|---|
| R1. Mise à jour des cinq paquets | Faite : `composer audit` ne signale plus aucun avis, 154 tests passent. | Commit `fix(securite): corriger les 11 avis…` |
| R2. Hôtes de confiance | Faite : motif ancré dérivé d'`APP_URL`, sous-domaines refusés, 4 tests Feature. | Commit `feat(securite): n'accepter que l'hôte d'APP_URL…` |
| R3. Contrôle d'accès dans Laravel | Déjà respectée : aucune règle d'accès dans `public/.htaccess`. | |
| R4. Pas de contournement de la CI | Respectée : aucun avis ignoré. | |
| R5. Suivi | Fait : audit en CI sur chaque PR, audit planifié chaque semaine sur main. | Commit `ci(securite): audit des dépendances planifié…` |

Ces corrections ne protègent la production qu'une fois livrées : Pull Request vers v2, release vers main, vérification d'`APP_URL` sur le serveur, puis `deploy.sh`.
