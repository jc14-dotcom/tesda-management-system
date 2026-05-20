<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search  = trim((string) $request->query('search', ''));
        $status  = $request->query('status', 'all');
        $type    = $request->query('type', 'all');
        $window  = (int) $request->query('window', 0);
        $userId  = (int) $request->query('user_id', 0);

        $certificates = Certificate::with(['user:id,name', 'user.profile:user_id,profile_photo_path'])
            ->select([
                'id', 'user_id', 'certificate_name', 'certificate_type',
                'qualification_title', 'certificate_number', 'expiration_date',
                'status',
            ])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('certificate_name', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%");
            }))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($type !== 'all', fn ($q) => $q->where('certificate_type', $type))
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
            'expiring' => Certificate::where('status', 'expiring')->count(),
        ];

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'search'       => $search,
            'status'       => $status,
            'type'         => $type,
            'window'       => $window,
            'userId'       => $userId,
            'typeLabels'   => Certificate::TYPE_LABELS,
            'stats'        => $stats,
        ]);
    }

    public function show(Certificate $certificate): View
    {
        $certificate->load(['user.profile', 'documents']);
        return view('admin.certificates.show', compact('certificate'));
    }
}
