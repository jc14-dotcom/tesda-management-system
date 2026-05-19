<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $type   = $request->query('type', 'all');

        $documents = Document::with('user:id,name')
            ->select(['id', 'user_id', 'document_name', 'original_name', 'type', 'mime_type', 'size', 'created_at'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            }))
            ->when($type !== 'all', fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => Document::count(),
            'thisWeek' => Document::where('created_at', '>=', now()->startOfWeek())->count(),
            'totalSize' => Document::sum('size'),
        ];

        return view('admin.documents.index', [
            'documents' => $documents,
            'search'    => $search,
            'type'      => $type,
            'stats'     => $stats,
        ]);
    }

    public function show(Document $document): View
    {
        $document->load(['user', 'certificate']);

        return view('admin.documents.show', compact('document'));
    }
}
