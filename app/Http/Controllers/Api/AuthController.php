<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(private AuditLogService $auditLogService)
    {
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = JWTAuth::setToken($token)->toUser();

        $this->auditLogService->log(
            eventType: 'login',
            userId: $user?->id,
            sessionId: session()->getId(),
            meta: [
                'email' => $user?->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth('api')->user();

        return response()->json($user?->load('roles'));
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {
        $user = auth('api')->user();

        $this->auditLogService->log(
            eventType: 'logout',
            userId: $user?->id,
            sessionId: session()->getId(),
            meta: [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    protected function respondWithToken($token)
    {
        $user = JWTAuth::setToken($token)->toUser();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user?->load('roles'),
        ]);
    }
}