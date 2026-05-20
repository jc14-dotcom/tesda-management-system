<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileCompletionController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($this->isProfileComplete($user)) {
            return redirect()->route('dashboard');
        }

        $profile = $user->profile;

        return view('profile.complete', compact('profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'            => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'middle_name'           => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'last_name'             => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'suffix'                => ['nullable', 'string', Rule::in(['jr', 'sr', 'ii', 'iii', 'iv', 'v'])],
            'date_of_birth'         => ['required', 'date'],
            'gender'                => ['required', 'string', Rule::in(['male', 'female'])],
            'contact_number'        => ['required', 'digits:11', 'regex:/^09\d{9}$/'],
            'address'               => ['nullable', 'string', 'max:500'],
            'position_roles'        => ['required', 'array', 'min:1'],
            'position_roles.*'      => ['string', Rule::in(['trainer', 'assessor'])],
            'trainer_qualification_titles'   => ['nullable', 'array', 'max:20'],
            'trainer_qualification_titles.*'  => ['nullable', 'string', 'max:255'],
            'assessor_qualification_titles'   => ['nullable', 'array', 'max:20'],
            'assessor_qualification_titles.*' => ['nullable', 'string', 'max:255'],
        ]);

        // Normalize names
        foreach (['first_name', 'middle_name', 'last_name'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = preg_replace('/\s+/', ' ', trim($data[$field])) ?: null;
            }
        }

        if (! empty($data['suffix']))         $data['suffix']         = strtolower($data['suffix']);
        if (! empty($data['gender']))         $data['gender']         = strtolower($data['gender']);
        if (! empty($data['contact_number'])) $data['contact_number'] = preg_replace('/\D+/', '', $data['contact_number']);

        // Reject if another user already has the same full name + date of birth
        foreach (['trainer_qualification_titles', 'assessor_qualification_titles'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = array_values(array_filter(
                    $data[$field],
                    static fn ($v) => trim($v ?? '') !== ''
                ));
                if (empty($data[$field])) {
                    $data[$field] = null;
                }
            }
        }

        // Reject if another user already has the same full name + date of birth
        $this->checkDuplicateIdentity($request->user()->id, $data);

        // Derive position_title from position_roles
        $data['position_title'] = implode(', ', array_map(
            static fn (string $r) => ucfirst($r),
            $data['position_roles']
        ));

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        CacheBuster::bumpUser($request->user()->id);
        CacheBuster::bumpAdminUsers();

        activity()
            ->causedBy($request->user())
            ->performedOn($request->user()->profile)
            ->event('profile_completed')
            ->log('User completed their profile on first login');

        return redirect()->route('dashboard')->with('profile_completed', true);
    }

    /**
     * Throw a validation error if another account already shares the same
     * full name (first + middle + last) and date of birth.
     */
    private function checkDuplicateIdentity(int $currentUserId, array $data): void
    {
        $exists = Profile::query()
            ->whereRaw('LOWER(TRIM(first_name))  = ?', [strtolower(trim($data['first_name']))])
            ->whereRaw('LOWER(TRIM(middle_name)) = ?', [strtolower(trim($data['middle_name']))])
            ->whereRaw('LOWER(TRIM(last_name))   = ?', [strtolower(trim($data['last_name']))])
            ->where('date_of_birth', $data['date_of_birth'])
            ->where('user_id', '!=', $currentUserId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'first_name' => 'An account with this full name and date of birth already exists. '
                    . 'If this is your account, please contact the administrator to recover or reset your password.',
            ]);
        }
    }

    private function isProfileComplete($user): bool
    {
        $profile = $user->profile;

        return $profile &&
            filled($profile->first_name) &&
            filled($profile->middle_name) &&
            filled($profile->last_name) &&
            filled($profile->date_of_birth) &&
            filled($profile->gender) &&
            filled($profile->contact_number) &&
            ! empty($profile->position_roles);
    }
}
