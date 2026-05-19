<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NotificationPollController extends Controller
{
    /**
     * Lightweight poll: returns unread count + latest notification ID and summary.
     * Called every 30 seconds by the front-end to detect new notifications.
     */
    public function poll(Request $request): JsonResponse
    {
        $user   = $request->user();
        $latest = $user->notifications()->latest()->first(['id', 'data', 'created_at']);

        $latestTitle   = null;
        $latestMessage = null;

        if ($latest) {
            $data        = $latest->data ?? [];
            $latestTitle = $data['certificate_name'] ?? 'New notification';
            $days        = $data['days_until_expiry'] ?? null;
            $expDate     = $data['expiration_date']   ?? null;

            if ($days !== null) {
                $label         = $days === 1 ? 'day' : 'days';
                $latestMessage = "Expiring in {$days} {$label}"
                    . ($expDate ? ' — ' . \Carbon\Carbon::parse($expDate)->format('M d, Y') : '');
            } else {
                $latestMessage = $data['message'] ?? null;
            }
        }

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'latest_id'     => $latest?->id,
            'latest_title'  => $latestTitle,
            'latest_message'=> $latestMessage,
        ]);
    }

    /**
     * Returns refreshed HTML for the notification panel list + updated unread count.
     * Called only when the poll detects a change (new notification arrived).
     */
    public function panel(Request $request): JsonResponse
    {
        $user                = $request->user();
        $recentNotifications = $user->notifications()->latest()->take(8)->get();
        $unreadCount         = $user->unreadNotifications()->count();

        $notificationsIndexUrl = $user->hasRole('admin') && Route::has('admin.notifications.index')
            ? route('admin.notifications.index')
            : route('account.notifications');

        return response()->json([
            'unread_count' => $unreadCount,
            'html'         => view('layouts.partials.notif-list-items', compact('recentNotifications', 'notificationsIndexUrl'))->render(),
        ]);
    }
}
