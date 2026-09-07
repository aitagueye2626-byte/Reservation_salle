<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;

final class SalleValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        if (!isset($data['nom']) || !v::stringType()->length(2, 100)->validate($data['nom'])) {
            $errors['nom'][] = 'Le nom doit contenir entre 2 et 100 caractères.';
        }

        if (!isset($data['batiment']) || !v::stringType()->length(2, 100)->validate($data['batiment'])) {
            $errors['batiment'][] = 'Le bâtiment doit contenir entre 2 et 100 caractères.';
        }

        if (!isset($data['capacite']) || !v::intVal()->between(1, 1000)->validate((int) $data['capacite'])) {
            $errors['capacite'][] = 'La capacité doit être comprise entre 1 et 1000.';
        }

        $types = [
            'cours',
            'informatique',
            'laboratoire',
            'amphitheatre',
            'reunion'
        ];

        if (!isset($data['type']) || !in_array($data['type'], $types, true)) {
            $errors['type'][] = 'Le type de salle est invalide.';
        }

        if (!isset($data['active']) || !in_array($data['active'], [true, false, 0, 1, '0', '1'], true)) {
            $errors['active'][] = 'Le champ active doit être un booléen.';
        }

        return new ValidationResult($errors, $data);
    }
}
