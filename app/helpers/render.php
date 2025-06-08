<?php
function render(string $view, array $data = [])
{
    extract($data);

    // Зареждаме изгледа в променлива $content
    ob_start();
    require __DIR__ . '/../views/' . $view . '.php';
    $content = ob_get_clean();

    // След това зареждаме основния layout, който ползва $content
    require __DIR__ . '/../views/layouts/main.php';
}
