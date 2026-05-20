<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with('sentBy')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $users = User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))->get();

        $announcement = Announcement::create([
            'title'            => $data['title'],
            'message'          => $data['message'],
            'sent_by'          => $request->user()->id,
            'recipient_type'   => 'all',
            'recipient_role'   => null,
            'recipients_count' => $users->count(),
        ]);

        Notification::send($users, new AnnouncementNotification($announcement));

        return redirect()->route('admin.announcements.index')
            ->with('status', 'announcement-sent');
    }
}
