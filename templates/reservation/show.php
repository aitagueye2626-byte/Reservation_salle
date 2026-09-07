<?php
use App\View\View;
?>
<h1>Réservation #<?= (int) $reservation->id ?></h1>
<p>Salle : <?= View::e($reservation->salle->nom ?? '—') ?></p>
<p>Responsable : <?= View::e($reservation->responsable) ?></p>
<p>Email : <?= View::e($reservation->email) ?></p>
<p>Motif : <?= View::e($reservation->motif) ?></p>
<p>Début : <?= $reservation->date_debut->format('d/m/Y H:i') ?></p>
<p>Fin : <?= $reservation->date_fin->format('d/m/Y H:i') ?></p>
<p>Statut : <?= View::e($reservation->statut) ?></p>

<?php if ($reservation->statut === 'confirmée'): ?>
    <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel">
        <button type="submit">Annuler cette réservation</button>
    </form>
<?php endif; ?>

<a href="/reservations">Retour à la liste</a>