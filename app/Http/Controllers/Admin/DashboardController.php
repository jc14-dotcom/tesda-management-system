<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = Cache::remember('admin:dashboard:metrics', now()->addMinutes(2), function () {
            $usersCount = User::count();
            $activeUsersCount = User::whereHas('profile', fn ($q) => $q->where('status', 'active'))->count();
            $certificatesCount = Certificate::count();
            $documentsCount = Document::count();
            $expiringSoonCount = Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '>=', now()->toDateString())
                ->where('expiration_date', '<=', now()->addDays(30)->toDateString())
                ->count();
            $expiredCount = Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '<', now()->toDateString())
                ->count();

            $expiringCertificates = Certificate::with('user:id,name')
                ->select(['id', 'user_id', 'certificate_name', 'certificate_type', 'expiration_date', 'status'])
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', now()->toDateString())
                ->where('expiration_date', '<=', now()->addDays(60)->toDateString())
                ->orderBy('expiration_date')
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'user' => $c->user->name ?? '—',
                    'certificate' => $c->certificate_name,
                    'expires' => $c->expiration_date->format('Y-m-d'),
                    'status' => ucfirst($c->status),
                    'tone' => $c->status === 'expired' ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning',
                ])->all();

            $recentUsers = User::with(['roles:id,name', 'profile:user_id,status'])
                ->select(['id', 'name', 'email', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn($u) => [
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => ucfirst($u->roles->first()?->name ?? 'user'),
                    'joined' => $u->created_at->format('M d, Y'),
                    'status' => ucfirst($u->profile?->status ?? 'pending'),
                ])->all();

            $recentUploads = Document::with('user:id,name')
                ->select(['id', 'user_id', 'document_name', 'original_name', 'type', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn($d) => [
                    'file' => $d->document_name ?? $d->original_name,
                    'user' => $d->user->name ?? '—',
                    'type' => ucfirst($d->type ?? 'document'),
                    'time' => $d->created_at->diffForHumans(),
                ])->all();

            return [
                'usersCount'       => $usersCount,
                'activeUsersCount' => $activeUsersCount,
                'certificatesCount' => $certificatesCount,
                'documentsCount' => $documentsCount,
                'expiringSoonCount' => $expiringSoonCount,
                'expiredCount' => $expiredCount,
                'expiringCertificates' => $expiringCertificates,
                'recentUsers' => $recentUsers,
                'recentUploads' => $recentUploads,
            ];
        });

        $recentActivity = Activity::with('causer:id,name')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($a) => [
                'title' => $a->description,
                'meta'  => $a->causer?->name ?? 'System',
                'time'  => $a->created_at->diffForHumans(),
            ])->all();

        return view('admin.dashboard', [
            'usersCount'           => $metrics['usersCount'],
            'certificatesCount'    => $metrics['certificatesCount'],
            'documentsCount'       => $metrics['documentsCount'],
            'expiringSoonCount'    => $metrics['expiringSoonCount'],
            'expiredCount'         => $metrics['expiredCount'],
            'expiringCertificates' => $metrics['expiringCertificates'],
            'recentActivity'       => $recentActivity,
            'recentUsers'          => $metrics['recentUsers'],
            'recentUploads'        => $metrics['recentUploads'],
            'statCards'            => [
                [
                    'label' => 'Total Users',
                    'value' => $metrics['usersCount'],
                    'note'  => 'Registered accounts',
                    'tone'  => 'bg-primary-soft text-primary',
                    'icon'  => 'M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                ],
                [
                    'label' => 'Active Users',
                    'value' => $metrics['activeUsersCount'],
                    'note'  => 'Active accounts',
                    'tone'  => 'bg-success-soft text-success',
                    'icon'  => 'M9 12l2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ],
                [
                    'label' => 'Certificates Tracked',
                    'value' => $metrics['certificatesCount'],
                    'note'  => 'All certificates',
                    'tone'  => 'bg-primary-soft text-primary',
                    'icon'  => 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm5 4h6m-6 4h6',
                ],
                [
                    'label' => 'Documents Uploaded',
                    'value' => $metrics['documentsCount'],
                    'note'  => 'All documents',
                    'tone'  => 'bg-info-soft text-info',
                    'icon'  => 'M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6',
                ],
                [
                    'label' => 'Expiring Soon',
                    'value' => $metrics['expiringSoonCount'],
                    'note'  => 'Next 30 days',
                    'tone'  => 'bg-warning-soft text-warning',
                    'icon'  => 'M12 8v4l3 3m7-3A10 10 0 1 1 2 12a10 10 0 0 1 20 0Z',
                ],
                [
                    'label' => 'Expired',
                    'value' => $metrics['expiredCount'],
                    'note'  => 'Needs follow-up',
                    'tone'  => 'bg-danger-soft text-danger',
                    'icon'  => 'M12 9v4m0 4h.01m8.938-2A10 10 0 1 1 3.062 8a10 10 0 0 1 17.876 7Z',
                ],
            ],
        ]);
    }

    public function live(): JsonResponse
    {
        return response()->json([
            'expiring-soon' => Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '>=', now()->toDateString())
                ->where('expiration_date', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'expired' => Certificate::whereNotNull('expiration_date')
                ->where('expiration_date', '<', now()->toDateString())
                ->count(),
            'recentActivity' => Activity::with('causer:id,name')
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn($a) => [
                    'title' => $a->description,
                    'meta'  => $a->causer?->name ?? 'System',
                    'time'  => $a->created_at->diffForHumans(),
                ])->all(),
        ]);
    }
}
