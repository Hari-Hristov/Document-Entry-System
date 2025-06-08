<?php
$uploadDir = __DIR__ . '/public/uploads';

if (is_dir($uploadDir)) {
    if (is_writable($uploadDir)) {
        echo "Директорията public/uploads съществува и е достъпна за запис.";
    } else {
        echo "Директорията public/uploads НЕ е достъпна за запис.";
    }
} else {
    echo "Директорията public/uploads НЕ съществува.";
}
