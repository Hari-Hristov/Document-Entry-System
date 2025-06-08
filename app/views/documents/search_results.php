<h1>Резултат от търсенето</h1>

<p><strong>Име на файл:</strong> <?php echo htmlspecialchars($document['filename']); ?></p>
<p><strong>Категория ID:</strong> <?php echo htmlspecialchars($document['category_id']); ?></p>
<p><strong>Статус:</strong> <?php echo htmlspecialchars($document['status']); ?></p>

<?php
$fileUrl = '/Document-Entry-System/public/uploads/' . $document['filename'];
?>

<p><a href="<?php echo htmlspecialchars($fileUrl); ?>" target="_blank">📂 Изтегли документа</a></p>

<p><a href="index.php?controller=document&action=search">← Назад към търсенето</a></p>
