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
}