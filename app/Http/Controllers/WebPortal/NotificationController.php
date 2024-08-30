<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    public function all(): JsonResponse
    {
        $notifications = Notification::latest()->with('sender')->get();

        return response()->json($notifications);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        $notification->read();

        return response()->json([
            'status' => 'success',
        ]);
    }
}
