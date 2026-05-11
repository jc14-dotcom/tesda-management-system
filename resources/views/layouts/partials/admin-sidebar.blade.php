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
    $adminActivity = $routeOrUrl('admin.activity.index', '/admin/activity');
    $adminBackups = $routeOrUrl('admin.backups.index', '/admin/backups');
    $adminSettings = $routeOrUrl('admin.settings.index', '/admin/settings');

    $onDashboard = request()->routeIs('admin.dashboard');
    $onUsers = request()->routeIs('admin.users.*');
    $onCertificates = request()->routeIs('admin.certificates.*') || request()->is('admin/certificates*');
    $onDocuments = request()->routeIs('admin.documents.*') || request()->is('admin/documents*');
    $onNotifications = request()->routeIs('admin.notifications.*') || request()->is('admin/notifications*');
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Dashboard</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Users</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Certificates</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Documents</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Notifications</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Activity Log</span>
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
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Backups</span>
        </a>
    </li>

    <li>
        <a href="{{ $adminSettings }}" @class([
            'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
            'bg-primary-hover border-l-4 border-accent text-white' => $onSettings,
            'text-white/80 hover:bg-primary-hover hover:text-white' => ! $onSettings,
        ])>
            <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                'text-accent' => $onSettings,
                'text-white/70' => ! $onSettings,
            ])>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13.46 8.291 3.849-3.849a1.5 1.5 0 0 1 2.122 0l.127.127a1.5 1.5 0 0 1 0 2.122l-3.84 3.838a4 4 0 0 0-2.258-2.238Zm0 0a4 4 0 0 1 2.263 2.238l3.662-3.662a8.961 8.961 0 0 1 0 10.27l-3.676-3.676m-2.25-5.17 3.678-3.676a8.961 8.961 0 0 0-10.27 0l3.662 3.662a4 4 0 0 0-2.238 2.258L4.615 6.863a8.96 8.96 0 0 0 0 10.27l3.662-3.662a4 4 0 0 0 2.258 2.238l-3.672 3.676a8.96 8.96 0 0 0 10.27 0l-3.662-3.662a4.001 4.001 0 0 0 2.238-2.262m0 0 3.849 3.848a1.5 1.5 0 0 1 0 2.122l-.127.126a1.499 1.499 0 0 1-2.122 0l-3.838-3.838a4 4 0 0 0 2.238-2.258Zm.29-1.461a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-7.718 1.471-3.84 3.838a1.5 1.5 0 0 0 0 2.122l.128.126a1.5 1.5 0 0 0 2.122 0l3.848-3.848a4 4 0 0 1-2.258-2.238Zm2.248-5.19L6.69 4.442a1.5 1.5 0 0 0-2.122 0l-.127.127a1.5 1.5 0 0 0 0 2.122l3.849 3.848a4 4 0 0 1 2.238-2.258Z" />
            </svg>
            <span x-show="!desktopCollapsed" x-transition class="ms-3 whitespace-nowrap">Settings</span>
        </a>
    </li>
</ul>
