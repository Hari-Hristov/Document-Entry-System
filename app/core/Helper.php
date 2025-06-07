<?php
// app/core/Helper.php

class Helper
{
    // Генерира произволен код с дължина $length символа
    public static function generateCode($length = 10)
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    // Безопасно форматиране на име на файл
    public static function sanitizeFilename($filename)
    {
        return preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
    }
}
