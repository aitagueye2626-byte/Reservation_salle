<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTOBuilder;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Double\InMemoryReservationRepository;
use Tests\Unit\Double\InMemorySalleRepository;

final class CreerReservationServiceTest extends TestCase
{
    private InMemorySalleRepository $salles;
    private InMemoryReservationRepository $reservations;
    private CreerReservationService $service;

    protected function setUp(): void
    {
        $this->salles = new InMemorySalleRepository();
        $this->reservations = new InMemoryReservationRepository();
        $this->service = new CreerReservationService($this->salles, $this->reservations);

        $this->salles->ajouter(new Salle([
            'id' => 1,
            'nom' => 'Salle B12',
            'batiment' => 'Bâtiment B',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
        ]));
    }

    private function dto(array $overrides = []): \App\DTO\CreerReservationDTO
    {
        $demain = (new DateTimeImmutable('+1 day'))->setTime(10, 0);

        $defaut = [
            'salle_id' => 1,
            'responsable' => 'Awa Ndiaye',
            'email' => 'awa.ndiaye@universite.sn',
            'motif' => "Cours d'architecture logicielle",
            'date_debut' => $demain,
            'date_fin' => $demain->modify('+2 hours'),
        ];

        $data = array_merge($defaut, $overrides);

        return (new CreerReservationDTOBuilder())
            ->avecSalleId($data['salle_id'])
            ->avecResponsable($data['responsable'])
            ->avecEmail($data['email'])
            ->avecMotif($data['motif'])
            ->avecDateDebut($data['date_debut'])
            ->avecDateFin($data['date_fin'])
            ->build();
    }

    public function testReservationValide(): void
    {
        $reservation = $this->service->executer($this->dto());

        self::assertSame('confirmée', $reservation->statut);
    }

    public function testSalleInexistante(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage("La salle #99 n'existe pas.");

        $this->service->executer($this->dto(['salle_id' => 99]));
    }

    public function testSalleInactive(): void
    {
        $this->salles->ajouter(new Salle([
            'id' => 2,
            'nom' => 'Salle désactivée',
            'batiment' => 'Bâtiment C',
            'capacite' => 20,
            'type' => 'cours',
            'active' => false,
        ]));

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage('ne peut pas être réservée.');

        $this->service->executer($this->dto(['salle_id' => 2]));
    }

    public function testDateFinAnterieureAuDebut(): void
    {
        $demain = (new DateTimeImmutable('+1 day'))->setTime(10, 0);

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage('doit précéder');

        $this->service->executer($this->dto([
            'date_debut' => $demain,
            'date_fin' => $demain->modify('-1 hour'),
        ]));
    }

    public function testDureeSuperieureAQuatreHeures(): void
    {
        $demain = (new DateTimeImmutable('+1 day'))->setTime(8, 0);

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage('quatre heures');

        $this->service->executer($this->dto([
            'date_debut' => $demain,
            'date_fin' => $demain->modify('+6 hours'),
        ]));
    }

    public function testDatePassee(): void
    {
        $hier = (new DateTimeImmutable('-1 day'))->setTime(10, 0);

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage('futur');

        $this->service->executer($this->dto([
            'date_debut' => $hier,
            'date_fin' => $hier->modify('+2 hours'),
        ]));
    }

    public function testConflitAvecUneReservation(): void
    {
        $this->service->executer($this->dto()); // 10h-12h, demain

        $demain = (new DateTimeImmutable('+1 day'))->setTime(11, 0);

        $this->expectException(SalleIndisponibleException::class);
        $this->expectExceptionMessage('indisponible');

        $this->service->executer($this->dto([
            'date_debut' => $demain,           // 11h
            'date_fin' => $demain->modify('+2 hours'), // 13h — chevauche 10h-12h
        ]));
    }

    public function testReservationVoisineSansChevauchement(): void
    {
        $this->service->executer($this->dto()); // 10h-12h, demain

        $demain = (new DateTimeImmutable('+1 day'))->setTime(12, 0);

        $reservation = $this->service->executer($this->dto([
            'date_debut' => $demain,           // 12h
            'date_fin' => $demain->modify('+2 hours'), // 14h — juste après, pas de chevauchement
        ]));

        self::assertSame('confirmée', $reservation->statut);
    }
}