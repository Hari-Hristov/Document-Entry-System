<h1>Търсене на документ по входящ номер</h1>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="index.php?controller=document&action=search">
    <label for="entry_number">Входящ номер:</label>
    <input type="text" id="entry_number" name="entry_number" required>
    <button type="submit">Търси</button>
</form>
