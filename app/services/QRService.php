<?php
namespace App\Services;

class QRService {
    public static function generate(string $incomingNumber, string $accessCode): string {
        $text = "http://localhost/document-entry-system/public/view.php?incoming=$incomingNumber&access_code=$accessCode";
        $fileName = "qr_" . $incomingNumber . ".png";
        $outputPath = __DIR__ . "/../../public/uploads/" . $fileName;

        // Създай изображение (placeholder) - реално използвай библиотека
        $im = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagestring($im, 5, 20, 90, 'QR CODE', $black);
        imagepng($im, $outputPath);
        imagedestroy($im);

        return "uploads/" . $fileName;
    }
}
