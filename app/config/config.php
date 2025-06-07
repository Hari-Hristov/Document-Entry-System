<?php
// app/config/config.php

// За опростяване - дефинираме променливи директно тук
define('DB_HOST', 'localhost');
define('DB_NAME', 'document_entry_db');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpassword');

// Пътища
define('BASE_PATH', realpath(__DIR__ . '/../../'));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

// Други конфигурации
define('DEBUG_MODE', true);
