# Reservation Salles

Projet PHP de gestion des salles et des réservations.

## Présentation du projet

L'objectif de ce projet est de développer une application permettant de gérer des salles et leurs réservations.

L'application devra notamment permettre de :

- consulter les salles ;
- afficher le détail d'une salle ;
- ajouter une salle ;
- modifier une salle ;
- activer ou désactiver une salle ;
- consulter les réservations ;
- filtrer les réservations par salle ;
- afficher une réservation ;
- créer une réservation ;
- annuler une réservation.

---

# Étape 0 — Initialiser le dépôt

## Travail réalisé

Pour commencer le projet, j'ai initialisé un dépôt Git et créé la branche principale `main`.

J'ai également créé les fichiers :

- `.gitignore`
- `README.md`
- `CHANGELOG.md`

Le fichier `.gitignore` permet notamment d'empêcher le versionnement de fichiers sensibles ou générés automatiquement, comme `.env` et `vendor/`.

Le premier commit a été créé avec le message :

```text
init: initialiser le dépôt

ChatGPT a dit :
Questions — Étape 1
1. Quel est le rôle de Composer ?

Composer est le gestionnaire de dépendances de PHP. Il permet de déclarer, installer et gérer les bibliothèques utilisées dans un projet, ainsi que leurs versions et leurs dépendances.

Il permet également de générer automatiquement le chargement des classes grâce à l’autoloading, notamment avec la norme PSR-4.
2. Quelle différence existe entre require et require-dev ?

require contient les dépendances nécessaires au fonctionnement de l'application.

require-dev contient les dépendances utilisées uniquement pendant le développement, par exemple les outils de test.

Les dépendances de développement peuvent être ignorées en production avec :

composer install --no-dev

3. Pourquoi faut-il versionner composer.lock ?

composer.lock enregistre les versions exactes des dépendances installées.

Il faut le versionner afin que tous les développeurs utilisent les mêmes versions des bibliothèques et que l'environnement du projet soit reproductible.

Avec :

composer install

Composer utilise composer.lock pour installer les versions enregistrées.
4. Pourquoi ne versionne-t-on pas vendor/ ?

Le dossier vendor/ contient les bibliothèques installées automatiquement par Composer.

Il n'est pas nécessaire de le versionner car il peut être recréé à partir de composer.json et composer.lock avec :

composer install

De plus, vendor/ contient beaucoup de fichiers générés automatiquement et alourdirait inutilement le dépôt Git. Il est donc ajouté au .gitignore.
  Etape 3


1. Quel rôle joue Capsule\Manager ?

Capsule\Manager permet d'utiliser Eloquent en dehors de Laravel.

Il sert notamment à :

    configurer la connexion à MySQL ;
    définir les paramètres de connexion ;
    démarrer Eloquent ;
    rendre la connexion disponible aux modèles Eloquent.

Dans notre projet, c'est lui qui fait le lien entre PHP et MySQL.
2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Parce qu'Eloquent est disponible sous forme de composant indépendant via :

illuminate/database

Laravel utilise Eloquent, mais Eloquent n'a pas besoin de tout Laravel pour fonctionner.

Dans notre projet, nous avons donc installé :

composer require illuminate/database:^12.0

et nous utilisons directement :

use Illuminate\Database\Capsule\Manager as Capsule;

Cela permet d'avoir l'ORM Eloquent sans installer le framework Laravel complet.

Avantage : notre projet reste léger et nous apprenons à assembler nous-mêmes les composants.

3. Où doit se trouver le démarrage de l'ORM ?

Le démarrage de l'ORM doit se trouver dans la configuration de l'application, et non dans les modèles ou les contrôleurs.

Dans notre projet, nous avons :

config/
└── database.php

C'est ce fichier qui :

    charge les variables d'environnement ;
    configure Capsule\Manager ;
    configure la connexion MySQL ;
    démarre Eloquent.

Les classes métier ne doivent donc pas faire elles-mêmes :

new Capsule();

ou :

Dotenv::createImmutable(...);

Elles doivent simplement recevoir leurs dépendances.

C'est important pour respecter la séparation des responsabilités.
4. Quelle différence existe entre ORM et SQL écrit à la main ?

Avec du SQL classique, on écrit directement les requêtes :

SELECT * FROM salles WHERE active = 1;

Avec Eloquent, on manipule des objets et des modèles :

$salles = Salle::where('active', true)->get();

L'ORM (Object-Relational Mapping) fait le lien entre les objets PHP et les tables SQL.

Par exemple :

Objet PHP                  Base MySQL
──────────                 ──────────
Salle                      salles
$salle->nom                salles.nom
$salle->capacite           salles.capacite

Comparaison
SQL écrit à la main	Eloquent
SELECT * FROM salles	Salle::all()
SQL directement	Objets PHP
Gestion manuelle des résultats	Modèles Eloquent
Plus proche de la base	Plus proche du code objet
Beaucoup de SQL	Moins de SQL explicite

Eloquent simplifie donc la manipulation de la base de données, tout en permettant également d'utiliser du Query Builder lorsque nécessaire.

## Étape 3 — Modèles Eloquent

### Questions

#### 1. Quel type de relation Eloquent avons-nous utilisé ?

Nous avons utilisé une relation **One-to-Many (un-à-plusieurs)**.

Une salle peut avoir plusieurs réservations. Dans le modèle `Salle`, nous utilisons `hasMany()` :

```php
public function reservations(): HasMany
{
    return $this->hasMany(Reservation::class, 'salle_id');
}

