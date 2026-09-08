<?php
/** @var \App\Model\Salle[] $salles */
use App\View\View;
?>
<h1>Salles</h1>

<div class="page-actions">
    <a href="/salles/create" class="btn-lien">+ Ajouter une salle</a>
</div>

<table>
    <thead>
        <tr><th>Nom</th><th>Bâtiment</th><th>Capacité</th><th>Type</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($salles as $salle): ?>
        <tr>
            <td><a href="/salles/<?= (int) $salle->id ?>"><?= View::e($salle->nom) ?></a></td>
            <td><?= View::e($salle->batiment) ?></td>
            <td><?= (int) $salle->capacite ?></td>
            <td><?= View::e($salle->type) ?></td>
            <td>
                <span class="statut-<?= $salle->active ? 'active' : 'inactive' ?>">
                    <?= $salle->active ? '● Active' : '○ Désactivée' ?>
                </span>
            </td>
            <td><a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>