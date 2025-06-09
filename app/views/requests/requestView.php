<h1>Панел "Заявки"</h1>

<?php if (empty($requests)): ?>
    <div class="alert alert-info mt-4">
        Няма нови заявки за обработка.
    </div>
<?php else: ?>
    <table class="table table-striped mt-4 align-middle">
        <thead class="table-light">
            <tr>
                <th>Име на файл</th>
                <th>Категория</th>
                <th>Качен от</th>
                <th>Дата на качване</th>
                <th class="text-center">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td><?= htmlspecialchars($request['filename']) ?></td>
                <td><?= htmlspecialchars($request['category_name'] ?? '–') ?></td>
                <td><?= htmlspecialchars($request['username'] ?? '–') ?></td>
                <td><?= date('d.m.Y H:i', strtotime($request['uploaded_at'])) ?></td>
                <td class="text-center" style="white-space: nowrap;">
                    <form method="post" action="index.php?controller=requests&action=accept" class="d-inline">
                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                        <button type="submit" class="btn btn-success btn-sm" title="Приеми">
                            <i class="bi bi-check-circle"></i> Приеми
                        </button>
                    </form>
                    <form method="post" action="index.php?controller=requests&action=reject" class="d-inline ms-2">
                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" title="Отхвърли">
                            <i class="bi bi-x-circle"></i> Отхвърли
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
