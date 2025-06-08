<?php

require_once _DIR_ . '/../models/Document.php';
require_once _DIR_ . '/../core/Helper.php';

class DocumentService
{
    private Document $documentModel;

    public function __construct()
    {
        $this->documentModel = new Document();
    }

    /**
     * Обработка на качване на документ
     * @param array $file - данни за качения файл ($_FILES['document'])
     * @param int|null $categoryId - избрана категория
     * @return array - ['success' => bool, 'message' => string, 'documentId' => int|null, 'accessCode' => string|null]
     */
    public function uploadDocument(array $file, ?int $categoryId): array
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Грешка при качване на файл.', 'documentId' => null, 'accessCode' => null];
        }

        $filename = Helper::sanitizeFilename($file['name']);
        $uploadDir = __DIR__ . '/../public/uploads';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            error_log("Failed to move uploaded file from {$file['tmp_name']} to $destination");
            return ['success' => false, 'message' => 'Неуспешно записване на файла.', 'documentId' => null, 'accessCode' => null];
        }

        $accessCode = Helper::generateCode(10);

        $documentId = $this->documentModel->create($filename, $categoryId, $accessCode);

        if (!$documentId) {
            return ['success' => false, 'message' => 'Грешка при запис в базата данни.', 'documentId' => null, 'accessCode' => null];
        }

        return ['success' => true, 'message' => 'Документът е качен успешно.', 'documentId' => $documentId, 'accessCode' => $accessCode];
    }


    /**
     * Връща информация за документа по входящ номер или код за достъп
     */
    public function getDocumentStatus(string $accessCode)
    {
        return $this->documentModel->getByAccessCode($accessCode);
    }
}