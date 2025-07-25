<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($title ?? 'Система за входиране на документ') ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>

    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Превключване на навигация">
            <span class="navbar-toggler-icon"></span>
        </button>

        <section class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=document&action=uploadForm">📁 Качи документ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=document&action=search">🔍 Търси документ</a>
                    </li>
                    <?php if ($_SESSION['role'] === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=document&action=myDocuments">🗂️ Моите документи</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'responsible'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=requests&action=index">📋 Заявки</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=admin&action=dashboard">🛠️ Админ панел</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=document&action=pendingRequests">⏳ Непопълнени
                                заявки</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">Здравей, <?= htmlspecialchars($_SESSION['username']) ?>
                            👋</span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php?controller=auth&action=logout">Изход</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light" href="index.php?controller=auth&action=register">Регистрация</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php?controller=auth&action=loginForm">Вход</a>
                    </li>
                <?php endif; ?>
            </ul>
        </section>
    </nav>

    <!-- Основно съдържание -->
    <main class="container py-4">
        <?= $content ?? '' ?>
    </main>

    <!-- Футър -->
    <footer class="bg-light text-center text-muted py-3 border-top">
        <small>&copy; <?= date('Y') ?> Система за входиране на документ</small>
    </footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>

</body>

</html>