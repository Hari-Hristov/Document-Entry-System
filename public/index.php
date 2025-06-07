<?php

require_once _DIR_ . '/../app/controllers/DocumentController.php';

$controller = new DocumentController();

$action = $_GET['action'] ?? 'uploadForm';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'upload':
        $controller->upload();
        break;
    case 'status':
        if ($id) {
            $controller->status($id);
        } else {
            echo "Липсва идентификатор на документа.";
        }
        break;
    case 'uploadForm':
    default:
        $controller->showUploadForm();
        break;
}

