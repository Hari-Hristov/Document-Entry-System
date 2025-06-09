<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/render.php';

class AuthController
{
    private AuthService $authService;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->authService = new AuthService($pdo);
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function loginForm()
    {
        render('auth/login');
    }

    public function login()
    {
        $this->ensureSessionStarted();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if (!$result['success']) {
            $error = $result['message'];
            render('auth/login', ['error' => $error]);
            return;
        }

        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['username'] = $result['user']['username'];
        $_SESSION['role'] = $result['user']['role'];

        header('Location: index.php?controller=document&action=uploadForm');
        exit;
    }

    public function logout()
    {
        $this->ensureSessionStarted();

        AuthService::logout(); // Тук приемам, че прави session_destroy() и unset на $_SESSION

        header('Location: index.php?controller=auth&action=loginForm');
        exit;
    }

    public function register()
    {
        $this->ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $fullName = $_POST['full_name'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;

            if (!$username || !$password || !$fullName) {
                $error = "Моля, попълнете всички задължителни полета.";
                render('auth/register', ['error' => $error]);
                return;
            }

            if ($role === 'responsible') {
                if (empty($categoryId)) {
                    $error = "Моля, изберете категория, за която ще отговаряте.";
                    render('auth/register', ['error' => $error]);
                    return;
                }
                // Проверка дали категорията вече има отговорник
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ? AND responsible_user_id IS NOT NULL");
                $stmt->execute([$categoryId]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $error = "Тази категория вече има отговорник.";
                    render('auth/register', ['error' => $error]);
                    return;
                }
            }

            $success = $this->authService->register($username, $password, $role, $fullName);

            if ($success) {
                $user = $this->authService->login($username, $password)['user'];

                if ($role === 'responsible') {
                    // Актуализиране на категорията с този user като отговорник
                    $stmt = $this->pdo->prepare("UPDATE categories SET responsible_user_id = ? WHERE id = ?");
                    $stmt->execute([$user['id'], $categoryId]);
                }

                // Автоматично логване след регистрация
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: index.php?controller=document&action=uploadForm');
                exit;
            } else {
                $error = "Регистрацията неуспешна (възможно потребителското име вече съществува).";
                render('auth/register', ['error' => $error]);
            }
        } else {
            render('auth/register');
        }
    }

}
