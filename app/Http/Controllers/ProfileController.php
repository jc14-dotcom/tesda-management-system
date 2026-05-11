<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $tab = $request->query('tab', 'dashboard');
        $user = $request->user()->load('profile');
        $certStatus = $request->query('cert_status', 'all');
        $certWindow = (int) $request->query('cert_window', 0);
        $docType = $request->query('doc_type', 'all');
        $cacheVersion = CacheBuster::userVersion($user->id);

        $certificates = collect();
        $documents = collect();
        $certificatesSelect = collect();
        $certificatesCount = null;
        $documentsCount = null;

        if ($tab === 'dashboard') {
            $counts = Cache::remember(CacheBuster::userDashboardKey($user->id), now()->addMinutes(5), function () use ($user) {
                $user->loadCount(['certificates', 'documents']);

                return [
                    'certificatesCount' => $user->certificates_count,
                    'documentsCount' => $user->documents_count,
                ];
            });

            $certificatesCount = $counts['certificatesCount'];
            $documentsCount = $counts['documentsCount'];
        }

        if ($tab === 'certificates') {
            $page = (int) $request->query('page', 1);
            $cacheKey = "user:{$user->id}:certificates:v{$cacheVersion}:status={$certStatus}:window={$certWindow}:page={$page}";

            $certificates = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($user, $certStatus, $certWindow, $page) {
                $query = $user->certificates()
                    ->select(['id', 'user_id', 'certificate_name', 'certificate_type', 'qualification_title', 'expiration_date', 'status'])
                    ->with(['documents' => function ($query) {
                        $query->select(['id', 'certificate_id', 'document_name', 'original_name', 'created_at'])
                            ->latest();
                    }]);

                if ($certStatus !== 'all') {
                    $query->where('status', $certStatus);
                }

                if ($certWindow > 0) {
                    $query
                        ->whereNotNull('expiration_date')
                        ->whereBetween('expiration_date', [
                            now()->toDateString(),
                            now()->addDays($certWindow)->toDateString(),
                        ]);
                }

                return $query
                    ->orderByDesc('expiration_date')
                    ->paginate(10, ['*'], 'page', $page);
            });

            $certificates->withQueryString();

            if ($request->boolean('certificates_partial')) {
                return response()->json([
                    'html' => view('profile.partials.certificate-rows', [
                        'certificates' => $certificates,
                    ])->render(),
                    'nextUrl' => $certificates->nextPageUrl(),
                ]);
            }
        }

        if ($tab === 'documents') {
            $page = (int) $request->query('page', 1);
            $cacheKey = "user:{$user->id}:documents:v{$cacheVersion}:type={$docType}:page={$page}";

            $documents = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($user, $docType, $page) {
                $query = $user->documents()
                    ->select(['id', 'user_id', 'type', 'document_name', 'certificate_no', 'issued_on', 'valid_until', 'original_name', 'created_at'])
                    ->latest();

                if ($docType !== 'all') {
                    $query->where('type', $docType);
                }

                return $query
                    ->paginate(10, ['*'], 'page', $page);
            });

            $documents->withQueryString();

            $selectKey = "user:{$user->id}:certificates-select:v{$cacheVersion}";
            $certificatesSelect = Cache::remember($selectKey, now()->addMinutes(10), function () use ($user) {
                return $user->certificates()
                    ->select(['id', 'certificate_name', 'certificate_type', 'qualification_title'])
                    ->orderBy('certificate_name')
                    ->get();
            });

            if ($request->boolean('documents_partial')) {
                return response()->json([
                    'html' => view('profile.partials.document-cards', [
                        'documents' => $documents,
                    ])->render(),
                    'nextUrl' => $documents->nextPageUrl(),
                ]);
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile,
            'certificates' => $certificates,
            'documents' => $documents,
            'certificatesSelect' => $certificatesSelect,
            'certificatesCount' => $certificatesCount,
            'documentsCount' => $documentsCount,
            'certStatus' => $certStatus,
            'certWindow' => $certWindow,
            'docType' => $docType,
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
