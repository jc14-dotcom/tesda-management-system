<?php

namespace App\Http\Controllers;

use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'address' => ['required', 'string', 'max:500'],
            'company_id' => ['nullable', 'string', 'max:255'],
            'position_roles' => ['required', 'array', 'min:1', 'max:2'],
            'position_roles.*' => ['string', Rule::in(['trainer', 'assessor'])],
            'employment_status' => ['nullable', 'string', Rule::in(['regular', 'probationary', 'contractual', 'part-time', 'internship', 'self-employed', 'unemployed'])],
            'date_hired' => ['nullable', 'date'],
            'tesda_registry_number' => ['nullable', 'string', 'max:255'],
            'qualification_title' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:100'],
            'branch' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
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

        $data['position_title'] = implode(', ', array_map(static fn (string $role) => ucfirst($role), $data['position_roles']));
        // Keep position_roles as a JSON array for queryability alongside the display string

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
