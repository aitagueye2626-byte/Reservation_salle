<?php
use App\View\View;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= View::e($title ?? 'Réservation de salles') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Accueil</a>
            <a href="/salles">Salles</a>
            <a href="/reservations">Réservations</a>
        </nav>
    </header>

    <?php if (!empty($_SESSION['succes'])): ?>
        <p class="message succes"><?= View::e($_SESSION['succes']) ?></p>
        <?php unset($_SESSION['succes']); ?>
    <?php endif; ?>

    <main>
        <?= $content ?>
    </main>
</body>
</html>