---
format: 1920x1080
message: "Oeil360 Finance te donne une vue 360° sur tes finances - tout centralisé, tout sécurisé, tout conforme APDP."
arc: Hook → Problem → Product Intro → Feature Loop → Trust → CTA
audience: Partenaires et clients - particuliers et décideurs en Afrique de l'Ouest
mode: autonomous
music: confident, modern, minimal - low-tempo electronic with subtle percussion
language: fr
---

## Video direction

**Palette (frame.md roles)** - fond sombre : `text` (#1a2e4a) navy · fond clair : `bg` (#ffffff) blanc · accent : `primary` (#28c98a) teal · texte secondaire : `text-muted` (#7b8fa6) · positif : `positive` (#059669) · négatif : `negative` (#dc2626). Jamais d'inventé.

**Motion grammar** - long-tail settle (`power3`) systématique, jamais de bounce. Chaque élément révélé quand la voix le nomme, répartis sur le back ~50% de chaque plan. Zero breathing circulaire - préférer `subtle jitter` (`sine-wave-loop`, faible amplitude) sur les holds. Pas de pan/push dans le back half sauf si c'est un seam cut calibré.

**Reveal model** - à t=0 uniquement ce que la VO dit à ce moment. Chaque pièce suivante révélée sur son cue spoken : une ligne, une carte, même un h1.

**Frames de respiration** - Frame 7 (Récurrences) et Frame 8 (APDP) sont des breathers intentionnels : peu de motion, contenu déjà résolu tôt, hold long. Donnent du rythme après 3 features denses.

**Négatif** - pas de nav bars, scrollbars, chrome de navigateur, shapes décoratifs flottants, dégradés violet/bleu "IA". Pas de slideshow (tout déversé en 25% puis figé). Pas de screensaver (éléments flottants indépendants).

---

## Frame 1 - Hook

- scene: Mots qui s'enchaînent en hard-cut - tableur ? notes ? app ? - puis silence
- voiceover: "Tes finances dans un tableur ? Des notes éparpillées ? Une appli par compte ?"
- duration: 3.904s
- transition_in: cut
- status: outline
- src: compositions/frames/01-hook.html
- type: hook
- persuasion: Pain validation par escalade rhétorique
- beat: tension + frustration reconnue
- blueprint: kinetic-type-beats (Reproduce)
- focal: (typographie pure - pas d'asset)
- sfx: impact-soft

Scene 1 (0.0–1.5s): Fond plein navy (#1a2e4a) ; "Tes finances" s'abat via **kinetic beat-slam** (`kinetic-beat-slam`) en h1 display (Instrument Sans 700, ~4.2cqw), blanc pur, centré, ~60% largeur frame. Long-tail settle (`power3`). Seul élément visible.

Scene 2 (1.5–2.6s): Hard-cut - "dans un tableur ?" remplace en **in-place token cycle** (`discrete-text-sequence`) sur la même ligne en teal (`primary`), même position. La couleur change EST le beat. Hold instantané.

Scene 3 (2.6–3.7s): Hard-cut - "Des notes éparpillées ?" token cycle encore, blanc, légèrement plus petit (h2 ~2.6cqw), toujours centré. Le shrink léger signale l'escalade.

Scene 4 (3.7–5.0s): Hard-cut - "Une appli par compte ?" token cycle, teal, taille h3 (~1.25cqw). Hold - **subtle jitter** (`sine-wave-loop`, amplitude 1.5px) sur le point d'interrogation. Silence. Tension maximale.

narrativeRole: Créer une tension immédiate en miroir du vécu du spectateur - le spectateur se reconnaît avant même de connaître le produit.
keyMessage: Ta gestion financière actuelle est fragmentée - et tu le sais.

## Frame 2 - Le problème

- scene: Logos d'apps s'accumulent autour d'une silhouette centrale jusqu'à l'étouffement
- voiceover: "Wave, Ecobank, espèces, abonnements, impôts - tout en silo. Jamais une vue d'ensemble."
- duration: 5.803s
- transition_in: zoom-through
- status: outline
- src: compositions/frames/02-problem.html
- type: pain_point
- persuasion: Pain agitation - accumulation visuelle jusqu'au point de rupture
- beat: overwhelm + anxiété
- blueprint: overwhelm-surround (Adapt)
- focal: (silhouette + labels texte - pas d'asset capturé)
- sfx: riser

Adapt : garder la signature move (elements close in from all sides) ; adapter avec des labels texte pill au lieu de vrais logos app (no-capture mode).

Scene 1 (0.0–1.5s): Fond blanc `bg`. Avatar silhouette simple (cercle + corps, navy, ~15% frame height) centré. "WAVE" pill teal entre en haut-gauche via **spring-pop-entrance** (`spring-pop-entrance`, long-tail settle) quand la VO dit "Wave". Layered-depth : 3 couches (bg blanc, avatar mid, label fg).

Scene 2 (1.5–3.5s): "ECOBANK" pill (navy border) entre haut-droit sur son cue spoken via spring-pop-entrance · "ESPÈCES" bas-gauche · "ABONNEMENTS" bas-droit - chacun révélé au mot VO correspondant, stagger 0.3s. Density croissante autour de l'avatar.

Scene 3 (3.5–5.5s): "IMPÔTS" et "NOTES" pills entrent des côtés gauche et droit, plus proches du centre - **depth-of-field-blur** (`depth-of-field-blur`) progressif sur l'avatar (blur monte de 0 à 4px), signalant l'étouffement. La silhouette est encerclée.

Scene 4 (5.5–7.0s): Tous les labels en position ; léger **ambient-glow-bloom** (`ambient-glow-bloom`) rouge-orangé derrière l'avatar (#dc2626, opacity 0.15) - l'oppression est visible. Hold avec subtle jitter sur les pills.

narrativeRole: Aggraver le problème - rendre la fragmentation visible et physique.
keyMessage: La fragmentation n'est pas une petite gêne - c'est un problème qui grandit.

## Frame 3 - Introducing Oeil360 Finance

- scene: Trois verbes s'abattent en hard-cut, puis le nom du produit en teal
- voiceover: "Centralise. Visualise. Maîtrise. - Oeil360 Finance."
- duration: 3.989s
- transition_in: zoom-through
- status: outline
- src: compositions/frames/03-product-intro.html
- type: product_intro
- persuasion: Negative contrast - la douleur établie, le produit arrive comme la réponse évidente
- beat: relief + curiosité
- blueprint: kinetic-type-beats (Reproduce)
- focal: (typographie pure)
- sfx: impact-soft

Scene 1 (0.0–1.0s): Fond navy profond. "Centralise." s'abat via **kinetic beat-slam** (`kinetic-beat-slam`), h1 blanc centré. Long-tail settle. Seul élément.

Scene 2 (1.0–2.0s): Hard-cut - "Visualise." remplace via **in-place token cycle** (`discrete-text-sequence`), même position, même taille. White.

Scene 3 (2.0–3.0s): Hard-cut - "Maîtrise." token cycle, teal (`primary`). La couleur change marque l'apex.

Scene 4 (3.0–5.0s): "Oeil360 Finance." slide-up de 30px via **per-word staggered reveal** (`dynamic-content-sequencing`), h2 (~2.6cqw), teal, centré sous un séparateur hairline teal. Long-tail settle. Hold - **subtle jitter** (`sine-wave-loop`, 1px) sur le point. C'est la première fois que le produit est nommé.

narrativeRole: Résoudre la tension narrative - après le chaos, la solution arrive avec autorité.
keyMessage: Il existe une alternative au chaos - Oeil360 Finance.

## Frame 4 - Le tableau de bord 360°

- scene: Curseur explore un dashboard navy - solde global, graphique, transactions récentes
- voiceover: "Un seul écran. Tous tes comptes, toutes tes catégories, ton solde global - d'un coup d'œil."
- duration: 4.651s
- transition_in: blur-crossfade
- status: outline
- src: compositions/frames/04-dashboard.html
- type: feature_showcase
- persuasion: Show-don't-tell proof - le curseur guide le regard à travers le produit réel
- beat: clarté + contrôle
- blueprint: cursor-ui-demo (Adapt)
- focal: (UI construite en code - dashboard card)
- sfx: click-soft

Adapt : curseur-ui-demo sur surface reconstruite en code (pas screenshot capturé) ; fond navy de l'app, pas un fond blanc.

Scene 1 (0.0–2.0s): Fond navy (#1a2e4a). Grande card UI (14px radius, tinted `card-bg`) s'élève depuis le bas via **spring-pop-entrance** (`spring-pop-entrance`), centrée, ~70% largeur frame. Card montre : "Solde global" label (h4-eyebrow teal), "1 250 000 F CFA" stat-num en teal. Donut chart à droite, 3 petites transaction rows en bas. Asymétrique 65/35.

Scene 2 (2.0–4.5s): Custom cursor (cercle 12px teal) entre bas-droit via **cursor-click-ripple** (`cursor-click-ripple`), glisse jusqu'au solde - ripple teal léger sur le click. "1 250 000 F CFA" **keyword-glow** (`asr-keyword-glow`) : léger glow teal, scale +5%. Cue spoken "solde global".

Scene 3 (4.5–6.5s): Curseur pivote vers le donut chart - **camera-cursor-tracking** (`camera-cursor-tracking`) micro-zoom sur le chart ; 3 segments teal/navy se highlight séquentiellement (Alimentation, Transport, Épargne) à mesure que la VO liste les catégories.

Scene 4 (6.5–8.0s): Hold - curseur blink statique (caret blink, `context-sensitive-cursor`). Dashboard complètement visible. Subtle jitter sur la card.

narrativeRole: Montrer le produit en action - preuve que l'interface est réelle, claire et lisible d'un coup d'œil.
keyMessage: Un seul dashboard remplace tous les outils épars.

## Frame 5 - Revenus & Dépenses

- scene: Fenêtre flottante - saisie d'un revenu puis d'une dépense, classés automatiquement
- voiceover: "Revenu ou dépense - en quelques secondes, classé, tracé, visualisé."
- duration: 3.776s
- transition_in: push-slide LEFT
- status: outline
- src: compositions/frames/05-transactions.html
- type: feature_showcase
- persuasion: Feature-to-benefit translation - la saisie rapide traduite en gain de temps
- beat: ease + satisfaction
- blueprint: device-surface-showcase (Adapt)
- focal: (fenêtre flottante construite en code)
- sfx: click-soft

Adapt : floating-window push-scroll variant ; fond blanc (`bg`) pour contraster avec le frame navy précédent.

Scene 1 (0.0–2.0s): Fond blanc. Fenêtre app flottante (card-lg radius, navy header "Transactions") entre depuis la droite via **card-morph-anchor** (`card-morph-anchor`), se stabilise en légère position asymétrique 60/40. Montre une liste vide "Aucune transaction".

Scene 2 (2.0–4.5s): Screen avance (internal cut) - formulaire "Ajouter une transaction". "Revenu" pill teal se sélectionne via **button-press** (`press-release-spring`) ; montant "350 000" se type via **per-word staggered reveal** sur le champ ; catégorie "Salaire" pop via spring-pop-entrance. Tout révélé sur les cues VO.

Scene 3 (4.5–6.5s): Screen avance - la transaction ajoutée apparaît dans la liste : icône salaire teal + "+350 000 F CFA" en `positive` vert (#059669) via spring-pop-entrance. Une seconde transaction "Transport -15 000 F" en `negative` rouge pop dessous.

Scene 4 (6.5–8.0s): Hold - deux lignes visibles, couleurs `positive`/`negative` bien lisibles. Subtle jitter sur la card flottante.

narrativeRole: Démontrer le workflow principal - saisie → classement → visualisation - pour que le spectateur se projette dans l'usage.
keyMessage: Chaque transaction prend quelques secondes et enrichit la vue 360°.

## Frame 6 - Comptes & Virements

- scene: Trois cartes compte s'assemblent en cascade, puis une flèche de virement se trace
- voiceover: "Banque, mobile money, espèces - tous tes comptes réunis. Virer de l'un à l'autre, c'est deux clics."
- duration: 5.099s
- transition_in: push-slide LEFT
- status: outline
- src: compositions/frames/06-accounts.html
- type: feature_showcase
- persuasion: Value stacking - breadth locale (Banque + Mobile Money + Espèces)
- beat: contrôle + puissance
- blueprint: grid-card-assemble (Adapt)
- focal: (cartes construites en code)
- sfx: whoosh

Adapt : grid-card-assemble en 3 colonnes horizontales plutôt que grille ; ajouter SVG arc de virement après l'assemblage.

Scene 1 (0.0–2.0s): Fond blanc. "Banque Atlantique" card (card-lg radius, border teal, solde "450 000 F CFA" en stat-num teal) entre centre-gauche via **spring-pop-entrance** (`spring-pop-entrance`). Centred template évolue vers triptych.

Scene 2 (2.0–4.0s): "Wave Mobile Money" card (icône orange stylisée, solde "180 000 F") pop au centre, stagger 0.4s. "Espèces" card (icône billet, solde "75 000 F") pop droite, stagger 0.4s. Les 3 forment un triptych complet.

Scene 3 (4.0–6.0s): Arc SVG courbe teal se dessine via **svg-path-draw** (`svg-path-draw`) de la card Banque vers Wave, avec une flèche directionnelle. Cue spoken "Virer de l'un à l'autre".

Scene 4 (6.0–7.0s): Hold - 3 cards + arc visible. **Ambient-glow-bloom** (`ambient-glow-bloom`) teal léger sous l'arc. Subtle jitter sur les cards.

narrativeRole: Montrer la breadth - Oeil360 couvre tous les comptes courants en Afrique de l'Ouest, mobile money inclus.
keyMessage: Tous les comptes au même endroit, les virements sans friction.

## Frame 7 - Transactions récurrentes

- scene: Icône calendrier puis trois lignes de charges fixes - "automatisés" en teal
- voiceover: "Loyer, abonnements, charges fixes - programmés une fois, jamais oubliés."
- duration: 3.733s
- transition_in: crossfade
- status: outline
- src: compositions/frames/07-recurring.html
- type: benefit_highlight
- persuasion: Friction reduction - supprimer une charge mentale récurrente
- beat: peace of mind + ease
- blueprint: titlecard-reveal (Reproduce)
- focal: (typographie + icône SVG)
- sfx: (silencieux - frame de respiration)

Scene 1 (0.0–1.5s): Fond blanc très propre. Icône SVG calendrier + flèche circulaire (navy, ~80px) slides-up depuis +40px via spring-pop-entrance (`spring-pop-entrance`), centré en haut-tiers. Long-tail settle. Seul élément - breather volontaire.

Scene 2 (1.5–3.5s): Trois lignes texte apparaissent séquentiellement via **per-word staggered reveal** (`dynamic-content-sequencing`) sous l'icône, centré - "Loyer" · "Abonnements" · "Charges fixes" - chacune h3 navy, stagger 0.35s par ligne.

Scene 3 (3.5–5.0s): "- automatisés" slide-in depuis la droite en teal h3 (`primary`), settle. Hold complet, très peu de motion - stillness est le point ici.

narrativeRole: Beat de respiration après deux features denses - une seule idée, calme et claire : les récurrences sont automatisées.
keyMessage: Oublie les oublis - les charges fixes tournent seules.

## Frame 8 - Sécurité & Conformité APDP

- scene: Deux badges côte à côte - bouclier APDP + cadenas Auth0 - sur fond blanc
- voiceover: "Tes données t'appartiennent. Conforme Loi APDP Bénin. Authentification sécurisée par Auth0 - zéro mot de passe stocké."
- duration: 8.363s
- transition_in: crossfade
- status: outline
- src: compositions/frames/08-trust.html
- type: social_proof
- persuasion: Authority by association + risk reversal
- beat: trust + confidence
- blueprint: titlecard-reveal (Adapt)
- focal: (badges construits en code - icônes SVG)
- sfx: (silencieux - frame de confiance)

Adapt : deux cards côte à côte plutôt qu'une seule card ; garder la signature move (slide-up + hold).

Scene 1 (0.0–2.0s): Fond blanc. Card gauche (card-lg, border teal) slide-up depuis +40px via **spring-pop-entrance** (`spring-pop-entrance`) : icône bouclier SVG teal (~48px) + "Conforme APDP Bénin" h3 navy + "Loi n°2017-20 Bénin" en text-muted body. Split-screen 50/50, card centrée à gauche.

Scene 2 (2.0–4.0s): Card droite (card-lg, border navy tinted) slide-up, stagger 0.4s : icône cadenas SVG navy + "Auth0" h3 + "Zéro mot de passe stocké" body text-muted. Les deux cards forment un comparison-split visual.

Scene 3 (4.0–6.0s): "Tes données t'appartiennent." - h4-eyebrow teal révélée via per-word staggered reveal au-dessus des cards, centré. Hold. Pas de motion dans le back-half - stillness = confiance.

narrativeRole: Lever l'objection de confiance avant le CTA - conformité légale locale + sécurité technique reconnue.
keyMessage: APDP + Auth0 = tes données sont protégées par la loi et la technologie.

## Frame 9 - CTA

- scene: Logo Oeil360 s'assemble sur fond navy, tagline, URL teal pulsant
- voiceover: "Reprends le contrôle de tes finances. - Oeil360 Finance."
- duration: 3.968s
- transition_in: zoom-through
- status: outline
- src: compositions/frames/09-cta.html
- type: cta
- persuasion: Future pacing - projeter dans l'état "après"
- beat: motivation + urgency-to-act
- blueprint: logo-assemble-lockup (Adapt)
- focal: (logo SVG construit en code)
- sfx: riser

Adapt : logo-assemble-lockup avec SVG self-draw de l'arc 360°, puis tagline et URL ; pas de camera push-through (impossible sans asset vidéo).

Scene 1 (0.0–2.5s): Fond navy profond (#1a2e4a). Cercle SVG outline (~200px) se dessine via **svg-path-draw** (`svg-path-draw`), stroke teal, centré. Long-tail draw (easeInOut). Seul élément.

Scene 2 (2.5–4.5s): Arc "360°" se dessine à l'intérieur du cercle via svg-path-draw, puis "360" texte en teal stat-num pop au centre via spring-pop-entrance.

Scene 3 (4.5–6.5s): "Oeil360 Finance" wordmark révélée lettre par lettre via **per-word staggered reveal** (`dynamic-content-sequencing`) sous le mark, h2 blanc. Long-tail settle.

Scene 4 (6.5–8.0s): "Reprends le contrôle de tes finances." slide-up h3 text-muted via spring-pop-entrance. Cue VO final.

Scene 5 (8.0–9.0s): Hold complet - **ambient-glow-bloom** (`ambient-glow-bloom`) teal derrière le cercle (bloom radius 120px, opacity 0.25). Subtle jitter sur le wordmark. Le glow est fini (durée bornée, pas de repeat).

narrativeRole: Convertir l'émotion construite tout au long de la vidéo en mémorisation - logo + tagline comme ancre.
keyMessage: Oeil360 Finance - disponible maintenant.
