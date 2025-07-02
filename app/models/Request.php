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

// Използваме оригиналния access_code от заявката
$accessCode = $request['access_code'];

// Вмъкваме в таблицата с документи
$stmt = $this->db->prepare("
    INSERT INTO documents (user_id, filename, category_id, access_code, created_at, status)
    VALUES (?, ?, ?, ?, NOW(), 'approved')
");

$result = $stmt->execute([
    $request['uploaded_by_user_id'], // user_id
    $request['filename'],
    $request['category_id'],
    $accessCode
]);


    if (!$result) {
        return false;
    }

    // Вземаме ID на новия документ
    $documentId = $this->db->lastInsertId();

    // Логваме действието
    require_once __DIR__ . '/../models/AccessLog.php';
    $log = new AccessLog($this->db);
    $log->log($documentId, 'approved_upload', $request['uploaded_by_user_id']);

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

        // Изтриване на файла от диска, ако съществува
        $uploadDir = __DIR__ . '/../../uploads/';
        $fullPath = realpath($uploadDir . basename($request['filename']));
        if ($fullPath && file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Изтриване на документа от таблицата с документи, ако съществува
        $stmt = $this->db->prepare("DELETE FROM documents WHERE filename = ?");
        $stmt->execute([$request['filename']]);

        // Маркиране на заявката като отхвърлена (не я изтриваме)
        $stmt = $this->db->prepare("UPDATE document_requests SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$requestId]);

        // По желание, можете също да изтриете request_steps, ако желаете:
        // $stmt = $this->db->prepare("DELETE FROM request_steps WHERE request_id = ?");
        // $stmt->execute([$requestId]);

        return true;
    }
}
