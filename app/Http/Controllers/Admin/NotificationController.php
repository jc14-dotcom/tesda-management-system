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

        $notifications = DatabaseNotification::with('notifiable')
            ->where('notifiable_type', \App\Models\User::class)
            ->when($status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($status === 'read',   fn ($q) => $q->whereNotNull('read_at'))
            ->when($search, fn ($q) => $q->whereHasMorph('notifiable', [\App\Models\User::class], fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'search'        => $search,
            'status'        => $status,
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
