<?php

require_once _DIR_ . '/../models/Document.php';

class DocumentController {

    protected $documentModel;

    public function __construct() {
        $this->documentModel = new Document();
    }

    public function showUploadForm() {
        include _DIR_ . '/../../views/documents/upload.php';
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            $file = $_FILES['document'];

            $allowedTypes = ['application/pdf', 'application/zip', 'text/html'];
            if (!in_array($file['type'], $allowedTypes)) {
                die('Невалиден файлов формат.');
            }

            $filename = uniqid() . '_' . basename($file['name']);
            $target = UPLOAD_DIR . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $category_id = intval($_POST['category_id'] ?? 0);
                $access_code = bin2hex(random_bytes(8));
                $docId = $this->documentModel->create($filename, $category_id, $access_code);

                echo "Документът е качен успешно! Входящ номер: {$docId}, Код за достъп: {$access_code}";
            } else {
                echo "Грешка при качване на файла.";
            }
        }
    }

    public function status($idOrCode) {
        $document = $this->documentModel->getById($idOrCode);
        if (!$document) {
            $document = $this->documentModel->getByAccessCode($idOrCode);
        }

        if (!$document) {
            echo "Документът не е намерен.";
            return;
        }

        include _DIR_ . '/../../views/documents/status.php';
    }
}