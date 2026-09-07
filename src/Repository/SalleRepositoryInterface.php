<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

interface SalleRepositoryInterface
{
   
    public function findAll(): array;

    public function find(int $id): ?Salle;

    public function save(Salle $salle): Salle;
}