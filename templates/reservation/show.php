<?php
/** @var \App\Model\Reservation $reservation */
use App\View\View;
?>
<h1>Réservation #<?= (int) $reservation->id ?></h1>

<div class="fiche">
    <dl>
        <dt>Salle</dt>
        <dd><?= View::e($reservation->salle->nom ?? '—') ?></dd>

        <dt>Responsable</dt>
        <dd><?= View::e($reservation->responsable) ?></dd>

        <dt>Email</dt>
        <dd><?= View::e($reservation->email) ?></dd>

        <dt>Motif</dt>
        <dd><?= View::e($reservation->motif) ?></dd>

        <dt>Début</dt>
        <dd><?= $reservation->date_debut->format('d/m/Y H:i') ?></dd>

        <dt>Fin</dt>
        <dd><?= $reservation->date_fin->format('d/m/Y H:i') ?></dd>

        <dt>Statut</dt>
        <dd>
            <span class="statut statut-<?= $reservation->statut === 'confirmée' ? 'confirmee' : 'annulee' ?>">
                <?= View::e($reservation->statut) ?>
            </span>
        </dd>
    </dl>

    <?php if ($reservation->statut === 'confirmée'): ?>
        <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel">
            <button type="submit">Annuler cette réservation</button>
        </form>
    <?php endif; ?>
</div>

<p class="page-actions">
    <a href="/reservations">Retour à la liste</a>
</p>