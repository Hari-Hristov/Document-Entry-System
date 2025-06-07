<?php
// app/services/AuthService.php

require_once _DIR_ . '/../models/User.php';

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Опитва да логне потребител с подадено потребителско име и парола
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login(string $username, string $password): array
    {
        $user = $this->userModel->getByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Потребителят не е намерен.', 'user' => null];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Невалидна парола.', 'user' => null];
        }

        return ['success' => true, 'message' => 'Успешен вход.', 'user' => $user];
    }

    /**
     * Създава нов потребител (регистрация)
     * @param string $username
     * @param string $password
     * @param string $role
     * @return bool
     */
    public function register(string $username, string $password, string $role = 'user'): bool
    {
        return $this->userModel->create($username, $password, $role);
    }

    /**
     * Проверява дали потребителят е логнат (по сесия)
     */
    public static function isAuthenticated(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    /**
     * Проверява дали потребителят е администратор
     */
    public static function isAdmin(): bool
    {
        session_start();
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }

    /**
     * Извършва изход (чисти сесия)
     */
    public static function logout(): void
    {
        session_start();
        session_destroy();
    }
}