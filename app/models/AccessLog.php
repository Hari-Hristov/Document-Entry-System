<?php


class AccessLog
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function log(int $documentId, string $action, ?int $userId = null): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO access_logs (document_id, action, user_id, accessed_at)
            VALUES (:doc, :action, :uid, NOW())
        ");
        $stmt->execute([
            ':doc' => $documentId,
            ':action' => $action,
            ':uid' => $userId
        ]);
    }

    public function getByDocument(int $documentId): array
{
    $stmt = $this->db->prepare("
        SELECT al.*, u.username, d.access_code
        FROM access_logs al
        LEFT JOIN users u ON al.user_id = u.id
        LEFT JOIN documents d ON al.document_id = d.id
        WHERE al.document_id = :doc
        ORDER BY al.accessed_at DESC
    ");
    $stmt->execute([':doc' => $documentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAll(): array
{
    $stmt = $this->db->prepare("
        SELECT al.*, u.username, d.access_code
        FROM access_logs al
        LEFT JOIN users u ON al.user_id = u.id
        LEFT JOIN documents d ON al.document_id = d.id
        ORDER BY al.accessed_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
