<?php
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
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'responsible') {
            header('Location: index.php?controller=auth&action=loginForm');
            exit;
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $requests = $this->requestModel->getRequestsByResponsibleUser($userId);

        require_once __DIR__ . '/../models/RequiredDocument.php';
        $requiredDocModel = new RequiredDocument($this->pdo);

        // Collect required documents for each request
        $requiredDocsByRequest = [];
        foreach ($requests as $request) {
            $requiredDocsByRequest[$request['id']] = $requiredDocModel->getAllByCategoryAndType($request['category_id'], $request['document_type']);
        }

        require_once __DIR__ . '/../models/RequestStep.php';
        $stepModel = new RequestStep($this->pdo);
        $steps = $stepModel->getStepsForResponsible($userId);

        // Fetch the latest pending step for each request
        $pendingStepsByRequestId = [];
        foreach ($requests as $request) {
            $stmt = $this->pdo->prepare("SELECT * FROM request_steps WHERE request_id = ? AND status IN ('waiting_user', 'waiting_responsible') ORDER BY id DESC LIMIT 1");
            $stmt->execute([$request['id']]);
            $pendingStep = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pendingStep) {
                $pendingStepsByRequestId[$request['id']] = $pendingStep;
            }
        }
    
        ob_start();
        require __DIR__ . '/../views/requests/requestView.php';
        $content = ob_get_clean();

        $title = "Панел Заявки";
        require __DIR__ . '/../views/layouts/main.php';
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
            $pdo->prepare("UPDATE request_steps SET status = 'waiting_user' WHERE request_id = ? AND step_order = ?")
                ->execute([$requestId, $stepOrder]);
            header('Location: index.php?controller=requests&action=index');
            exit;
        }
    }

    public function approveStep()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['step_id'])) {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }
        $stepId = (int)$_POST['step_id'];
        require_once __DIR__ . '/../models/RequestStep.php';
        $stepModel = new RequestStep($this->pdo);
        $stepModel->approveStepAndMove($stepId);

        header('Location: index.php?controller=requests&action=index');
        exit;
    }

    public function rejectStep()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['step_id'])) {
            header('Location: index.php?controller=requests&action=index');
            exit;
        }
        $stepId = (int)$_POST['step_id'];
        require_once __DIR__ . '/../models/RequestStep.php';
        $stepModel = new RequestStep($this->pdo);
        $stepModel->rejectStep($stepId);

        header('Location: index.php?controller=requests&action=index');
        exit;
    }
}