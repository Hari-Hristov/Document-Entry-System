<?php
namespace App\Services;

class CryptoService {
    public static function splitKey(string $secret, int $parts = 3, int $threshold = 2): array {
        $length = strlen($secret);
        $fragmentSize = max(1, intdiv($length, $parts));
        return str_split($secret, $fragmentSize);
    }

    public static function reconstructKey(array $fragments): string {
        return implode('', $fragments);
    }
}
