<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title>Качване на документ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/Document-Entry-System/public/assets/css/style.css" rel="stylesheet" />
</head>
<body>
<section class="container py-5 main-section">
    <h1>Качи документ</h1>

    <?php if (!empty($error)): ?>
        <section class="alert alert-danger"><?= htmlspecialchars($error) ?></section>
    <?php endif; ?>

    <?php if (!empty($success) && !empty($entry_number)): ?>
        <section class="alert alert-success">
            <?= htmlspecialchars($message ?? 'Документът е качен успешно!') ?><br>
            <strong>Входящ номер: <?= htmlspecialchars($entry_number) ?></strong>
            <button class="btn btn-sm btn-outline-secondary mt-2 copy-btn" data-entry="<?= htmlspecialchars($entry_number) ?>">Копирай</button>
        </section>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" action="index.php?controller=document&action=upload">
        <section class="mb-3">
            <label for="category" class="form-label">Избери категория</label>
            <select id="category" name="category_id" class="form-select" required>
                <option value="5">Без категория</option>
                <option value="1">Отдел Студенти</option>
                <option value="2">Учебен отдел – Магистри</option>
                <option value="3">Кандидат-студенти</option>
                <option value="4">Сесия</option>
            </select>
        </section>

        <section class="mb-3">
            <label for="document" class="form-label">Файл (.zip или .pdf)</label>
            <input type="file" id="document" name="document" class="form-control" required />
        </section>

        <button type="submit" class="btn btn-primary">Качи</button>
    </form>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Copy to clipboard functionality for the entry number
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            navigator.clipboard.writeText(this.getAttribute('data-entry'));
        });
    });
</script>
</body>
</html>