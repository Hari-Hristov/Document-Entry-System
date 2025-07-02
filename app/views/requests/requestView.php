<h1>Панел "Заявки"</h1>

<?php if (empty($requests) && empty($steps)): ?>
    <div class="alert alert-info mt-4">
        Няма нови заявки за обработка.
    </div>
<?php else: ?>
    <table class="table table-striped mt-4 align-middle">
        <thead class="table-light">
            <tr>
                <th>Име на файл / Изискан документ</th>
                <th>Категория</th>
                <th>Качен от</th>
                <th>Дата на качване</th>
                <th>Тип</th>
                <th class="text-center">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request['filename']) ?></td>
                    <td><?= htmlspecialchars($request['category_name'] ?? '–') ?></td>
                    <td><?= htmlspecialchars($request['username'] ?? '–') ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($request['uploaded_at'])) ?></td>
                    <td><span class="badge bg-primary">Нова заявка</span></td>
                    <td class="text-center" style="white-space: nowrap;">
                        <!-- Always show download button for the initial document -->
                        <a href="public/uploads/<?= htmlspecialchars($request['filename']) ?>" class="btn btn-outline-primary btn-sm ms-2" target="_blank">Изтегли</a>
                        <?php if (!empty($pendingStepsByRequestId[$request['id']])): ?>
                            <!-- If a document is already requested or being answered, show info and hide actions -->
                            <span class="text-warning ms-2">
                                Изискан е документ: <strong><?= htmlspecialchars($pendingStepsByRequestId[$request['id']]['required_document']) ?></strong>
                            </span>
                        <?php else: ?>
                            <!-- Show actions only if no document is requested yet -->
                            <form method="post" action="index.php?controller=requests&action=requestNextStep" class="d-inline ms-2">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="step_order" value="1">
                                <select name="required_document" class="form-select form-select-sm d-inline w-auto" required>
                                    <option value="" disabled selected>Избери документ</option>
                                    <?php
                                    $docs = $requiredDocsByRequest[$request['id']] ?? [];
                                    // If this is 'Сесия' and 'Заявление за поправка', add 'Заявление за студентски права' to dropdown
                                    if ($request['category_name'] === 'Сесия' && $request['document_type'] === 'Заявление за поправка') {
                                        if (!in_array('Заявление за студентски права', $docs)) {
                                            $docs[] = 'Заявление за студентски права';
                                        }
                                    }
                                    foreach ($docs as $doc) {
                                        echo '<option value="' . htmlspecialchars($doc) . '">' . htmlspecialchars($doc) . '</option>';
                                    }
                                    ?>
                                </select>
                                <button type="submit" class="btn btn-warning btn-sm ms-1" title="Изискай документ">
                                    <i class="bi bi-arrow-down-circle"></i> Изискай документ
                                </button>
                            </form>
                            <form method="post" action="index.php?controller=requests&action=accept" class="d-inline ms-2">
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
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($steps)): ?>
                <?php foreach ($steps as $step): ?>
                <tr>
                    <td style="vertical-align: middle;">
                        <?= htmlspecialchars($step['required_document']) ?>
                    </td>
                    <td><?= htmlspecialchars($step['category_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($step['username'] ?? '-') ?></td>  
                    <td><?= htmlspecialchars($step['updated_at']) ?></td>
                                        
                    <td>
                        <span class="badge bg-info text-dark" style="vertical-align: middle;">Отговор на заявка</span>
                    </td>
                    <td class="text-center" style="white-space: nowrap;">
                        <?php if (!empty($step['uploaded_file'])): ?>
                            <a href="public/uploads/<?= htmlspecialchars($step['uploaded_file']) ?>"
                               class="btn btn-outline-primary btn-sm ms-2"
                               target="_blank">Изтегли</a>
                        <?php endif; ?>
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
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>