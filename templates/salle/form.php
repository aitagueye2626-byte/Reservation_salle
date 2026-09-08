<?php
/** @var \App\Model\Salle|null $salle */
/** @var array<string,string[]> $errors */
/** @var array<string,mixed> $old */
use App\View\View;

$salle ??= null;
$errors ??= [];
$old ??= [];

$valeur = static function (string $champ, mixed $defaut = '') use ($salle, $old): mixed {
    if (array_key_exists($champ, $old)) {
        return $old[$champ];
    }

    return $salle?->{$champ} ?? $defaut;
};
?>
<h1><?= $salle ? 'Modifier la salle' : 'Ajouter une salle' ?></h1>

<form method="post" action="<?= $salle ? "/salles/{$salle->id}/edit" : '/salles' ?>">
    <label>Nom
        <input type="text" name="nom" value="<?= View::e((string) $valeur('nom')) ?>">
    </label>
    <?php foreach ($errors['nom'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Bâtiment
        <input type="text" name="batiment" value="<?= View::e((string) $valeur('batiment')) ?>">
    </label>
    <?php foreach ($errors['batiment'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Capacité
        <input type="number" name="capacite" value="<?= View::e((string) $valeur('capacite')) ?>">
    </label>
    <?php foreach ($errors['capacite'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Type
        <select name="type">
            <?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?>
                <option value="<?= $type ?>" <?= $valeur('type') === $type ? 'selected' : '' ?>>
                    <?= $type ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php foreach ($errors['type'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>
        <input type="checkbox" name="active" value="1" style="width:auto;display:inline-block;margin-right:0.4rem;" <?= $valeur('active', true) ? 'checked' : '' ?>>
        Active
    </label>

    <button type="submit">Enregistrer</button>
</form>