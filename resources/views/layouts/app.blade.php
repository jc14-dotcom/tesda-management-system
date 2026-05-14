<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Apply sidebar collapsed state before first paint (prevents layout shift) --}}
        <script>if(localStorage.getItem('sidebar-collapsed')==='true'){document.documentElement.classList.add('sidebar-collapsed');}</script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-grayTheme-light text-grayTheme-dark">
        <div class="app-shell">
            @include('layouts.navigation')
            @include('layouts.sidebar')

            <main class="layout-main transition-all duration-200">
                {{ $slot }}
            </main>
        </div>
        {{-- Toast container — populated by vanilla JS (layout.js) --}}
        <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none" aria-live="polite" aria-atomic="false"></div>

        {{-- Global confirmation modal — trigger via window.showConfirm({...}) or data-confirm-message attribute --}}
        <div id="confirm-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
            <div id="confirm-modal-backdrop" class="fixed inset-0 bg-black/50"></div>
            <div class="relative w-full max-w-md bg-white rounded-card shadow-xl p-6 z-10">
                <div class="flex items-start gap-4">
                    <div id="confirm-modal-icon" class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-red-50">
                        <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.538-1.333-3.308 0L3.268 18c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h3 id="confirm-modal-title" class="text-base font-bold text-grayTheme-dark"></h3>
                        <p id="confirm-modal-message" class="mt-1 text-sm text-grayTheme-medium"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button id="confirm-modal-cancel" type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-button border border-grayTheme-border bg-white text-sm font-semibold text-grayTheme-dark shadow-sm transition hover:bg-grayTheme-light focus:outline-none focus:ring-2 focus:ring-primary/20">Cancel</button>
                    <button id="confirm-modal-confirm" type="button" class="btn-danger">Confirm</button>
                </div>
            </div>
        </div>

        {{-- Error modal — shown/hidden by vanilla JS (layout.js) --}}
        <div id="error-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="error-modal-title">
            <div id="error-modal-backdrop" class="fixed inset-0 bg-black/40"></div>
            <div class="relative max-w-2xl w-full bg-white rounded-lg shadow-lg p-6 z-10">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 id="error-modal-title" class="text-lg font-bold text-gray-900"></h3>
                        <p id="error-modal-message" class="mt-2 text-sm text-gray-600"></p>
                    </div>
                    <button class="text-gray-500 hover:text-gray-800 flex-shrink-0" data-close-error-modal aria-label="Close">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="error-modal-errors" hidden></div>
                <div class="mt-6 text-right">
                    <button class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200" data-close-error-modal>Close</button>
                </div>
            </div>
        </div>

        @stack('scripts')

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </body>
</html>
