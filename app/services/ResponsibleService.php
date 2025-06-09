<?php
require_once __DIR__ . '/../config/config.php';

class ResponsibleService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDbConnection();
    }

    // Взимаме категориите, за които отговаря даден потребител
    public function getCategoriesByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE responsible_user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Взимаме документи с статус 'new' от тези категории
    public function getPendingDocumentsForUser(int $userId): array
    {
        $categories = $this->getCategoriesByUserId($userId);
        if (empty($categories)) {
            return [];
        }
        $categoryIds = array_column($categories, 'id');

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT d.*, c.name AS category_name
            FROM documents d
            JOIN categories c ON d.category_id = c.id
            WHERE d.status = 'new' AND d.category_id IN ($placeholders)
            ORDER BY d.created_at DESC
        ");
        $stmt->execute($categoryIds);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Приемане на заявка - промяна статус на 'in_review' или 'archived' (според логика)
    public function acceptDocument(int $documentId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE documents SET status = 'in_review' WHERE id = :id");
        return $stmt->execute([':id' => $documentId]);
    }

    // Отхвърляне на заявка - изтриване на файла и реда в базата
    public function rejectDocument(int $documentId): bool
    {
        // Първо взимаме името на файла
        $stmt = $this->pdo->prepare("SELECT filename FROM documents WHERE id = :id");
        $stmt->execute([':id' => $documentId]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$doc) return false;

        $filePath = UPLOAD_PATH . '/' . $doc['filename'];

        // Трием файла от диска, ако съществува
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Трием записа от базата
        $stmt = $this->pdo->prepare("DELETE FROM documents WHERE id = :id");
        return $stmt->execute([':id' => $documentId]);
    }
}
