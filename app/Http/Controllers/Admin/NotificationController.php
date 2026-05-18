<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', 'all'); // all / unread / read
        $type   = $request->query('type', 'all');   // all / admin / user / <specific type>

        $adminTypes = [
            'certificate_submitted', 'certificate_expiry', 'certificate_expired',
            'document_uploaded', 'user_registered', 'user_status_changed',
            'verification_reminder', 'weekly_digest',
        ];

        $notifications = DatabaseNotification::with('notifiable')
            ->where('notifiable_type', \App\Models\User::class)
            ->when($status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($status === 'read',   fn ($q) => $q->whereNotNull('read_at'))
            ->when($type === 'admin',    fn ($q) => $q->where(function ($q) use ($adminTypes) {
                foreach ($adminTypes as $t) {
                    $q->orWhere('data->type', $t);
                }
            }))
            ->when($type === 'user',     fn ($q) => $q->where(function ($q) use ($adminTypes) {
                $q->whereNull('data->type');
                foreach ($adminTypes as $t) {
                    $q->where('data->type', '!=', $t);
                }
            }))
            ->when($type !== 'all' && $type !== 'admin' && $type !== 'user', fn ($q) => $q->where('data->type', $type))
            ->when($search, fn ($q) => $q->whereHasMorph('notifiable', [\App\Models\User::class], fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => DatabaseNotification::where('notifiable_type', \App\Models\User::class)->count(),
            'unread'   => DatabaseNotification::where('notifiable_type', \App\Models\User::class)->whereNull('read_at')->count(),
            'thisWeek' => DatabaseNotification::where('notifiable_type', \App\Models\User::class)->where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'search'        => $search,
            'status'        => $status,
            'type'          => $type,
            'stats'         => $stats,
        ]);
    }

    public function destroy(string $id)
    {
        DatabaseNotification::findOrFail($id)->delete();

        return back()->with('status', 'notification-deleted');
    }

    public function destroyAll(Request $request)
    {
        DatabaseNotification::where('notifiable_type', \App\Models\User::class)->delete();

        return back()->with('status', 'all-notifications-deleted');
    }
}
