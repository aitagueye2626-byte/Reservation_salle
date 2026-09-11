<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class ReservationQueryService
{
    public function __construct(
        private ReservationRepositoryInterface $reservations,
    ) {
    }

    public function lister(?int $salleId = null): array
    {
        return $salleId !== null
            ? $this->reservations->findBySalle($salleId)
            : $this->reservations->findAll();
    }

    public function trouver(int $id): ?Reservation
    {
        return $this->reservations->find($id);
    }
}