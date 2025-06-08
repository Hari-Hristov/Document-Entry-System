<?php
require_once __DIR__ . '/../../config/config.php'; // коригирай пътя спрямо местоположението на файла

// Функция за свързване с базата (пример с PDO)

$incoming = $_GET['incoming'];
$accessCode = $_GET['access_code'];

// Свързване с базата
$pdo = getDbConnection();

// Търсим документа по входящ номер и код за достъп
$stmt = $pdo->prepare("SELECT filename FROM documents WHERE filename LIKE :incoming AND access_code = :access_code LIMIT 1");
$stmt->execute([':incoming' => "%$incoming%", ':access_code' => $accessCode]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    echo "<h2>Документ не е намерен или грешни данни.</h2>";
    exit;
}

$filename = $document['filename'];

// Генерираме път към файла
$fileUrl = '/Document-Entry-System/public/uploads/' . $filename;

?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Статус на документ</title>
</head>
<body>
    <h1>Статус на документ</h1>
    <p><strong>Входящ номер:</strong> <?php echo htmlspecialchars($incoming); ?></p>
    <p><strong>Код за достъп:</strong> <?php echo htmlspecialchars($accessCode); ?></p>
    <p><a href="<?php echo htmlspecialchars($fileUrl); ?>" target="_blank">📂 Изтегли документа</a></p>
</body>
</html>
