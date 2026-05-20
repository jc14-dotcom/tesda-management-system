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
use Symfony\Component\HttpFoundation\HeaderUtils;

class DocumentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'document_name'  => ['nullable', 'string', 'max:255'],
            'type'           => ['required', 'string', 'in:cv,training,other'],
            'certificate_no' => ['nullable', 'string', 'max:255'],
            'issued_on'      => ['nullable', 'date'],
            'valid_until'    => ['nullable', 'date', 'after_or_equal:issued_on'],
            'certificate_id' => ['nullable', 'integer', 'exists:certificates,id'],
            'files'          => ['required', 'array', 'min:1'],
            'files.*'        => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,tif,tiff,doc,docx,xls,xlsx,ppt,pptx,txt,csv'],
        ]);

        $user          = $request->user();
        $type          = $request->input('type');
        $customName    = $request->filled('document_name') ? $request->input('document_name') : null;
        $certificateId = $request->input('certificate_id');

        if ($certificateId) {
            $certificate = Certificate::where('id', $certificateId)
                ->where('user_id', $user->id)
                ->first();

            if (! $certificate && ! $user->hasRole('admin')) {
                abort(403);
            }
        }

        // Demote all existing CVs before inserting new ones
        if ($type === 'cv') {
            $user->documents()->where('type', 'cv')->update(['is_primary' => false]);
        }

        $uploadedFiles = $request->file('files');
        $firstCv = true;

        foreach ($uploadedFiles as $file) {
            $isPrimary = false;
            if ($type === 'cv' && $firstCv) {
                $isPrimary = true;
                $firstCv   = false;
            }

            // Use the custom label only when a single file is uploaded; otherwise use filename
            $docName = ($customName && count($uploadedFiles) === 1)
                ? $customName
                : ($customName ? $customName . ' — ' . $file->getClientOriginalName() : $file->getClientOriginalName());

            $user->documents()->create([
                'certificate_id' => $certificateId,
                'document_name'  => $docName,
                'certificate_no' => $request->input('certificate_no'),
                'issued_on'      => $request->input('issued_on'),
                'valid_until'    => $request->input('valid_until'),
                'type'           => $type,
                'is_primary'     => $isPrimary,
                'path'           => $file->store("documents/{$user->id}", 'local'),
                'original_name'  => $file->getClientOriginalName(),
                'mime_type'      => $file->getClientMimeType(),
                'size'           => $file->getSize(),
            ]);
        }

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

        $name = $document->document_name ?: $document->original_name;
        $ext  = pathinfo($document->original_name ?? '', PATHINFO_EXTENSION);
        if ($ext && ! str_ends_with(strtolower($name), '.' . strtolower($ext))) {
            $name .= '.' . $ext;
        }

        $headers = $document->mime_type ? ['Content-Type' => $document->mime_type] : [];

        return response()->download(
            Storage::disk('local')->path($document->path),
            $name,
            $headers
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

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $document->document_name ?: $document->original_name,
            'document'
        );

        return response()->file(Storage::disk('local')->path($document->path), [
            'Content-Disposition' => $disposition,
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
