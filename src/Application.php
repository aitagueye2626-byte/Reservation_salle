<?php

declare(strict_types=1);

namespace App;

use App\View\View;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;

final class Application
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ContainerInterface $container,
    ) {
    }

    public function run(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        $routeInfo = $this->dispatcher->dispatch(
            $httpMethod,
            $uri
        );

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

                $controller = $this->container->get($class);

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
    }
}