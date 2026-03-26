<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIpAddressRequest;
use App\Http\Requests\UpdateIpAddressRequest;
use App\Models\IpAddress;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class IpAddressController extends Controller
{
    public function __construct(private AuditLogService $auditLogService)
    {
    }

    public function index()
    {
        $ips = IpAddress::with('creator:id,name,email')
            ->latest()
            ->get();

        return response()->json($ips);
    }

    public function store(StoreIpAddressRequest $request)
    {
        $user = auth('api')->user();

        $ip = IpAddress::create([
            'ip_address' => $request->ip_address,
            'label' => $request->label,
            'comment' => $request->comment,
            'created_by' => $user->id,
        ]);

        $this->auditLogService->log(
            eventType: 'ip_created',
            userId: $user->id,
            sessionId: session()->getId(),
            type: IpAddress::class,
            id: $ip->id,
            old: null,
            new: $ip->toArray(),
            meta: ['ip_address' => $ip->ip_address]
        );

        return response()->json($ip, 201);
    }

    public function show(IpAddress $ipAddress)
    {
        return response()->json($ipAddress->load('creator:id,name,email'));
    }

    public function update(UpdateIpAddressRequest $request, IpAddress $ipAddress)
    {
        $this->authorize('update', $ipAddress);

        $user = auth('api')->user();
        $oldValues = $ipAddress->toArray();

        $ipAddress->update([
            'label' => $request->label,
            'comment' => $request->comment,
        ]);

        $this->auditLogService->log(
            eventType: 'ip_updated',
            userId: $user->id,
            sessionId: session()->getId(),
            type: IpAddress::class,
            id: $ipAddress->id,
            old: $oldValues,
            new: $ipAddress->fresh()->toArray(),
            meta: ['ip_address' => $ipAddress->ip_address]
        );

        return response()->json($ipAddress->fresh());
    }

    public function destroy(IpAddress $ipAddress)
    {
        $this->authorize('delete', $ipAddress);

        $user = auth('api')->user();
        $oldValues = $ipAddress->toArray();

        $this->auditLogService->log(
            eventType: 'ip_deleted',
            userId: $user->id,
            sessionId: session()->getId(),
            type: IpAddress::class,
            id: $ipAddress->id,
            old: $oldValues,
            new: null,
            meta: ['ip_address' => $ipAddress->ip_address]
        );

        $ipAddress->delete();

        return response()->json([
            'message' => 'IP address deleted successfully'
        ]);
    }
}