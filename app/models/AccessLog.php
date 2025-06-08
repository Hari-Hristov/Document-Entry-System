<?php

require_once 'Model.php';

class AccessLog extends Model {

    public function log($document_id, $action, $user_id = null) {
        $stmt = $this->db->prepare("
            INSERT INTO access_logs (document_id, action, user_id, accessed_at)
            VALUES (:doc, :action, :uid, NOW())
        ");
        $stmt->execute([
            ':doc' => $document_id,
            ':action' => $action,
            ':uid' => $user_id
        ]);
    }

    public function getByDocument($document_id) {
        $stmt = $this->db->prepare("SELECT * FROM access_logs WHERE document_id = :doc ORDER BY accessed_at DESC");
        $stmt->execute([':doc' => $document_id]);
        return $stmt->fetchAll();
    }

    public static function create(int $documentId, ?int $userId, string $action, int $duration = 0): void {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO logs (document_id, performed_by, action, duration_sec)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$documentId, $userId, $action, $duration]);
    }

    public static function statsByDocument(int $documentId): array {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as views, SUM(duration_sec) as total_time
            FROM logs
            WHERE document_id = ? AND action = 'open'
        ");
        $stmt->execute([$documentId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}