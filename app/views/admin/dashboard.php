<?php include __DIR__ . '/../layouts/main.php'; ?>

<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h2>📑 Всички документи</h2>
    <input type="text" class="form-control w-25" id="searchInput" placeholder="🔍 Търси по номер...">
</div>

<table class="table table-hover table-bordered" id="docTable">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Категория</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($documents as $doc): ?>
            <tr>
                <td><?= htmlspecialchars($doc['incoming_number']) ?></td>
                <td><?= htmlspecialchars($doc['category_name'] ?? 'Без категория') ?></td>
                <td>
                    <span class="badge <?= $doc['status'] === 'archived' ? 'bg-secondary' : 'bg-success' ?>">
                        <?= htmlspecialchars($doc['status']) ?>
                    </span>
                    <?php if ($doc['priority']): ?>
                        <span class="badge bg-danger">🔥 Приоритет</span>
                    <?php endif; ?>
                    <?php if ($doc['pause']): ?>
                        <span class="badge bg-warning text-dark">⏸ Пауза</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($doc['created_at']) ?></td>
                <td class="text-center">
                    <a href="view.php?incoming=<?= $doc['incoming_number'] ?>&access_code=<?= $doc['access_code'] ?>"
                       class="btn btn-sm btn-outline-primary mb-1">Преглед</a>

                    <form method="POST" action="index.php?controller=admin&action=archive" class="d-inline">
                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                        <button class="btn btn-sm btn-outline-secondary mb-1">Архивирай</button>
                    </form>

                    <form method="POST" action="index.php?controller=admin&action=togglePriority" class="d-inline">
                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                        <button class="btn btn-sm <?= $doc['priority'] ? 'btn-warning' : 'btn-outline-warning' ?> mb-1">
                            <?= $doc['priority'] ? 'Премахни приоритет' : 'Приоритет' ?>
                        </button>
                    </form>

                    <form method="POST" action="index.php?controller=admin&action=togglePause" class="d-inline">
                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                        <button class="btn btn-sm <?= $doc['pause'] ? 'btn-danger' : 'btn-outline-danger' ?> mb-1">
                            <?= $doc['pause'] ? 'Пусни' : 'Пауза' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
