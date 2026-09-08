<?php

declare(strict_types=1);

namespace Tests\Unit\Double;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class InMemorySalleRepository implements SalleRepositoryInterface
{
    /** @var array<int, Salle> */
    private array $salles = [];

    private int $nextId = 1;

    public function ajouter(Salle $salle): void
    {
        if ($salle->id === null) {
            $salle->id = $this->nextId++;
        }

        $this->salles[$salle->id] = $salle;
    }

    public function findAll(): array
    {
        return array_values($this->salles);
    }

    public function find(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function save(Salle $salle): Salle
    {
        $this->ajouter($salle);

        return $salle;
    }
}