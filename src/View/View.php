<?php

declare(strict_types=1);

namespace App\View;

final class View
{
    public function render(string $template, array $data = []): string
    {
        $path = dirname(__DIR__, 2) . '/templates/' . $template . '.php';

        extract($data);

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }

    public function json(array $data, int $statusCode = 200): string
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}