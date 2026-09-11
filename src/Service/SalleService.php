<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class SalleService
{
    public function __construct(
        private SalleRepositoryInterface $salles,
    ) {
    }

    public function lister(): array
    {
        return $this->salles->findAll();
    }

    public function trouver(int $id): ?Salle
    {
        return $this->salles->find($id);
    }

    public function creer(array $data): Salle
    {
        $salle = new Salle($data);

        return $this->salles->save($salle);
    }

    public function modifier(Salle $salle, array $data): Salle
    {
        $salle->fill($data);

        return $this->salles->save($salle);
    }
}