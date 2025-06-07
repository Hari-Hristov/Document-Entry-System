<?php
// app/models/User.php

class User
{
    private $db;

    public function __construct()
    {
        // Тук се свързваш с БД, например PDO
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getByUsername(string $username)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $username, string $password, string $role = 'user')
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)');
        return $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'role' => $role
        ]);
    }
}
