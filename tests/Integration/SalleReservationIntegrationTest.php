<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use PHPUnit\Framework\TestCase;

final class SalleReservationIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        require dirname(__DIR__, 2) . '/config/database.php';
    }

    protected function tearDown(): void
    {
        Reservation::query()->where('responsable', 'like', 'Test Intégration%')->delete();
        Salle::query()->where('nom', 'like', 'Salle Test Intégration%')->delete();
    }

    public function testCreationDUneSalleAvecEloquent(): void
    {
        $salle = new Salle([
            'nom' => 'Salle Test Intégration',
            'batiment' => 'Bâtiment Test',
            'capacite' => 25,
            'type' => 'cours',
            'active' => true,
        ]);
        $salle->save();

        self::assertNotNull($salle->id);

        $retrouvee = Salle::find($salle->id);
        self::assertSame('Salle Test Intégration', $retrouvee->nom);
    }

    public function testRelationSalleReservations(): void
    {
        $salle = new Salle([
            'nom' => 'Salle Test Intégration Relation',
            'batiment' => 'Bâtiment Test',
            'capacite' => 25,
            'type' => 'cours',
            'active' => true,
        ]);
        $salle->save();

        $reservation = new Reservation([
            'salle_id' => $salle->id,
            'responsable' => 'Test Intégration Relation',
            'email' => 'test@universite.sn',
            'motif' => 'Test de la relation',
            'date_debut' => '2027-01-01 10:00:00',
            'date_fin' => '2027-01-01 12:00:00',
            'statut' => 'confirmée',
        ]);
        $reservation->save();

        self::assertCount(1, $salle->reservations()->get());
        self::assertSame($salle->id, $reservation->salle->id);
    }

    public function testRechercheDeChevauchement(): void
    {
        $salle = new Salle([
            'nom' => 'Salle Test Intégration Conflit',
            'batiment' => 'Bâtiment Test',
            'capacite' => 25,
            'type' => 'cours',
            'active' => true,
        ]);
        $salle->save();

        $existante = new Reservation([
            'salle_id' => $salle->id,
            'responsable' => 'Test Intégration Existante',
            'email' => 'test@universite.sn',
            'motif' => 'Réservation existante',
            'date_debut' => '2027-02-01 10:00:00',
            'date_fin' => '2027-02-01 12:00:00',
            'statut' => 'confirmée',
        ]);
        $existante->save();

        $repository = new EloquentReservationRepository();

        $conflit = $repository->findConflict(
            $salle->id,
            new \DateTimeImmutable('2027-02-01 11:00:00'),
            new \DateTimeImmutable('2027-02-01 13:00:00')
        );

        self::assertNotNull($conflit);
        self::assertSame($existante->id, $conflit->id);
    }

    public function testAnnulationDUneReservation(): void
    {
        $salle = new Salle([
            'nom' => 'Salle Test Intégration Annulation',
            'batiment' => 'Bâtiment Test',
            'capacite' => 25,
            'type' => 'cours',
            'active' => true,
        ]);
        $salle->save();

        $reservation = new Reservation([
            'salle_id' => $salle->id,
            'responsable' => 'Test Intégration Annulation',
            'email' => 'test@universite.sn',
            'motif' => 'À annuler',
            'date_debut' => '2027-03-01 10:00:00',
            'date_fin' => '2027-03-01 12:00:00',
            'statut' => 'confirmée',
        ]);
        $reservation->save();

        $repository = new EloquentReservationRepository();
        $repository->cancel($reservation);

        $retrouvee = Reservation::find($reservation->id);
        self::assertSame('annulée', $retrouvee->statut);
    }
}