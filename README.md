# Réservation de salles universitaires

Application PHP orientée objet (sans framework complet, avec des composants
Composer) permettant de gérer les salles et leurs réservations.

## Présentation du projet

L'objectif de ce projet est de développer une application permettant de
gérer des salles et leurs réservations.

L'application permet de :

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

## Prérequis

- PHP 8.2 ou 8.3
- Composer
- MySQL 8.0 (ou Docker)

## Installation (sans Docker)

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/aitagueye2626-byte/Reservation_salle.git
   cd Reservation_salle
   ```

2. Installer les dépendances :
   ```bash
   composer install
   ```

3. Copier le fichier d'environnement et le configurer :
   ```bash
   cp .env.example .env
   ```
   Renseigner les identifiants MySQL dans `.env` (`DB_HOST=127.0.0.1` pour
   un usage local).

4. Créer les tables :
   ```bash
   php database/migrate.php
   ```

5. Ajouter les données initiales (5 salles) :
   ```bash
   php database/seed.php
   ```

6. Lancer le serveur de développement :
   ```bash
   php -S localhost:8000 -t public
   ```

7. Ouvrir `http://localhost:8000/salles` dans un navigateur.

## Installation avec Docker

1. Copier `.env.example` vers `.env`, et mettre `DB_HOST=mysql` (au lieu de
   `127.0.0.1`).
2. Lancer les conteneurs :
   ```bash
   docker compose up -d --build
   ```
3. Exécuter les migrations et le seeder :
   ```bash
   docker compose exec app php database/migrate.php
   docker compose exec app php database/seed.php
   ```
4. L'application est accessible sur `http://localhost:8080`.

## Exécution des tests

```bash
vendor/bin/phpunit --testsuite Unit          # rapides, sans MySQL
vendor/bin/phpunit --testsuite Integration   # nécessite une base MySQL démarrée
vendor/bin/phpunit                           # les deux suites
```

Les tests unitaires du service (`CreerReservationServiceTest`) utilisent des
implémentations en mémoire des repositories (`tests/Unit/Double/`), sans
jamais se connecter à MySQL, conformément à la contrainte du sujet.

**Note d'environnement** : `.env` doit avoir `DB_HOST=127.0.0.1` pour les
tests d'intégration lancés en local (hors Docker). Si l'exécution se fait
dans le conteneur `app`, `DB_HOST=mysql` doit être utilisé à la place.

## Architecture

Voir [ARCHITECTURE.md](ARCHITECTURE.md) pour l'analyse détaillée des choix
architecturaux (MVC, Repository, Service, injection de dépendances,
principes SOLID...).
---

# Étapes du projet

## Étape 0 — Initialiser le dépôt

### Travail réalisé

Pour commencer le projet, j'ai initialisé un dépôt Git et créé la branche
principale `main`.

J'ai également créé les fichiers :

- `.gitignore`
- `README.md`
- `CHANGELOG.md`

Le fichier `.gitignore` permet notamment d'empêcher le versionnement de
fichiers sensibles ou générés automatiquement, comme `.env` et `vendor/`.

Le premier commit a été créé avec le message :

```text
init: initialiser le dépôt
```

## Étape 1 — Initialiser le projet Composer

### Questions

#### 1. Quel est le rôle de Composer ?

Composer est le gestionnaire de dépendances de PHP. Il permet de déclarer,
installer et gérer les bibliothèques utilisées dans un projet, ainsi que
leurs versions et leurs dépendances.

Il permet également de générer automatiquement le chargement des classes
grâce à l'autoloading, notamment avec la norme PSR-4.

#### 2. Quelle différence existe entre require et require-dev ?

`require` contient les dépendances nécessaires au fonctionnement de
l'application.

`require-dev` contient les dépendances utilisées uniquement pendant le
développement, par exemple les outils de test.

Les dépendances de développement peuvent être ignorées en production avec :

```bash
composer install --no-dev
```

#### 3. Pourquoi faut-il versionner composer.lock ?

`composer.lock` enregistre les versions exactes des dépendances installées.

Il faut le versionner afin que tous les développeurs utilisent les mêmes
versions des bibliothèques et que l'environnement du projet soit
reproductible.

Avec `composer install`, Composer utilise `composer.lock` pour installer
les versions enregistrées.

#### 4. Pourquoi ne versionne-t-on pas vendor/ ?

