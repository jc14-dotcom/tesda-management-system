<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = Cache::remember('admin:dashboard:metrics', now()->addMinutes(2), function () {
            $usersCount = User::count();
            $certificatesCount = Certificate::count();
            $expiringSoonCount = Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '>=', now()->toDateString())
                ->where('expiration_date', '<=', now()->addDays(30)->toDateString())
                ->count();
            $expiredCount = Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '<', now()->toDateString())
                ->count();

            return [
                'usersCount' => $usersCount,
                'certificatesCount' => $certificatesCount,
                'expiringSoonCount' => $expiringSoonCount,
                'expiredCount' => $expiredCount,
            ];
        });

        return view('admin.dashboard', [
            'usersCount' => $metrics['usersCount'],
            'certificatesCount' => $metrics['certificatesCount'],
            'expiringSoonCount' => $metrics['expiringSoonCount'],
            'expiredCount' => $metrics['expiredCount'],
        ]);
    }
}
