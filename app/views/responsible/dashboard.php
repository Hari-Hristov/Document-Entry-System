<pre><?php var_dump($steps); ?></pre>
<h1>Панел на отговорника</h1>

<h2>Нови документи</h2>
<?php if (empty($documents)): ?>
    <div class="alert alert-info">Няма нови документи.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Име на файл</th>
                <th>Категория</th>
                <th>Дата</th>
                <th>Тип</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['filename']) ?></td>
                    <td><?= htmlspecialchars($doc['category_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($doc['created_at']) ?></td>
                    <td>Обикновен документ</td>
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

<h2>Отговори на заявени документи</h2>
<?php if (empty($steps)): ?>
    <div class="alert alert-info">Няма качени отговори на заявени документи.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Изискан документ</th>
                <th>Файл</th>
                <th>Категория</th>
                <th>Качен от</th>
                <th>Дата</th>
                <th>Тип</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($steps as $step): ?>
                <tr>
                    <td><?= htmlspecialchars($step['required_document']) ?></td>
                    <td>
                        <?php if (!empty($step['uploaded_file'])): ?>
                            <a href="/Document-Entry-System/public/uploads/<?= htmlspecialchars($step['uploaded_file']) ?>" target="_blank">Виж файл</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($step['category_id']) ?></td>
                    <td><?= htmlspecialchars($step['username'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($step['updated_at']) ?></td>
                    <td><span class="badge bg-info text-dark">Отговор на заявка</span></td>
                    <td>
                        <form method="post" action="index.php?controller=requests&action=approveStep" class="d-inline">
                            <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm" title="Приеми">
                                <i class="bi bi-check-circle"></i> Приеми
                            </button>
                        </form>
                        <form method="post" action="index.php?controller=requests&action=rejectStep" class="d-inline ms-2">
                            <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
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
