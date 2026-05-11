<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('profile')
            ->withCount('certificates')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['profile', 'certificates.documents', 'documents']);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }
}
