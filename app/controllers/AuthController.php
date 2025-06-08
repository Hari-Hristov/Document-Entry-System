<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/render.php';

class AuthController
{
    private AuthService $authService;

    public function __construct(PDO $pdo)
    {
        $this->authService = new AuthService($pdo);
    }

    public function loginForm()
    {
        render('auth/login');
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if (!$result['success']) {
            $error = $result['message'];
            render('auth/login', ['error' => $error]);
            return;
        }

        session_start();
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['username'] = $result['user']['username'];
        $_SESSION['role'] = $result['user']['role'];

        header('Location: index.php');
        exit;
    }

    public function logout()
    {
        AuthService::logout();
        header('Location: index.php?controller=auth&action=loginForm');
        exit;
    }

    public function register()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $fullName = $_POST['full_name'] ?? '';

        if (!$username || !$password || !$fullName) {
            $error = "Моля, попълнете всички задължителни полета.";
            render('auth/register', ['error' => $error]);
            return;
        }

        $success = $this->authService->register($username, $password, $role, $fullName);

        if ($success) {
            // автоматично логване след регистрация
            $result = $this->authService->login($username, $password);
            if ($result['success']) {
                session_start();
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['role'] = $result['user']['role'];

                header('Location: index.php');
                exit;
            } else {
                $error = "Регистрацията мина успешно, но автоматичното логване се провали.";
                render('auth/login', ['error' => $error]);
                return;
            }
        } else {
            $error = "Регистрацията неуспешна (възможно потребителското име вече съществува).";
            render('auth/register', ['error' => $error]);
        }
    } else {
        render('auth/register');
    }
}

}
