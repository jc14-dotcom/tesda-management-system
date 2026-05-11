<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Document;
use App\Support\CacheBuster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:cv,certificate,other'],
            'certificate_no' => ['nullable', 'string', 'max:255'],
            'issued_on' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issued_on'],
            'certificate_id' => ['nullable', 'integer', 'exists:certificates,id'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $user = $request->user();

        if ($data['type'] === 'certificate' && empty($data['certificate_id'])) {
            return back()->withErrors([
                'certificate_id' => 'Certificate is required for certificate uploads.',
            ]);
        }

        if (! empty($data['certificate_id'])) {
            $certificate = Certificate::where('id', $data['certificate_id'])
                ->where('user_id', $user->id)
                ->first();

            if (! $certificate && ! $user->hasRole('admin')) {
                abort(403);
            }
        }

        $file = $request->file('file');
        $path = $file->store("documents/{$user->id}", 'local');

        if ($data['type'] === 'cv') {
            $user->documents()->where('type', 'cv')->update(['is_primary' => false]);
        }

        $user->documents()->create([
            'certificate_id' => $data['certificate_id'] ?? null,
            'document_name' => $data['document_name'],
            'certificate_no' => $data['certificate_no'] ?? null,
            'issued_on' => $data['issued_on'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'type' => $data['type'],
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'is_primary' => $data['type'] === 'cv',
        ]);

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'document-uploaded');
    }

    public function download(Request $request, Document $document): BinaryFileResponse
    {
        if (! $request->user()->hasRole('admin') && $document->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($document->path)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('local')->path($document->path),
            $document->document_name ?: $document->original_name
        );
    }

    public function view(Request $request, Document $document): View
    {
        if (! $request->user()->hasRole('admin') && $document->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($document->path)) {
            abort(404);
        }

        return view('documents.show', [
            'document' => $document,
            'previewUrl' => route('documents.preview', $document),
        ]);
    }

    public function preview(Request $request, Document $document)
    {
        if (! $request->user()->hasRole('admin') && $document->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($document->path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($document->path), [
            'Content-Disposition' => 'inline; filename="'.addslashes($document->document_name ?: $document->original_name).'"',
        ]);
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        if (! $request->user()->hasRole('admin') && $document->user_id !== $request->user()->id) {
            abort(403);
        }

        if (Storage::disk('local')->exists($document->path)) {
            Storage::disk('local')->delete($document->path);
        }

        $document->delete();

        CacheBuster::bumpUser($document->user_id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'document-deleted');
    }
}
