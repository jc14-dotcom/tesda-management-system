<?php

namespace App\Http\Controllers;

use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'address'               => ['required', 'string', 'max:500'],
            'position_roles'        => ['required', 'array', 'min:1'],
            'position_roles.*'      => ['string', Rule::in(['trainer', 'assessor'])],
            'region'                => ['nullable', 'string', 'max:100'],
            'qualification_title'   => ['nullable', 'string', 'max:255'],
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
            filled($profile->address) &&
            ! empty($profile->position_roles);
    }
}
