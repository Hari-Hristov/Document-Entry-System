<?php
require_once __DIR__ . '/../config/config.php';

class DocumentService
{
    /**
     * Качва документ и го добавя в базата данни или създава заявка.
     *
     * @param array|null $file
     * @param int|null $categoryId
     * @param string|null $documentType
     * @return array
     */
    public function uploadDocument(?array $file, ?int $categoryId, ?string $documentType): array
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Грешка при качването на файла.'
            ];
        }

        $allowedExtensions = ['zip', 'pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return [
                'success' => false,
                'message' => 'Неразрешен тип файл. Разрешени са само .zip и .pdf файлове.'
            ];
        }

        $entryNumber = 'DOC-' . date('YmdHis') . '-' . rand(1000, 9999);
        $accessCode = bin2hex(random_bytes(8));

        $uploadDir = __DIR__ . '/../../public/uploads/';
        $fileName = $entryNumber . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => false,
                'message' => 'Неуспешно местене на файла.'
            ];
        }

        $pdo = getDbConnection();
        $userId = $_SESSION['user_id'] ?? null;

        $stmt = $pdo->prepare("SELECT responsible_user_id FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $responsibleUserId = $stmt->fetchColumn();

        if (!$responsibleUserId || $responsibleUserId == $userId) {
            $stmt = $pdo->prepare("
                INSERT INTO documents (user_id, filename, category_id, access_code, created_at, document_type)
                VALUES (:user_id, :filename, :category_id, :access_code, NOW(), :document_type)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':filename' => $fileName,
                ':category_id' => $categoryId,
                ':access_code' => $accessCode,
                ':document_type' => $documentType
            ]);
            $documentId = $pdo->lastInsertId();
            $this->logAction($userId, $documentId, 'upload');
            return [
                'success' => true,
                'message' => 'Документът е качен.',
                'documentId' => $documentId,
                'accessCode' => $accessCode,
                'fileName' => $fileName,
                'incomingNumber' => $accessCode
            ];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO document_requests (filename, category_id, uploaded_by_user_id, uploaded_at, document_type, access_code)
                VALUES (:filename, :category_id, :user_id, NOW(), :document_type, :access_code)
            ");
            $stmt->execute([
                ':filename' => $fileName,
                ':category_id' => $categoryId,
                ':user_id' => $userId,
                ':document_type' => $documentType,
                ':access_code' => $accessCode
            ]);
            return [
                'success' => true,
                'message' => 'Заявката е изпратена за одобрение от отговорника.',
                'accessCode' => $accessCode,
                'fileName' => $fileName,
                'incomingNumber' => $accessCode
            ];
        }
    }

    private function logAction(?int $userId, int $documentId, string $action): void
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            INSERT INTO access_logs (document_id, user_id, action, accessed_at)
            VALUES (:document_id, :user_id, :action, NOW())
        ");
        $stmt->execute([
            ':document_id' => $documentId,
            ':user_id' => $userId,
            ':action' => $action
        ]);
    }

    public function getDocumentsByUser(int $userId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            SELECT *
            FROM documents
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByIncomingNumber(string $incomingNumber): ?array
{
    $pdo = getDbConnection();

    // For documents (approved)
    $stmt = $pdo->prepare("
        SELECT d.*, c.name AS category_name, 'approved' as workflow_status
        FROM documents d
        LEFT JOIN categories c ON d.category_id = c.id
        WHERE d.access_code = :incomingNumber
        LIMIT 1
    ");
    $stmt->execute([':incomingNumber' => $incomingNumber]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($document) {
        return $document;
    }

    // For requests (pending, rejected, etc)
    $stmt = $pdo->prepare("
        SELECT dr.*, c.name AS category_name, dr.status as workflow_status
        FROM document_requests dr
        LEFT JOIN categories c ON dr.category_id = c.id
        WHERE dr.access_code = :incomingNumber
        LIMIT 1
    ");
    $stmt->execute([':incomingNumber' => $incomingNumber]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($request) {
        // Optionally, check for steps and add more info
        $stmt2 = $pdo->prepare("SELECT * FROM request_steps WHERE request_id = ? ORDER BY id DESC LIMIT 1");
        $stmt2->execute([$request['id']]);
        $step = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($step) {
            $request['workflow_status'] = $step['status'];
        } else {
            // If no steps, use the main request status (pending, rejected, etc)
            $request['workflow_status'] = $request['status'];
        }
        $request['filename'] = $request['filename'];
        $request['category_id'] = $request['category_id'];
        $request['status'] = $request['status'];
        $request['is_request'] = true;
        return $request;
    }

    return null;
}
}