<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;
use App\View\View;

final class SalleController
{
    public function __construct(
        private SalleRepositoryInterface $salles,
        private SalleValidator $validator,
    ) {
    }

    public function index(): string
    {
        return View::render('layout/base', [
            'title' => 'Salles',
            'content' => View::render('salle/index', ['salles' => $this->salles->findAll()]),
        ]);
    }

    public function show(int $id): string
    {
        $salle = $this->salles->find($id);

        if ($salle === null) {
            return View::render('error/404');
        }

        return View::render('layout/base', [
            'title' => $salle->nom,
            'content' => View::render('salle/show', ['salle' => $salle]),
        ]);
    }

    public function create(): string
    {
        return View::render('layout/base', [
            'title' => 'Ajouter une salle',
            'content' => View::render('salle/form', ['salle' => null, 'errors' => [], 'old' => []]),
        ]);
    }

    public function store(array $data): string
    {
        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return View::render('layout/base', [
                'title' => 'Ajouter une salle',
                'content' => View::render('salle/form', [
                    'salle' => null,
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $salle = new \App\Model\Salle($resultat->data());
        $this->salles->save($salle);

        header('Location: /salles');
        exit;
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->find($id);

        if ($salle === null) {
            return View::render('error/404');
        }

        return View::render('layout/base', [
            'title' => 'Modifier ' . $salle->nom,
            'content' => View::render('salle/form', ['salle' => $salle, 'errors' => [], 'old' => []]),
        ]);
    }

    public function update(int $id, array $data): string
    {
        $salle = $this->salles->find($id);

        if ($salle === null) {
            return View::render('error/404');
        }

        $resultat = $this->validator->validate($data);

        if (!$resultat->isValid()) {
            return View::render('layout/base', [
                'title' => 'Modifier ' . $salle->nom,
                'content' => View::render('salle/form', [
                    'salle' => $salle,
                    'errors' => $resultat->errors(),
                    'old' => $data,
                ]),
            ]);
        }

        $salle->fill($resultat->data());
        $this->salles->save($salle);

        header('Location: /salles/' . $id);
        exit;
    }
}