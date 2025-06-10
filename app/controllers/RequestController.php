<?php
// app/controllers/RequestsController.php

require_once __DIR__ . '/../models/Request.php';

class RequestsController
{
    private PDO $pdo;
    private Request $requestModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->requestModel = new Request($pdo);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Ограничаваме достъпа само за 'responsible'
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'responsible') {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $requests = $this->requestModel->getRequestsByResponsibleUser($userId);

        // Стартирам буферизация на изхода
        ob_start();
        require __DIR__ . '/../views/requests/requestView.php';
        $content = ob_get_clean();

        $title = "Панел Заявки";
        require __DIR__ . '/../views/layouts/main.php';  // Твойят файл с навигация, футър и др.
    }


    public function accept()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        if ($requestId === null) {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }

        // Приемаме заявката
        $this->requestModel->acceptRequest($requestId);

        header('Location: index.php?controller=requests&action=index');
        exit;
    }

    public function reject()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        if ($requestId === null) {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }

        // Отхвърляме заявката (изтриваме)
        $this->requestModel->rejectRequest($requestId);

        header('Location: index.php?controller=requests&action=index');
        exit;
    }

    public function requestNextStep()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['required_document'])) {
            $pdo = getDbConnection();
            require_once __DIR__ . '/../models/RequestStep.php';
            $stepModel = new RequestStep($pdo);
            $requestId = (int)$_POST['request_id'];
            $requiredDocument = trim($_POST['required_document']);
            $stepOrder = (int)($_POST['step_order'] ?? 1);
            $stepModel->create($requestId, $stepOrder, $requiredDocument);
            // Set status to 'waiting_user'
            $pdo->prepare("UPDATE request_steps SET status = 'waiting_user' WHERE request_id = ? AND step_order = ?")
                ->execute([$requestId, $stepOrder]);
            header('Location: index.php?controller=requests&action=index');
            exit;
        }
    }
}
