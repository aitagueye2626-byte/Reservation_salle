<?php
/** @var \App\Model\Salle[] $salles */
/** @var array<string,string[]> $errors */
/** @var array<string,mixed> $old */
use App\View\View;

$errors ??= [];
$old ??= [];

$valeur = static fn (string $champ, mixed $defaut = '') => $old[$champ] ?? $defaut;
?>
<h1>Nouvelle réservation</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="erreurs-generales">
        <?php foreach ($errors['general'] as $erreur): ?>
            <p style="margin:0;"><?= View::e($erreur) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="/reservations">
    <label>Salle
        <select name="salle_id">
            <option value="">— Choisir une salle —</option>
            <?php foreach ($salles as $salle): ?>
                <option value="<?= (int) $salle->id ?>" <?= (string) $valeur('salle_id') === (string) $salle->id ? 'selected' : '' ?>>
                    <?= View::e($salle->nom) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php foreach ($errors['salle_id'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Responsable
        <input type="text" name="responsable" value="<?= View::e((string) $valeur('responsable')) ?>">
    </label>
    <?php foreach ($errors['responsable'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Email
        <input type="email" name="email" value="<?= View::e((string) $valeur('email')) ?>">
    </label>
    <?php foreach ($errors['email'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Motif
        <textarea name="motif"><?= View::e((string) $valeur('motif')) ?></textarea>
    </label>
    <?php foreach ($errors['motif'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Début
        <input type="datetime-local" name="date_debut" value="<?= View::e((string) $valeur('date_debut')) ?>">
    </label>
    <?php foreach ($errors['date_debut'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <label>Fin
        <input type="datetime-local" name="date_fin" value="<?= View::e((string) $valeur('date_fin')) ?>">
    </label>
    <?php foreach ($errors['date_fin'] ?? [] as $erreur): ?>
        <p class="erreur"><?= View::e($erreur) ?></p>
    <?php endforeach; ?>

    <button type="submit">Réserver</button>
</form>