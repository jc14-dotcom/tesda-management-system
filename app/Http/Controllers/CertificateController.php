<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'certificate_name' => ['required', 'string', 'max:255'],
            'certificate_type' => ['required', 'string', 'in:nc_i,nc_ii,nc_iii,nc_iv,nttc,trainer,assessor,other'],
            'qualification_title' => ['nullable', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'issued_by' => ['nullable', 'string', 'max:255'],
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
        $certificate = $user->certificates()->create([
            ...$data,
            'status' => $status,
            'verification_status' => 'pending',
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

        return back()->with('status', 'certificate-added');
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

        return back()->with('status', 'certificate-deleted');
    }
}
