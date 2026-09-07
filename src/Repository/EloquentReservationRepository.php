<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function findAll(): array
    {
        return Reservation::all()->all();
    }

    public function findBySalle(int $salleId): array
    {
        return Reservation::where('salle_id', $salleId)->get()->all();
    }

    public function find(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function findConflict(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin
    ): ?Reservation {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $dateFin->format('Y-m-d H:i:s'))
            ->where('date_fin', '>', $dateDebut->format('Y-m-d H:i:s'))
            ->first();
    }

    public function save(Reservation $reservation): Reservation
    {
        $reservation->save();

        return $reservation;
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulée';
        $reservation->save();

        return $reservation;
    }
}