<?php

declare(strict_types=1);

namespace App\Validation;

final class ValidationResult
{
  
    public function __construct(
        private readonly array $errors,
        private readonly array $data
    ) {
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

   
    public function errors(): array
    {
        return $this->errors;
    }

  
    public function data(): array
    {
        return $this->data;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function errorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
}
