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
        $fromDate    = $request->query('from_date', '');
        $toDate      = $request->query('to_date', '');
        $causerId    = (int) $request->query('causer_id', 0);

        $subjectMap = [
            'user'        => \App\Models\User::class,
            'certificate' => \App\Models\Certificate::class,
            'document'    => \App\Models\Document::class,
            'profile'     => \App\Models\Profile::class,
        ];

        // Stats
        $totalCount    = Activity::count();
        $todayCount    = Activity::whereDate('created_at', today())->count();
        $thisWeekCount = Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $causerUser = $causerId > 0 ? \App\Models\User::select('id', 'name')->find($causerId) : null;

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
            ->when($causerId > 0, fn ($q) => $q
                ->where('causer_id', $causerId)
                ->where('causer_type', \App\Models\User::class)
            )
            ->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate,   fn ($q) => $q->whereDate('created_at', '<=', $toDate))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', compact(
            'logs', 'search', 'event', 'subjectType', 'fromDate', 'toDate',
            'totalCount', 'todayCount', 'thisWeekCount', 'causerId', 'causerUser'
        ));
    }
}
