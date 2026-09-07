<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;

final class CreerReservationService
{
    private const DUREE_MAX_HEURES = 4;

    public function __construct(
        private SalleRepositoryInterface $salles,
        private ReservationRepositoryInterface $reservations,
    ) {
    }

    public function executer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salles->find($dto->salleId);

        if ($salle === null) {
            throw SalleIndisponibleException::inexistante($dto->salleId);
        }

        if (!$salle->active) {
            throw SalleIndisponibleException::inactive($dto->salleId);
        }

        if ($dto->dateDebut >= $dto->dateFin) {
            throw SalleIndisponibleException::periodeInvalide();
        }

        $dureeEnHeures = ($dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp()) / 3600;

        if ($dureeEnHeures > self::DUREE_MAX_HEURES) {
            throw SalleIndisponibleException::dureeExcessive();
        }

        if ($dto->dateDebut <= new \DateTimeImmutable()) {
            throw SalleIndisponibleException::datePassee();
        }

        $conflit = $this->reservations->findConflict($dto->salleId, $dto->dateDebut, $dto->dateFin);

        if ($conflit !== null) {
            throw SalleIndisponibleException::conflit();
        }

        $reservation = new Reservation([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => 'confirmée',
        ]);

        return $this->reservations->save($reservation);
    }
}