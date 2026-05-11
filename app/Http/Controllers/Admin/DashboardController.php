<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usersCount = User::count();
        $certificatesCount = Certificate::count();
        $expiringSoonCount = Certificate::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '>=', now())
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->count();
        $expiredCount = Certificate::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now())
            ->count();

        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'certificatesCount' => $certificatesCount,
            'expiringSoonCount' => $expiringSoonCount,
            'expiredCount' => $expiredCount,
        ]);
    }
}
