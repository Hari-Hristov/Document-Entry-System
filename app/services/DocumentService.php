<?php
// app/services/DocumentService.php

require_once __DIR__ . '/../config/config.php';  // за getDbConnection()

class DocumentService
{

    /**
     * Качва документ и го добавя в базата данни.
     * Ако потребителят е отговорник на категорията, документът се качва директно.
     * Ако не е отговорник, се създава заявка за одобрение.
     *
     * @param array|null $file - информация за качения файл
     * @param int|null $categoryId - ID на категорията
     * @return array - резултат от операцията
     */
    public function uploadDocument(?array $file, ?int $categoryId): array
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
        INSERT INTO documents (user_id, filename, category_id, access_code, created_at)
        VALUES (:user_id, :filename, :category_id, :access_code, NOW())
    ");

            $stmt->execute([
                ':user_id' => $userId,
                ':filename' => $fileName,
                ':category_id' => $categoryId,
                ':access_code' => $accessCode
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
            INSERT INTO document_requests (filename, category_id, uploaded_by_user_id, uploaded_at)
            VALUES (:filename, :category_id, :user_id, NOW())
        ");

            $stmt->execute([
                ':filename' => $fileName,
                ':category_id' => $categoryId,
                ':user_id' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'Заявката е изпратена за одобрение от отговорника.',
                'accessCode' => 'Ще бъде генериран при одобрение',
                'fileName' => $fileName,
                'incomingNumber' => $access_code
            ];
        }
    }

    /**
    * Намира документ по входящ номер (incomingNumber = access_code).
     *
     * @param string $incomingNumber - входящ номер (access_code)
     * @return array|null - информация за документа или null, ако не е намерен
     */
    public function findByIncomingNumber(string $incomingNumber): ?array
    {
        $pdo = getDbConnection();
    
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE access_code = :incomingNumber LIMIT 1");
        $stmt->execute([':incomingNumber' => $incomingNumber]);
    
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $document ?: null;
    }

    private function logAction(?int $userId, int $documentId, string $action): void
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            INSERT INTO access_logs (document_id, user_id, action, accessed_at)_
            VALUES (:document_id, :user_id, :action, NOW())
        ");
        $stmt->execute([
            ':document_id' => $documentId,
            ':user_id' => $userId,
            ':action' => $action
        ]);
    }

    /**
     * Връща всички документи, качени от потребител с даден ID.
     * Включва документи, които са качени директно от потребителя или чрез одобрена заявка.
     *
     * @param int $userId - ID на потребителя
     * @return array - списък с документи
     */
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

}