Nous pouvons donc utiliser :

$salle->reservations;

pour récupérer les réservations d'une salle, et :

$reservation->salle;

pour récupérer la salle d'une réservation.



2. Pourquoi déclarer $fillable ou $guarded ?

$fillable permet de définir les attributs qui peuvent être remplis lors d'une assignation de masse.

Par exemple, dans Salle :

protected $fillable = [
    'nom',
    'batiment',
    'capacite',
    'type',
    'active',
];

Cela permet d'utiliser :

Salle::create($data);

tout en contrôlant les champs qui peuvent être affectés.

Cela protège l'application contre l'assignation accidentelle de champs qui ne devraient pas être modifiés.
3. Pourquoi convertir active en booléen ?

La colonne active est définie comme un booléen dans la base de données :

$table->boolean('active')->default(true);

Nous avons donc ajouté dans le modèle Salle :

protected $casts = [
    'active' => 'boolean',
];

Eloquent convertit automatiquement la valeur en true ou false.

Cela permet d'écrire simplement :

if ($salle->active) {
    // La salle est active
}

au lieu de manipuler directement 0 et 1.
4. Pourquoi convertir les dates en objets ?

Dans le modèle Reservation, nous avons :

protected $casts = [
    'salle_id' => 'integer',
    'date_debut' => 'datetime',
    'date_fin' => 'datetime',
];

Les dates sont ainsi manipulées comme des objets plutôt que comme de simples chaînes de caractères.

Cela facilite les comparaisons et les calculs de durée.

Cette conversion sera notamment utile pour appliquer les règles métier suivantes :

    la date de début doit précéder la date de fin ;
    la réservation ne doit pas dépasser quatre heures ;
    la réservation doit commencer dans le futur ;
    deux réservations ne doivent pas se chevaucher.
## Étape 4 — Données initiales

### Seeder

Le fichier `database/seed.php` permet d'insérer les données initiales de l'application.

Il ajoute cinq salles :

- Amphithéâtre A — 250 places
- Salle B12 — 40 places
- Laboratoire Chimie — 24 places
- Salle Informatique 1 — 30 places
- Salle de réunion — 12 places

Le script utilise `firstOrCreate()` afin d'éviter la création de doublons lorsqu'il est exécuté plusieurs fois.

### Questions

#### 1. Quelle différence existe entre une migration et un seeder ?

Une migration sert à créer ou modifier la structure de la base de données.

Par exemple, nos migrations permettent de créer les tables `salles` et `reservations`, ainsi que leurs colonnes et leurs relations.

Un seeder sert à insérer des données initiales ou de démonstration dans les tables.

Dans notre projet :

- `database/migrations/` contient les migrations ;
- `database/seed.php` contient les données initiales.

La migration définit donc la structure de la base, tandis que le seeder fournit les données.

#### 2. Pourquoi les données initiales doivent-elles être reproductibles ?

Les données initiales doivent être reproductibles afin de pouvoir installer ou réinitialiser l'application facilement dans différents environnements.

Un autre développeur doit pouvoir cloner le projet, créer la base de données, exécuter les migrations puis le seeder et obtenir les mêmes données initiales.

Le script peut également être exécuté plusieurs fois sans provoquer de doublons.

Notre seeder est donc conçu pour être idempotent.

#### 3. Comment empêcher les doublons ?

Nous utilisons `firstOrCreate()` d'Eloquent.

Le script recherche une salle en utilisant son nom et son bâtiment :

