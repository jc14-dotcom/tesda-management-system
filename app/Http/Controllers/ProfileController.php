<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Certificate;
use App\Models\User;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user()->load('profile');
        $cacheVersion = CacheBuster::userVersion($user->id);

        $counts = Cache::remember(CacheBuster::userDashboardKey($user->id), now()->addMinutes(30), function () use ($user) {
            $user->loadCount(['certificates', 'documents']);

            return [
                'certificatesCount' => $user->certificates_count,
                'documentsCount' => $user->documents_count,
            ];
        });

        $sectionsKey = "user:{$user->id}:dashboard:sections:v{$cacheVersion}";
        $sections = Cache::remember($sectionsKey, now()->addMinutes(30), function () use ($user) {
            $now = now()->toDateString();

            // One aggregate query replaces three separate COUNT queries.
            $expiryRow = $user->certificates()
                ->selectRaw(
                    'SUM(expiration_date <= ?) AS expiring30,' .
                    'SUM(expiration_date <= ?) AS expiring60,' .
                    'SUM(expiration_date <= ?) AS expiring90',
                    [
                        now()->addDays(30)->toDateString(),
                        now()->addDays(60)->toDateString(),
                        now()->addDays(90)->toDateString(),
                    ]
                )
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->first();

            $expiring30 = (int) ($expiryRow->expiring30 ?? 0);
            $expiring60 = (int) ($expiryRow->expiring60 ?? 0);
            $expiring90 = (int) ($expiryRow->expiring90 ?? 0);

            $expiringList = $user->certificates()
                ->select(['id', 'certificate_name', 'certificate_type', 'expiration_date', 'status'])
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->where('expiration_date', '<=', now()->addDays(90)->toDateString())
                ->orderBy('expiration_date')
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'name'         => $c->certificate_name,
                    'type'         => $c->certificate_type_label,
                    'date'         => $c->expiration_date->format('Y-m-d'),
                    'status'       => ucfirst($c->status),
                    'status_class' => match($c->status) {
                        'expiring' => 'bg-warning-soft text-warning',
                        'expired'  => 'bg-danger-soft text-danger',
                        default    => 'bg-success-soft text-success',
                    },
                ])->all();

            $recentUploads = $user->documents()
                ->select(['id', 'document_name', 'original_name', 'type', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(3)
                ->get()
                ->map(fn($d) => [
                    'file' => $d->document_name ?? $d->original_name,
                    'type' => ucfirst($d->type ?? 'document'),
                    'time' => $d->created_at->diffForHumans(),
                ])->all();

            // GROUP BY in SQL — no PHP-side allocation of all rows.
            $typeCounts = $user->certificates()
                ->selectRaw('certificate_type, COUNT(*) as type_count')
                ->groupBy('certificate_type')
                ->pluck('type_count', 'certificate_type');
            $total = $typeCounts->sum();
            $programs = $typeCounts->sortDesc()->take(4)->map(function ($count, $type) use ($total) {
                return [
                    'label' => Certificate::TYPE_LABELS[$type] ?? ucfirst((string) $type),
                    'value' => $count,
                    'percent' => $total > 0 ? (int) round($count / $total * 100) : 0,
                ];
            })->values()->all();

            return [
                'expiring30' => $expiring30,
                'expiring60' => $expiring60,
                'expiring90' => $expiring90,
                'expiringList' => $expiringList,
                'recentUploads' => $recentUploads,
                'programs' => $programs,
            ];
        });

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        $statCards = [
            [
                'label' => 'Total Certificates',
                'value' => $counts['certificatesCount'],
                'note' => 'All certificates',
                'tone' => 'bg-primary-soft text-primary',
                'icon' => 'M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6',
            ],
            [
                'label' => 'Expiring Soon',
                'value' => $sections['expiring30'],
                'note' => 'Within 30 days',
                'tone' => 'bg-accent-soft text-accent-hover',
                'icon' => 'M12 8v4l3 3m7-3A10 10 0 1 1 2 12a10 10 0 0 1 20 0Z',
            ],
            [
                'label' => 'Documents Uploaded',
                'value' => $counts['documentsCount'],
                'note' => 'All documents',
                'tone' => 'bg-primary-soft text-primary',
                'icon' => 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm5 4h6m-6 4h6',
            ],
            [
                'label' => 'Notifications',
                'value' => $unreadNotificationsCount,
                'note' => $unreadNotificationsCount > 0 ? "{$unreadNotificationsCount} unread" : 'No new alerts',
                'tone' => $unreadNotificationsCount > 0 ? 'bg-accent-soft text-accent-hover' : 'bg-primary-soft text-primary',
                'icon' => 'M6 8a6 6 0 1 1 12 0c0 7 3 7 3 7H3s3 0 3-7Zm3 11a3 3 0 0 0 6 0',
            ],
        ];

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $user->profile,
            'statCards' => $statCards,
            'certificatesCount' => $counts['certificatesCount'],
            'documentsCount' => $counts['documentsCount'],
            'expiringSoon30' => $sections['expiring30'],
            'expiring60' => $sections['expiring60'],
            'expiring90' => $sections['expiring90'],
            'expiring' => $sections['expiringList'],
            'uploads' => $sections['recentUploads'],
            'programs' => $sections['programs'],
        ]);
    }

    /**
     * Live JSON endpoint for the user dashboard 60-second poller.
     */
    public function dashboardLive(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $now  = now()->toDateString();

        $row = $user->certificates()
            ->selectRaw(
                'SUM(expiration_date <= ?) AS e30,' .
                'SUM(expiration_date <= ?) AS e60,' .
                'SUM(expiration_date <= ?) AS e90',
                [
                    now()->addDays(30)->toDateString(),
                    now()->addDays(60)->toDateString(),
                    now()->addDays(90)->toDateString(),
                ]
            )
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '>=', $now)
            ->first();

        $e30    = (int) ($row->e30 ?? 0);
        $e60    = (int) ($row->e60 ?? 0);
        $e90    = (int) ($row->e90 ?? 0);
        $unread = $user->unreadNotifications()->count();

        return response()->json([
            'expiring-soon'      => $e30,
            'notifications'      => $unread,
            'notifications-note' => $unread > 0 ? "{$unread} unread" : 'No new alerts',
            'b1'                 => $e30,
            'b2'                 => max(0, $e60 - $e30),
            'b3'                 => max(0, $e90 - $e60),
        ]);
    }

    /**
     * Display the user's profile information form.
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('user.profile.index', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Display the user's certificates.
     */
    public function certificates(Request $request)
    {
        $user = $request->user()->load('profile');
        $certStatus = $request->query('cert_status', 'all');
        $certWindow = (int) $request->query('cert_window', 0);

        $certificates = $user->certificates()
            ->select(['id', 'user_id', 'certificate_name', 'certificate_type', 'qualification_title', 'certificate_number', 'issued_by', 'issue_date', 'expiration_date', 'status', 'remarks'])
            ->with(['documents' => fn($q) => $q->select(['id', 'certificate_id', 'document_name', 'original_name'])->latest()])
            ->when($certStatus !== 'all', fn($q) => $q->where('status', $certStatus))
            ->when($certWindow > 0, fn($q) => $q
                ->whereNotNull('expiration_date')
                ->whereBetween('expiration_date', [
                    now()->toDateString(),
                    now()->addDays($certWindow)->toDateString(),
                ])
            )
            ->orderByDesc('expiration_date')
            ->paginate(10)
            ->withQueryString();

        if ($request->boolean('certificates_partial')) {
            return response()->json([
                'items' => $certificates->map(function ($cert) {
                    $firstDoc = $cert->documents->first();
                    return [
                        'id'                 => $cert->id,
                        'name'              => $cert->certificate_name,
                        'type'              => $cert->certificate_type_label,
                        'qualification'     => $cert->qualification_title ?? '',
                        'certificateNumber' => $cert->certificate_number ?? '',
                        'issuedBy'          => $cert->issued_by ?? '—',
                        'issueDate'         => $cert->issue_date ? $cert->issue_date->format('M d, Y') : '—',
                        'expirationDate'    => $cert->expiration_date ? $cert->expiration_date->format('M d, Y') : '—',
                        'status'            => $cert->status,
                        'statusLabel'       => ucfirst($cert->status),
                        'remarks'           => $cert->remarks ?? '',
                        'hasFile'           => (bool) $firstDoc,
                        'previewUrl'        => $firstDoc ? route('documents.preview', $firstDoc) : null,
                        'downloadUrl'       => $firstDoc ? route('documents.download', $firstDoc) : null,
                        'deleteUrl'         => route('certificates.destroy', $cert),
                        'updateUrl'         => route('certificates.update', $cert),
                        'showUrl'           => route('account.certificates.show', $cert),
                        'certificateTypeRaw' => $cert->certificate_type,
                        'issueDateRaw'      => $cert->issue_date ? $cert->issue_date->format('Y-m-d') : '',
                        'expirationDateRaw' => $cert->expiration_date ? $cert->expiration_date->format('Y-m-d') : '',
                    ];
                })->values(),
                'nextUrl' => $certificates->nextPageUrl(),
            ]);
        }

        return view('user.certificates.index', [
            'user'         => $user,
            'profile'      => $user->profile,
            'certificates' => $certificates,
            'certStatus'   => $certStatus,
            'certWindow'   => $certWindow,
        ]);
    }

    /**
     * Display the user's documents.
     */
    public function documents(Request $request)
    {
        $user = $request->user()->load('profile');
        $docType = $request->query('doc_type', 'all');
        $cacheVersion = CacheBuster::userVersion($user->id);

        $documents = $user->documents()
            ->select(['id', 'user_id', 'type', 'document_name', 'certificate_no', 'issued_on', 'valid_until', 'original_name', 'created_at'])
            ->when($docType !== 'all', fn($q) => $q->where('type', $docType))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $selectKey = "user:{$user->id}:certificates-select:v{$cacheVersion}";
        $certificatesSelect = Cache::remember($selectKey, now()->addHours(2), function () use ($user) {
            return $user->certificates()
                ->select(['id', 'certificate_name', 'certificate_type', 'qualification_title'])
                ->orderBy('certificate_name')
                ->get()
                ->map(function ($certificate) {
                    $label = $certificate->certificate_name;
                    if ($certificate->qualification_title) {
                        $label .= ' - ' . $certificate->qualification_title;
                    }
                    $label .= ' (' . $certificate->certificate_type_label . ')';
                    return ['id' => $certificate->id, 'label' => $label];
                })
                ->values()
                ->all();
        });

        if ($request->boolean('documents_partial')) {
            return response()->json([
                'items' => $documents->map(function ($doc) {
                    return [
                        'id'          => $doc->id,
                        'name'        => $doc->document_name ?? $doc->original_name,
                        'originalName'=> $doc->original_name,
                        'type'        => strtoupper($doc->type),
                        'previewUrl'  => route('documents.preview', $doc),
                        'downloadUrl' => route('documents.download', $doc),
                        'viewUrl'     => route('documents.view', $doc),
                        'deleteUrl'   => route('documents.destroy', $doc),
                    ];
                })->values(),
                'nextUrl' => $documents->nextPageUrl(),
            ]);
        }

        return view('user.documents.index', [
            'user'              => $user,
            'profile'           => $user->profile,
            'documents'         => $documents,
            'certificatesSelect'=> $certificatesSelect,
            'docType'           => $docType,
        ]);
    }

    /**
     * Display the user's notifications.
     */
    public function notifications(Request $request)
    {
        $user = $request->user()->load('profile');
        $notifications = $user->notifications()->paginate(15);

        return view('user.notifications.index', [
            'user'          => $user,
            'profile'       => $user->profile,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function notificationsMarkAllRead(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mark a single notification as read.
     */
    public function notificationMarkRead(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $notif = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notif->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function notificationDelete(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $notif = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notif->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Display the user's settings.
     */
    public function settings(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('user.settings.index', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Serve the authenticated user's profile photo.
     */
    public function photo(Request $request): BinaryFileResponse
    {
        $user = $request->user()->load('profile');
        $photoPath = $user->profile?->profile_photo_path;

        if (! $photoPath) {
            abort(404);
        }

        $localDisk = Storage::disk('local');
        if ($localDisk->exists($photoPath)) {
            return response()->file($localDisk->path($photoPath));
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($photoPath)) {
            return response()->file($publicDisk->path($photoPath));
        }

        abort(404);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $profilePhoto = $request->file('profile_photo');

        unset($validated['profile_photo']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($profilePhoto) {
            $profile = $user->profile;

            $this->deleteProfilePhoto($profile?->profile_photo_path);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_photo_path' => $profilePhoto->store("profiles/{$user->id}", 'local'),
                ]
            );
        }

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        if ($request->expectsJson()) {
            $user->refresh();
            return response()->json([
                'message' => 'Profile updated',
                'profile_photo_url' => $user->profile?->profile_photo_url ?? null,
            ]);
        }

        return Redirect::route('account.profile')->with('status', 'profile-updated');
    }

    /**
     * Remove the user's profile photo.
     */
    public function removePhoto(Request $request)
    {
        $user = $request->user()->load('profile');
        $profile = $user->profile;

        $this->deleteProfilePhoto($profile?->profile_photo_path);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['profile_photo_path' => null]
        );

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Profile photo removed']);
        }

        return Redirect::route('account.profile')->with('status', 'profile-photo-removed');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user()->load(['profile', 'documents:id,user_id,path']);

        $this->deleteProfilePhoto($user->profile?->profile_photo_path);

        foreach ($user->documents as $document) {
            if ($document->path && Storage::disk('local')->exists($document->path)) {
                Storage::disk('local')->delete($document->path);
            }
        }

        Storage::disk('local')->deleteDirectory("documents/{$user->id}");
        Storage::disk('local')->deleteDirectory("profiles/{$user->id}");
        Storage::disk('public')->deleteDirectory("profiles/{$user->id}");

        Auth::logout();

        User::destroy($user->id);

        CacheBuster::bumpAdminUsers();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function deleteProfilePhoto(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            $storage = Storage::disk($disk);
            if ($storage->exists($path)) {
                $storage->delete($path);
            }
        }
    }

    /**
     * Serve a specific user's profile photo (admin can view any user, users can only view their own).
     */
    public function photoForUser(Request $request, User $user): BinaryFileResponse
    {
        if (! $request->user()->hasRole('admin') && $request->user()->id !== $user->id) {
            abort(403);
        }

        $photoPath = $user->profile?->profile_photo_path;

        if (! $photoPath) {
            abort(404);
        }

        $localDisk = Storage::disk('local');
        if ($localDisk->exists($photoPath)) {
            return response()->file($localDisk->path($photoPath));
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($photoPath)) {
            return response()->file($publicDisk->path($photoPath));
        }

        abort(404);
    }
}
