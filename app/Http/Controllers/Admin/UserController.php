<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\Admin\UserStatusChangedNotification;
use App\Support\CacheBuster;
use App\Support\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', 'string', 'in:admin,user'],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);
        $user->profile()->create(['status' => 'active']);

        CacheBuster::bumpAdminUsers();

        return redirect()->route('admin.users.show', $user)->with('status', 'user-created');
    }

    public function approve(Request $request, User $user)
    {
        if ($user->profile?->status !== 'pending') {
            return back()->with('status', 'user-already-active');
        }

        $user->profile->update(['status' => 'active']);

        NotificationHelper::sendNowOrQueue($user, new AccountApprovedNotification());

        activity()
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('account_approved')
            ->log('User account approved by admin');

        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'user-approved');
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            abort(403, 'You cannot change your own status.');
        }

        $currentStatus = $user->profile?->status ?? 'active';
        // Pending → treat as active on toggle (same as approve without email)
        $newStatus     = $currentStatus === 'active' ? 'inactive' : 'active';

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['status'  => $newStatus]
        );

        // Notify other admins of the status change
        $changedBy = $request->user()->name;
        User::role('admin')
            ->where('id', '!=', $request->user()->id)
            ->each(fn (User $admin) => $admin->notify(
                new UserStatusChangedNotification($user, $newStatus, $changedBy)
            ));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'old'        => ['status' => $currentStatus],
                'attributes' => ['status' => $newStatus],
            ])
            ->event('updated')
            ->log('User status changed to ' . $newStatus);

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'user-updated');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('updated')
            ->log('Password was reset by admin');

        return back()->with('status', 'password-reset');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role   = $request->query('role', 'all');
        $status = $request->query('status', 'all');

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->with(['profile:id,user_id,status,profile_photo_path', 'roles:id,name'])
            ->withCount('certificates')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($role === 'admin', fn ($q) => $q->role('admin'))
            ->when($role === 'user', fn ($q) => $q->role('user'))
            ->when($status !== 'all', fn ($q) => $q->whereHas('profile', fn ($pq) => $pq->where('status', $status)))
            ->orderByRaw('EXISTS(SELECT 1 FROM model_has_roles mhr INNER JOIN roles r ON mhr.role_id = r.id WHERE mhr.model_id = users.id AND mhr.model_type = ? AND r.name = ?) DESC', [User::class, 'admin'])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'    => User::count(),
            'active'   => User::whereHas('profile', fn ($q) => $q->where('status', 'active'))->count(),
            'inactive' => User::whereHas('profile', fn ($q) => $q->where('status', 'inactive'))->count(),
            'pending'  => User::whereHas('profile', fn ($q) => $q->where('status', 'pending'))->count(),
        ];

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
            'role'   => $role,
            'status' => $status,
            'stats'  => $stats,
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
                'verification_status',
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
                'items' => $certificates->map(function ($cert) {
                    return [
                        'id'                 => $cert->id,
                        'name'               => $cert->certificate_name,
                        'type'               => $cert->certificate_type_label,
                        'qualification'      => $cert->qualification_title ?? '—',
                        'number'             => $cert->certificate_number ?? '—',
                        'expirationDate'     => $cert->expiration_date?->format('Y-m-d') ?? '—',
                        'status'             => ucfirst($cert->status),
                        'verificationStatus' => ucfirst($cert->verification_status ?? 'pending'),
                        'documents'          => $cert->documents->map(fn($d) => [
                            'name'        => $d->document_name ?? $d->original_name,
                            'downloadUrl' => route('documents.download', $d),
                        ])->values()->all(),
                    ];
                })->values(),
                'nextUrl' => $certificates->nextPageUrl(),
            ]);
        }

        if ($request->boolean('documents_partial')) {
            return response()->json([
                'items' => $documents->map(fn($doc) => [
                    'id'          => $doc->id,
                    'type'        => strtoupper($doc->type),
                    'name'        => $doc->document_name ?? $doc->original_name,
                    'downloadUrl' => route('documents.download', $doc),
                ])->values(),
                'nextUrl' => $documents->nextPageUrl(),
            ]);
        }

        return view('admin.users.show', [
            'user'         => $user,
            'certificates' => $certificates,
            'documents'    => $documents,
            'certStatus'   => $certStatus,
            'certWindow'   => $certWindow,
            'docType'      => $docType,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role'   => ['required', 'string', 'in:admin,user'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $oldRole = $user->roles->first()?->name;

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if ($oldRole !== $data['role']) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'old'        => ['role' => $oldRole ?? 'none'],
                    'attributes' => ['role' => $data['role']],
                ])
                ->event('updated')
                ->log('User role changed');
        }

        $user->syncRoles([$data['role']]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['status'  => $data['status']]
        );

        CacheBuster::bumpUser($user->id);
        CacheBuster::bumpAdminUsers();

        return back()->with('status', 'user-updated');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            abort(403, 'You cannot delete your own account.');
        }

        $user->load(['documents:id,user_id,path', 'profile:id,user_id,profile_photo_path']);

        // Delete individual document files
        foreach ($user->documents as $document) {
            if ($document->path && Storage::disk('local')->exists($document->path)) {
                Storage::disk('local')->delete($document->path);
            }
        }

        // Delete profile photo from both local and public disks
        if ($user->profile?->profile_photo_path) {
            foreach (['local', 'public'] as $disk) {
                $storage = Storage::disk($disk);
                if ($storage->exists($user->profile->profile_photo_path)) {
                    $storage->delete($user->profile->profile_photo_path);
                }
            }
        }

        // Clean up remaining directories
        Storage::disk('local')->deleteDirectory("documents/{$user->id}");
        Storage::disk('local')->deleteDirectory("profiles/{$user->id}");
        Storage::disk('public')->deleteDirectory("profiles/{$user->id}");

        CacheBuster::bumpAdminUsers();

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'user-deleted');
    }
}
