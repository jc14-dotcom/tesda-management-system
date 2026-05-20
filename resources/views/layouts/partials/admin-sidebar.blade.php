@php
    $routeOrUrl = function (?string $routeName, string $fallback, array $params = []) {
        if ($routeName && Route::has($routeName)) {
            return route($routeName, $params);
        }

        $query = $params ? ('?' . http_build_query($params)) : '';

        return url($fallback . $query);
    };

    $adminDashboard = $routeOrUrl('admin.dashboard', '/admin/dashboard');
    $adminUsers = $routeOrUrl('admin.users.index', '/admin/users');
    $adminCertificates = $routeOrUrl('admin.certificates.index', '/admin/certificates');
    $adminDocuments = $routeOrUrl('admin.documents.index', '/admin/documents');
    $adminNotifications = $routeOrUrl('admin.notifications.index', '/admin/notifications');
    $adminAnnouncements = $routeOrUrl('admin.announcements.index', '/admin/announcements');
    $adminActivity = $routeOrUrl('admin.activity.index', '/admin/activity');
    $adminBackups = $routeOrUrl('admin.backups.index', '/admin/backups');
    $adminSettings = $routeOrUrl('admin.settings.index', '/admin/settings');

    $onDashboard = request()->routeIs('admin.dashboard');
    $onUsers = request()->routeIs('admin.users.*');
    $onCertificates = request()->routeIs('admin.certificates.*') || request()->is('admin/certificates*');
    $onDocuments = request()->routeIs('admin.documents.*') || request()->is('admin/documents*');
    $onNotifications = request()->routeIs('admin.notifications.*') || request()->is('admin/notifications*');
    $onAnnouncements = request()->routeIs('admin.announcements.*') || request()->is('admin/announcements*');
    $onActivity = request()->routeIs('admin.activity.*') || request()->is('admin/activity*');
    $onBackups = request()->routeIs('admin.backups.*') || request()->is('admin/backups*');
    $onSettings = request()->routeIs('admin.settings.*') || request()->is('admin/settings*');
@endphp

<ul class="space-y-2 font-medium">
    <li>
        <a href="{{ $adminDashboard }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onDashboard,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onDashboard,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onDashboard,
                'text-white/70' => ! $onDashboard,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z" />
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Dashboard</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminUsers }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onUsers,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onUsers,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onUsers,
                'text-white/70' => ! $onUsers,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Users</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminCertificates }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onCertificates,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onCertificates,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onCertificates,
                'text-white/70' => ! $onCertificates,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6m-6 4h6" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Certificates</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminDocuments }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onDocuments,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onDocuments,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onDocuments,
                'text-white/70' => ! $onDocuments,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13h3.439a.991.991 0 0 1 .908.6 3.978 3.978 0 0 0 7.306 0 .99.99 0 0 1 .908-.6H20M4 13v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6M4 13l2-9h12l2 9M9 7h6m-7 3h8" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Documents</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminNotifications }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onNotifications,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onNotifications,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onNotifications,
                'text-white/70' => ! $onNotifications,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 1 1-6 0h6Z" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Notifications</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminAnnouncements }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onAnnouncements,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onAnnouncements,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onAnnouncements,
                'text-white/70' => ! $onAnnouncements,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317Z" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Announcements</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminActivity }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onActivity,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onActivity,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onActivity,
                'text-white/70' => ! $onActivity,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Activity Log</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminBackups }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onBackups,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onBackups,
        ])>
            <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onBackups,
                'text-white/70' => ! $onBackups,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7M9 11h6" />
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Backup &amp; Restore</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminSettings }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onSettings,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onSettings,
        ])>
            <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" @class([
                'text-accent' => $onSettings,
                'text-white/70' => ! $onSettings,
            ])>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
            <span class="sidebar-label ms-3 whitespace-nowrap">Settings</span>
        </a>
    </li>
</ul>
