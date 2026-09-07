<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;

final class CreerReservationDTOBuilder
{
    private int $salleId = 0;
    private string $responsable = '';
    private string $email = '';
    private string $motif = '';
    private DateTimeImmutable $dateDebut;
    private DateTimeImmutable $dateFin;

    public function __construct()
    {
        $this->dateDebut = new DateTimeImmutable();
        $this->dateFin = new DateTimeImmutable();
    }

    public function avecSalleId(int $salleId): self
    {
        $this->salleId = $salleId;

        return $this;
    }

    public function avecResponsable(string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function avecEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function avecMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function avecDateDebut(DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function avecDateFin(DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function build(): CreerReservationDTO
    {
        return new CreerReservationDTO(
            salleId: $this->salleId,
            responsable: $this->responsable,
            email: $this->email,
            motif: $this->motif,
            dateDebut: $this->dateDebut,
            dateFin: $this->dateFin,
        );
    }

    public static function fromArray(array $data): CreerReservationDTO
    {
        return (new self())
            ->avecSalleId((int) $data['salle_id'])
            ->avecResponsable((string) $data['responsable'])
            ->avecEmail((string) $data['email'])
            ->avecMotif((string) $data['motif'])
            ->avecDateDebut(new DateTimeImmutable($data['date_debut']))
            ->avecDateFin(new DateTimeImmutable($data['date_fin']))
            ->build();
    }
}