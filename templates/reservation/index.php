<?php

use App\View\View;
?>
<h1>Réservations</h1>
<a href="/reservations/create">+ Nouvelle réservation</a>

<form method="get" action="/reservations">
    <label>Filtrer par salle
        <select name="salle_id" onchange="this.form.submit()">
            <option value="">Toutes les salles</option>
            <?php foreach ($salles as $salle): ?>
                <option value="<?= (int) $salle->id ?>" <?= $salleId === $salle->id ? 'selected' : '' ?>>
                    <?= View::e($salle->nom) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<table>
    <thead>
        <tr><th>Salle</th><th>Responsable</th><th>Début</th><th>Fin</th><th>Statut</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $reservation): ?>
        <tr>
            <td><?= View::e($reservation->salle->nom ?? '—') ?></td>
            <td><a href="/reservations/<?= (int) $reservation->id ?>"><?= View::e($reservation->responsable) ?></a></td>
            <td><?= $reservation->date_debut->format('d/m/Y H:i') ?></td>
            <td><?= $reservation->date_fin->format('d/m/Y H:i') ?></td>
            <td><?= View::e($reservation->statut) ?></td>
            <td>
                <?php if ($reservation->statut === 'confirmée'): ?>
                    <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel">
                        <button type="submit">Annuler</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>