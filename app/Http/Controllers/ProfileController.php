<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $tab = $request->query('tab', 'dashboard');
        $user = $request->user()->load('profile');

        $certificates = collect();
        $documents = collect();
        $certificatesSelect = collect();
        $certificatesCount = null;
        $documentsCount = null;

        if ($tab === 'dashboard') {
            $user->loadCount(['certificates', 'documents']);
            $certificatesCount = $user->certificates_count;
            $documentsCount = $user->documents_count;
        }

        if ($tab === 'certificates') {
            $certificates = $user->certificates()
                ->select(['id', 'user_id', 'certificate_name', 'certificate_type', 'qualification_title', 'expiration_date', 'status'])
                ->with(['documents' => function ($query) {
                    $query->latest();
                }])
                ->orderByDesc('expiration_date')
                ->paginate(10)
                ->withQueryString();
        }

        if ($tab === 'documents') {
            $documents = $user->documents()
                ->select(['id', 'user_id', 'type', 'document_name', 'certificate_no', 'issued_on', 'valid_until', 'original_name'])
                ->latest()
                ->paginate(10)
                ->withQueryString();

            $certificatesSelect = $user->certificates()
                ->select(['id', 'certificate_name', 'certificate_type', 'qualification_title'])
                ->orderBy('certificate_name')
                ->get();
        }

        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile,
            'certificates' => $certificates,
            'documents' => $documents,
            'certificatesSelect' => $certificatesSelect,
            'certificatesCount' => $certificatesCount,
            'documentsCount' => $documentsCount,
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

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