```php
Salle::firstOrCreate(
    [
        'nom' => $data['nom'],
        'batiment' => $data['batiment'],
    ],
    $data
);

Si la salle existe déjà, Eloquent la récupère sans créer une nouvelle ligne.

Si elle n'existe pas, Eloquent l'insère dans la base de données.

Cette approche permet d'exécuter le seeder plusieurs fois sans créer inutilement de doublons.


---

# 5. Tester les données dans MySQL

Tu peux également vérifier directement dans MySQL :

```bash
sudo mysql

USE reservation_salles;

SELECT id, nom, batiment, capacite, type, active
FROM salles;
SELECT COUNT(*) FROM salles;
Resultat: 5

## Étape 5 — Validation

### Questions

#### 1. Pourquoi séparer la validation syntaxique des règles métier ?

La validation syntaxique vérifie la forme des données (une chaîne a la bonne
longueur, un email a le bon format). Les règles métier vérifient la cohérence
fonctionnelle (la salle existe, elle est active, pas de chevauchement).
Séparer les deux permet de réutiliser le validateur indépendamment de la
logique métier, et de tester chaque couche isolément — un validateur n'a pas
besoin de MySQL pour fonctionner.

#### 2. Pourquoi créer une interface de validation ?

Cela impose un contrat commun (`validate(array): ValidationResult`) à tous
les validateurs. N'importe quelle classe qui dépend d'un validateur peut
alors dépendre de `ValidatorInterface` plutôt que d'une classe concrète
précise — ce qui respecte le principe d'inversion des dépendances (le « D »
de SOLID) et facilite les tests (on peut injecter un faux validateur).

#### 3. Pourquoi le validateur ne doit-il pas enregistrer les données ?

Parce que sa seule responsabilité est de vérifier des données (principe de
responsabilité unique). S'il sauvegardait aussi en base, on ne pourrait plus
le tester sans base de données, et on mélangerait deux préoccupations
différentes (validation et persistance).

#### 4. Comment retourner plusieurs erreurs en une seule fois ?

En accumulant les erreurs dans un tableau associatif
(`$errors['champ'][] = 'message'`) au lieu de s'arrêter au premier échec.
`ValidationResult` transporte ensuite tout le tableau, ce qui permet
d'afficher toutes les erreurs d'un formulaire en une seule soumission.

## Étape 6 — Objets de transport (DTO)

### Questions

#### 1. Quelle différence existe entre DTO et modèle Eloquent ?

Le modèle Eloquent (`Salle`, `Reservation`) est lié à la base de données : il
sait se sauvegarder, se retrouver, se supprimer, et porte des relations
(`hasMany`, `belongsTo`). Le DTO, lui, ne connaît rien à la base de données —
c'est un simple conteneur de données typées, dont le seul rôle est de
transporter de l'information entre deux couches (contrôleur → service) de
façon sûre et prévisible.

#### 2. Pourquoi le DTO ne doit-il pas appeler `save()` ?

Parce qu'un DTO n'a pas de comportement lié à la persistance — ce n'est pas
son rôle (responsabilité unique). S'il savait se sauvegarder, il faudrait
qu'il connaisse Eloquent ou la base de données, ce qui casserait la
séparation entre « transporter des données » et « les persister ».

#### 3. À quel moment transforme-t-on les chaînes en dates ?

Au moment de la construction du DTO (`fromArray()`), une fois que le
validateur a confirmé que la chaîne est bien une date valide. Le service
métier reçoit donc directement des objets `DateTimeImmutable` prêts à
l'emploi.

#### 4. Le DTO doit-il contenir la règle de chevauchement ?

Non. Le DTO ne contient aucune règle métier — ni le chevauchement, ni la
comparaison des dates, ni la durée maximale. Toutes ces règles vivent
exclusivement dans le service métier (étape 8).

## Étape 7 — Accès aux données (Repositories)

### Questions

#### 1. Eloquent constitue-t-il déjà un accès aux données ?

Oui, Eloquent est déjà en soi une couche d'accès aux données : il fournit un
ORM (Active Record) qui sait interroger et manipuler les tables `salles` et
`reservations`.

#### 2. Pourquoi ajouter un Repository au-dessus d'Eloquent ?

Pour isoler le reste de l'application (contrôleurs, services) de la manière
concrète dont les données sont récupérées. Les contrôleurs et services ne
dépendent que d'une interface (`SalleRepositoryInterface`,
`ReservationRepositoryInterface`), jamais d'Eloquent directement. Cela
respecte la contrainte du sujet : aucun `Salle::query()` ni `->save()` dans
les contrôleurs.

