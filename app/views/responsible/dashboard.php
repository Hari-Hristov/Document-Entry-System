<h1>Заявки за одобрение</h1>

<?php if (empty($documents)): ?>
    <p>Няма нови заявки.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Име на файл</th>
                <th>Категория</th>
                <th>Дата на качване</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['filename']) ?></td>
                    <td><?= htmlspecialchars($doc['category_name']) ?></td>
                    <td><?= htmlspecialchars($doc['created_at']) ?></td>
                    <td>
                        <form method="POST" action="index.php?controller=responsible&action=accept" style="display:inline;">
                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                            <button type="submit">Приеми</button>
                        </form>
                        <form method="POST" action="index.php?controller=responsible&action=reject" style="display:inline;">
                            <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                            <button type="submit" onclick="return confirm('Сигурни ли сте, че искате да отхвърлите документа?')">Отхвърли</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
