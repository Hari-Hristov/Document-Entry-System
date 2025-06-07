<?php
// app/models/User.php

require_once 'Model.php';

class User extends Model {

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function create($username, $password, $role = 'user') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (:u, 😛, :r)");
        $stmt->execute([
            ':u' => $username,
            '😛' => $hashed,
            ':r' => $role
        ]);
        return $this->db->lastInsertId();
    }
}
