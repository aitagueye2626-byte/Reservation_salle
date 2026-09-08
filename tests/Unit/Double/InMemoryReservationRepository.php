<?php

declare(strict_types=1);

namespace Tests\Unit\Double;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;

final class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    /** @var array<int, Reservation> */
    private array $reservations = [];

    private int $nextId = 1;

    public function ajouter(Reservation $reservation): void
    {
        if ($reservation->id === null) {
            $reservation->id = $this->nextId++;
        }

        $this->reservations[$reservation->id] = $reservation;
    }

    public function findAll(): array
    {
        return array_values($this->reservations);
    }

    public function findBySalle(int $salleId): array
    {
        return array_values(array_filter(
            $this->reservations,
            static fn (Reservation $r): bool => $r->salle_id === $salleId
        ));
    }

    public function find(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function findConflict(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin
    ): ?Reservation {
        foreach ($this->reservations as $reservation) {
            if ($reservation->salle_id !== $salleId) {
                continue;
            }

            if ($reservation->statut !== 'confirmée') {
                continue;
            }

            if ($dateDebut < $reservation->date_fin && $dateFin > $reservation->date_debut) {
                return $reservation;
            }
        }

        return null;
    }

    public function save(Reservation $reservation): Reservation
    {
        $this->ajouter($reservation);

        return $reservation;
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulée';

        return $reservation;
    }
}