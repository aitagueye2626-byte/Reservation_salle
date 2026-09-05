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
