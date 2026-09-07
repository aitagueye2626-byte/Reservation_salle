<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function findAll(): array
    {
        return Salle::all()->all();
    }

    public function find(int $id): ?Salle
    {
        return Salle::find($id);
    }

    public function save(Salle $salle): Salle
    {
        $salle->save();

        return $salle;
    }
}