#### 3. Cette abstraction est-elle toujours nécessaire ?

Pas toujours : sur un petit projet ou un prototype, elle peut être considérée
comme un sur-découpage inutile. Elle prend tout son sens quand on veut
pouvoir tester sans base de données, ou changer d'ORM/de source de données
sans toucher au reste de l'application.

#### 4. Quel avantage apporte-t-elle ?

Elle permet de tester les services métier avec une implémentation en
mémoire du repository (sans MySQL), et de centraliser toutes les requêtes
liées à une entité en un seul endroit — notamment la logique de recherche de
chevauchement (`findConflict`), qui ne doit être écrite qu'une fois.

## Étape 9 — Contrôleurs et vues

Les contrôleurs (`SalleController`, `ReservationController`) ne font aucune
requête ORM directe : ils dépendent uniquement des interfaces de repository
et des validateurs. Toutes les sorties dynamiques dans les vues sont
échappées via `View::e()`. Les erreurs de validation (format des champs)
s'affichent près du champ concerné ; les erreurs métier (salle inactive,
conflit de créneau...) s'affichent dans une zone générale du formulaire de
réservation, car elles ne concernent pas un champ précis.

## Étape 10 — Routeur (FastRoute)

### Questions

#### 1. Pourquoi FastRoute ne construit-il pas lui-même le contrôleur ?

FastRoute a une seule responsabilité : faire correspondre une méthode HTTP et
une URL à un « handler » (ici, un tableau `[Classe::class, 'methode']`). Il
ne sait rien des dépendances dont cette classe a besoin pour être construite
(repositories, validateurs, services...). Lui déléguer aussi la construction
mélangerait deux responsabilités différentes — le routage, et l'injection de
dépendances — qui appartiennent à des composants distincts (FastRoute et
PHP-DI).

#### 2. Quelle différence existe entre 404 et 405 ?

