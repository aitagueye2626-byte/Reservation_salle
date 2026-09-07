<?php
/** @var \App\Model\Salle[] $salles */
use App\View\View;
?>
<h1>Salles</h1>
<a href="/salles/create">+ Ajouter une salle</a>

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
            <td><?= $salle->active ? 'Active' : 'Désactivée' ?></td>
            <td><a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>