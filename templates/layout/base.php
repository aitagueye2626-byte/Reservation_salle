<?php
/** @var string $title */
/** @var string $content */
use App\View\View;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($title ?? 'Réservation de salles') ?> — Registre des salles</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <div class="header-inner">
            <span class="brand">Registre des salles universitaires</span>
            <nav>
                <a href="/">Accueil</a>
                <a href="/salles">Salles</a>
                <a href="/reservations">Réservations</a>
            </nav>
        </div>
    </header>

    <main>
        <?php if (!empty($_SESSION['succes'])): ?>
            <p class="message succes"><?= View::e($_SESSION['succes']) ?></p>
            <?php unset($_SESSION['succes']); ?>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>