<?php
// public/index.php

session_start();

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/helpers/render.php';  // render функцията

require_once __DIR__ . '/../app/controllers/DocumentController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$controllerName = $_GET['controller'] ?? 'document';
$action = $_GET['action'] ?? 'uploadForm';
$id = $_GET['id'] ?? null;
$pdo = getDbConnection();

switch ($controllerName) {
    case 'document':
        $controller = new DocumentController();
        break;
    case 'category':
        $controller = new CategoryController();
        break;
    case 'auth':
        $controller = new AuthController($pdo);
        break;
    default:
        http_response_code(404);
        echo "Неразпознат контролер.";
        exit;
}

if (method_exists($controller, $action)) {
    if ($id !== null) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
} else {
    http_response_code(404);
    echo "Действието не е намерено.";
}
