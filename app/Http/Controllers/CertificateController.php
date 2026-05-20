<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Document;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'certificate_type' => ['required', 'string', 'in:nc_i,nc_ii,nc_iii,nc_iv,nttc,assessor,other'],
            'qualification_title' => ['required', 'string', 'max:255'],
            'certificate_number' => ['required', 'string', 'max:255'],
            'issued_by' => ['required', 'string', 'max:255'],
            'issue_date' => ['nullable', 'date'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'certificate_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,tif,tiff'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = 'valid';
        if (! empty($data['expiration_date'])) {
            $expiration = Carbon::parse($data['expiration_date']);
            if ($expiration->isPast()) {
                $status = 'expired';
            } elseif ($expiration->lte(now()->addDays(30))) {
                $status = 'expiring';
            }
        }

        $user = $request->user();
        $data['certificate_name'] = $data['qualification_title'];
        $certificate = $user->certificates()->create([
            ...$data,
            'status' => $status,
            'notified_days' => [],
            'notification_count' => 0,
        ]);

        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $path = $file->store("documents/{$user->id}", 'local');

            Document::create([
                'user_id' => $user->id,
                'certificate_id' => $certificate->id,
                'document_name' => $certificate->certificate_name,
                'type' => 'certificate',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'is_primary' => false,
            ]);
        }

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'certificate-added');
    }

    public function show(Request $request, Certificate $certificate): \Illuminate\View\View
    {
        if ($certificate->user_id !== $request->user()->id) {
            abort(403);
        }

        $certificate->load('documents');

        $profile = $request->user()->profile;

        return view('user.certificates.show', compact('certificate', 'profile'));
    }

    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        if ($certificate->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'certificate_type'    => ['required', 'string', 'in:nc_i,nc_ii,nc_iii,nc_iv,nttc,assessor,other'],
            'qualification_title' => ['required', 'string', 'max:255'],
            'certificate_number'  => ['required', 'string', 'max:255'],
            'issued_by'           => ['required', 'string', 'max:255'],
            'issue_date'          => ['nullable', 'date'],
            'expiration_date'     => ['nullable', 'date', 'after_or_equal:issue_date'],
            'certificate_file'    => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,tif,tiff'],
            'remarks'             => ['nullable', 'string', 'max:1000'],
        ]);

        $status = 'valid';
        if (! empty($data['expiration_date'])) {
            $expiration = Carbon::parse($data['expiration_date']);
            if ($expiration->isPast()) {
                $status = 'expired';
            } elseif ($expiration->lte(now()->addDays(30))) {
                $status = 'expiring';
            }
        }

        $file = $request->file('certificate_file');
        unset($data['certificate_file']);
        $data['certificate_name'] = $data['qualification_title'];
        $data['status'] = $status;

        $certificate->update($data);

        if ($file) {
            $user = $request->user();
            foreach ($certificate->documents as $document) {
                if (Storage::disk('local')->exists($document->path)) {
                    Storage::disk('local')->delete($document->path);
                }
                $document->delete();
            }

            $path = $file->store("documents/{$user->id}", 'local');
            Document::create([
                'user_id'        => $user->id,
                'certificate_id' => $certificate->id,
                'document_name'  => $certificate->certificate_name,
                'type'           => 'certificate',
                'path'           => $path,
                'original_name'  => $file->getClientOriginalName(),
                'mime_type'      => $file->getClientMimeType(),
                'size'           => $file->getSize(),
                'is_primary'     => false,
            ]);
        }

        CacheBuster::bumpUser($certificate->user_id);
        CacheBuster::bumpAdminUsers();

        return redirect()->route('account.certificates.show', $certificate)
            ->with('status', 'certificate-updated');
    }

    public function destroy(Request $request, Certificate $certificate): RedirectResponse
    {
        if (! $request->user()->hasRole('admin') && $certificate->user_id !== $request->user()->id) {
            abort(403);
        }

        foreach ($certificate->documents as $document) {
            if (Storage::disk('local')->exists($document->path)) {
                Storage::disk('local')->delete($document->path);
            }

            $document->delete();
        }

        $certificate->delete();

        CacheBuster::bumpUser($certificate->user_id);
        CacheBuster::bumpAdminUsers();

        if ($request->user()->hasRole('admin')) {
            return redirect()->back()->with('status', 'certificate-deleted');
        }

        return redirect()->route('account.certificates')->with('status', 'certificate-deleted');
    }
}