Un **404** signifie que l'URL demandée ne correspond à aucune route connue
(la ressource n'existe pas). Un **405** signifie que l'URL existe bien en
tant que route, mais que la méthode HTTP utilisée n'est pas autorisée pour
cette URL précise (par exemple, envoyer un `DELETE` sur `/salles`, alors que
seules les méthodes `GET` et `POST` y sont déclarées).

#### 3. Pourquoi contraindre {id} avec \d+ ?

Pour que FastRoute ne fasse correspondre cette portion de l'URL qu'à des
chiffres. Sans cette contrainte, une URL comme `/salles/abc` ou
`/salles/create` pourrait être confondue avec `/salles/{id}` selon l'ordre
de déclaration des routes, ce qui provoquerait des erreurs ou des
comportements ambigus. `\d+` garantit qu'on reçoit toujours un identifiant
numérique valide dans le contrôleur.

#### 4. Quel composant doit interpréter le handler retourné ?

Le point d'entrée de l'application (`public/index.php`), à travers le
conteneur d'injection de dépendances (PHP-DI, étape 11). C'est lui qui lit
le tableau `[Classe::class, 'methode']` retourné par FastRoute, résout les
dépendances de `Classe` via le conteneur, l'instancie, puis appelle
`methode` avec les paramètres dynamiques de la route.

## Étape 10 — Routeur (FastRoute)

### Questions

#### 1. Pourquoi FastRoute ne construit-il pas lui-même le contrôleur ?

FastRoute a une seule responsabilité : faire correspondre une méthode HTTP et
une URL à un « handler » (ici, un tableau `[Classe::class, 'methode']`). Il
ne sait rien des dépendances dont cette classe a besoin pour être construite
(repositories, validateurs, services...). Lui déléguer aussi la construction
mélangerait deux responsabilités différentes — le routage, et l'injection de
dépendances — qui appartiennent à des composants distincts (FastRoute et
PHP-DI).

#### 2. Quelle différence existe entre 404 et 405 ?

Un 404 signifie que l'URL demandée ne correspond à aucune route connue (la
ressource n'existe pas). Un 405 signifie que l'URL existe bien en tant que
route, mais que la méthode HTTP utilisée n'est pas autorisée pour cette URL
précise (par exemple, envoyer un DELETE sur /salles, alors que seules les
méthodes GET et POST y sont déclarées).

#### 3. Pourquoi contraindre {id} avec \d+ ?

Pour que FastRoute ne fasse correspondre cette portion de l'URL qu'à des
chiffres. Sans cette contrainte, une URL comme /salles/abc ou
/salles/create pourrait être confondue avec /salles/{id} selon l'ordre de
déclaration des routes, ce qui provoquerait des erreurs ou des
comportements ambigus. \d+ garantit qu'on reçoit toujours un identifiant
numérique valide dans le contrôleur.

#### 4. Quel composant doit interpréter le handler retourné ?

Le point d'entrée de l'application (public/index.php), à travers le
conteneur d'injection de dépendances (PHP-DI, étape 11). C'est lui qui lit
le tableau [Classe::class, 'methode'] retourné par FastRoute, résout les
dépendances de Classe via le conteneur, l'instancie, puis appelle methode
avec les paramètres dynamiques de la route.

## Étape 11 — Conteneur d'injection de dépendances (PHP-DI)

### Questions

#### 1. Quelle différence existe entre injection et conteneur ?

L'injection de dépendances est un principe : donner à un objet ce dont il a
besoin depuis l'extérieur (via son constructeur), plutôt que de le laisser
le créer lui-même. Le conteneur est un outil qui automatise ce principe : il
sait comment construire chaque classe et lui fournir ses dépendances, sans
que le développeur ait à écrire `new Xxx(new Yyy(), new Zzz())` à la main
partout dans le code.

#### 2. Qu'est-ce que l'autowiring ?

C'est la capacité du conteneur à deviner automatiquement comment construire
une classe, en lisant les types déclarés dans son constructeur, sans qu'on
ait besoin d'écrire de configuration explicite pour elle. Par exemple,
`autowire(CreerReservationService::class)` suffit : PHP-DI lit le
constructeur, voit qu'il attend un `SalleRepositoryInterface` et un
`ReservationRepositoryInterface`, et va chercher comment résoudre chacun
d'eux dans le reste de la configuration.

#### 3. Pourquoi les interfaces nécessitent-elles une définition ?

Parce que l'autowiring ne peut deviner une classe concrète qu'à partir
d'elle-même — face à une interface, PHP-DI ne peut pas savoir laquelle de
ses implémentations utiliser (il pourrait en exister plusieurs). Il faut
donc lui dire explicitement, par exemple
`SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class)`,
quelle implémentation concrète associer à cette interface.

#### 4. Pourquoi limiter `$container->get()` au point d'entrée ?

Pour que la construction des objets reste centralisée et prévisible. Si
n'importe quelle classe pouvait interroger le conteneur à sa guise, on
perdrait la traçabilité des dépendances réelles de chaque classe (elles ne
seraient plus visibles dans son constructeur), et on romprait le principe
d'inversion de contrôle. Seul `Application` (via le point d'entrée) a
besoin d'interroger le conteneur, car c'est elle qui doit résoudre un nom de
classe connu uniquement à l'exécution (le contrôleur trouvé par le
routeur) — un cas différent de « chercher ses propres dépendances ».

#### 5. Quel anti-pattern apparaît si toutes les classes interrogent le conteneur ?

Le « Service Locator » : au lieu de déclarer clairement ses dépendances dans
son constructeur, une classe va les chercher elle-même dans un conteneur
global, à n'importe quel moment. Ça rend le code plus difficile à tester
(on ne peut plus injecter facilement une fausse dépendance) et plus
difficile à comprendre (il faut lire tout le corps de la classe pour savoir
de quoi elle dépend réellement, plutôt que de simplement lire son
constructeur).


## Installation avec Docker

1. Copier `.env.example` vers `.env`, et mettre `DB_HOST=mysql` (au lieu de `127.0.0.1`).
2. Lancer les conteneurs :
   \`\`\`bash
   docker compose up -d --build
   \`\`\`
3. Exécuter les migrations et le seeder :
   \`\`\`bash
   docker compose exec app php database/migrate.php
   docker compose exec app php database/seed.php
   \`\`\`
4. L'application est accessible sur http://localhost:8080


## Étape 12 — Tests

### Exécution

\`\`\`bash
vendor/bin/phpunit --testsuite Unit          # rapides, sans MySQL
vendor/bin/phpunit --testsuite Integration   # nécessite une base MySQL démarrée
vendor/bin/phpunit                           # les deux suites
\`\`\`

Les tests unitaires du service (`CreerReservationServiceTest`) utilisent des
implémentations en mémoire des repositories (`tests/Unit/Double/`), sans
jamais se connecter à MySQL, conformément à la contrainte du sujet.

**Note d'environnement** : `.env` doit avoir `DB_HOST=127.0.0.1` pour les
tests d'intégration lancés en local (hors Docker). Si l'exécution se fait
dans le conteneur `app`, `DB_HOST=mysql` doit être utilisé à la place.