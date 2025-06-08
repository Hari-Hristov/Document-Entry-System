<?php
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../services/StatsService.php';

class AdminController {

    public function dashboard() {
        $documents = Document::allWithCategories();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    public function archive() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            Document::updateStatus($id, 'archived');
        }
        header("Location: index.php?controller=admin&action=dashboard");
        exit;
    }

    public function togglePriority() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            Document::toggleFlag($id, 'priority');
        }
        header("Location: index.php?controller=admin&action=dashboard");
        exit;
    }

    public function togglePause() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            Document::toggleFlag($id, 'pause');
        }
        header("Location: index.php?controller=admin&action=dashboard");
        exit;
    }
}
