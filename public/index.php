<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\View;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require dirname(__DIR__) . '/config/database.php';

$dispatcher = simpleDispatcher(require dirname(__DIR__) . '/routes/web.php');

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo View::render('layout/base', [
            'title' => 'Page introuvable',
            'content' => View::render('error/404'),
        ]);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowedMethods));
        echo View::render('layout/base', [
            'title' => 'Méthode non autorisée',
            'content' => View::render('error/405'),
        ]);
        break;

    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $salleRepo = new EloquentSalleRepository();
        $reservationRepo = new EloquentReservationRepository();

        $controller = match ($class) {
            SalleController::class => new SalleController($salleRepo, new SalleValidator()),
            ReservationController::class => new ReservationController(
                $reservationRepo,
                $salleRepo,
                new ReservationValidator(),
                new CreerReservationService($salleRepo, $reservationRepo),
                new AnnulerReservationService($reservationRepo),
            ),
        };

        $params = array_map(
            static fn (string $value): int => (int) $value,
            array_values($vars)
        );

        if (in_array($method, ['store', 'update'], true)) {
            $params[] = $_POST;
        }

        echo $controller->{$method}(...$params);
        break;
}