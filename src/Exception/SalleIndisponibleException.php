<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class SalleIndisponibleException extends RuntimeException
{
    public static function inexistante(int $salleId): self
    {
        return new self("La salle #{$salleId} n'existe pas.");
    }

    public static function inactive(int $salleId): self
    {
        return new self("La salle #{$salleId} ne peut pas être réservée.");
    }

    public static function periodeInvalide(): self
    {
        return new self('La date de début doit précéder la date de fin.');
    }

    public static function dureeExcessive(): self
    {
        return new self('Une réservation ne peut pas dépasser quatre heures.');
    }

    public static function datePassee(): self
    {
        return new self('La réservation doit commencer dans le futur.');
    }

    public static function conflit(): self
    {
        return new self('La salle est indisponible pendant cette période.');
    }
}