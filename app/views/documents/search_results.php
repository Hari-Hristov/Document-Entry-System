<h1>Резултат от търсенето</h1>

<p><strong>Име на файл:</strong> <?= htmlspecialchars($document['filename']) ?></p>
<p><strong>Категория ID:</strong> <?= htmlspecialchars($document['category_id']) ?></p>
<p><strong>Статус на заявката:</strong>
    <?php
        $catName = !empty($document['category_name']) ? htmlspecialchars($document['category_name']) : '';
        switch ($document['workflow_status']) {
            case 'pending':
                echo 'Очаква одобрение от отговорник';
                if ($catName) echo " за категория: <strong>$catName</strong>";
                echo '.';
                break;
            case 'waiting_user':
                echo 'Очаква отговор/документ от потребителя.';
                break;
            case 'waiting_responsible':
                echo 'Очаква одобрение от отговорник';
                if ($catName) echo " за категория: <strong>$catName</strong>";
                echo ' (след отговор от потребителя).';
                break;
            case 'approved':
                echo 'Заявката е одобрена.';
                break;
            case 'rejected':
                echo 'Заявката е отхвърлена.';
                break;
            default:
                echo htmlspecialchars($document['workflow_status']);
        }
    ?>
</p>
<?php if (
    isset($document['workflow_status']) && $document['workflow_status'] === 'approved'
    && !empty($document['filename'])
): ?>
    <?php $fileUrl = 'uploads/' . $document['filename']; ?>
    <p><a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank">📂 Изтегли документа</a></p>
<?php endif; ?>

<p><a href="index.php?controller=document&action=search">← Назад към търсенето</a></p>
