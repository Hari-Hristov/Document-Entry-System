<?php
// app/services/AuthService.php

require_once __DIR__ . '/../models/User.php';

class AuthService
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function login(string $username, string $password): array
    {
        $user = $this->userModel->getByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Потребителят не е намерен.', 'user' => null];
        }

        if (!password_verify($password, $user['password'])) { 
            return ['success' => false, 'message' => 'Невалидна парола.', 'user' => null];
        }

        return ['success' => true, 'message' => 'Успешен вход.', 'user' => $user];
    }

   public function register(string $username, string $password, string $role = 'user', string $fullName = ''): ?int
    {
        $result = $this->userModel->create($username, $password, $role, $fullName);
        if ($result) {
            return (int)$this->userModel->getLastInsertId();
        }
        return null;
    }

    public static function isAuthenticated(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        session_start();
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
    }
}
