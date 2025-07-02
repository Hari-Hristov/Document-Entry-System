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
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }
        render('documents/upload', []);
    }

    public function upload()
    {
        if (!isset($_SESSION['username'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['document'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;
            $documentType = $_POST['document_type'] ?? null;

            $result = $this->documentService->uploadDocument($file, $categoryId, $documentType);

            if ($result['success']) {
                render('documents/upload', [
                    'success' => true,
                    'entry_number' => $result['incomingNumber'] ?? '',
                    'access_code' => $result['accessCode'] ?? '',
                    'message' => $result['message'] ?? ''
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

    public function pendingRequests()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }
        $pdo = getDbConnection();
        require_once __DIR__ . '/../models/RequestStep.php';
        $stepModel = new RequestStep($pdo);
        $pendingSteps = $stepModel->getPendingStepsForUser($_SESSION['user_id']);
        render('documents/pending_requests', ['pendingSteps' => $pendingSteps]);
    }

    public function uploadStepDocument()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step_id']) && isset($_FILES['document'])) {
            $stepId = (int)$_POST['step_id'];
            $file = $_FILES['document'];
            $uploadDir = __DIR__ . '/../../uploads/';
            $fileName = uniqid('step_') . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;
            move_uploaded_file($file['tmp_name'], $filePath);

            require_once __DIR__ . '/../models/RequestStep.php';
            $pdo = getDbConnection();
            $stepModel = new RequestStep($pdo);
            $stepModel->updateStepFile($stepId, $fileName);

            header('Location: index.php?controller=document&action=pendingRequests');
            exit;
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

            $document = $this->documentService->findByIncomingNumber($entryNumber);

            if ($document) {
                if ($document && isset($document['id'])) {
                    $pdo = getDbConnection();
                    $stmt = $pdo->prepare("
                        INSERT INTO access_logs (document_id, user_id, action, accessed_at)
                        VALUES (:document_id, :user_id, 'search', NOW())
                    ");
                    $stmt->execute([
                        ':document_id' => $document['id'],
                        ':user_id' => $_SESSION['user_id'] ?? null
                    ]);
                }
                render('documents/search_results', ['document' => $document]);
            } else {
                if ($document && isset($document['id'])) {
                    $pdo = getDbConnection();
                    $stmt = $pdo->prepare("
                        INSERT INTO access_logs (document_id, user_id, action, accessed_at)
                        VALUES (:document_id, :user_id, 'search', NOW())
                    ");
                    $stmt->execute([
                        ':document_id' => $document['id'],
                        ':user_id' => $_SESSION['user_id'] ?? null
                    ]);
                }
                render('documents/search', ['error' => 'Документът не е намерен.']);
            }
        } else {
            render('documents/search');
        }
    }

    public function myDocuments()
    {
        if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $documents = $this->documentService->getDocumentsByUser($userId);

        render('documents/my_documents', ['documents' => $documents]);
    }
}