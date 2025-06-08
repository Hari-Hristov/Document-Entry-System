<?php

function render(string $view, array $data = [])
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    extract($data);

    ob_start();
    require __DIR__ . '/../views/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/../views/layouts/main.php';
}
