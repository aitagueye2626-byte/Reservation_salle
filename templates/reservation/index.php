<?php
/** @var \App\Model\Reservation[] $reservations */
/** @var \App\Model\Salle[] $salles */
/** @var int|null $salleId */
use App\View\View;
?>
<h1>Réservations</h1>

<div class="page-actions">
    <a href="/reservations/create" class="btn-lien">+ Nouvelle réservation</a>
</div>

<form method="get" action="/reservations" style="max-width:320px;">
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
            <td>
                <span class="statut statut-<?= $reservation->statut === 'confirmée' ? 'confirmee' : 'annulee' ?>">
                    <?= View::e($reservation->statut) ?>
                </span>
            </td>
            <td>
                <?php if ($reservation->statut === 'confirmée'): ?>
                    <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" style="margin:0;padding:0;border:none;max-width:none;">
                        <button type="submit">Annuler</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>