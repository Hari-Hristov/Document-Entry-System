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
            SELECT 
                al.*, 
                u.username, 
                COALESCE(d.access_code, dr.access_code) AS access_code
            FROM access_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN documents d ON al.document_id = d.id
            LEFT JOIN document_requests dr ON al.document_id = dr.id
            ORDER BY al.accessed_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
