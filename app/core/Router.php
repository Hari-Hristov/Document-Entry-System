<?php
// app/core/Router.php

class Router
{
    public static function route()
    {
        $controllerName = $_GET['controller'] ?? 'document';
        $action = $_GET['action'] ?? 'upload';

        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(404);
            die("Контролерът $controllerName не съществува.");
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            die("Класът $controllerClass не е намерен.");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            die("Методът $action не съществува в $controllerClass.");
        }

        $controller->$action();
    }
}
