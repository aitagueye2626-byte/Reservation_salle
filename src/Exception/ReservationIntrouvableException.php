<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class ReservationIntrouvableException extends RuntimeException
{
    public static function avecId(int $id): self
    {
        return new self("La réservation #{$id} n'existe pas.");
    }
}