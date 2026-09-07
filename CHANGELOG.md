# Changelog

Toutes les modifications importantes du projet sont documentées dans ce fichier.

## [v0.0.0] - Étape 0 — Initialiser le dépôt

### Ajouté
- Initialisation du dépôt Git et de la branche `main`.
- Création de `.gitignore`.
- Création de `README.md`.
- Création de `CHANGELOG.md`.

## [v0.1.0] - Étape 1 — Initialiser le projet Composer

### Ajouté
- Initialisation du projet PHP avec Composer.
- Configuration de l'autoloading PSR-4.
- Création de l'arborescence du projet.
- Création de la classe `App\Application`.
- Installation de `nikic/fast-route`, `respect/validation`, `illuminate/database`, `php-di/php-di`, `vlucas/phpdotenv`.
- Création de `composer.lock` et `.env.example`.
- Configuration initiale de `phpunit.xml`.

## [v0.2.0] - Étape 2 — Configurer Eloquent

### Ajouté
- Chargement des variables d'environnement avec `vlucas/phpdotenv`.
- Configuration de `Capsule\Manager` dans `config/database.php`.
- Connexion à la base MySQL `reservation_salles`.
- Création des migrations pour les tables `salles` et `reservations`.

## [v0.3.0] - Étape 3 — Créer les modèles

### Ajouté
- Modèle `App\Model\Salle` avec `$fillable` et `$casts`.
- Modèle `App\Model\Reservation` avec `$fillable` et `$casts`.
- Relation `hasMany` entre `Salle` et `Reservation`.
- Relation `belongsTo` entre `Reservation` et `Salle`.

## [v0.4.0] - Étape 4 — Ajouter les données initiales

### Ajouté
- Script `database/seed.php` insérant 5 salles.
- Utilisation de `firstOrCreate()` pour éviter les doublons lors des exécutions répétées.

## [v0.5.0] - Étape 5 — Créer la validation

### Ajouté
- Interface `ValidatorInterface`.
- Classe `ValidationResult` (isValid, errors, data).
- `SalleValidator` avec Respect\Validation.
- `ReservationValidator` avec Respect\Validation.
- Script de test manuel `test-validation.php`.
## [v0.7.0] - Étape 7 — Accès aux données (Repositories)

### Ajouté
- `SalleRepositoryInterface` et `EloquentSalleRepository`.
- `ReservationRepositoryInterface` et `EloquentReservationRepository`.
- Méthode `findConflict()` traduisant la règle de chevauchement.