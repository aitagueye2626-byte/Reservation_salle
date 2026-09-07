<?php
use App\View\View;
?>
<h1><?= View::e($salle->nom) ?></h1>
<p>Bâtiment : <?= View::e($salle->batiment) ?></p>
<p>Capacité : <?= (int) $salle->capacite ?> places</p>
<p>Type : <?= View::e($salle->type) ?></p>
<p>Statut : <?= $salle->active ? 'Active' : 'Désactivée' ?></p>
<a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
<a href="/salles">Retour à la liste</a>