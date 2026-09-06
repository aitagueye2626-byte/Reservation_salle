<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Model\Salle;

require __DIR__ . '/../config/database.php';

$salles = [
    [
        'nom' => 'Amphithéâtre A',
        'batiment' => 'Bâtiment A',
        'capacite' => 250,
        'type' => 'amphitheatre',
        'active' => true,
    ],
    [
        'nom' => 'Salle B12',
        'batiment' => 'Bâtiment B',
        'capacite' => 40,
        'type' => 'cours',
        'active' => true,
    ],
    [
        'nom' => 'Laboratoire Chimie',
        'batiment' => 'Bâtiment C',
        'capacite' => 24,
        'type' => 'laboratoire',
        'active' => true,
    ],
    [
        'nom' => 'Salle Informatique 1',
        'batiment' => 'Bâtiment D',
        'capacite' => 30,
        'type' => 'informatique',
        'active' => true,
    ],
    [
        'nom' => 'Salle de réunion',
        'batiment' => 'Bâtiment E',
        'capacite' => 12,
        'type' => 'reunion',
        'active' => true,
    ],
];

foreach ($salles as $data) {
    $salle = Salle::firstOrCreate(
        [
            'nom' => $data['nom'],
            'batiment' => $data['batiment'],
        ],
        $data
    );

    echo ($salle->wasRecentlyCreated ? 'Créée : ' : 'Déjà existante : ')
        . $salle->nom
        . PHP_EOL;
}
