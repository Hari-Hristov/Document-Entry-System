<!-- views/documents/status.php -->
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Статус на документа</title>
</head>
<body>
    <h1>Статус на документ</h1>

    <p><strong>Входящ номер:</strong> <?= htmlspecialchars($document['id']) ?></p>
    <p><strong>Име на файл:</strong> <?= htmlspecialchars($document['filename']) ?></p>
    <p><strong>Категория:</strong> <?= htmlspecialchars($document['category_id']) ?></p>
    <p><strong>Код за достъп:</strong> <?= htmlspecialchars($document['access_code']) ?></p>
    <p><strong>Създаден на:</strong> <?= htmlspecialchars($document['created_at']) ?></p>
    <p><strong>Статус:</strong> <?= htmlspecialchars($document['status']) ?></p>
</body>
</html>
