<?php

require_once __DIR__ . '/../config/config.php'; 

class AdminService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    public function getAccessLogs(): array
    {
        $stmt = $this->pdo->query("
            SELECT access_logs.*, users.username 
            FROM access_logs
            LEFT JOIN users ON access_logs.user_id = users.id
            ORDER BY accessed_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
