<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTOBuilder;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Service\ReservationQueryService;
use App\Service\SalleService;
use App\Validation\ReservationValidator;
use App\View\View;

final class ReservationController
{
    public function __construct(
        private ReservationQueryService $reservationQueryService,
        private SalleService $salleService,
        private ReservationValidator $validator,
        private CreerReservationService $creerReservationService,
        private AnnulerReservationService $annulerReservationService,
        private View $view,
    ) {
    }

    public function index(?int $salleId = null): string
    {
        return $this->view->render('layout/base', [
            'title' => 'Réservations',
            'content' => $this->view->render('reservation/index', [
                'reservations' => $this->reservationQueryService->lister($salleId),
                'salles' => $this->salleService->lister(),
                'salleId' => $salleId,
            ]),
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservationQueryService->trouver($id);

        if ($reservation === null) {
            return $this->view->render('layout/base', [
                'title' => 'Introuvable',
                'content' => $this->view->render('error/404'),
            ]);
        }

        return $this->view->render('layout/base', [
            'title' => 'Réservation #' . $id,
            'content' => $this->view->render('reservation/show', ['reservation' => $reservation]),
        ]);
    }

    public function create(): string
    {
        return $this->view->render('layout/base', [
            'title' => 'Nouvelle réservation',
            'content' => $this->view->render('reservation/form', [
                'salles' => $this->salleService->lister(),
                'errors' => [],
                'old' => [],
            ]),
        ]);
    }

    public function store(array $data): string
    {
        foreach (['date_debut', 'date_fin'] as $champ) {
            if (!empty($data[$champ])) {
                $data[$champ] = str_replace('T', ' ', $data[$champ]);

                if (strlen($data[$champ]) === 16) {
                    $data[$champ] .= ':00';
                }
            }
        }

        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return $this->view->render('layout/base', [
                'title' => 'Nouvelle réservation',
                'content' => $this->view->render('reservation/form', [
                    'salles' => $this->salleService->lister(),
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $dto = CreerReservationDTOBuilder::fromArray($resultat->data());

        try {
            $this->creerReservationService->executer($dto);
        } catch (SalleIndisponibleException $e) {
            return $this->view->render('layout/base', [
                'title' => 'Nouvelle réservation',
                'content' => $this->view->render('reservation/form', [
                    'salles' => $this->salleService->lister(),
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
            return $this->view->render('layout/base', [
                'title' => 'Introuvable',
                'content' => $this->view->render('error/404'),
            ]);
        }

        header('Location: /reservations');
        exit;
    }
}