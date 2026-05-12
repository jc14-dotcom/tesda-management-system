<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Document;
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

            $recentUsers = User::with('roles:id,name')
                ->select(['id', 'name', 'email', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn($u) => [
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => ucfirst($u->roles->first()?->name ?? 'user'),
                    'joined' => $u->created_at->format('M d, Y'),
                    'status' => 'Active',
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
                'usersCount' => $usersCount,
                'certificatesCount' => $certificatesCount,
                'documentsCount' => $documentsCount,
                'expiringSoonCount' => $expiringSoonCount,
                'expiredCount' => $expiredCount,
                'expiringCertificates' => $expiringCertificates,
                'recentUsers' => $recentUsers,
                'recentUploads' => $recentUploads,
            ];
        });

        return view('admin.dashboard', [
            'usersCount' => $metrics['usersCount'],
            'certificatesCount' => $metrics['certificatesCount'],
            'documentsCount' => $metrics['documentsCount'],
            'expiringSoonCount' => $metrics['expiringSoonCount'],
            'expiredCount' => $metrics['expiredCount'],
            'expiringCertificates' => $metrics['expiringCertificates'],
            'recentActivity' => [],
            'recentUsers' => $metrics['recentUsers'],
            'recentUploads' => $metrics['recentUploads'],
        ]);
    }
}
