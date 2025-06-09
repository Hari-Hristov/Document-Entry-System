<?php
// app/models/Request.php

class Request
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

   public function getRequestsByResponsibleUser(int $userId): array
{
    // Вземаме категорията за която отговаря потребителя
    $stmt = $this->db->prepare("SELECT id FROM categories WHERE responsible_user_id = ?");
    $stmt->execute([$userId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        return [];
    }
    $categoryId = $category['id'];

    // Вземаме заявките с JOIN, за да покажем потребителя и категорията
    $stmt = $this->db->prepare("
        SELECT dr.*, u.username, c.name AS category_name 
        FROM document_requests dr
        JOIN users u ON dr.uploaded_by_user_id = u.id
        JOIN categories c ON dr.category_id = c.id
        WHERE dr.category_id = ? AND dr.status = 'pending'
    ");
    $stmt->execute([$categoryId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function acceptRequest(int $requestId): bool
{
    // Вземаме заявката
    $stmt = $this->db->prepare("SELECT * FROM document_requests WHERE id = ? AND status = 'pending'");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        return false;
    }

    // Генерираме код за достъп
    $accessCode = bin2hex(random_bytes(8));

    // Вмъкваме в таблицата с документи (без uploaded_by_user_id)
    $stmt = $this->db->prepare("
        INSERT INTO documents (filename, category_id, access_code, created_at, status)
        VALUES (?, ?, ?, NOW(), 'new')
    ");

    $result = $stmt->execute([
        $request['filename'],
        $request['category_id'],
        $accessCode
    ]);

    if (!$result) {
        return false;
    }

    // Изтриваме заявката
    $stmt = $this->db->prepare("DELETE FROM document_requests WHERE id = ?");
    $stmt->execute([$requestId]);

    return true;
}




    public function rejectRequest(int $requestId): bool
    {
        // Вземи заявката, за да изтриеш файла от диска (ако е необходимо)
        $stmt = $this->db->prepare("SELECT filename FROM document_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            return false;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        $fullPath = realpath($uploadDir . basename($request['filename']));

        if ($fullPath && file_exists($fullPath)) {
           unlink($fullPath);
        }

        // Изтрий заявката от базата
        $stmt = $this->db->prepare("DELETE FROM document_requests WHERE id = ?");
        $stmt->execute([$requestId]);

        return true;
    }
}