Le dossier `vendor/` contient les bibliothèques installées automatiquement
par Composer.

Il n'est pas nécessaire de le versionner car il peut être recréé à partir
de `composer.json` et `composer.lock` avec `composer install`.

De plus, `vendor/` contient beaucoup de fichiers générés automatiquement et
alourdirait inutilement le dépôt Git. Il est donc ajouté au `.gitignore`.

## Étape 2 — Configurer Eloquent

### Questions

#### 1. Quel rôle joue Capsule\Manager ?

`Capsule\Manager` permet d'utiliser Eloquent en dehors de Laravel.

Il sert notamment à :

- configurer la connexion à MySQL ;
- définir les paramètres de connexion ;
- démarrer Eloquent ;
- rendre la connexion disponible aux modèles Eloquent.

Dans notre projet, c'est lui qui fait le lien entre PHP et MySQL.

#### 2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Parce qu'Eloquent est disponible sous forme de composant indépendant via
`illuminate/database`.

Laravel utilise Eloquent, mais Eloquent n'a pas besoin de tout Laravel pour
fonctionner.

Dans notre projet, nous avons donc installé :

```bash
composer require illuminate/database:^12.0
```

et nous utilisons directement :

```php
use Illuminate\Database\Capsule\Manager as Capsule;
```

Cela permet d'avoir l'ORM Eloquent sans installer le framework Laravel
complet.

Avantage : notre projet reste léger et nous apprenons à assembler
nous-mêmes les composants.

#### 3. Où doit se trouver le démarrage de l'ORM ?

Le démarrage de l'ORM doit se trouver dans la configuration de
l'application, et non dans les modèles ou les contrôleurs.

Dans notre projet, nous avons `config/database.php`. C'est ce fichier qui :

- charge les variables d'environnement ;
- configure `Capsule\Manager` ;
- configure la connexion MySQL ;
- démarre Eloquent.

Les classes métier ne doivent donc pas faire elles-mêmes `new Capsule()` ou
`Dotenv::createImmutable(...)`. Elles doivent simplement recevoir leurs
dépendances.

C'est important pour respecter la séparation des responsabilités.

#### 4. Quelle différence existe entre ORM et SQL écrit à la main ?

Avec du SQL classique, on écrit directement les requêtes :

```sql
SELECT * FROM salles WHERE active = 1;
```

Avec Eloquent, on manipule des objets et des modèles :

```php
$salles = Salle::where('active', true)->get();
```

L'ORM (Object-Relational Mapping) fait le lien entre les objets PHP et les
tables SQL :

| Objet PHP | Base MySQL |
|---|---|
| `Salle` | `salles` |
| `$salle->nom` | `salles.nom` |
| `$salle->capacite` | `salles.capacite` |

Eloquent simplifie donc la manipulation de la base de données, tout en
permettant également d'utiliser du Query Builder lorsque nécessaire.

## Étape 3 — Modèles Eloquent

### Questions

#### 1. Quel type de relation Eloquent avons-nous utilisé ?

Nous avons utilisé une relation **One-to-Many (un-à-plusieurs)**.

Une salle peut avoir plusieurs réservations. Dans le modèle `Salle`, nous
utilisons `hasMany()` :

```php
public function reservations(): HasMany
{
    return $this->hasMany(Reservation::class, 'salle_id');
}
```

Nous pouvons donc utiliser `$salle->reservations` pour récupérer les
réservations d'une salle, et `$reservation->salle` pour récupérer la salle
d'une réservation.

#### 2. Pourquoi déclarer $fillable ou $guarded ?

`$fillable` permet de définir les attributs qui peuvent être remplis lors
d'une assignation de masse. Par exemple, dans `Salle` :

```php
protected $fillable = [
    'nom',
    'batiment',
    'capacite',
    'type',
    'active',
];
```

Cela permet d'utiliser `Salle::create($data)` tout en contrôlant les champs
qui peuvent être affectés. Cela protège l'application contre l'assignation
accidentelle de champs qui ne devraient pas être modifiés.

#### 3. Pourquoi convertir active en booléen ?

La colonne `active` est définie comme un booléen dans la base de données.
Nous avons donc ajouté dans le modèle `Salle` :

```php
protected $casts = [
    'active' => 'boolean',
];
```

Eloquent convertit automatiquement la valeur en `true` ou `false`. Cela
permet d'écrire simplement `if ($salle->active) { ... }` au lieu de
manipuler directement `0` et `1`.

