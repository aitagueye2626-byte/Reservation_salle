<?php

declare(strict_types=1);

[$_, $host, $port, $username, $password] = $argv;

try {
    new PDO(
        "mysql:host={$host};port={$port}",
        $username,
        $password
    );

    exit(0);
} catch (\PDOException) {
    exit(1);
}
