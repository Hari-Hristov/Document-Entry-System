<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Регистрация</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px; }
        .register-container { max-width: 400px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; }
        input[type="text"], input[type="password"], select {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            width: 100%; padding: 10px; background: #28a745;
            border: none; color: white; font-size: 16px;
            border-radius: 4px; cursor: pointer;
        }
        button:hover { background: #218838; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Регистрация</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?controller=auth&action=register">
            <label for="full_name">Пълно име:</label>
            <input type="text" id="full_name" name="full_name" required />

            <label for="username">Потребителско име:</label>
            <input type="text" id="username" name="username" required />

            <label for="password">Парола:</label>
            <input type="password" id="password" name="password" required />

            <label for="role">Роля:</label>
            <select id="role" name="role" required>
                <option value="">-- Избери роля --</option>
                <option value="admin">Администратор</option>
                <option value="category_manager">Отговарящ</option>
                <option value="user">Обикновен потребител</option>
            </select>

            <button type="submit">Регистрирай се</button>
        </form>
    </div>
</body>
</html>
