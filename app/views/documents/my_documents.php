<h2>🗂️ Моите документи</h2>

<?php if (empty($documents)): ?>
    <div class="alert alert-info">Нямате качени документи.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Входящ номер</th>
                <th>Име на файл</th>
                <th>Категория</th>
                <th>Дата на качване</th>
                <th>Статус</th>
                <th>Изтегли</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['access_code']) ?></td>
                    <td><?= htmlspecialchars($doc['filename']) ?></td>
                    <td><?= htmlspecialchars($doc['category_id']) ?></td>
                    <td><?= htmlspecialchars($doc['created_at']) ?></td>
                    <td><?= htmlspecialchars($doc['status']) ?></td>
                    <td>
                        <a href="/Document-Entry-System/public/uploads/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">📥</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>