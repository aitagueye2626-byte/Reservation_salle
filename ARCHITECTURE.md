# Analyse architecturale

## MVC (Model-View-Controller)

**Classes concernées** : `src/Model/`, `src/View/View.php` + `templates/`, `src/Controller/`

**Rôle** : sépare les données (Model), l'affichage (View) et la logique de
coordination (Controller), pour que chaque couche puisse évoluer
indépendamment des autres.

**Avantage** : un changement de mise en page ne touche jamais les règles
métier ni l'accès aux données.

**Limite** : sur un projet plus complexe, le contrôleur peut vite grossir
s'il n'est pas suffisamment déchargé vers des services.

**Extrait** :
\`\`\`php
public function show(int $id): string
{
    $salle = $this->salles->find($id);
    return View::render('layout/base', [...]);
}
\`\`\`

## Front Controller

**Classes concernées** : `public/index.php`

**Rôle** : point d'entrée unique de l'application ; toutes les requêtes HTTP
y passent avant d'être dispatchées.

**Avantage** : centralise l'initialisation (autoload, conteneur, routage),
évite la duplication de code d'amorçage.

**Limite** : un bug dans ce fichier impacte toute l'application.

**Extrait** :
\`\`\`php
$application = $container->get(Application::class);
$application->run();
\`\`\`

## Router

**Classes concernées** : `routes/web.php`, `nikic/fast-route`

**Rôle** : associe une méthode HTTP + une URL à un handler (contrôleur +
méthode), avec gestion des paramètres dynamiques et des erreurs 404/405.

**Avantage** : déclaration centralisée et lisible de toutes les routes.

**Limite** : ne gère pas la construction des objets, doit être combiné à un
conteneur.

**Extrait** :
\`\`\`php
$r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
\`\`\`

## Validator

**Classes concernées** : `src/Validation/`

**Rôle** : vérifie la conformité syntaxique des données reçues avant tout
traitement métier.

**Avantage** : centralise les règles de format, réutilisable et testable
indépendamment de la base de données.

**Limite** : ne connaît pas le contexte métier (ex : chevauchement de
réservations), qui reste du ressort du service.

**Extrait** :
\`\`\`php
v::dateTime('Y-m-d H:i:s')->validate($data['date_debut']);
\`\`\`

## DTO (Data Transfer Object)

**Classes concernées** : `src/DTO/`

**Rôle** : transporte des données déjà validées et typées entre les
couches, sans logique métier ni lien avec la base de données.

**Avantage** : garantit un typage strict (ex : `DateTimeImmutable` plutôt
qu'une chaîne) au moment où le service les reçoit.

**Limite** : ajoute une classe supplémentaire par entité à maintenir.

**Extrait** :
\`\`\`php
public readonly DateTimeImmutable $dateDebut,
\`\`\`

## ORM (Object-Relational Mapping) / Active Record

**Classes concernées** : `illuminate/database`, `src/Model/Salle.php`, `src/Model/Reservation.php`

**Rôle** : représente les tables SQL sous forme de classes PHP, et permet de
manipuler les lignes comme des objets.

**Avantage** : évite d'écrire du SQL à la main pour les opérations
courantes.

**Limite** : masque parfois la requête SQL réellement générée, ce qui peut
compliquer l'optimisation de requêtes complexes.

**Extrait** :
\`\`\`php
final class Salle extends Model
{
    protected $casts = ['active' => 'boolean'];
}
\`\`\`

## Repository

**Classes concernées** : `src/Repository/`

**Rôle** : isole l'accès aux données derrière une interface, pour que le
reste de l'application ne dépende jamais directement d'Eloquent.

**Avantage** : permet de remplacer l'implémentation par une version en
mémoire pendant les tests, sans MySQL.

**Limite** : ajoute une couche d'indirection qui peut sembler redondante
sur un projet très simple.

**Extrait** :
\`\`\`php
interface SalleRepositoryInterface
{
    public function find(int $id): ?Salle;
}
\`\`\`

## Service

**Classes concernées** : `src/Service/CreerReservationService.php`, `AnnulerReservationService.php`

**Rôle** : centralise les règles métier (chevauchement, durée maximale,
salle active...), indépendamment de la couche HTTP.

**Avantage** : testable sans navigateur ni requête HTTP, réutilisable
depuis n'importe quel point d'entrée (web, CLI, API future).

**Limite** : peut devenir volumineux si trop de règles s'y accumulent sans
découpage supplémentaire.

**Extrait** :
\`\`\`php
if ($dureeEnHeures > self::DUREE_MAX_HEURES) {
    throw SalleIndisponibleException::dureeExcessive();
}
\`\`\`

## Injection par constructeur

**Classes concernées** : tous les services, repositories, contrôleurs

**Rôle** : fournit à un objet ses dépendances via son constructeur, plutôt
que de le laisser les construire lui-même.

**Avantage** : dépendances explicites et remplaçables facilement (tests).

**Limite** : nécessite un conteneur pour rester pratique à grande échelle
(sinon la construction manuelle devient lourde).

**Extrait** :
\`\`\`php
public function __construct(
    private SalleRepositoryInterface $salles,
    private ReservationRepositoryInterface $reservations,
) {}
\`\`\`

## Conteneur d'injection

**Classes concernées** : `config/container.php`, `php-di/php-di`

**Rôle** : construit automatiquement les objets et leurs dépendances, à
partir de définitions explicites ou de l'autowiring.

**Avantage** : élimine le besoin d'écrire `new Xxx(new Yyy(), ...)` à la
main partout.

**Limite** : peut masquer la manière dont un objet est réellement construit
si on ne lit pas la configuration.

**Extrait** :
\`\`\`php
SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
\`\`\`

## Autowiring

**Classes concernées** : `config/container.php`

**Rôle** : PHP-DI déduit automatiquement comment construire une classe
concrète, en lisant les types déclarés dans son constructeur.

**Avantage** : aucune configuration nécessaire pour la majorité des
classes.

**Limite** : ne fonctionne pas pour les interfaces, qui nécessitent une
définition explicite.

**Extrait** :
\`\`\`php
CreerReservationService::class => autowire(CreerReservationService::class),
\`\`\`

## Inversion de contrôle

**Classes concernées** : l'ensemble de l'architecture (services, repositories, contrôleurs)

**Rôle** : le flux de contrôle est inversé — ce n'est plus l'objet qui
décide de ses dépendances, mais une entité externe (le conteneur) qui les
lui fournit.

**Avantage** : découplage fort entre les composants, facilite les tests et
l'évolution du code.

**Limite** : peut rendre le flux d'exécution moins évident à suivre pour un
lecteur non familier du pattern.

## Principes SOLID

- **S — Responsabilité unique** : chaque classe a un seul rôle (ex :
  `ReservationValidator` valide, `CreerReservationService` applique les
  règles métier, `EloquentReservationRepository` accède aux données).
- **O — Ouvert/fermé** : on peut ajouter une nouvelle implémentation de
  `ReservationRepositoryInterface` (ex : en mémoire, pour les tests) sans
  modifier le code existant.
- **L — Substitution de Liskov** : n'importe quelle implémentation de
  `SalleRepositoryInterface` peut remplacer une autre sans casser le
  comportement attendu par les services.
- **I — Ségrégation des interfaces** : `SalleRepositoryInterface` et
  `ReservationRepositoryInterface` sont séparées, chacune ne contenant que
  les méthodes pertinentes à son entité.
- **D — Inversion des dépendances** : les services dépendent d'interfaces
  (`SalleRepositoryInterface`), jamais d'implémentations concrètes
  (`EloquentSalleRepository`).