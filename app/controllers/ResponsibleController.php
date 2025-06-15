<?php
require_once __DIR__ . '/../services/ResponsibleService.php';

class ResponsibleController
{
    private ResponsibleService $service;

    public function __construct()
    {
        $this->service = new ResponsibleService();
    }

    public function dashboard()
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'responsible') {
            header('Location: index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $pendingDocuments = $this->service->getPendingDocumentsForUser($userId);

        // Fetch steps waiting for responsible review
        require_once __DIR__ . '/../models/RequestStep.php';
        $pdo = getDbConnection();
        $stepModel = new RequestStep($pdo);
        $pendingSteps = $stepModel->getStepsForResponsible($userId);

        render('responsible/dashboard', [
            'documents' => $pendingDocuments,
            'steps' => $pendingSteps
        ]);
    }

    public function accept()
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'responsible') {
            header('Location: index.php');
            exit;
        }

        $documentId = $_POST['document_id'] ?? null;
        if (!$documentId) {
            header('Location: index.php?controller=responsible&action=dashboard');
            exit;
        }

        $this->service->acceptDocument((int)$documentId);
        header('Location: index.php?controller=responsible&action=dashboard');
    }

    public function reject()
    {
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'responsible') {
            header('Location: index.php');
            exit;
        }

        $documentId = $_POST['document_id'] ?? null;
        if (!$documentId) {
            header('Location: index.php?controller=responsible&action=dashboard');
            exit;
        }

        $this->service->rejectDocument((int)$documentId);
        header('Location: index.php?controller=responsible&action=dashboard');
    }
}
