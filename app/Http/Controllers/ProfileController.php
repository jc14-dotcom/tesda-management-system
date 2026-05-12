<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Certificate;
use App\Models\User;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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

            $expiring30 = $user->certificates()->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->where('expiration_date', '<=', now()->addDays(30)->toDateString())
                ->count();

            $expiring60 = $user->certificates()->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->where('expiration_date', '<=', now()->addDays(60)->toDateString())
                ->count();

            $expiring90 = $user->certificates()->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->where('expiration_date', '<=', now()->addDays(90)->toDateString())
                ->count();

            $expiringList = $user->certificates()
                ->select(['id', 'certificate_name', 'certificate_type', 'expiration_date', 'status'])
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now)
                ->orderBy('expiration_date')
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'name' => $c->certificate_name,
                    'type' => $c->certificate_type_label,
                    'date' => $c->expiration_date->format('Y-m-d'),
                    'status' => ucfirst($c->status),
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

            $typeCounts = $user->certificates()
                ->select(['certificate_type'])
                ->get()
                ->countBy('certificate_type');
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

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $user->profile,
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
        $page = (int) $request->query('page', 1);
        $perPage = 10;
        $cacheVersion = CacheBuster::userVersion($user->id);
        $tabCacheTtl = now()->addMinutes(30);

        $cacheKey = "user:{$user->id}:certificates-ids:v{$cacheVersion}:status={$certStatus}:window={$certWindow}:page={$page}";

        $cached = Cache::remember($cacheKey, $tabCacheTtl, function () use ($user, $certStatus, $certWindow, $page, $perPage) {
            $makeQuery = function () use ($user, $certStatus, $certWindow) {
                $q = $user->certificates();
                if ($certStatus !== 'all') {
                    $q->where('status', $certStatus);
                }
                if ($certWindow > 0) {
                    $q->whereNotNull('expiration_date')
                      ->whereBetween('expiration_date', [
                          now()->toDateString(),
                          now()->addDays($certWindow)->toDateString(),
                      ]);
                }
                return $q;
            };

            $total = $makeQuery()->count();
            $ids   = $makeQuery()
                ->orderByDesc('expiration_date')
                ->forPage($page, $perPage)
                ->pluck('id')
                ->all();

            return ['ids' => $ids, 'total' => $total];
        });

        $certificatesItems = collect();
        if (!empty($cached['ids'])) {
            $idPositions = array_flip($cached['ids']);
            $certificatesItems = $user->certificates()
                ->select(['id', 'user_id', 'certificate_name', 'certificate_type', 'qualification_title', 'expiration_date', 'status'])
                ->with(['documents' => function ($query) {
                    $query->select(['id', 'certificate_id', 'document_name', 'original_name', 'created_at'])
                          ->latest();
                }])
                ->whereIn('id', $cached['ids'])
                ->get()
                ->sortBy(fn($c) => $idPositions[$c->id] ?? PHP_INT_MAX)
                ->values();
        }

        $certificates = new LengthAwarePaginator(
            $certificatesItems,
            $cached['total'],
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $certificates->withQueryString();

        if ($request->boolean('certificates_partial')) {
            return response()->json([
                'html' => view('user.certificates.partials.certificate-rows', [
                    'certificates' => $certificates,
                ])->render(),
                'nextUrl' => $certificates->nextPageUrl(),
            ]);
        }

        return view('user.certificates.index', [
            'user' => $user,
            'profile' => $user->profile,
            'certificates' => $certificates,
            'certStatus' => $certStatus,
            'certWindow' => $certWindow,
        ]);
    }

    /**
     * Display the user's documents.
     */
    public function documents(Request $request)
    {
        $user = $request->user()->load('profile');
        $docType = $request->query('doc_type', 'all');
        $page = (int) $request->query('page', 1);
        $perPage = 10;
        $cacheVersion = CacheBuster::userVersion($user->id);
        $tabCacheTtl = now()->addMinutes(30);
        $selectCacheTtl = now()->addHours(2);

        $cacheKey = "user:{$user->id}:documents-ids:v{$cacheVersion}:type={$docType}:page={$page}";

        $cached = Cache::remember($cacheKey, $tabCacheTtl, function () use ($user, $docType, $page, $perPage) {
            $makeQuery = function () use ($user, $docType) {
                $q = $user->documents();
                if ($docType !== 'all') {
                    $q->where('type', $docType);
                }
                return $q;
            };

            $total = $makeQuery()->count();
            $ids   = $makeQuery()
                ->orderByDesc('created_at')
                ->forPage($page, $perPage)
                ->pluck('id')
                ->all();

            return ['ids' => $ids, 'total' => $total];
        });

        $documentsItems = collect();
        if (!empty($cached['ids'])) {
            $idPositions = array_flip($cached['ids']);
            $documentsItems = $user->documents()
                ->select(['id', 'user_id', 'type', 'document_name', 'certificate_no', 'issued_on', 'valid_until', 'original_name', 'created_at'])
                ->whereIn('id', $cached['ids'])
                ->get()
                ->sortBy(fn($d) => $idPositions[$d->id] ?? PHP_INT_MAX)
                ->values();
        }

        $documents = new LengthAwarePaginator(
            $documentsItems,
            $cached['total'],
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $documents->withQueryString();

        $selectKey = "user:{$user->id}:certificates-select:v{$cacheVersion}";
        $certificatesSelect = Cache::remember($selectKey, $selectCacheTtl, function () use ($user) {
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

                    return [
                        'id' => $certificate->id,
                        'label' => $label,
                    ];
                })
                ->values()
                ->all();
        });

        if ($request->boolean('documents_partial')) {
            return response()->json([
                'html' => view('user.documents.partials.document-cards', [
                    'documents' => $documents,
                ])->render(),
                'nextUrl' => $documents->nextPageUrl(),
            ]);
        }

        return view('user.documents.index', [
            'user' => $user,
            'profile' => $user->profile,
            'documents' => $documents,
            'certificatesSelect' => $certificatesSelect,
            'docType' => $docType,
        ]);
    }

    /**
     * Display the user's notifications.
     */
    public function notifications(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('user.notifications.index', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
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

            if ($profile?->profile_photo_path && Storage::disk('public')->exists($profile->profile_photo_path)) {
                Storage::disk('public')->delete($profile->profile_photo_path);
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_photo_path' => $profilePhoto->store("profiles/{$user->id}", 'public'),
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

        if ($profile?->profile_photo_path && Storage::disk('public')->exists($profile->profile_photo_path)) {
            Storage::disk('public')->delete($profile->profile_photo_path);
        }

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

        $user = $request->user();

        Auth::logout();

        User::destroy($user->id);

        CacheBuster::bumpAdminUsers();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
