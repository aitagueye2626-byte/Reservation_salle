<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservations,
    ) {
    }

    public function executer(int $reservationId): Reservation
    {
        $reservation = $this->reservations->find($reservationId);

        if ($reservation === null) {
            throw ReservationIntrouvableException::avecId($reservationId);
        }

        return $this->reservations->cancel($reservation);
    }
}        