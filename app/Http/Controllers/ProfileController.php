<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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

        return view('user.profile.dashboard', [
            'user' => $user,
            'profile' => $user->profile,
            'certificatesCount' => $counts['certificatesCount'],
            'documentsCount' => $counts['documentsCount'],
        ]);
    }

    /**
     * Display the user's profile information form.
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('user.profile.info', [
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

        $cacheKey = "user:{$user->id}:certificates:v{$cacheVersion}:status={$certStatus}:window={$certWindow}:page={$page}";

        $cached = Cache::remember($cacheKey, $tabCacheTtl, function () use ($user, $certStatus, $certWindow, $page, $perPage) {
            $baseQuery = $user->certificates()->select(['id']);

            if ($certStatus !== 'all') {
                $baseQuery->where('status', $certStatus);
            }

            if ($certWindow > 0) {
                $baseQuery
                    ->whereNotNull('expiration_date')
                    ->whereBetween('expiration_date', [
                        now()->toDateString(),
                        now()->addDays($certWindow)->toDateString(),
                    ]);
            }

            $ids = (clone $baseQuery)
                ->orderByDesc('expiration_date')
                ->forPage($page, $perPage)
                ->pluck('id')
                ->all();

            return [
                'ids' => $ids,
                'total' => (clone $baseQuery)->count(),
            ];
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
                ->sortBy(function ($certificate) use ($idPositions) {
                    return $idPositions[$certificate->id] ?? PHP_INT_MAX;
                })
                ->values();
        }

        $certificates = new LengthAwarePaginator(
            $certificatesItems,
            $cached['total'] ?? 0,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $certificates->withQueryString();

        if ($request->boolean('certificates_partial')) {
            return response()->json([
                'html' => view('profile.partials.certificate-rows', [
                    'certificates' => $certificates,
                ])->render(),
                'nextUrl' => $certificates->nextPageUrl(),
            ]);
        }

        return view('user.profile.certificates', [
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

        $cacheKey = "user:{$user->id}:documents:v{$cacheVersion}:type={$docType}:page={$page}";

        $cached = Cache::remember($cacheKey, $tabCacheTtl, function () use ($user, $docType, $page, $perPage) {
            $baseQuery = $user->documents()->select(['id']);

            if ($docType !== 'all') {
                $baseQuery->where('type', $docType);
            }

            $ids = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->forPage($page, $perPage)
                ->pluck('id')
                ->all();

            return [
                'ids' => $ids,
                'total' => (clone $baseQuery)->count(),
            ];
        });

        $documentsItems = collect();

        if (!empty($cached['ids'])) {
            $idPositions = array_flip($cached['ids']);

            $documentsItems = $user->documents()
                ->select(['id', 'user_id', 'type', 'document_name', 'certificate_no', 'issued_on', 'valid_until', 'original_name', 'created_at'])
                ->whereIn('id', $cached['ids'])
                ->get()
                ->sortBy(function ($document) use ($idPositions) {
                    return $idPositions[$document->id] ?? PHP_INT_MAX;
                })
                ->values();
        }

        $documents = new LengthAwarePaginator(
            $documentsItems,
            $cached['total'] ?? 0,
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
                'html' => view('profile.partials.document-cards', [
                    'documents' => $documents,
                ])->render(),
                'nextUrl' => $documents->nextPageUrl(),
            ]);
        }

        return view('user.profile.documents', [
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

        return view('user.profile.notifications', [
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

        return view('user.profile.settings', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
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

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

        $user->delete();

        CacheBuster::bumpAdminUsers();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
