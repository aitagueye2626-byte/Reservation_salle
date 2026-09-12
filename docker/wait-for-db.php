<?php

declare(strict_types=1);

[$_, $host, $port, $username, $password] = $argv;

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    if (($_ENV['DB_SSL'] ?? 'false') === 'true') {
        $options[PDO::MYSQL_ATTR_SSL_CA] =
            $_ENV['DB_SSL_CA'] ?? '/etc/ssl/certs/ca-certificates.crt';
    }

    new PDO(
        "mysql:host={$host};port={$port}",
        $username,
        $password,
        $options
    );

    exit(0);

} catch (\PDOException $e) {
    exit(1);
}