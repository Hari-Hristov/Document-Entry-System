<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Регистрация</title>
    <link rel="stylesheet" href="/Document-Entry-System/public/assets/css/style.css" />
    <script>
    function toggleCategorySelect() {
        const roleSelect = document.getElementById('role');
        const categoryContainer = document.getElementById('category-select-container');
        if (roleSelect.value === 'responsible') {
            categoryContainer.classList.remove('hidden');
        } else {
            categoryContainer.classList.add('hidden');
            document.getElementById('category_id').value = '';
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('role');
        toggleCategorySelect();
        roleSelect.addEventListener('change', toggleCategorySelect);
    });
    </script>
</head>
<body>
    <section id="register-container">
        <h2>Регистрация</h2>
        <?php if (!empty($error)): ?>
            <section id="error-message"><?= htmlspecialchars($error) ?></section>
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
                <option value="responsible">Отговарящ</option>
                <option value="user">Обикновен потребител</option>
            </select>
            <section id="category-select-container" class="hidden">
                <label for="category_id">Категория:</label>
                <select id="category_id" name="category_id">
                    <option value="">-- Избери категория --</option>
                    <option value="1">Отдел Студенти</option>
                    <option value="2">Учебен отдел – Магистри</option>
                    <option value="3">Кандидат-студенти</option>
                    <option value="4">Сесия</option>
                </select>
            </section>
            <button type="submit">Регистрирай се</button>
        </form>
    </section>
</body>
</html>