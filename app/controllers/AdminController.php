<?php

require_once __DIR__ . '/../services/AdminService.php';

class AdminController
{
    private AdminService $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $logs = $this->adminService->getAccessLogs();
        render('admin/dashboard', ['logs' => $logs]);
    }
}
