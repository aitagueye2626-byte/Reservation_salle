<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$capsule = require dirname(__DIR__) . '/config/database.php';

$files = glob(__DIR__ . '/migrations/*.php');

sort($files);

foreach ($files as $file) {
    echo "Migration : " . basename($file) . PHP_EOL;

    $migration = require $file;
    $migration($capsule);
}

echo "Migrations terminées.";
