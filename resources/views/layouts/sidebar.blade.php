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
                    <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-accent" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" @class([
                        'text-accent' => request()->routeIs('account.settings'),
                        'text-white/70' => !request()->routeIs('account.settings'),
                    ])>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13.46 8.291 3.849-3.849a1.5 1.5 0 0 1 2.122 0l.127.127a1.5 1.5 0 0 1 0 2.122l-3.84 3.838a4 4 0 0 0-2.258-2.238Zm0 0a4 4 0 0 1 2.263 2.238l3.662-3.662a8.961 8.961 0 0 1 0 10.27l-3.676-3.676m-2.25-5.17 3.678-3.676a8.961 8.961 0 0 0-10.27 0l3.662 3.662a4 4 0 0 0-2.238 2.258L4.615 6.863a8.96 8.96 0 0 0 0 10.27l3.662-3.662a4 4 0 0 0 2.258 2.238l-3.672 3.676a8.96 8.96 0 0 0 10.27 0l-3.662-3.662a4.001 4.001 0 0 0 2.238-2.262m0 0 3.849 3.848a1.5 1.5 0 0 1 0 2.122l-.127.126a1.499 1.499 0 0 1-2.122 0l-3.838-3.838a4 4 0 0 0 2.238-2.258Zm.29-1.461a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm-7.718 1.471-3.84 3.838a1.5 1.5 0 0 0 0 2.122l.128.126a1.5 1.5 0 0 0 2.122 0l3.848-3.848a4 4 0 0 1-2.258-2.238Zm2.248-5.19L6.69 4.442a1.5 1.5 0 0 0-2.122 0l-.127.127a1.5 1.5 0 0 0 0 2.122l3.849 3.848a4 4 0 0 1 2.238-2.258Z" />
                    </svg>
                    <span class="sidebar-label ms-3 whitespace-nowrap">Account Settings</span>
                </a>
            </li>

        </ul>
        @endif
        </div>
    </div>
</aside>