#### 4. Pourquoi convertir les dates en objets ?

Dans le modèle `Reservation`, nous avons :

```php
protected $casts = [
    'salle_id' => 'integer',
    'date_debut' => 'datetime',
    'date_fin' => 'datetime',
];
```

Les dates sont ainsi manipulées comme des objets plutôt que comme de
simples chaînes de caractères. Cela facilite les comparaisons et les
calculs de durée, utiles pour les règles métier (date de début avant la
fin, durée maximale, réservation future, chevauchement).

## Étape 4 — Données initiales

### Seeder

Le fichier `database/seed.php` permet d'insérer les données initiales de
l'application. Il ajoute cinq salles :

- Amphithéâtre A — 250 places
- Salle B12 — 40 places
- Laboratoire Chimie — 24 places
- Salle Informatique 1 — 30 places
- Salle de réunion — 12 places

Le script utilise `firstOrCreate()` afin d'éviter la création de doublons
lorsqu'il est exécuté plusieurs fois.

### Questions

#### 1. Quelle différence existe entre une migration et un seeder ?

Une migration sert à créer ou modifier la structure de la base de données.
Un seeder sert à insérer des données initiales ou de démonstration dans les
tables. La migration définit donc la structure de la base, tandis que le
seeder fournit les données.

#### 2. Pourquoi les données initiales doivent-elles être reproductibles ?

Afin de pouvoir installer ou réinitialiser l'application facilement dans
différents environnements. Un autre développeur doit pouvoir cloner le
projet, créer la base de données, exécuter les migrations puis le seeder et
obtenir les mêmes données initiales, sans provoquer de doublons.

#### 3. Comment empêcher les doublons ?

En utilisant `firstOrCreate()` d'Eloquent :

```php
Salle::firstOrCreate(
    ['nom' => $data['nom'], 'batiment' => $data['batiment']],
    $data
);
```

Si la salle existe déjà, Eloquent la récupère sans créer une nouvelle
ligne. Sinon, il l'insère.

## Étape 5 — Validation

### Questions

#### 1. Pourquoi séparer la validation syntaxique des règles métier ?

La validation syntaxique vérifie la forme des données (une chaîne a la
bonne longueur, un email a le bon format). Les règles métier vérifient la
cohérence fonctionnelle (la salle existe, elle est active, pas de
chevauchement). Séparer les deux permet de réutiliser le validateur
indépendamment de la logique métier, et de tester chaque couche isolément —
un validateur n'a pas besoin de MySQL pour fonctionner.

#### 2. Pourquoi créer une interface de validation ?

Cela impose un contrat commun (`validate(array): ValidationResult`) à tous
les validateurs. N'importe quelle classe qui dépend d'un validateur peut
alors dépendre de `ValidatorInterface` plutôt que d'une classe concrète
précise — ce qui respecte le principe d'inversion des dépendances et
facilite les tests.

#### 3. Pourquoi le validateur ne doit-il pas enregistrer les données ?

Parce que sa seule responsabilité est de vérifier des données. S'il
sauvegardait aussi en base, on ne pourrait plus le tester sans base de
données, et on mélangerait deux préoccupations différentes.

#### 4. Comment retourner plusieurs erreurs en une seule fois ?

En accumulant les erreurs dans un tableau associatif
(`$errors['champ'][] = 'message'`) au lieu de s'arrêter au premier échec.
`ValidationResult` transporte ensuite tout le tableau.

## Étape 6 — Objets de transport (DTO)

### Questions

#### 1. Quelle différence existe entre DTO et modèle Eloquent ?

Le modèle Eloquent est lié à la base de données : il sait se sauvegarder,
se retrouver, et porte des relations. Le DTO ne connaît rien à la base de
données — c'est un simple conteneur de données typées, dont le seul rôle
est de transporter de l'information entre deux couches.

#### 2. Pourquoi le DTO ne doit-il pas appeler save() ?

Parce qu'un DTO n'a pas de comportement lié à la persistance. S'il savait
se sauvegarder, il faudrait qu'il connaisse Eloquent, ce qui casserait la
séparation entre transporter des données et les persister.

#### 3. À quel moment transforme-t-on les chaînes en dates ?

Au moment de la construction du DTO, une fois que le validateur a confirmé
que la chaîne est bien une date valide.

#### 4. Le DTO doit-il contenir la règle de chevauchement ?

