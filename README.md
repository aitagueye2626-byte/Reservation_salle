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