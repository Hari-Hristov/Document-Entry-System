<?php
// app/config/config.php

// За опростяване - дефинираме променливи директно тук
define('DB_HOST', 'localhost');
define('DB_NAME', 'document_entry_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Пътища
define('BASE_PATH', realpath(__DIR__ . '/../../'));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

// Други конфигурации
define('DEBUG_MODE', true);

function getDbConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Грешка при свързване с базата: " . $e->getMessage());
    }
}