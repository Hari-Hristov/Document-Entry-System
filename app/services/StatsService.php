<?php
namespace App\Services;

use App\Models\AccessLog;

class StatsService {
    public static function logAccess(int $documentId, ?int $userId = null, int $duration = 0): void {
        AccessLog::create($documentId, $userId, 'open', $duration);
    }

    public static function getDocumentStats(int $documentId): array {
        return AccessLog::statsByDocument($documentId);
    }
}
