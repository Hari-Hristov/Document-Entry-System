<h2>⏳ Непопълнени заявки</h2>
<?php if (empty($pendingSteps)): ?>
    <div class="alert alert-info">Нямате непопълнени заявки.</div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Изискван документ</th>
                <th>Категория</th>
                <th>Качване</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pendingSteps as $step): ?>
                <tr>
                    <td><?= htmlspecialchars($step['required_document']) ?></td>
                    <td><?= htmlspecialchars($step['category_name']) ?></td>
                    <td>
                        <form method="POST" enctype="multipart/form-data" action="index.php?controller=document&action=uploadStepDocument">
                            <input type="hidden" name="step_id" value="<?= $step['id'] ?>">
                            <input type="file" name="document" required>
                            <button type="submit" class="btn btn-primary btn-sm">Качи</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>