<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTOBuilder;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\View\View;

final class ReservationController
{
    public function __construct(
        private ReservationRepositoryInterface $reservations,
        private SalleRepositoryInterface $salles,
        private ReservationValidator $validator,
        private CreerReservationService $creerReservationService,
        private AnnulerReservationService $annulerReservationService,
    ) {
    }

    public function index(?int $salleId = null): string
    {
        $reservations = $salleId !== null
            ? $this->reservations->findBySalle($salleId)
            : $this->reservations->findAll();

        return View::render('layout/base', [
            'title' => 'Réservations',
            'content' => View::render('reservation/index', [
                'reservations' => $reservations,
                'salles' => $this->salles->findAll(),
                'salleId' => $salleId,
            ]),
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->find($id);

        if ($reservation === null) {
        return View::render('layout/base', [
    'title' => 'Introuvable',
    'content' => View::render('error/404'),
]);
        }

        return View::render('layout/base', [
            'title' => 'Réservation #' . $id,
            'content' => View::render('reservation/show', ['reservation' => $reservation]),
        ]);
    }

    public function create(): string
    {
        return View::render('layout/base', [
            'title' => 'Nouvelle réservation',
            'content' => View::render('reservation/form', [
                'salles' => $this->salles->findAll(),
                'errors' => [],
                'old' => [],
            ]),
        ]);
    }

    public function store(array $data): string
    {
      
        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return View::render('layout/base', [
                'title' => 'Nouvelle réservation',
                'content' => View::render('reservation/form', [
                    'salles' => $this->salles->findAll(),
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $dto = CreerReservationDTOBuilder::fromArray($resultat->data());
        try {
            $this->creerReservationService->executer($dto);
        } catch (SalleIndisponibleException $e) {
            return View::render('layout/base', [
                'title' => 'Nouvelle réservation',
                'content' => View::render('reservation/form', [
                    'salles' => $this->salles->findAll(),
                    'errors' => ['general' => [$e->getMessage()]],
                    'old' => $data,
                ]),
            ]);
        }

        header('Location: /reservations');
        exit;
    }

    public function cancel(int $id): string
    {
        try {
            $this->annulerReservationService->executer($id);
        } catch (ReservationIntrouvableException) {
            return View::render('error/404');
        }

        header('Location: /reservations');
        exit;
    }
}