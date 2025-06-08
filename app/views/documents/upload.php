<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? null;
    $file = $_FILES['document'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        echo "⚠️ Грешка при качването.";
        exit;
    }

    $allowedExtensions = ['zip', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        echo "⚠️ Неразрешен тип файл. Разрешени: .zip, .pdf";
        exit;
    }

    $incomingNumber = 'DOC-' . date('YmdHis') . '-' . rand(1000, 9999);
    $accessCode = bin2hex(random_bytes(8));

    $uploadDir = __DIR__ . '/public/uploads/';
    $fileName = $incomingNumber . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    // Създай директория, ако не съществува
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo "⚠️ Неуспешно местене на файла.";
        exit;
    }

    // Тук може да се добави запис в базата
    echo "<h2>✅ Успешно качен документ!</h2>";
    echo "<p><strong>Входящ номер:</strong> $incomingNumber</p>";
    echo "<p><strong>Код за достъп:</strong> $accessCode</p>";
    echo "<p><a href='/uploads/$fileName' target='_blank'>📂 Изтегли качения файл</a></p>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Избери категория:</label>
    <select name="category">
        <option value="">-- Без категория --</option>
        <option value="1">Отдел Студенти</option>
        <option value="2">Сесия</option>
    </select><br><br>

    <label>Файл (.zip или .pdf):</label>
    <input type="file" name="document" required><br><br>

    <button type="submit">Качи</button>
</form>
