{{-- $isAdminUser is injected by App\View\Composers\SidebarComposer (cached per user). --}}

<div id="sidebar-overlay" class="hidden fixed inset-0 z-30 bg-grayTheme-dark/40 sm:hidden" aria-hidden="true"></div>

<aside
    id="app-sidebar"
    class="fixed top-0 left-0 z-50 h-screen transform transition-all duration-200 ease-out"
    aria-label="Sidebar"
>
    <div class="flex h-full flex-col bg-primary text-white shadow-sidebar">
        <div class="sticky top-0 z-20 border-b border-white/10 bg-primary px-4 py-4">
            <div class="relative flex items-center justify-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-2 text-center" aria-label="Go to dashboard">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" alt="Alcatt system logo" class="h-14 w-14 object-contain sm:h-16 sm:w-16" />
                    <span class="sidebar-label text-[11px] font-semibold uppercase tracking-[0.24em] text-white/85">
                        Alcatt Portal
                    </span>
                </a>

            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-4">
        @if ($isAdminUser)
            @include('layouts.partials.admin-sidebar')
        @else
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('dashboard') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('dashboard'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('dashboard'),
                ])>
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('dashboard'),
                        'text-white/70' => !request()->routeIs('dashboard'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('account.profile') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('account.profile'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('account.profile'),
                ])>
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('account.profile'),
                        'text-white/70' => !request()->routeIs('account.profile'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">My Profile</span>
                </a>
            </li>

            <li>
                <a href="{{ route('account.certificates') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('account.certificates'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('account.certificates'),
                ])>
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('account.certificates'),
                        'text-white/70' => !request()->routeIs('account.certificates'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6m-6 4h6" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">My Certificates</span>
                </a>
            </li>

            <li>
                <a href="{{ route('account.documents') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('account.documents'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('account.documents'),
                ])>
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('account.documents'),
                        'text-white/70' => !request()->routeIs('account.documents'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13h3.439a.991.991 0 0 1 .908.6 3.978 3.978 0 0 0 7.306 0 .99.99 0 0 1 .908-.6H20M4 13v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6M4 13l2-9h12l2 9M9 7h6m-7 3h8" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">My Documents</span>
                </a>
            </li>

            <li>
                <a href="{{ route('account.notifications') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('account.notifications'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('account.notifications'),
                ])>
                    <svg class="w-5 h-5 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('account.notifications'),
                        'text-white/70' => !request()->routeIs('account.notifications'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13h16M6 9h12m-8 8h4" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">Notifications</span>
                </a>
            </li>

            <li>
                <a href="{{ route('account.settings') }}" @class([
                    'flex items-center px-2 py-1.5 rounded-lg transition duration-250 group',
                    'bg-primary-hover border-l-4 border-accent text-white' => request()->routeIs('account.settings'),
                    'text-white/80 hover:bg-primary-hover hover:text-white' => !request()->routeIs('account.settings'),
                ])>
                    <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" @class([
                        'text-accent' => request()->routeIs('account.settings'),
                        'text-white/70' => !request()->routeIs('account.settings'),
                    ])>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">Account Settings</span>
                </a>
            </li>

        </ul>
        @endif
        </div>
    </div>
</aside>
