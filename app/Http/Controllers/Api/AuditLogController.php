<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();

        if (!$user->hasRole('super-admin')) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $logs = AuditLog::with('user:id,name,email')
            ->latest('occurred_at')
            ->get();

        return response()->json($logs);
    }
}