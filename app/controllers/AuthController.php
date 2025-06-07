<?php
// app/controllers/AuthController.php

require_once _DIR_ . '/../services/AuthService.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function loginForm()
    {
        include _DIR_ . '/../views/auth/login.php';
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if (!$result['success']) {
            $error = $result['message'];
            include _DIR_ . '/../views/auth/login.php';
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
    
    public function register() {
    global $pdo;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $full_name = $_POST['full_name'];

        // Валидации...
        if (!$username || !$role || !$full_name) {
            $error = "Моля, попълнете всички полета.";
            include __DIR__ . '/../../views/auth/register.php';
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $password, $role, $full_name]);
            header("Location: index.php?controller=auth&action=login");
        } catch (PDOException $e) {
            $error = "Потребителското име вече съществува.";
            include __DIR__ . '/../../views/auth/register.php';
        }
    } else {
        include __DIR__ . '/../../views/auth/register.php';
    }
}

}