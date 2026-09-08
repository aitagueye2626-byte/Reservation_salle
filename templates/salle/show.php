<?php
/** @var \App\Model\Salle $salle */
use App\View\View;
?>
<h1><?= View::e($salle->nom) ?></h1>

<div class="fiche">
    <dl>
        <dt>Bâtiment</dt>
        <dd><?= View::e($salle->batiment) ?></dd>

        <dt>Capacité</dt>
        <dd><?= (int) $salle->capacite ?> places</dd>

        <dt>Type</dt>
        <dd><?= View::e($salle->type) ?></dd>

        <dt>Statut</dt>
        <dd>
            <span class="statut-<?= $salle->active ? 'active' : 'inactive' ?>">
                <?= $salle->active ? '● Active' : '○ Désactivée' ?>
            </span>
        </dd>
    </dl>
</div>

<p class="page-actions">
    <a href="/salles/<?= (int) $salle->id ?>/edit" class="btn-lien">Modifier</a>
    <a href="/salles">Retour à la liste</a>
</p>