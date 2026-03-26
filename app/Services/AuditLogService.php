<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function log(
        string $eventType,
        ?int $userId = null,
        ?string $sessionId = null,
        ?string $type = null,
        ?int $id = null,
        ?array $old = null,
        ?array $new = null,
        ?array $meta = null
    ): AuditLog {
        $payload = [
            'event_type' => $eventType,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'auditable_type' => $type,
            'auditable_id' => $id,
            'old_values' => $old,
            'new_values' => $new,
            'meta' => $meta,
            'occurred_at' => now()->toIso8601String(),
        ];

        $previousHash = AuditLog::latest('id')->value('current_hash');

        $currentHash = hash('sha256', json_encode($payload) . '|' . ($previousHash ?? ''));

        return AuditLog::create([
            'event_type' => $eventType,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'auditable_type' => $type,
            'auditable_id' => $id,
            'old_values' => $old,
            'new_values' => $new,
            'meta' => $meta,
            'occurred_at' => now(),
            'previous_hash' => $previousHash,
            'current_hash' => $currentHash,
        ]);
    }
}