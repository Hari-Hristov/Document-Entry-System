<!-- views/documents/upload.php -->
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Качване на документ</title>
</head>
<body>
    <h1>Качване на нов документ</h1>
    <form action="?action=upload" method="post" enctype="multipart/form-data">
        <label for="document">Файл (PDF / ZIP / HTML):</label><br>
        <input type="file" name="document" id="document" required><br><br>

        <label for="category_id">Категория:</label><br>
        <select name="category_id" id="category_id">
            <option value="0">Без категория</option>
            <option value="1">Отдел Студенти</option>
            <option value="2">Учебен отдел – Магистри</option>
            <option value="3">Кандидат-студенти</option>
            <option value="4">Сесия</option>
        </select><br><br>

        <button type="submit">Качи документа</button>
    </form>
</body>
</html>
