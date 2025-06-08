<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Качване на документ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-5">
    <h1>Качи документ</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success) && !empty($entry_number)): ?>
        <div class="alert alert-success">
            Документът е качен успешно!<br>
            <strong>Входящ номер: <?= htmlspecialchars($entry_number) ?></strong>
            <button class="btn btn-sm btn-outline-secondary mt-2" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($entry_number) ?>')">Копирай</button>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="index.php?action=upload">
        <div class="mb-3">
            <label for="category" class="form-label">Избери категория</label>
            <select id="category" name="category_id" class="form-select" required>
                <option value="5">Без категория</option>
                <option value="1">Отдел Студенти</option>
                <option value="2">Учебен отдел – Магистри</option>
                <option value="3">Кандидат-студенти</option>
                <option value="4">Сесия</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="document" class="form-label">Файл (.zip или .pdf)</label>
            <input type="file" id="document" name="document" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary">Качи</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
