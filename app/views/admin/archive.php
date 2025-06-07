<?php
$title = "Архивирани документи";
ob_start();
?>

<h1>Архивирани документи</h1>

<table style="border: 1px solid black;" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Входящ номер</th>
            <th>Име</th>
            <th>Категория</th>
            <th>Дата архивиране</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($archivedDocuments as $doc): ?>
        <tr>
            <td><?= htmlspecialchars($doc['incoming_number']) ?></td>
            <td><?= htmlspecialchars($doc['name']) ?></td>
            <td><?= htmlspecialchars($doc['category_name'] ?? 'Без категория') ?></td>
            <td><?= htmlspecialchars($doc['archived_at']) ?></td>
            <td>
                <a href="index.php?controller=admin&action=restore&id=<?= $doc['id'] ?>">Възстанови</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
