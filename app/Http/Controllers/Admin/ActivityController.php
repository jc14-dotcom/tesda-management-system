<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search      = trim((string) $request->query('search', ''));
        $event       = $request->query('event', 'all');
        $subjectType = $request->query('subject_type', 'all');

        $subjectMap = [
            'user'        => \App\Models\User::class,
            'certificate' => \App\Models\Certificate::class,
            'document'    => \App\Models\Document::class,
        ];

        $logs = Activity::with(['causer', 'subject'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHasMorph('causer', [\App\Models\User::class], fn ($q) =>
                      $q->where('name', 'like', "%{$search}%")
                  );
            }))
            ->when($event !== 'all', fn ($q) => $q->where('event', $event))
            ->when($subjectType !== 'all' && isset($subjectMap[$subjectType]), fn ($q) =>
                $q->where('subject_type', $subjectMap[$subjectType])
            )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', [
            'logs'        => $logs,
            'search'      => $search,
            'event'       => $event,
            'subjectType' => $subjectType,
        ]);
    }
}
