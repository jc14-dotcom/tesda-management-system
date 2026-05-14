<nav class="layout-nav sticky top-0 z-40 bg-white border-b border-grayTheme-border shadow-card transition-all duration-200">
    <!-- Primary Navigation Menu -->
    <div class="page-container">
        <div class="flex h-16 items-center">
            <div class="hidden sm:flex items-center">
                <button type="button" id="sidebar-desktop-toggle" aria-controls="app-sidebar" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-primary-hover bg-white text-primary shadow-card transition hover:bg-primary-soft hover:text-primary-hover">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path class="icon-collapsed" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4 12h16" />
                        <path class="icon-expanded" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-1 items-center justify-end gap-2">
                @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp

                <!-- Notification Bell + Panel (desktop) -->
                <div class="relative hidden sm:block" id="notif-panel-wrapper">
                    @php $recentNotifications = Auth::user()->notifications()->latest()->take(8)->get(); @endphp

                    <button
                        id="notif-panel-toggle"
                        type="button"
                        class="relative inline-flex h-9 w-9 items-center justify-center rounded-full border border-grayTheme-border bg-white text-grayTheme-medium shadow-card transition hover:border-primary hover:bg-primary-soft hover:text-primary focus:outline-none"
                        aria-label="Notifications"
                        aria-expanded="false"
                        aria-controls="notif-panel"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if ($unreadCount > 0)
                            <span id="notif-badge" class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-danger text-[10px] font-bold leading-none text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>

                    {{-- Notification panel --}}
                    <div
                        id="notif-panel"
                        class="absolute right-0 top-full z-50 mt-2 hidden w-[22rem] overflow-hidden rounded-card border border-grayTheme-border bg-white shadow-modal"
                        role="dialog"
                        aria-label="Notifications"
                    >
                        {{-- Header --}}
                        <div class="flex items-center justify-between border-b border-grayTheme-border px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-grayTheme-dark">Notifications</span>
                                <span id="notif-unread-badge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                            </div>
                            <button
                                id="notif-mark-all-btn"
                                type="button"
                                class="{{ $unreadCount > 0 ? '' : 'hidden' }} rounded px-2 py-1 text-xs font-semibold text-primary transition hover:bg-primary-soft hover:text-primary-hover focus:outline-none"
                                data-url="{{ route('account.notifications.mark-all-read') }}"
                            >Mark all as read</button>
                        </div>

                        {{-- List --}}
                        <div id="notif-list" class="max-h-[20rem] divide-y divide-grayTheme-border overflow-y-auto">
                            @forelse ($recentNotifications as $notif)
                                <div
                                    class="notif-item group relative flex cursor-pointer items-start gap-3 px-4 py-3.5 transition hover:bg-primary-soft/60 {{ $notif->read_at ? '' : 'bg-primary-soft/30' }}"
                                    data-notif-id="{{ $notif->id }}"
                                    data-read="{{ $notif->read_at ? '1' : '0' }}"
                                    data-read-url="{{ route('account.notifications.mark-read', $notif->id) }}"
                                    data-delete-url="{{ route('account.notifications.delete', $notif->id) }}"
                                    data-view-url="{{ route('account.notifications') }}"
                                    role="button"
                                    tabindex="0"
                                >
                                    <span data-notif-dot class="mt-2 h-2 w-2 shrink-0 rounded-full {{ $notif->read_at ? 'bg-transparent' : 'bg-primary' }}"></span>
                                    <div class="min-w-0 flex-1">
                                        <p data-notif-title class="truncate text-sm {{ $notif->read_at ? 'font-medium text-grayTheme-medium' : 'font-semibold text-grayTheme-dark' }}">
                                            {{ $notif->data['certificate_name'] ?? 'Notification' }}
                                        </p>
                                        <p class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-grayTheme-medium">
                                            @if (!empty($notif->data['days_until_expiry']))
                                                Expiring in <strong>{{ $notif->data['days_until_expiry'] }} {{ $notif->data['days_until_expiry'] == 1 ? 'day' : 'days' }}</strong>@if (!empty($notif->data['expiration_date'])) &mdash; {{ \Carbon\Carbon::parse($notif->data['expiration_date'])->format('M d, Y') }}@endif
                                            @else
                                                {{ $notif->data['message'] ?? 'You have a new notification.' }}
                                            @endif
                                        </p>
                                        <p class="mt-1 text-[11px] text-grayTheme-medium/70">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                    <button
                                        type="button"
                                        class="notif-delete-btn mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-grayTheme-medium opacity-0 transition hover:bg-red-100 hover:text-danger group-hover:opacity-100 focus:outline-none focus:opacity-100"
                                        aria-label="Delete notification"
                                    >
                                        <svg class="pointer-events-none h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                        <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">All caught up!</p>
                                    <p class="text-xs text-grayTheme-medium">No notifications to show.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div class="border-t border-grayTheme-border bg-grayTheme-light px-4 py-3">
                            <a href="{{ route('account.notifications') }}" class="flex w-full items-center justify-center gap-1.5 text-sm font-semibold text-primary transition hover:text-primary-hover">
                                View all notifications
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Settings Dropdown (desktop) -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-lg border border-transparent px-2 py-1.5 transition hover:border-grayTheme-border hover:bg-grayTheme-light focus:outline-none">
                                @php
                                    $photoUrl = Auth::user()->profile?->profile_photo_url;
                                    $initials = collect(explode(' ', Auth::user()->name))
                                        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                @endphp
                                @if ($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-primary/20" />
                                @else
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">{{ $initials }}</span>
                                @endif
                                <svg class="fill-current h-4 w-4 text-grayTheme-medium" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="border-b border-grayTheme-border px-4 py-3">
                                <p class="truncate text-xs font-bold text-grayTheme-dark">{{ Auth::user()->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-grayTheme-medium">{{ Auth::user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('account.profile')">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    {{ __('My Profile') }}
                                </div>
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); window.showConfirm({ title: 'Log Out?', message: 'You will be signed out of your account.', confirmText: 'Log Out', onConfirm: () => document.getElementById('logout-form').submit() })">
                                    <div class="flex items-center gap-2 text-danger">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        {{ __('Log Out') }}
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (mobile) -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button id="sidebar-mobile-toggle" aria-controls="app-sidebar" class="inline-flex items-center justify-center rounded-md p-2 text-grayTheme-medium transition duration-150 ease-in-out hover:bg-primary-soft hover:text-primary focus:bg-primary-soft focus:text-primary focus:outline-none">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path class="mobile-icon-menu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path class="mobile-icon-close hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
