<?php

require_once _DIR_ . '/../services/DocumentService.php';

class DocumentController
{
    private DocumentService $documentService;

    public function __construct()
    {
        $this->documentService = new DocumentService();
    }

    // Action to handle document upload
    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['document'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

            $result = $this->documentService->uploadDocument($file, $categoryId);

            // Simple response handling - could be JSON or redirect with session flash messages
            if ($result['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message'],
                    'documentId' => $result['documentId'],
                    'accessCode' => $result['accessCode']
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }
        } else {
            // Display upload form (if not an API)
            require_once _DIR_ . '/../views/document_upload.php';
        }
    }

    // Action to check document status by access code
    public function status()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['access_code'])) {
            $accessCode = $_GET['access_code'];
            $document = $this->documentService->getDocumentStatus($accessCode);

            if ($document) {
                echo json_encode([
                    'status' => 'success',
                    'document' => $document
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Документът не е намерен.'
                ]);
            }
        } else {
            // Optionally show a form to input access code
            require_once _DIR_ . '/../views/document_status.php';
        }
    }
}