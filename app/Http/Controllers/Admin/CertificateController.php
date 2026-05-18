<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Support\CacheBuster;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search      = trim((string) $request->query('search', ''));
        $status      = $request->query('status', 'all');
        $type        = $request->query('type', 'all');
        $verifyStatus = $request->query('verify_status', 'all');
        $window      = (int) $request->query('window', 0);
        $userId      = (int) $request->query('user_id', 0);

        $certificates = Certificate::with('user:id,name')
            ->select([
                'id', 'user_id', 'certificate_name', 'certificate_type',
                'qualification_title', 'certificate_number', 'expiration_date',
                'status', 'verification_status', 'verified_at',
            ])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('certificate_name', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%");
            }))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($type !== 'all', fn ($q) => $q->where('certificate_type', $type))
            ->when($verifyStatus !== 'all', fn ($q) => $q->where('verification_status', $verifyStatus))
            ->when($window > 0, fn ($q) => $q
                ->whereNotNull('expiration_date')
                ->whereBetween('expiration_date', [
                    now()->toDateString(),
                    now()->addDays($window)->toDateString(),
                ])
            )
            ->when($userId > 0, fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => Certificate::count(),
            'pending'  => Certificate::where('verification_status', 'pending')->count(),
            'expiring' => Certificate::where('status', 'expiring')->count(),
        ];

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'search'       => $search,
            'status'       => $status,
            'type'         => $type,
            'verifyStatus' => $verifyStatus,
            'window'       => $window,
            'userId'       => $userId,
            'typeLabels'   => Certificate::TYPE_LABELS,
            'stats'        => $stats,
        ]);
    }

    public function show(Certificate $certificate): View
    {
        $certificate->load(['user.profile', 'documents', 'verifier']);
        return view('admin.certificates.show', compact('certificate'));
    }

    public function verify(Request $request, Certificate $certificate)
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'in:verify,reject,reset'],
        ]);

        $newStatus = match ($data['action']) {
            'verify' => 'verified',
            'reject' => 'rejected',
            'reset'  => 'pending',
        };

        $certificate->update([
            'verification_status' => $newStatus,
            'verified_by'         => $newStatus === 'pending' ? null : $request->user()->id,
            'verified_at'         => $newStatus === 'pending' ? null : now(),
        ]);

        CacheBuster::bumpUser($certificate->user_id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'cert-updated');
    }
}
