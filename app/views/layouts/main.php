<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($title ?? 'Система за входиране на документ') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
<header>
    <nav>
        <a href="index.php">Начало</a> |
        <a href="index.php?controller=document&action=list">Документи</a> |
        <a href="index.php?controller=category&action=list">Категории</a> |
        <?php if (isset($_SESSION['username'])): ?>
            <span>Здравей, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="index.php?controller=auth&action=logout">Изход</a>
        <?php else: ?>
            <a href="index.php?controller=auth&action=loginForm">Вход</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<footer>
    <hr />
    <p>&copy; <?= date('Y') ?> Система за входиране на документ</p>
</footer>
</body>
</html>
