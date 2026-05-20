<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileDetailsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'middle_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\'-]*$/'],
            'suffix' => ['nullable', 'string', Rule::in(['jr', 'sr', 'ii', 'iii', 'iv', 'v'])],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string', Rule::in(['male', 'female'])],
            'contact_number' => ['required', 'digits:11', 'regex:/^09\d{9}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'company_id' => ['nullable', 'string', 'max:255'],
            'position_roles' => ['nullable', 'array'],
            'position_roles.*' => ['string', Rule::in(['trainer', 'assessor'])],
            'employment_status' => ['nullable', 'string', Rule::in(['regular', 'probationary', 'contractual', 'part-time', 'internship', 'self-employed', 'unemployed'])],
            'date_hired' => ['nullable', 'date'],
            'tesda_registry_number' => ['nullable', 'string', 'max:255'],
            'trainer_qualification_titles'   => ['nullable', 'array', 'max:20'],
            'trainer_qualification_titles.*'  => ['nullable', 'string', 'max:255'],
            'assessor_qualification_titles'   => ['nullable', 'array', 'max:20'],
            'assessor_qualification_titles.*' => ['nullable', 'string', 'max:255'],
            'branch' => ['nullable', 'string', 'max:100'],
        ]);

        $normalizeName = static function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            $value = preg_replace('/\s+/', ' ', trim($value));

            return $value === '' ? null : $value;
        };

        $data['first_name'] = $normalizeName($data['first_name'] ?? null);
        $data['middle_name'] = $normalizeName($data['middle_name'] ?? null);
        $data['last_name'] = $normalizeName($data['last_name'] ?? null);

        if (!empty($data['suffix'])) {
            $data['suffix'] = strtolower($data['suffix']);
        }

        if (!empty($data['employment_status'])) {
            $data['employment_status'] = strtolower($data['employment_status']);
        }

        if (!empty($data['gender'])) {
            $data['gender'] = strtolower($data['gender']);
        }

        if (!empty($data['contact_number'])) {
            $data['contact_number'] = preg_replace('/\D+/', '', $data['contact_number']);
        }

        // Reject if another account already shares the same full name + date of birth
        $duplicate = Profile::query()
            ->whereRaw('LOWER(TRIM(first_name))  = ?', [strtolower(trim($data['first_name']))])
            ->whereRaw('LOWER(TRIM(middle_name)) = ?', [strtolower(trim($data['middle_name']))])
            ->whereRaw('LOWER(TRIM(last_name))   = ?', [strtolower(trim($data['last_name']))])
            ->where('date_of_birth', $data['date_of_birth'])
            ->where('user_id', '!=', $request->user()->id)
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'first_name' => 'An account with this full name and date of birth already exists. '
                    . 'If this is your account, please contact the administrator to recover or reset your password.',
            ]);
        }

        // Only set position_title when roles were submitted — otherwise leave existing value intact
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

        // Only set position_title when roles were submitted — otherwise leave existing value intact
        if (!empty($data['position_roles']) && is_array($data['position_roles'])) {
            $data['position_title'] = implode(', ', array_map(static fn (string $role) => ucfirst($role), $data['position_roles']));
            // Keep position_roles as a JSON array for queryability alongside the display string
        } else {
            // Ensure we don't overwrite existing stored roles/title when the form didn't submit them
            unset($data['position_roles']);
        }

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        CacheBuster::bumpUser($request->user()->id);
        CacheBuster::bumpAdminUsers();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Profile details updated']);
        }

        return back()->with('status', 'profile-details-updated');
    }
}
