<?php
// app/services/DocumentService.php

require_once __DIR__ . '/../config/config.php';  // за getDbConnection()

class DocumentService
{
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

        // Генериране на входящ номер и код за достъп
        $incomingNumber = 'DOC-' . date('YmdHis') . '-' . rand(1000, 9999);
        $accessCode = bin2hex(random_bytes(8));

        $uploadDir = __DIR__ . '/../../public/uploads/';
        $fileName = $incomingNumber . '_' . basename($file['name']);
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

        // Запис в базата
        $pdo = getDbConnection();

        $stmt = $pdo->prepare("INSERT INTO documents (filename, category_id, access_code, created_at, status) VALUES (:filename, :category_id, :access_code, NOW(), 'new')");
        $success = $stmt->execute([
            ':filename' => $fileName,
            ':category_id' => $categoryId ?: 5,
            ':access_code' => $accessCode,
        ]);

        if (!$success) {
            return [
                'success' => false,
                'message' => 'Грешка при запис в базата данни.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Документът е успешно качен.',
            'documentId' => $pdo->lastInsertId(),
            'accessCode' => $accessCode,
            'fileName' => $fileName,
            'incomingNumber' => $incomingNumber
        ];
    }
}