Non. Toutes les règles métier vivent exclusivement dans le service métier
(étape 8).

## Étape 7 — Accès aux données (Repositories)

### Questions

#### 1. Eloquent constitue-t-il déjà un accès aux données ?

Oui, Eloquent est déjà en soi une couche d'accès aux données.

#### 2. Pourquoi ajouter un Repository au-dessus d'Eloquent ?

Pour isoler le reste de l'application de la manière concrète dont les
données sont récupérées. Les contrôleurs et services ne dépendent que
d'une interface, jamais d'Eloquent directement.

#### 3. Cette abstraction est-elle toujours nécessaire ?

Pas toujours : sur un petit projet, elle peut être un sur-découpage
inutile. Elle prend tout son sens quand on veut tester sans base de
données.

#### 4. Quel avantage apporte-t-elle ?

Elle permet de tester les services métier avec une implémentation en
mémoire, sans MySQL.

## Étape 8 — Règles métier (Services)

### Questions

#### Pourquoi ces règles ne sont-elles pas dans le contrôleur ?

Parce que le contrôleur a une responsabilité différente : recevoir la
requête HTTP, appeler le validateur, construire le DTO, appeler le service,
puis rediriger. Isoler les règles dans un service les rend testables
indépendamment de FastRoute ou de `$_POST`.

#### 1. Pourquoi le service dépend-il d'une interface de Repository ?

Pour ne jamais dépendre d'une implémentation concrète, et permettre
d'injecter une fausse implémentation pendant les tests.

#### 2. Quelle exception doit être levée en cas de conflit ?

`SalleIndisponibleException`, avec le message « La salle est indisponible
pendant cette période. »

#### 3. Comment tester le service sans MySQL ?

En créant une implémentation en mémoire des interfaces de repository,
injectée à la place de la version Eloquent.

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

FastRoute a une seule responsabilité : faire correspondre une méthode HTTP
et une URL à un « handler ». Il ne sait rien des dépendances dont cette
classe a besoin pour être construite. Lui déléguer aussi la construction
mélangerait deux responsabilités distinctes.

#### 2. Quelle différence existe entre 404 et 405 ?

Un 404 signifie que l'URL demandée ne correspond à aucune route connue. Un
405 signifie que l'URL existe bien, mais que la méthode HTTP utilisée n'est
pas autorisée pour cette URL précise.

#### 3. Pourquoi contraindre {id} avec \d+ ?

Pour que FastRoute ne fasse correspondre cette portion de l'URL qu'à des
chiffres, évitant toute confusion avec d'autres routes comme
`/salles/create`.

#### 4. Quel composant doit interpréter le handler retourné ?

Le point d'entrée de l'application (`public/index.php`), à travers le
conteneur d'injection de dépendances (PHP-DI).

## Étape 11 — Conteneur d'injection de dépendances (PHP-DI)

### Questions

#### 1. Quelle différence existe entre injection et conteneur ?

L'injection de dépendances est un principe : donner à un objet ce dont il a
besoin depuis l'extérieur. Le conteneur est un outil qui automatise ce
principe.

#### 2. Qu'est-ce que l'autowiring ?

C'est la capacité du conteneur à deviner automatiquement comment construire
une classe, en lisant les types déclarés dans son constructeur.

#### 3. Pourquoi les interfaces nécessitent-elles une définition ?

Parce que l'autowiring ne peut deviner une classe concrète qu'à partir
d'elle-même — face à une interface, PHP-DI ne peut pas savoir laquelle de
ses implémentations utiliser.

#### 4. Pourquoi limiter $container->get() au point d'entrée ?

Pour que la construction des objets reste centralisée et prévisible, et ne
pas rompre le principe d'inversion de contrôle.

#### 5. Quel anti-pattern apparaît si toutes les classes interrogent le conteneur ?

Le « Service Locator » : les dépendances ne sont plus déclarées clairement
dans le constructeur, ce qui rend le code plus difficile à tester et à
comprendre.

## Étape 12 — Tests

Voir la section [Exécution des tests](#exécution-des-tests) ci-dessus.

Les tests unitaires des services utilisent des implémentations en mémoire
des repositories (`tests/Unit/Double/`), sans jamais se connecter à MySQL.
Les tests d'intégration vérifient le comportement réel avec Eloquent et
MySQL (création, relations, recherche de chevauchement, annulation).