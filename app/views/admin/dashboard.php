<h2>📊 Админ панел: История на действията</h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Потребител</th>
            <th>Действие</th>
            <th>Входен номер на документ</th>
            <th>Код за достъп</th>
            <th>Дата и час</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['username'] ?? 'Анонимен') ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= htmlspecialchars($log['entry_number'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['access_code'] ?? '-') ?></td>
                <td><?= htmlspecialchars($log['accessed_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
