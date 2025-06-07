<?php
// app/controllers/DocumentController.php

require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../core/Helper.php';

class DocumentController
{
    public function upload()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        include __DIR__ . '/../views/documents/upload.php';
    }

    public function store()
    {
        if (!isset($_FILES['document'])) {
            die('Няма качен файл.');
        }

        $filename = Helper::sanitizeFilename($_FILES['document']['name']);
        $destination = UPLOAD_PATH . '/' . $filename;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
            $docModel = new Document();
            $accessCode = Helper::generateCode(10);

            $docId = $docModel->create($filename, $_POST['category_id'], $accessCode);

            echo "Документът е качен успешно! Входящ номер: {$docId}, Код за достъп: {$accessCode}";
        } else {
            echo "Грешка при качване.";
        }
    }

    public function status()
    {
        $code = $_GET['code'] ?? '';
        if (!$code) {
            die('Невалиден код.');
        }

        $docModel = new Document();
        $document = $docModel->getByAccessCode($code);

        if (!$document) {
            echo "Документът не е намерен.";
            return;
        }

        include __DIR__ . '/../views/documents/status.php';
    }
}
