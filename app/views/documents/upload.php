<select name="category_id" id="category_id">
    <?php foreach ($categories as $category): ?>
        <option value="<?= $category['id'] ?>">
            <?= htmlspecialchars($category['name']) ?>
        </option>
    <?php endforeach; ?>
</select>
