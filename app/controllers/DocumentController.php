<?php

require_once __DIR__ . '/../services/DocumentService.php';

class DocumentController
{
    private DocumentService $documentService;

    public function __construct()
    {
        $this->documentService = new DocumentService();
    }

    public function uploadForm()
    {
        // Защита: само за влезли потребители
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        render('documents/upload', []);
    }

    public function upload()
    {
        // Защита: само за влезли потребители
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['document'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;

            $result = $this->documentService->uploadDocument($file, $categoryId);

            if ($result['success']) {
                render('documents/upload', [
                    'success' => true,
                    'entry_number' => $result['incomingNumber'],
                    'access_code' => $result['accessCode'],
                ]);
            } else {
                render('documents/upload', [
                    'error' => $result['message']
                ]);
            }
        } else {
            $this->uploadForm();
        }
    }

    public function search()
    {
        // Защита: само за влезли потребители
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entryNumber = $_POST['entry_number'] ?? '';

            if (!$entryNumber) {
                render('documents/search', ['error' => 'Моля, въведете входящ номер за търсене.']);
                return;
            }

            $document = $this->documentService->findByEntryNumber($entryNumber);

            if ($document) {
                render('documents/search_results', ['document' => $document]);
            } else {
                render('documents/search', ['error' => 'Документът не е намерен.']);
            }
        } else {
            render('documents/search');
        }
    }

    public function status($id = null)
    {
        // Защита: само за влезли потребители
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['access_code'])) {
            $accessCode = $_GET['access_code'];
            $document = $this->documentService->getDocumentStatus($accessCode);

            if ($document) {
                render('documents/status', ['document' => $document]);
            } else {
                render('documents/status', ['error' => 'Документът не е намерен.']);
            }
        } else {
            render('documents/status');
        }
    }
}
