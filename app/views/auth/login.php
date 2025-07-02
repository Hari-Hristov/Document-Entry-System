<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Вход в системата</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <section id="login-container">
        <h2 id="login-title">Вход в системата</h2>

        <?php if (!empty($error)): ?>
            <section id="error-message"><?= htmlspecialchars($error) ?></section>
        <?php endif; ?>

        <form id="login-form" method="post" action="index.php?controller=auth&action=login">
            <label for="username" id="label-username">Потребителско име:</label>
            <input type="text" id="username" name="username" required autofocus />

            <label for="password" id="label-password">Парола:</label>
            <input type="password" id="password" name="password" required />

            <button id="login-button" type="submit">Влез</button>
        </form>
    </section>
</body>
</html>