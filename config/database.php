<?php

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$capsule = new Capsule();

$options = [];

if (($_ENV['DB_SSL'] ?? 'false') === 'true') {
    $options[\PDO::MYSQL_ATTR_SSL_CA] = $_ENV['DB_SSL_CA'] ?? '/etc/ssl/certs/ca-certificates.crt';
}

$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER']   ?? 'mysql',
    'host'      => $_ENV['DB_HOST']     ?? '',
    'port'      => $_ENV['DB_PORT']     ?? '4000',
    'database'  => $_ENV['DB_DATABASE'] ?? '',
    'username'  => $_ENV['DB_USERNAME'] ?? '',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'options'   => $options,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

return $capsule;
