<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SalleService;
use App\Validation\SalleValidator;
use App\View\View;

final class SalleController
{
    public function __construct(
        private SalleService $salleService,
        private SalleValidator $validator,
        private View $view,
    ) {
    }

    public function index(): string
    {
        return $this->view->render('layout/base', [
            'title' => 'Salles',
            'content' => $this->view->render('salle/index', ['salles' => $this->salleService->lister()]),
        ]);
    }

    public function accueil(): string
    {
        return $this->view->render('layout/base', [
            'title' => 'Accueil',
            'content' => $this->view->render('accueil'),
        ]);
    }

    public function show(int $id): string
    {
        $salle = $this->salleService->trouver($id);

        if ($salle === null) {
            return $this->view->render('layout/base', [
                'title' => 'Introuvable',
                'content' => $this->view->render('error/404'),
            ]);
        }

        return $this->view->render('layout/base', [
            'title' => $salle->nom,
            'content' => $this->view->render('salle/show', ['salle' => $salle]),
        ]);
    }

    public function create(): string
    {
        return $this->view->render('layout/base', [
            'title' => 'Ajouter une salle',
            'content' => $this->view->render('salle/form', ['salle' => null, 'errors' => [], 'old' => []]),
        ]);
    }

    public function store(array $data): string
    {
        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return $this->view->render('layout/base', [
                'title' => 'Ajouter une salle',
                'content' => $this->view->render('salle/form', [
                    'salle' => null,
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $this->salleService->creer($resultat->data());

        header('Location: /salles');
        exit;
    }

    public function edit(int $id): string
    {
        $salle = $this->salleService->trouver($id);

        if ($salle === null) {
            return $this->view->render('layout/base', [
                'title' => 'Introuvable',
                'content' => $this->view->render('error/404'),
            ]);
        }

        return $this->view->render('layout/base', [
            'title' => 'Modifier ' . $salle->nom,
            'content' => $this->view->render('salle/form', ['salle' => $salle, 'errors' => [], 'old' => []]),
        ]);
    }

    public function update(int $id, array $data): string
    {
        $salle = $this->salleService->trouver($id);

        if ($salle === null) {
            return $this->view->render('layout/base', [
                'title' => 'Introuvable',
                'content' => $this->view->render('error/404'),
            ]);
        }

        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return $this->view->render('layout/base', [
                'title' => 'Modifier ' . $salle->nom,
                'content' => $this->view->render('salle/form', [
                    'salle' => $salle,
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $this->salleService->modifier($salle, $resultat->data());

        header('Location: /salles/' . $id);
        exit;
    }
}