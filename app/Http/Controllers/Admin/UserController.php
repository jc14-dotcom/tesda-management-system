<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $page = (int) $request->query('page', 1);
        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->with([
                'profile:id,user_id,status',
                'roles:id,name',
            ])
            ->withCount('certificates')
            ->orderBy('name')
            ->paginate(20, ['*'], 'page', $page)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function show(Request $request, User $user)
    {
        $user->load('profile');

        $certStatus = $request->query('cert_status', 'all');
        $certWindow = (int) $request->query('cert_window', 0);
        $docType = $request->query('doc_type', 'all');
        $certPage = (int) $request->query('certificates_page', 1);
        $docPage = (int) $request->query('documents_page', 1);

        $query = $user->certificates()
            ->select([
                'id',
                'user_id',
                'certificate_name',
                'certificate_type',
                'qualification_title',
                'certificate_number',
                'expiration_date',
                'status',
            ])
            ->with(['documents' => function ($query) {
                $query->select(['id', 'certificate_id', 'document_name', 'original_name']);
            }]);

        if ($certStatus !== 'all') {
            $query->where('status', $certStatus);
        }

        if ($certWindow > 0) {
            $query
                ->whereNotNull('expiration_date')
                ->whereBetween('expiration_date', [
                    now()->toDateString(),
                    now()->addDays($certWindow)->toDateString(),
                ]);
        }

        $certificates = $query
            ->orderByDesc('expiration_date')
            ->paginate(10, ['*'], 'certificates_page', $certPage)
            ->withQueryString();

        $docQuery = $user->documents()
            ->select(['id', 'user_id', 'type', 'document_name', 'original_name', 'created_at'])
            ->where('type', '!=', 'certificate');

        if ($docType !== 'all') {
            $docQuery->where('type', $docType);
        }

        $documents = $docQuery
            ->latest()
            ->paginate(10, ['*'], 'documents_page', $docPage)
            ->withQueryString();

        if ($request->boolean('certificates_partial')) {
            return response()->json([
                'html' => view('admin.users.partials.certificates-rows', [
                    'certificates' => $certificates,
                ])->render(),
                'nextUrl' => $certificates->nextPageUrl(),
            ]);
        }

        if ($request->boolean('documents_partial')) {
            return response()->json([
                'html' => view('admin.users.partials.documents-items', [
                    'documents' => $documents,
                ])->render(),
                'nextUrl' => $documents->nextPageUrl(),
            ]);
        }

        return view('admin.users.show', [
            'user' => $user,
            'certificates' => $certificates,
            'documents' => $documents,
            'certStatus' => $certStatus,
            'certWindow' => $certWindow,
            'docType' => $docType,
        ]);
    }
}
