<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class SalleValidatorTest extends TestCase
{
    private SalleValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SalleValidator();
    }

    private function donneesValides(): array
    {
        return [
            'nom' => 'Salle B12',
            'batiment' => 'Bâtiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ];
    }

    public function testCapaciteNegative(): void
    {
        $resultat = $this->validator->validate(array_merge(
            $this->donneesValides(),
            ['capacite' => -5]
        ));

        self::assertFalse($resultat->isValid());
        self::assertTrue($resultat->hasError('capacite'));
    }

    public function testTypeDeSalleInconnu(): void
    {
        $resultat = $this->validator->validate(array_merge(
            $this->donneesValides(),
            ['type' => 'piscine']
        ));

        self::assertFalse($resultat->isValid());
        self::assertTrue($resultat->hasError('type'));
    }
}