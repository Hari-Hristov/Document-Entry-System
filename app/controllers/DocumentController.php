<?php

require_once __DIR__ . '/../services/DocumentService.php';

class DocumentController
{
    private DocumentService $documentService;

    public function __construct()
    {
        $this->documentService = new DocumentService();
    }

    // Помощен метод за render с layout main.php
    private function render(string $view, array $params = []): void
    {
        // Извличаме параметрите като променливи
        extract($params);

        // Заснемаме съдържанието на view в буфер
        ob_start();
        require __DIR__ . "/../views/documents/{$view}.php";
        $content = ob_get_clean();

        // Вкарваме съдържанието във главния layout (main.php)
        require __DIR__ . '/../views/layouts/main.php';
    }

    public function uploadForm()
    {
        $this->render('upload');  // зареждаме views/documents/upload.php
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['document'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

            $result = $this->documentService->uploadDocument($file, $categoryId);

            if ($result['success']) {
                // Пример: показваме съобщение в нов изглед success.php
                $this->render('upload', [
                    'message' => $result['message'],
                    'documentId' => $result['documentId'],
                    'accessCode' => $result['accessCode'],
                ]);
            } else {
                $this->render('upload', [
                    'error' => $result['message'],
                ]);
            }
        } else {
            $this->showUploadForm();
        }
    }

    public function status($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['access_code'])) {
            $accessCode = $_GET['access_code'];
            $document = $this->documentService->getDocumentStatus($accessCode);

            if ($document) {
                $this->render('status', ['document' => $document]);
            } else {
                $this->render('status', ['error' => 'Документът не е намерен.']);
            }
        } else {
            $this->render('status');
        }
    }
}
